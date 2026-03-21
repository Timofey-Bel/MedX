# Новые страницы MedX

## Обзор

Добавлено 7 новых страниц из legacy модулей в Laravel проект.

## Список новых страниц

### 1. Регистрация (`/register`)
- **Контроллер:** `RegisterController`
- **View:** `resources/views/register.blade.php`
- **CSS:** `assets/medx/css/register.css`
- **Описание:** Страница регистрации с формой и социальными сетями

### 2. FAQ (`/faq`)
- **Контроллер:** `FaqController`
- **View:** `resources/views/faq.blade.php`
- **CSS:** `assets/medx/css/faq.css`
- **Описание:** Часто задаваемые вопросы с аккордеоном

### 3. О нас (`/about`)
- **Контроллер:** `AboutController`
- **View:** `resources/views/about.blade.php`
- **CSS:** `assets/medx/css/about.css`
- **Описание:** Информация о команде и авторах

### 4. Профиль (`/profile`)
- **Контроллер:** `ProfileController`
- **View:** `resources/views/profile.blade.php`
- **CSS:** `assets/medx/css/profile.css`
- **Описание:** Личный кабинет пользователя

### 5. Настройки (`/main_settings`)
- **Контроллер:** `MainSettingsController`
- **View:** `resources/views/main_settings.blade.php`
- **CSS:** `assets/medx/css/main_settings.css`
- **Описание:** Настройки профиля пользователя

### 6. Тесты (`/main_tests`)
- **Контроллер:** `MainTestsController`
- **View:** `resources/views/main_tests.blade.php`
- **CSS:** `assets/medx/css/main_settings.css`
- **Описание:** Создание и управление тестами

### 7. Сообщество (`/main_community`)
- **Контроллер:** `MainCommunityController`
- **View:** `resources/views/main_community.blade.php`
- **CSS:** `assets/medx/main_styles.css`
- **Описание:** Информация о сообществе MedX

## Структура контроллеров

Все контроллеры находятся в `app/Http/Controllers/`:

```
app/Http/Controllers/
├── AboutController.php
├── FaqController.php
├── MainCommunityController.php
├── MainSettingsController.php
├── MainTestsController.php
├── ProfileController.php
└── RegisterController.php
```

## Структура views

Все views находятся в `resources/views/`:

```
resources/views/
├── about.blade.php
├── faq.blade.php
├── main_community.blade.php
├── main_settings.blade.php
├── main_tests.blade.php
├── profile.blade.php
└── register.blade.php
```

## Маршруты

Все маршруты добавлены в `routes/web.php`:

```php
// Аутентификация
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::get('/register', [RegisterController::class, 'index'])->name('register');

// Главные страницы (main_*)
Route::get('/main_showcase', [MainShowcaseController::class, 'index'])->name('main_showcase');
Route::get('/main_settings', [MainSettingsController::class, 'index'])->name('main_settings');
Route::get('/main_tests', [MainTestsController::class, 'index'])->name('main_tests');
Route::get('/main_community', [MainCommunityController::class, 'index'])->name('main_community');

// Информационные страницы
Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/faq', [FaqController::class, 'index'])->name('faq');
```

## Использованные компоненты

### Общие partials
- `partials/header.blade.php` - для showcase, login, register, about, faq, profile
- `partials/footer.blade.php` - для всех страниц
- `partials/main_header.blade.php` - для main_* страниц
- `partials/main_mobile_menu.blade.php` - для main_* страниц

### JavaScript
- Аккордеоны (FAQ, main_tests)
- Модальные окна (register)
- Валидация форм (register, profile)
- Мобильное меню (main_* страницы)

## Особенности реализации

### 1. Регистрация
- Форма с валидацией
- Чекбокс согласия с политикой
- Социальные сети для входа
- Анимация ошибок

### 2. FAQ
- Аккордеон с вопросами
- Автоматическое открытие первого вопроса
- Плавная анимация

### 3. Профиль
- Табы для разных разделов
- Формы редактирования данных
- Управление адресами и картами
- Настройки уведомлений

### 4. Main Settings
- Редактирование профиля
- Смена пароля
- Настройки часового пояса

### 5. Main Tests
- Создание тестов
- Фильтры по сложности
- Выбор дисциплин

## Доступ к страницам

После настройки OSPanel страницы доступны по адресам:

- `http://medx/register` - Регистрация
- `http://medx/login` - Вход
- `http://medx/faq` - FAQ
- `http://medx/about` - О нас
- `http://medx/profile` - Профиль
- `http://medx/main_showcase` - База знаний
- `http://medx/main_settings` - Настройки
- `http://medx/main_tests` - Тесты
- `http://medx/main_community` - Сообщество

## Что работает

✅ HTML структура всех страниц  
✅ CSS стили из legacy  
✅ JavaScript функционал  
✅ Навигация между страницами  
✅ Адаптивный дизайн  
✅ Формы (без backend обработки)  

## Что требует доработки

- [ ] Backend обработка форм
- [ ] Аутентификация пользователей
- [ ] Сохранение данных в БД
- [ ] API для динамического контента
- [ ] Валидация на сервере
- [ ] Загрузка изображений

## Проверка работы

```bash
# Просмотр всех маршрутов
php artisan route:list

# Запуск сервера
php artisan serve

# Открыть в браузере
http://localhost:8000/register
http://localhost:8000/faq
http://localhost:8000/about
# и т.д.
```

## См. также

- [PROJECT_STRUCTURE.md](PROJECT_STRUCTURE.md) - Структура проекта
- [README_PHP85.md](README_PHP85.md) - Полное руководство
- [MIGRATION_README.md](MIGRATION_README.md) - Детали миграции

---

**Дата создания:** 18 марта 2024  
**Всего страниц:** 10 (3 ранее + 7 новых)  
**Статус:** ✅ Готово к использованию
