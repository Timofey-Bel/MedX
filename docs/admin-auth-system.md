# Система авторизации админ-панели

## Обзор

Система авторизации для админ-панели реализована в стиле **Windows 10 Lock Screen** с полноэкранным интерфейсом, анимациями и современным дизайном.

## Компоненты системы

### 1. Страница входа (Login View)

**Файл:** `resources/views/admin/auth/login.blade.php`

**Особенности:**
- Полноэкранный фон с затемнением
- Круглый аватар пользователя с градиентом
- Плавные анимации (fade-in, slide-up, shake)
- Поле пароля появляется при клике на аватар
- Дата и время в реальном времени (обновляется каждую секунду)
- Кнопка "На сайт" для возврата на главную страницу
- Shake-эффект при неверном пароле
- Автоматическое отображение формы при ошибке

**Дизайн:**
- Аватар: 150px круг с градиентом (#667eea → #764ba2)
- Поле пароля: 300px ширина, полупрозрачный фон с blur
- Кнопка входа: синяя (#0078d7), стрелка вправо
- Время: 72px шрифт, внизу слева
- Дата: 24px шрифт, формат "Понедельник, 1 января"

### 2. Контроллер авторизации

**Файл:** `app/Http/Controllers/Admin/AuthController.php`

**Методы:**

#### `showLoginForm()`
- Показывает форму входа
- Если пользователь уже авторизован → редирект на desktop

#### `login(Request $request)`
- Валидация: login (required), password (required)
- Поиск пользователя в таблице `admin_users`
- Проверка: `active = 1`
- Проверка пароля через `Hash::check()`
- Сохранение данных в сессию: `admin_user` (id, login, name, email)
- Обновление `last_login` в БД
- Редирект на `admin.desktop`

#### `logout()`
- Удаление сессии `admin_user`
- Редирект на `admin.login`

### 3. Middleware авторизации

**Файл:** `app/Http/Middleware/AdminAuth.php`

**Логика:**
- Проверяет наличие `session('admin_user')`
- Если нет → редирект на `admin.login`
- Если есть → пропускает запрос дальше

**Регистрация:** В `bootstrap/app.php` как `admin.auth`

### 4. Роуты

**Файл:** `routes/admin.php`

**Публичные роуты (без middleware):**
```php
GET  /admin/login  → showLoginForm()
POST /admin/login  → login()
GET  /admin/logout → logout()
```

**Защищенные роуты (с middleware admin.auth):**
```php
GET /admin/           → desktop
GET /admin/permissions → permissions
GET /admin/users      → users
GET /admin/import     → import
GET /admin/reviews    → reviews
GET /admin/app_installer → app_installer
GET /admin/page_builder → page_builder
```

### 5. Интеграция с AdminDesktopController

**Изменения:**
- Убраны mock данные `$adminUser`
- Используется `session('admin_user')` для получения данных администратора
- Данные передаются в view как `$admin_user`

## Структура таблицы admin_users

```sql
CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    login VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,  -- bcrypt hash
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    active TINYINT(1) DEFAULT 1,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Создание администратора

### Через Tinker

```bash
& "C:\OS\modules\PHP-8.5\php.exe" artisan tinker
```

```php
DB::table('admin_users')->insert([
    'login' => 'admin',
    'password' => Hash::make('your_password'),
    'name' => 'Администратор',
    'email' => 'admin@sfera.local',
    'active' => 1
]);
```

### Через SQL

```sql
INSERT INTO admin_users (login, password, name, email, active)
VALUES (
    'admin',
    '$2y$12$...',  -- bcrypt hash пароля
    'Администратор',
    'admin@sfera.local',
    1
);
```

## Использование в контроллерах

### Получение данных текущего администратора

```php
$adminUser = session('admin_user');

// Доступные поля:
// $adminUser['id']    - ID администратора
// $adminUser['login'] - Логин
// $adminUser['name']  - Имя
// $adminUser['email'] - Email
```

### Проверка авторизации в Blade

```blade
@if(session('admin_user'))
    <p>Привет, {{ session('admin_user')['name'] }}!</p>
@else
    <p>Вы не авторизованы</p>
@endif
```

### Добавление middleware к роуту

```php
Route::middleware(['admin.auth'])->group(function () {
    Route::get('/admin/settings', [SettingsController::class, 'index']);
});
```

## Тестирование

### 1. Проверка редиректа на login

```
Открыть: http://sfera/admin
Ожидается: Редирект на http://sfera/admin/login
```

### 2. Проверка страницы входа

```
Открыть: http://sfera/admin/login
Ожидается:
- Полноэкранный фон Windows 10
- Аватар с иконкой person
- Текст "Администратор"
- Дата и время внизу слева
- Кнопка "← На сайт" внизу справа
```

### 3. Проверка клика на аватар

```
Действие: Клик на аватар
Ожидается:
- Появление поля пароля (slide-up анимация)
- Появление кнопки "Назад"
- Фокус на поле пароля
```

### 4. Проверка неверного пароля

```
Действие: Ввести неверный пароль и нажать Enter
Ожидается:
- Shake-эффект
- Сообщение "Неверный логин или пароль" (красный фон)
- Форма остается открытой
```

### 5. Проверка успешного входа

```
Действие: Ввести правильный пароль и нажать Enter
Ожидается:
- Редирект на http://sfera/admin (desktop)
- Отображение Windows 10 Desktop UI
- Имя администратора в интерфейсе
```

### 6. Проверка выхода

```
Действие: Открыть http://sfera/admin/logout
Ожидается:
- Редирект на http://sfera/admin/login
- Сессия очищена
- При попытке открыть /admin → редирект на login
```

## Безопасность

### Реализовано

1. **CSRF защита:** Токен `@csrf` в форме
2. **Хеширование паролей:** `Hash::make()` и `Hash::check()`
3. **Проверка активности:** `active = 1` в БД
4. **Middleware защита:** Все роуты админ-панели защищены
5. **Сессионная авторизация:** Данные хранятся в сессии Laravel

### Рекомендации

1. **Rate limiting:** Добавить ограничение попыток входа
2. **2FA:** Двухфакторная аутентификация (опционально)
3. **Логирование:** Записывать попытки входа в лог
4. **IP whitelist:** Ограничение доступа по IP (опционально)
5. **Session timeout:** Автоматический выход после неактивности

## Кастомизация

### Изменение фона

В `login.blade.php` замените URL фона:

```css
background: url('YOUR_IMAGE_URL') center center no-repeat;
```

Или используйте градиент:

```css
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

### Изменение цветов

```css
/* Аватар */
.user-avatar {
    background: linear-gradient(135deg, #YOUR_COLOR_1 0%, #YOUR_COLOR_2 100%);
}

/* Кнопка входа */
.submit-btn {
    background: #YOUR_COLOR;
}
```

### Изменение имени пользователя

В `login.blade.php`:

```html
<div class="user-name">Ваше имя</div>
```

Или динамически из БД:

```blade
<div class="user-name">{{ $defaultAdminName ?? 'Администратор' }}</div>
```

## Troubleshooting

### Проблема: "Неверный логин или пароль" при правильных данных

**Решение:**
1. Проверьте, что пароль захеширован через `Hash::make()`
2. Проверьте, что `active = 1` в таблице `admin_users`
3. Проверьте логин (регистрозависимый)

### Проблема: Редирект на login после успешного входа

**Решение:**
1. Проверьте, что сессия работает: `php artisan config:cache`
2. Проверьте права на `storage/framework/sessions`
3. Проверьте настройки сессии в `.env`: `SESSION_DRIVER=file`

### Проблема: Middleware не работает

**Решение:**
1. Проверьте регистрацию в `bootstrap/app.php`
2. Очистите кеш: `php artisan config:cache`
3. Проверьте, что middleware указан в роутах: `->middleware(['admin.auth'])`

### Проблема: Blade не компилируется

**Решение:**
1. Очистите кеш view: `php artisan view:clear`
2. Проверьте синтаксис Blade (каждая директива на отдельной строке)
3. Проверьте логи: `storage/logs/laravel.log`

## Связанные документы

- [blade-directives-formatting.md](../.kiro/steering/blade-directives-formatting.md) - Правила форматирования Blade
- [blade-scripts-styles.md](../.kiro/steering/blade-scripts-styles.md) - Подключение скриптов и стилей
- [admin-desktop-blade-validation.md](admin-desktop-blade-validation.md) - Валидация админ-панели

## История изменений

- 2026-03-02: Создана система авторизации Windows 10 Lock Screen
- Реализованы: AuthController, AdminAuth middleware, login view
- Интегрировано с AdminDesktopController
- Добавлена защита всех роутов админ-панели
