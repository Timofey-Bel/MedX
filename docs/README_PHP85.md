# MedX - Laravel Project (PHP 8.5)

## 🚀 Быстрый старт

### Требования
- **PHP 8.5+** (обязательно!)
- Composer
- OSPanel или другой веб-сервер
- MySQL/SQLite

### Установка

1. **Проверьте версию PHP:**
   ```bash
   php -v
   ```
   Должно быть PHP 8.5 или выше.

2. **Установите зависимости:**
   ```bash
   composer install
   ```

3. **Настройте окружение:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Настройте домен в OSPanel:**
   - Добавьте домен `medx`
   - Укажите путь к папке `public`
   - Перезапустите OSPanel

5. **Откройте в браузере:**
   - `http://medx` - главная страница
   - `http://medx/login` - вход
   - `http://medx/main_showcase` - база знаний

## 📁 Структура проекта

```
MedX/
├── app/
│   └── Http/Controllers/
│       ├── ShowcaseController.php      # Главная страница
│       ├── LoginController.php         # Страница входа
│       └── MainShowcaseController.php  # База знаний
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php              # Основной layout
│   ├── partials/
│   │   ├── header.blade.php           # Хедер (showcase/login)
│   │   ├── footer.blade.php           # Футер
│   │   ├── main_header.blade.php      # Хедер (main_showcase)
│   │   └── main_mobile_menu.blade.php # Мобильное меню
│   ├── showcase.blade.php             # Главная страница
│   ├── login.blade.php                # Страница входа
│   └── main_showcase.blade.php        # База знаний
├── public/
│   ├── assets/                        # CSS, JS, изображения
│   ├── js/                            # JavaScript библиотеки
│   └── site/                          # Модули и изображения
└── routes/
    └── web.php                        # Маршруты
```

## 🔧 Настройка PHP 8.5

### В OSPanel

1. Откройте OSPanel → Настройки → Модули
2. Выберите PHP 8.5
3. Перезапустите OSPanel

### Проверка расширений

Убедитесь, что в `php.ini` включены:
```ini
extension=curl
extension=fileinfo
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=pdo_sqlite
```

## 📖 Доступные страницы

| URL | Описание | Контроллер |
|-----|----------|------------|
| `/` | Главная страница (showcase) | ShowcaseController |
| `/login` | Страница входа | LoginController |
| `/main_showcase` | База знаний | MainShowcaseController |

## 🛠️ Разработка

### Запуск встроенного сервера
```bash
php artisan serve
```
Откройте: `http://localhost:8000`

### Очистка кэша
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Проверка маршрутов
```bash
php artisan route:list
```

## 📚 Документация

- [PHP_8.5_UPGRADE.md](PHP_8.5_UPGRADE.md) - Обновление на PHP 8.5
- [OSPANEL_SETUP.md](OSPANEL_SETUP.md) - Настройка OSPanel
- [MIGRATION_README.md](MIGRATION_README.md) - Детали миграции
- [SUMMARY.md](SUMMARY.md) - Краткое резюме

## ⚠️ Важно

- Проект требует **PHP 8.5** (указано в `composer.json`)
- Все статические файлы находятся в `public/`
- Используйте Laravel helpers: `asset()`, `url()`, `route()`

## 🐛 Решение проблем

### Ошибка: "PHP version requirement"
```bash
# Обновите PHP до 8.5
# Затем:
composer update
```

### Ошибка: "Class not found"
```bash
composer dump-autoload
```

### Ошибка: "Permission denied"
```bash
chmod -R 775 storage bootstrap/cache
```

## 📞 Поддержка

При возникновении проблем:
1. Проверьте версию PHP: `php -v`
2. Проверьте логи: `storage/logs/laravel.log`
3. Очистите кэш: `php artisan cache:clear`

---

**Версия PHP:** 8.5+  
**Версия Laravel:** 12.x  
**Статус:** ✅ Готов к работе
