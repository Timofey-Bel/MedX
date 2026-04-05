# Календарь ежедневных входов

## Функционал
- Недельные слайды (7 дней в сетке) на главной странице
- **Полный календарь по месяцам** - открывается при клике на стрелку
- Автоматическая отметка текущего дня
- **Система заморозок (5 шт)** - автоматическая защита streak при пропуске дней
- Визуальные индикаторы: ✓ посещен, ❄️ заморозка (ледяной градиент), ✗ пропущен, ★ первый вход
- Навигация стрелками между неделями
- Сохранение в localStorage

## Модальное окно календаря
При клике на стрелку справа открывается полный календарь:
- Показывает один месяц за раз
- Название месяца отображается между кнопками навигации
- **Счетчик дней подряд** - 🔥 3 дня подряд
- **Счетчик заморозок** - ❄️ 5 (доступные заморозки)
- Кнопки "Назад" и "Вперед" для переключения между месяцами
- Заголовки дней недели (Пн, Вт, Ср, Чт, Пт, Сб, Вс)
- Первый вход отмечен звездочкой ★
- Посещенные дни - синий фон с галочкой ✓
- Замороженные дни - ледяной градиент со снежинкой ❄️
- Пропущенные дни - крестик ✗
- Дни до регистрации - полупрозрачные
- Будущие дни - полупрозрачные

## Система заморозок
- Изначально 5 заморозок
- При пропуске дня автоматически тратится заморозка
- Заморозка сохраняет streak и предотвращает крестик
- Замороженные дни имеют ледяной градиент (голубой → бирюзовый) с тенью
- В будущем заморозки будут даваться за определённые действия

## Логика крестиков
Крестики появляются только для дней **после первого входа**, которые не были защищены заморозкой.

Условия для крестика:
1. День в прошлом
2. День не был посещен
3. День >= дате первого входа
4. На день не была использована заморозка

## Сохраняемые данные

### База данных (основное хранилище)
Все данные пользователя хранятся в таблице `users`:

```php
// Поля в БД
visited_days: JSON массив ["2026-03-27", "2026-03-26"]
first_visit_date: DATE "2026-03-20"
freeze_count: INTEGER 5
used_freezes: JSON объект {"2026-03-25": true}
achievements: JSON массив ["first_login", "streak_3"]
pomodoro_state: JSON объект с состоянием таймера
```

### localStorage (временный кэш)
localStorage используется только как кэш для быстрого доступа. При загрузке страницы данные автоматически синхронизируются из БД:

```javascript
// Автоматическая синхронизация
medx_visited_days → из БД
medx_first_visit_date → из БД
medx_freeze_count → из БД
medx_used_freezes → из БД
medx_achievements → из БД
medx_pomodoro_state → из БД
```

### API для работы с данными

```javascript
// Загрузка данных из БД
GET /api/user-data/

// Сохранение календаря
POST /api/user-data/calendar
Body: { visited_days, first_visit_date, freeze_count, used_freezes }

// Сохранение достижений
POST /api/user-data/achievements
Body: { achievements }

// Сохранение помодоро
POST /api/user-data/pomodoro
Body: { pomodoro_state }
```

### Автоматическая миграция
При первой загрузке страницы после обновления:
1. Скрипт `user-data-sync.js` проверяет наличие данных в localStorage
2. Если данные есть - мигрирует их в БД
3. Загружает данные из БД обратно в localStorage
4. Все последующие изменения автоматически сохраняются в БД

## Тестирование

### Консольные команды
```javascript
// Посмотреть данные в localStorage (кэш)
localStorage.getItem('medx_visited_days')
localStorage.getItem('medx_first_visit_date')
localStorage.getItem('medx_freeze_count')
localStorage.getItem('medx_used_freezes')

// Посмотреть логи синхронизации
// Откройте консоль браузера и обновите страницу
// Вы увидите логи: [UserDataSync] Starting initialization...

// Очистить localStorage и перезагрузить (данные загрузятся из БД)
localStorage.clear()
location.reload()

// Симуляция входа неделю назад (сохранится в БД)
const weekAgo = new Date()
weekAgo.setDate(weekAgo.getDate() - 7)
localStorage.setItem('medx_first_visit_date', weekAgo.toISOString())
// Данные автоматически сохранятся в БД при следующем изменении
```

### Проверка миграции данных
1. Откройте консоль браузера (F12)
2. Обновите страницу
3. Проверьте логи:
   - `[UserDataSync] Starting initialization...`
   - `[UserDataSync] Found local data, migrating to DB...` (если есть данные в localStorage)
   - `[UserDataSync] Loading data from DB...`
   - `[UserDataSync] DB data loaded: {...}`
   - `[UserDataSync] Sync completed successfully`

## Файлы

### Frontend
- `public/js/daily-calendar.js` - логика календаря + модальное окно + система заморозок
- `public/js/user-data-sync.js` - синхронизация данных между localStorage и БД
- `public/js/api-client.js` - API клиент для запросов к серверу
- `public/css/main_styles.css` - стили календаря + модального окна + ледяной эффект
- `resources/views/main_showcase.blade.php` - HTML

### Backend
- `app/Http/Controllers/Api/UserDataController.php` - API для синхронизации данных
- `app/Http/Controllers/CalendarController.php` - логика календаря (markToday, applyFreezes, calculateStreak)
- `app/Models/User.php` - модель пользователя с полями данных
- `database/migrations/2026_04_05_092329_add_user_data_fields_to_users_table.php` - миграция БД
- `routes/web.php` - маршруты API

