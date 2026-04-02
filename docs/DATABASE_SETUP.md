# Подключение к базе данных

## Настройки подключения

База данных: MariaDB 10.4
Хост: mariadb-10.4
Порт: 3306
Пользователь: root
Пароль: (пустой)
База: medx

## Конфигурация (.env)

```env
DB_CONNECTION=mysql
DB_HOST=mariadb-10.4
DB_PORT=3306
DB_DATABASE=medx
DB_USERNAME=root
DB_PASSWORD=
```

## Создание базы данных

Если база данных еще не создана, выполните:

```sql
CREATE DATABASE medx CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Запуск миграций

```bash
php artisan migrate
```

## Аутентификация

Реализована система регистрации и входа:
- Регистрация: email + пароль (минимум 6 символов)
- Вход: email + пароль
- Выход: кнопка в настройках профиля
- Защита страниц: все main_* страницы требуют авторизации
- Автоматическое перенаправление на main_showcase после входа

Контроллер: `app/Http/Controllers/AuthController.php`

## Примечания

- MariaDB совместима с MySQL, поэтому используется драйвер 'mysql'
- Кодировка utf8mb4 для поддержки эмодзи и всех символов Unicode
- Пароль пустой (для локальной разработки)
