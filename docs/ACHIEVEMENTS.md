# Система достижений

## Описание
Система достижений мотивирует пользователей к регулярному использованию платформы и выполнению различных активностей.

## Список достижений

### Начальные достижения
1. **Первые шаги** 🎯
   - Условие: Первый вход на платформу
   - Выдается автоматически

2. **Начинающий практик** 📝
   - Условие: Пройти первый тест
   - Вызов: `MedXAchievements.unlock('first_test')`

3. **Любознательный читатель** 📚
   - Условие: Прочитать первую статью
   - Вызов: `MedXAchievements.unlock('first_article')`

4. **Мастер концентрации** 🍅
   - Условие: Завершить полный цикл помодоро (4 раунда)
   - Вызов: `MedXAchievements.unlock('pomodoro_cycle')`

### Достижения за streak (дни подряд)
5. **Постоянство** 🔥 - 3 дня подряд
6. **Неделя силы** 💪 - 7 дней подряд
7. **Две недели упорства** ⚡ - 14 дней подряд
8. **Месяц дисциплины** 🏆 - 30 дней подряд
9. **Золотой юбилей** 👑 - 50 дней подряд
10. **Платиновая серия** 💎 - 75 дней подряд
11. **Легенда постоянства** 🌟 - 125 дней подряд
12. **Полгода совершенства** 🎖️ - 180 дней подряд

## Отображение в профиле

В профиле пользователя есть отдельная вкладка "Достижения":
- Сначала показываются разблокированные достижения (цветные, с галочкой)
- Затем заблокированные (полупрозрачные, с замком, название скрыто "???")
- При наведении на заблокированное достижение:
  - Увеличивается прозрачность
  - Уменьшается grayscale
  - Появляется тень
  - Показывается описание (условие получения)
- Адаптивная сетка (3 колонки на десктопе, 1 на мобильных)

## Уведомления
При получении достижения появляется уведомление справа снизу:
- Градиентный фон (бирюзовый)
- Иконка достижения (эмодзи)
- Название достижения
- Описание
- Автоматическое скрытие через 5 секунд
- Закрытие по клику

## API

### Разблокировка достижения
```javascript
MedXAchievements.unlock('achievement_id')
```

### Проверка streak достижений
```javascript
MedXAchievements.checkStreak(streak)
```

### Получение всех достижений
```javascript
const all = MedXAchievements.getAll()
```

### Получение разблокированных достижений
```javascript
const unlocked = MedXAchievements.getUnlocked()
```

## Интеграция

### Календарь
Автоматически проверяет streak достижения при расчете дней подряд.

### Помодоро таймер
Добавить в конец 4-го раунда:
```javascript
if (window.MedXAchievements) {
    MedXAchievements.unlock('pomodoro_cycle');
}
```

### Тесты
При завершении первого теста:
```javascript
if (window.MedXAchievements) {
    MedXAchievements.unlock('first_test');
}
```

### Статьи
При прочтении первой статьи:
```javascript
if (window.MedXAchievements) {
    MedXAchievements.unlock('first_article');
}
```

## Хранение данных

### База данных (основное хранилище)
Достижения хранятся в таблице `users` в поле `achievements` (JSON массив):

```php
// Пример в БД
achievements: ["first_login", "streak_3", "streak_7"]
```

### localStorage (временный кэш)
localStorage используется только как кэш. При загрузке страницы данные автоматически синхронизируются из БД:

```javascript
// Автоматическая синхронизация
medx_achievements → из БД
```

### API для работы с достижениями

```javascript
// Получить все достижения с статусом
GET /api/achievements/

// Разблокировать достижение
POST /api/achievements/unlock
Body: { achievement_id: "first_login" }

// Проверить и выдать достижения за streak
POST /api/achievements/check-streak
Body: { streak: 7 }
```

### Автоматическая синхронизация
- При разблокировке достижения оно сохраняется в localStorage
- Скрипт `user-data-sync.js` автоматически синхронизирует изменения с БД
- При загрузке страницы данные загружаются из БД в localStorage

## Файлы

### Frontend
- `public/js/achievements.js` - логика системы достижений + отображение в профиле
- `public/js/user-data-sync.js` - синхронизация данных между localStorage и БД
- `public/css/main_styles.css` - стили уведомлений
- `public/css/profile.css` - стили карточек достижений
- `resources/views/layouts/app.blade.php` - подключение скрипта
- `resources/views/profile.blade.php` - страница профиля с достижениями

### Backend
- `app/Http/Controllers/AchievementsController.php` - API для достижений (getAll, unlock, checkStreak)
- `app/Http/Controllers/Api/UserDataController.php` - API для синхронизации данных
- `app/Models/User.php` - модель пользователя с полем achievements
- `database/migrations/2026_04_05_092329_add_user_data_fields_to_users_table.php` - миграция БД
- `routes/web.php` - маршруты API
