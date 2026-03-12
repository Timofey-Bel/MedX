<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Organization;
use App\Services\DaDataService;

/**
 * Контроллер авторизации и регистрации
 * 
 * Обрабатывает вход, регистрацию и выход пользователей
 */
class AuthController extends Controller
{
    protected DaDataService $dadataService;

    public function __construct(DaDataService $dadataService)
    {
        $this->dadataService = $dadataService;
    }
    /**
     * Отображение формы входа
     * 
     * @return \Illuminate\View\View
     */
    public function login()
    {
        // Если пользователь уже авторизован, перенаправляем в соответствующий кабинет
        if (Auth::check()) {
            $user = Auth::user();
            return redirect()->route($user->isWholesale() ? 'lk.index' : 'my.profile');
        }
        
        return view('auth.login');
    }

    /**
     * Обработка входа пользователя
     * 
     * Валидирует данные и аутентифицирует пользователя
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authenticate(Request $request)
    {
        // Валидация входных данных
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Введите email',
            'email.email' => 'Введите корректный email',
            'password.required' => 'Введите пароль',
        ]);

        // Попытка аутентификации
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            // Проверяем, нужно ли принудительно сменить пароль
            if (isset($user->password_reset_required) && $user->password_reset_required) {
                return redirect()->route('my.profile')->with('warning', 'Пожалуйста, смените временный пароль на новый в настройках профиля.');
            }

            // Определяем куда перенаправить пользователя в зависимости от типа
            $intendedUrl = $user->isWholesale() 
                ? route('lk.index') 
                : route('my.profile');

            // Перенаправление на страницу, с которой пришел пользователь, или в соответствующий кабинет
            return redirect()->intended($intendedUrl);
        }

        // Если аутентификация не удалась
        return back()->withErrors([
            'email' => 'Неверный email или пароль.',
        ])->onlyInput('email');
    }

    /**
     * Отображение формы регистрации
     * 
     * @return \Illuminate\View\View
     */
    public function register()
    {
        // Если пользователь уже авторизован, перенаправляем в соответствующий кабинет
        if (Auth::check()) {
            $user = Auth::user();
            return redirect()->route($user->isWholesale() ? 'lk.index' : 'my.profile');
        }
        
        return view('auth.register');
    }

    /**
     * Обработка регистрации нового пользователя
     * 
     * Валидирует данные, создает пользователя и выполняет вход
     * Поддерживает регистрацию как физических, так и юридических лиц
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $userType = $request->input('user_type', 'retail');

        // Базовая валидация для всех типов пользователей
        $rules = [
            'user_type' => ['required', 'in:retail,wholesale'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];

        $messages = [
            'name.required' => 'Введите имя',
            'name.max' => 'Имя не должно превышать 255 символов',
            'email.required' => 'Введите email',
            'email.email' => 'Введите корректный email',
            'email.unique' => 'Пользователь с таким email уже существует',
            'phone.required' => 'Введите номер телефона',
            'phone.max' => 'Номер телефона не должен превышать 20 символов',
            'password.required' => 'Введите пароль',
            'password.min' => 'Пароль должен содержать минимум 6 символов',
            'password.confirmed' => 'Пароли не совпадают',
        ];

        // Дополнительная валидация для юридических лиц
        if ($userType === 'wholesale') {
            $rules['inn'] = ['required', 'string', 'min:10', 'max:12'];
            $rules['org_name_full'] = ['required', 'string', 'max:500'];
            
            $messages['inn.required'] = 'Введите ИНН организации';
            $messages['inn.min'] = 'ИНН должен содержать минимум 10 цифр';
            $messages['inn.max'] = 'ИНН должен содержать максимум 12 цифр';
            $messages['org_name_full.required'] = 'Введите полное наименование организации';
        }

        $validated = $request->validate($rules, $messages);

        // Начинаем транзакцию
        DB::beginTransaction();

        try {
            $orgId = null;

            // Если регистрируется юридическое лицо
            if ($userType === 'wholesale') {
                $orgId = $this->createOrUpdateOrganization($request);
            }

            // Создание пользователя
            DB::insert("
                INSERT INTO users (name, email, phone, user_type, org_id, password, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
            ", [
                $validated['name'],
                $validated['email'],
                $validated['phone'],
                $userType,
                $orgId,
                Hash::make($validated['password'])
            ]);

            // Получаем созданного пользователя
            $user = DB::selectOne("
                SELECT * FROM users WHERE email = ?
            ", [$validated['email']]);

            // Преобразуем stdClass в модель User для аутентификации
            $userModel = User::find($user->id);

            // Автоматический вход после регистрации
            Auth::login($userModel);

            DB::commit();

            // Перенаправление в соответствующий кабинет
            if ($userType === 'wholesale') {
                return redirect()->route('lk.index')->with('success', 'Регистрация успешно завершена! Добро пожаловать в оптовый кабинет.');
            }

            return redirect()->route('my.profile')->with('success', 'Регистрация успешно завершена!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors([
                'error' => 'Ошибка при регистрации: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Создать или обновить организацию
     * 
     * @param Request $request
     * @return int ID организации
     */
    protected function createOrUpdateOrganization(Request $request): int
    {
        $inn = preg_replace('/\D/', '', $request->input('inn'));

        // Проверяем, существует ли организация с таким ИНН
        $existingOrg = Organization::where('inn', $inn)->first();

        if ($existingOrg) {
            // Обновляем существующую организацию
            $existingOrg->update([
                'name_full' => $request->input('org_name_full'),
                'name_short' => $request->input('org_name_short'),
                'kpp' => $request->input('org_kpp'),
                'ogrn' => $request->input('org_ogrn'),
                'legal_address' => $request->input('org_legal_address'),
                'director_name' => $request->input('org_director_name'),
                'director_position' => $request->input('org_director_position'),
                'opf' => $request->input('org_opf'),
                'status' => $request->input('org_status', 'active'),
                'dadata_json' => $request->input('dadata_json'),
            ]);

            return $existingOrg->id;
        }

        // Создаем новую организацию
        $organization = Organization::create([
            'inn' => $inn,
            'kpp' => $request->input('org_kpp'),
            'ogrn' => $request->input('org_ogrn'),
            'name_full' => $request->input('org_name_full'),
            'name_short' => $request->input('org_name_short'),
            'legal_address' => $request->input('org_legal_address'),
            'director_name' => $request->input('org_director_name'),
            'director_position' => $request->input('org_director_position'),
            'opf' => $request->input('org_opf'),
            'status' => $request->input('org_status', 'active'),
            'dadata_json' => $request->input('dadata_json'),
        ]);

        return $organization->id;
    }

    /**
     * Отображение формы восстановления пароля
     * 
     * @return \Illuminate\View\View
     */
    public function forgotPassword()
    {
        \Log::info('AuthController::forgotPassword called');
        
        // Если пользователь уже авторизован, перенаправляем в соответствующий кабинет
        if (Auth::check()) {
            \Log::info('User already authenticated, redirecting');
            $user = Auth::user();
            return redirect()->route($user->isWholesale() ? 'lk.index' : 'my.profile');
        }
        
        \Log::info('Rendering forgot-password view');
        
        try {
            $view = view('auth.forgot-password-minimal');
            \Log::info('Using minimal view for forgot-password');
            return $view;
        } catch (\Exception $e) {
            \Log::error('Error rendering forgot-password view: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Отправка email с временным паролем
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Введите email',
            'email.email' => 'Введите корректный email',
            'email.exists' => 'Пользователь с таким email не найден',
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors([
                'email' => 'Пользователь с таким email не найден.',
            ])->onlyInput('email');
        }

        // Генерируем временный пароль
        $temporaryPassword = $this->generateTemporaryPassword();
        
        // Сохраняем временный пароль в базе данных
        $user->update([
            'password' => Hash::make($temporaryPassword),
            'password_reset_required' => true, // Флаг для принудительной смены пароля
            'updated_at' => now()
        ]);

        // Отправляем email с временным паролем
        try {
            Mail::send('emails.password-reset', [
                'user' => $user,
                'temporaryPassword' => $temporaryPassword,
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Восстановление пароля - ' . config('app.name'));
            });

            return back()->with('success', 'Временный пароль отправлен на ваш email. Проверьте почту и войдите с новым паролем.');
            
        } catch (\Exception $e) {
            // В случае ошибки отправки email, возвращаем временный пароль обратно
            return back()->withErrors([
                'email' => 'Ошибка отправки email. Попробуйте позже или обратитесь в поддержку.',
            ])->onlyInput('email');
        }
    }

    /**
     * Генерация временного пароля
     * 
     * @return string
     */
    protected function generateTemporaryPassword(): string
    {
        // Генерируем пароль из 8 символов: буквы и цифры
        return Str::random(4) . rand(1000, 9999);
    }

    /**
     * Выход пользователя из системы
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
