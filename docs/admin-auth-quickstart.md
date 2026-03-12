# Быстрый старт: Авторизация админ-панели

## Шаг 1: Создание администратора

### Вариант А: Через Tinker (рекомендуется)

```bash
& "C:\OS\modules\PHP-8.5\php.exe" artisan tinker
```

```php
DB::table('admin_users')->insert([
    'login' => 'admin',
    'password' => Hash::make('admin123'),
    'name' => 'Администратор',
    'email' => 'admin@sfera.local',
    'active' => 1
]);
```

Нажмите `Ctrl+C` для выхода из Tinker.

### Вариант Б: Через SQL

```sql
INSERT INTO admin_users (login, password, name, email, active)
VALUES (
    'admin',
    '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYIvnYvZBGm',  -- пароль: admin123
    'Администратор',
    'admin@sfera.local',
    1
);
```

## Шаг 2: Очистка кеша

```bash
& "C:\OS\modules\PHP-8.5\php.exe" artisan view:clear
& "C:\OS\modules\PHP-8.5\php.exe" artisan config:cache
```

## Шаг 3: Проверка

1. Откройте браузер: `http://sfera/admin`
2. Вы будете перенаправлены на `http://sfera/admin/login`
3. Кликните на аватар
4. Введите пароль: `admin123`
5. Нажмите Enter или кликните на стрелку
6. Вы будете перенаправлены на desktop

## Готово!

Теперь вы можете:
- Войти в админ-панель: `http://sfera/admin`
- Выйти: `http://sfera/admin/logout`
- Изменить пароль через Tinker:

```php
DB::table('admin_users')
    ->where('login', 'admin')
    ->update(['password' => Hash::make('new_password')]);
```

## Тестовые данные

**Логин:** admin  
**Пароль:** admin123

**ВАЖНО:** Измените пароль после первого входа!

## Если что-то не работает

1. Проверьте, что таблица `admin_users` существует
2. Проверьте, что пользователь создан: `SELECT * FROM admin_users;`
3. Проверьте, что `active = 1`
4. Очистите кеш: `php artisan view:clear && php artisan config:cache`
5. Проверьте логи: `storage/logs/laravel.log`

## Дополнительная информация

Полная документация: [admin-auth-system.md](admin-auth-system.md)
