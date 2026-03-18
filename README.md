# MedX - Образовательная платформа для медиков

![PHP Version](https://img.shields.io/badge/PHP-8.5+-blue)
![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red)
![License](https://img.shields.io/badge/license-MIT-green)

Интерактивная образовательная платформа для студентов медицинских вузов с базой знаний, тестами и сообществом.

## 🚀 Быстрый старт

```bash
# 1. Проверьте версию PHP (должна быть 8.5+)
php -v

# 2. Установите зависимости
composer install

# 3. Настройте окружение
cp .env.example .env
php artisan key:generate

# 4. Запустите сервер
php artisan serve
```

Откройте: `http://localhost:8000`

## 📋 Требования

- **PHP 8.5+** (обязательно!)
- Composer
- OSPanel или другой веб-сервер
- MySQL/SQLite

## 🌐 Доступные страницы

| URL | Описание |
|-----|----------|
| `/` | Главная страница (showcase) |
| `/login` | Страница входа |
| `/main_showcase` | База знаний (личный кабинет) |

## 📚 Документация

Вся документация находится в папке [`docs/`](docs/):

### Начните здесь:
- 📖 **[docs/DOCS_INDEX.md](docs/DOCS_INDEX.md)** - Навигация по документации
- 🚀 **[docs/QUICK_START.md](docs/QUICK_START.md)** - Быстрая установка (5 минут)
- 📘 **[docs/README_PHP85.md](docs/README_PHP85.md)** - Полное руководство

### Настройка:
- [docs/OSPANEL_SETUP.md](docs/OSPANEL_SETUP.md) - Настройка OSPanel
- [docs/PHP_8.5_UPGRADE.md](docs/PHP_8.5_UPGRADE.md) - Обновление PHP до 8.5

### Разработка:
- [docs/MIGRATION_README.md](docs/MIGRATION_README.md) - Детали миграции
- [docs/SUMMARY.md](docs/SUMMARY.md) - Краткое резюме проекта
- [docs/CHANGELOG_PHP85.md](docs/CHANGELOG_PHP85.md) - История изменений

## 🏗️ Структура проекта

```
MedX/
├── app/
│   └── Http/Controllers/     # Контроллеры
├── resources/views/          # Blade шаблоны
│   ├── layouts/             # Layouts
│   ├── partials/            # Переиспользуемые части
│   ├── showcase.blade.php   # Главная страница
│   ├── login.blade.php      # Страница входа
│   └── main_showcase.blade.php  # База знаний
├── public/
│   ├── assets/              # CSS, JS, изображения
│   ├── js/                  # JavaScript библиотеки
│   └── site/                # Модули и изображения
├── docs/                    # 📚 Документация
└── routes/web.php           # Маршруты
```

## 🛠️ Разработка

### Запуск сервера разработки
```bash
php artisan serve
```

### Очистка кэша
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Просмотр маршрутов
```bash
php artisan route:list
```

## 🔧 Настройка OSPanel

1. Откройте OSPanel → Настройки → Домены
2. Добавьте домен `medx`
3. Укажите путь к папке `public`
4. Перезапустите OSPanel
5. Откройте `http://medx` в браузере

Подробнее: [docs/OSPANEL_SETUP.md](docs/OSPANEL_SETUP.md)

## ⚠️ Важно

- Проект требует **PHP 8.5** или выше
- Все статические файлы находятся в `public/`
- Используйте Laravel helpers: `asset()`, `url()`, `route()`

## 🐛 Решение проблем

### Ошибка версии PHP
```bash
# Обновите PHP до 8.5
# См. docs/PHP_8.5_UPGRADE.md
```

### CSS/JS не загружаются
Убедитесь, что папки скопированы в `public/`:
- `public/assets/`
- `public/js/`
- `public/site/`

### Ошибка прав доступа
```bash
chmod -R 775 storage bootstrap/cache
```

Больше решений: [docs/README_PHP85.md](docs/README_PHP85.md#-решение-проблем)

## 📦 Что включено

✅ Главная страница с полным дизайном  
✅ Страница входа с формой авторизации  
✅ База знаний (личный кабинет)  
✅ Адаптивный дизайн  
✅ Мобильное меню  
✅ Все CSS стили и анимации  
✅ JavaScript функционал  
✅ Изображения и иконки  

## 🚀 Следующие шаги

- [ ] Добавить аутентификацию Laravel
- [ ] Подключить базу данных
- [ ] Создать API для динамического контента
- [ ] Добавить остальные страницы (register, faq, about)
- [ ] Настроить HTTPS

## 📞 Поддержка

При возникновении проблем:
1. Проверьте [docs/DOCS_INDEX.md](docs/DOCS_INDEX.md)
2. Убедитесь, что используете PHP 8.5: `php -v`
3. Проверьте логи: `storage/logs/laravel.log`

## 📄 Лицензия

MIT License

---

**Версия:** 1.0.0  
**PHP:** 8.5+  
**Laravel:** 12.x  
**Статус:** ✅ Готов к работе

**Документация:** [docs/](docs/) | **Быстрый старт:** [docs/QUICK_START.md](docs/QUICK_START.md)
