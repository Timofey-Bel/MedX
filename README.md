# MedX - Образовательная платформа для медиков

![PHP Version](https://img.shields.io/badge/PHP-8.5+-blue)
![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red)
![License](https://img.shields.io/badge/license-MIT-green)

Интерактивная образовательная платформа для студентов медицинских вузов с базой знаний, тестами и сообществом.

## 🚀 Быстрый старт

⚠️ **ВАЖНО:** Перед развертыванием прочитайте [SECURITY.md](SECURITY.md) для настройки конфиденциальных данных.

```bash
# 1. Проверьте версию PHP (должна быть 8.5+)
php -v

# 2. Установите зависимости
composer install

# 3. Настройте окружение
cp .env.example .env
php artisan key:generate

# 4. Создайте базу данных
touch database/database.sqlite
php artisan migrate

# 5. Запустите сервер
php artisan serve
```

Откройте: `http://localhost:8000`

## 📋 Требования

- **PHP 8.5+** (обязательно!)
- Composer
- OSPanel или другой веб-сервер
- MySQL/SQLite

## 🌐 Доступные страницы

### Публичные страницы
| URL | Описание | Контроллер |
|-----|----------|------------|
| `/` | Главная страница (showcase) | ShowcaseController |
| `/login` | Страница входа | LoginController |
| `/register` | Регистрация | RegisterController |
| `/about` | О нас | AboutController |
| `/faq` | Часто задаваемые вопросы | FaqController |

### Личный кабинет (main_*)
| URL | Описание | Контроллер |
|-----|----------|------------|
| `/main_showcase` | Главная (личный кабинет) | MainShowcaseController |
| `/main_settings` | Настройки профиля | MainSettingsController |
| `/design_settings` | Оформление (темы) | DesignSettingsController |
| `/support_settings` | Поддержка и FAQ | SupportSettingsController |
| `/main_tests` | Тесты и задания | MainTestsController |
| `/main_community` | Сообщество | MainCommunityController |
| `/main_community_rules` | Правила сообщества | MainCommunityRulesController |
| `/main_confidentiality` | Конфиденциальность | MainConfidentialityController |
| `/main_base` | База знаний (фундаментальные дисциплины) | MainBaseController |
| `/main_base_clinical` | База знаний (клинические дисциплины) | MainBaseClinicalController |
| `/profile` | Профиль пользователя | ProfileController |

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
│   ├── Helpers/
│   │   └── ImageHelper.php      # Хелпер для изображений
│   └── Http/Controllers/        # Контроллеры
│       ├── ShowcaseController.php
│       ├── LoginController.php
│       ├── MainShowcaseController.php
│       ├── MainSettingsController.php
│       ├── DesignSettingsController.php
│       ├── SupportSettingsController.php
│       ├── MainTestsController.php
│       ├── MainCommunityController.php
│       ├── MainCommunityRulesController.php
│       ├── MainConfidentialityController.php
│       ├── MainBaseController.php
│       └── MainBaseClinicalController.php
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php        # Основной layout
│   ├── partials/
│   │   ├── main_header.blade.php
│   │   └── main_mobile_menu.blade.php
│   ├── components/
│   │   └── img.blade.php        # Компонент изображений
│   ├── showcase.blade.php
│   ├── login.blade.php
│   ├── main_showcase.blade.php
│   ├── main_settings.blade.php
│   ├── design_settings.blade.php
│   ├── support_settings.blade.php
│   ├── main_tests.blade.php
│   ├── main_community.blade.php
│   ├── main_community_rules.blade.php
│   ├── main_confidentiality.blade.php
│   ├── main_base.blade.php
│   └── main_base_clinical.blade.php
├── public/
│   ├── css/
│   │   ├── main_styles.css      # Общие стили
│   │   ├── main_header.css
│   │   ├── main_settings.css
│   │   ├── main_tests.css
│   │   ├── main_base.css
│   │   └── ...
│   ├── assets/medx/
│   │   └── main_script.js       # Основной JavaScript
│   ├── images/
│   │   ├── base/                # Изображения базы знаний
│   │   ├── medical/             # Медицинские иконки
│   │   ├── community/
│   │   └── ...
│   └── js/                      # JavaScript библиотеки
├── docs/                        # 📚 Документация
│   ├── DOCS_INDEX.md
│   ├── QUICK_START.md
│   ├── IMAGES_GUIDE.md          # Руководство по изображениям
│   └── ...
└── routes/web.php               # Маршруты
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

✅ 16 полностью функциональных страниц  
✅ Главная страница с полным дизайном  
✅ Страницы входа и регистрации  
✅ Личный кабинет (main_showcase)  
✅ Настройки профиля с подразделами  
✅ Оформление (выбор темы)  
✅ Поддержка с FAQ  
✅ Тесты и задания с аккордеонами  
✅ Сообщество с правилами  
✅ База знаний (2 раздела: фундаментальные и клинические дисциплины)  
✅ Страница конфиденциальности  
✅ FAQ с аккордеоном  
✅ О нас (команда)  
✅ Профиль пользователя  
✅ Адаптивный дизайн  
✅ Мобильное меню с анимацией  
✅ Поиск по страницам в header  
✅ Все CSS стили и анимации  
✅ JavaScript функционал  
✅ Изображения и иконки  
✅ Медицинские иконки для дисциплин  

## 🚀 Следующие шаги

- [ ] Добавить аутентификацию Laravel
- [ ] Подключить базу данных
- [ ] Создать API для динамического контента
- [ ] Добавить функционал тестов
- [ ] Реализовать систему сообщества
- [ ] Настроить HTTPS

## 📞 Поддержка

При возникновении проблем:
1. Проверьте [docs/DOCS_INDEX.md](docs/DOCS_INDEX.md)
2. Убедитесь, что используете PHP 8.5: `php -v`
3. Проверьте логи: `storage/logs/laravel.log`

## 📄 Лицензия

MIT License

---

**Версия:** 2.0.0  
**PHP:** 8.5+  
**Laravel:** 12.x  
**Статус:** ✅ Готов к работе  
**Страниц:** 16

**Документация:** [docs/](docs/) | **Быстрый старт:** [docs/QUICK_START.md](docs/QUICK_START.md)
