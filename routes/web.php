<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Mail;

// Временный тестовый маршрут
Route::get('/test-simple', function () {
    return view('test-simple');
});

// Тестовый маршрут для проверки email
Route::get('/test-mail', function () {
    Mail::raw('Письмо дошло в Mailpit', function ($message) {
        $message->to('test@example.com')
            ->subject('Тест Mailpit');
    });
    
    return 'Email отправлен! Проверьте Mailpit на http://localhost:8025';
});

// Тестовый маршрут для проверки восстановления пароля
Route::get('/test-password-reset', function () {
    try {
        // Получаем первого пользователя из БД
        $user = \App\Models\User::first();
        
        if (!$user) {
            return 'Ошибка: Пользователи не найдены в БД';
        }
        
        // Генерируем временный пароль
        $temporaryPassword = \Illuminate\Support\Str::random(4) . rand(1000, 9999);
        
        // Обновляем пароль в БД
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($temporaryPassword),
            'password_reset_required' => true,
        ]);
        
        // Отправляем email
        Mail::send('emails.password-reset', [
            'user' => $user,
            'temporaryPassword' => $temporaryPassword,
        ], function ($message) use ($user) {
            $message->to($user->email, $user->name)
                    ->subject('Восстановление пароля - ' . config('app.name'));
        });
        
        return "Email восстановления пароля отправлен на {$user->email}!<br>" .
               "Временный пароль: <strong>{$temporaryPassword}</strong><br>" .
               "Проверьте Mailpit на http://localhost:8025";
               
    } catch (\Exception $e) {
        return 'Ошибка: ' . $e->getMessage();
    }
});
use App\Http\Controllers\PageController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\SecondaryNavController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\YmlController;
use App\Http\Controllers\Exchange1cController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\ShowcaseController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\WholesaleProfileController;
use App\Http\Controllers\Api\DaDataController;


// ============================================
// API Routes
// ============================================
Route::prefix('api')->group(function () {
    // DaData API
    Route::post('/dadata/check-inn', [DaDataController::class, 'checkInn'])->name('api.dadata.check-inn');
});

// ============================================
// Главная
// ============================================
Route::get('/', [ShowcaseController::class, 'index'])->name('home');

// ============================================
// Авторизация / Регистрация
// ============================================
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'store']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Восстановление пароля
Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetEmail'])->name('password.email');

// =============================================
// Catalog
// =============================================
// Эти маршруты заменяют старые и обеспечивают правильные имена 'catalog.index' и 'catalog.category'
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{category_id}', [CatalogController::class, 'index'])->name('catalog.category');

// ============================================
// Товар
// ============================================
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product');

// ============================================
// Авторы
// ============================================
Route::get('/authors', [App\Http\Controllers\AuthorController::class, 'index'])->name('authors');
// Поддержка обоих форматов URL для обратной совместимости:
// - Новый формат: /author/{slug}
// - Старый формат: /author?author_name=...
Route::get('/author/{slug?}', [App\Http\Controllers\AuthorController::class, 'show'])->name('author');

// ============================================
// Серии
// ============================================
Route::get('/series', [App\Http\Controllers\SeriesController::class, 'index'])->name('series');
// Поддержка обоих форматов URL для обратной совместимости:
// - Новый формат: /seriya/{slug}
// - Старый формат: /seriya?seriya_id=...
Route::get('/seriya/{slug?}', [App\Http\Controllers\SeriesController::class, 'show'])->name('seriya');

// ============================================
// Тематики
// ============================================
Route::get('/topics', [App\Http\Controllers\TopicController::class, 'index'])->name('topics');
// Поддержка обоих форматов URL для обратной совместимости:
// - Новый формат: /topic/{slug}
// - Старый формат: /topic?topic_id=...
Route::get('/topic/{slug?}', [App\Http\Controllers\TopicController::class, 'show'])->name('topic');

// ============================================
// Типы товаров
// ============================================
Route::get('/product_types', [App\Http\Controllers\ProductTypeController::class, 'index'])->name('product_types');
// Поддержка обоих форматов URL для обратной совместимости:
// - Новый формат: /product_type/{slug}
// - Старый формат: /product_type?product_type_id=...
Route::get('/product_type/{slug?}', [App\Http\Controllers\ProductTypeController::class, 'show'])->name('product_type');

// ============================================
// Хештеги
// ============================================
Route::get('/hashtag/{slug}', [CatalogController::class, 'hashtag'])->name('hashtag');

// ============================================
// Корзина
// ============================================
Route::get('/cart', [CartController::class, 'index'])->name('cart');
// AJAX endpoint перенесен в routes/api.php

// ============================================
// Избранное
// ============================================
Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites');

// ============================================
// Оформление заказа (доступно всем)
// ============================================
Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [OrderController::class, 'placeOrder'])->name('checkout.place');
Route::post('/checkout/get-pickpoint-list', [OrderController::class, 'getPickpointList'])->name('checkout.pickpoint.list');
Route::post('/checkout/get-pickpoint-data', [OrderController::class, 'getPickpointData'])->name('checkout.pickpoint.data');
Route::get('/thankyoupage', [OrderController::class, 'thankyoupage'])->name('thankyoupage');

// ============================================
// Личные кабинеты (требуют авторизации)
// ============================================
Route::middleware('auth')->group(function () {
    // Розничный кабинет (мой профиль)
    Route::prefix('my')->name('my.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile');
        Route::post('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/orders', [OrdersController::class, 'index'])->name('orders');
    });

    // Оптовый кабинет (личный кабинет организации)
    Route::middleware('wholesale')->prefix('lk')->name('lk.')->group(function () {
        Route::get('/', [App\Http\Controllers\WholesaleProfileController::class, 'index'])->name('index');
        Route::get('/organization', [App\Http\Controllers\WholesaleProfileController::class, 'organization'])->name('organization');
        Route::get('/profile', [App\Http\Controllers\WholesaleProfileController::class, 'profile'])->name('profile');
        Route::get('/profile/edit', [App\Http\Controllers\WholesaleProfileController::class, 'editProfile'])->name('profile.edit');
        Route::post('/profile/update', [App\Http\Controllers\WholesaleProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/update-password', [App\Http\Controllers\WholesaleProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::get('/orders', [App\Http\Controllers\WholesaleProfileController::class, 'orders'])->name('orders');
        Route::get('/orders/{id}', [App\Http\Controllers\WholesaleProfileController::class, 'showOrder'])->name('orders.show');
        Route::get('/orders/{id}/repeat', [App\Http\Controllers\WholesaleProfileController::class, 'repeatOrder'])->name('orders.repeat');
        Route::get('/product/{id}', [App\Http\Controllers\WholesaleProfileController::class, 'showProduct'])->name('product.show');
    });

    // Алиасы для обратной совместимости
    Route::get('/profile', function () {
        return redirect()->route('my.profile');
    });
    Route::get('/orders', function () {
        if (auth()->user()->isWholesale()) {
            return redirect()->route('lk.orders');
        }
        return redirect()->route('my.orders');
    });
});

// ============================================
// Поиск
// ============================================
Route::get('/search', [SearchController::class, 'index'])->name('search');

// ============================================
// AJAX-запросы
// ============================================
Route::prefix('ajax')->group(function () {
    Route::post('/add-to-cart', [AjaxController::class, 'addToCart']);
    Route::post('/remove-from-cart', [AjaxController::class, 'removeFromCart']);
    Route::post('/toggle-favorite', [AjaxController::class, 'toggleFavorite']);
    // ... добавьте свои ajax-методы
});

// ============================================
// YML фид
// ============================================
Route::get('/yml', [YmlController::class, 'index'])->name('yml');

// ============================================
// Обмен 1С
// ============================================
Route::match(['get', 'post'], '/exchange1c', [Exchange1cController::class, 'index']);

// =============================================
// Sitemap
// =============================================
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// ============================================
// API маршруты для AJAX-запросов компонентов UI
// ============================================
// Эти маршруты используются для динамической загрузки данных в header, footer и других компонентах
// Миграция из legacy системы: legacy/site/modules/sfera/ → Laravel API endpoints

// Автодополнение поиска (используется в header.blade.php)
// Legacy: search.class.php::autocomplete()
Route::get('/api/search/autocomplete', [SearchController::class, 'autocomplete'])->name('api.search.autocomplete');

// Подкатегории меню (используется для динамической загрузки в catalog-menu.blade.php)
// Legacy: catalog_menu.class.php::getChildren()
Route::get('/api/menu/subcategories/{categoryId}', [MenuController::class, 'getSubcategories'])->name('api.menu.subcategories');

// Примечание: маршруты /api/cart и /api/favorites уже работают через AjaxController
// Они используются для обновления счетчиков корзины и избранного в header и mobile-bottom-nav

// ============================================
// Статические страницы (универсальный роут — ДОЛЖЕН БЫТЬ ПОСЛЕДНИМ!)
// ============================================
// Все страницы создаются динамически через добавление записей в таблицу pages
// Примеры: /page/О%20нас, /page/Контакты, /page/13 (по ID)
// В footer используются ссылки вида: route('page', ['slug' => 'О нас'])
Route::get('/page/{slug}', [PageController::class, 'show'])->name('page');

// Роут для мобильного меню (алиас для /page/{slug})
// Используется в mobile-menu.blade.php для разделов: pedagogam, rukovoditelyam, logopedam и т.д.
Route::get('/section/{slug}', [PageController::class, 'show'])->name('section');

// ============================================
// 404 — fallback (если ничего не подошло)
// ============================================

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

// ============================================
// Админ-панель
// ============================================
// Подключаем административные маршруты
Route::prefix('admin')
    ->name('admin.')
    ->group(base_path('routes/admin.php'));
