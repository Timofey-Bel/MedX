# MedX - Быстрый старт

## Установка зависимостей

```bash
# Установить PHP зависимости
composer install

# Установить Node.js зависимости (если используются)
npm install

# Скопировать .env файл
cp .env.example .env

# Сгенерировать ключ приложения
php artisan key:generate

# Запустить миграции (если есть)
php artisan migrate
```

## Проверка структуры MedX

### Созданные файлы:

#### Контроллер
- ✅ `app/Http/Controllers/MedxController.php`

#### Views
- ✅ `resources/views/layouts/medx.blade.php`
- ✅ `resources/views/medx/components/header.blade.php`
- ✅ `resources/views/medx/components/footer.blade.php`
- ✅ `resources/views/medx/showcase/index.blade.php`
- ✅ `resources/views/medx/showcase/disciplines.blade.php`
- ✅ `resources/views/medx/showcase/feature-cards.blade.php`
- ✅ `resources/views/medx/showcase/learn-section.blade.php`
- ✅ `resources/views/medx/auth/login.blade.php`
- ✅ `resources/views/medx/auth/register.blade.php`

#### CSS
- ✅ `public/assets/medx/css/header.css`
- ✅ `public/assets/medx/css/footer.css`
- ✅ `public/assets/medx/css/showcase.css`
- ✅ `public/assets/medx/css/disciplines.css`
- ✅ `public/assets/medx/css/features.css`
- ✅ `public/assets/medx/css/learn-section.css`
- ✅ `public/assets/medx/css/auth.css`

#### JavaScript
- ✅ `public/assets/medx/js/showcase.js`

#### Роуты
- ✅ Добавлены в `routes/web.php`

## Запуск проекта

```bash
# Очистить кеш
php artisan view:clear
php artisan cache:clear
php artisan config:clear

# Запустить сервер разработки
php artisan serve
```

## Доступные страницы

После запуска сервера откройте в браузере:

1. **Главная страница MedX**
   ```
   http://localhost:8000/medx
   ```

2. **Страница входа**
   ```
   http://localhost:8000/medx/login
   ```

3. **Страница регистрации**
   ```
   http://localhost:8000/medx/register
   ```

## Структура главной страницы

Главная страница (`/medx`) состоит из следующих секций:

1. **Header** - Шапка с навигацией
2. **Hero Section** - Главный баннер с призывом к действию
3. **Promo Banner** - Бегущая строка с акцией
4. **About Section** - О платформе с карточками
5. **Disciplines Section** - Дисциплины с табами (Фундаментальные/Клинические)
6. **Features Section** - 4 карточки возможностей
7. **Learn Section** - Преимущества обучения
8. **CTA Section** - Призыв к регистрации
9. **Devices Section** - Мультиплатформенность
10. **Footer** - Подвал с ссылками и соцсетями

## Интерактивные элементы

### JavaScript функционал:

1. **Табы дисциплин**
   - Переключение между "Фундаментальные" и "Клинические"
   - Плавная анимация

2. **Бегущая строка**
   - Автоматическая анимация промо-баннера
   - Бесконечный цикл

3. **Горизонтальная прокрутка**
   - Карточки материалов можно прокручивать колесом мыши

4. **Плавная прокрутка**
   - Якорные ссылки работают с плавной анимацией

## Проверка работоспособности

### 1. Проверьте загрузку стилей

Откройте DevTools (F12) → Network → CSS:
- `styles.css` - основные стили
- `header.css` - стили шапки
- `footer.css` - стили подвала
- `showcase.css` - стили главной страницы
- `disciplines.css` - стили дисциплин
- `features.css` - стили карточек
- `learn-section.css` - стили секции обучения

Все файлы должны загружаться со статусом 200.

### 2. Проверьте загрузку JavaScript

DevTools → Network → JS:
- `jquery.min.js` - jQuery (из layout)
- `showcase.js` - интерактивность страницы

### 3. Проверьте работу табов

На главной странице в секции "Дисциплины":
1. Нажмите на таб "Клинические"
2. Содержимое должно переключиться
3. Активный таб должен быть подсвечен

### 4. Проверьте консоль браузера

DevTools → Console:
- Не должно быть ошибок JavaScript
- Не должно быть ошибок 404 для ресурсов

## Возможные проблемы

### Проблема: Стили не применяются

**Решение:**
```bash
# Очистить кеш
php artisan view:clear
php artisan cache:clear

# Очистить кеш браузера
Ctrl + Shift + R (или Cmd + Shift + R на Mac)
```

### Проблема: 404 на CSS/JS файлы

**Решение:**
1. Проверьте, что файлы существуют в `public/assets/medx/`
2. Проверьте права доступа к папке `public/`
3. Проверьте настройки веб-сервера

### Проблема: Табы не переключаются

**Решение:**
1. Откройте консоль браузера (F12)
2. Проверьте наличие ошибок JavaScript
3. Убедитесь, что `showcase.js` загружен
4. Проверьте, что jQuery загружен (если используется)

### Проблема: Blade ошибки

**Решение:**
```bash
# Очистить кеш view
php artisan view:clear

# Проверить логи
tail -f storage/logs/laravel.log
```

## Следующие шаги

### 1. Добавить изображения

Разместите изображения в:
```
public/assets/medx/img/
├── main/showcase/
│   ├── logo@2x.png
│   ├── logo-2@3x.png
│   ├── vector-11.png
│   ├── devices.png
│   └── learn-illustration.png
├── disciplines/
│   ├── anatomy.svg
│   ├── histology.svg
│   └── ... (другие иконки)
├── features/
│   ├── knowledge-base.svg
│   ├── tests.svg
│   ├── practice.svg
│   └── community.svg
└── footer/
    ├── logo-footer.svg
    └── icon-*.svg (социальные сети)
```

### 2. Настроить авторизацию

В `MedxController.php` добавьте обработку форм:
```php
public function authenticate(Request $request)
{
    // Логика входа
}

public function store(Request $request)
{
    // Логика регистрации
}
```

### 3. Подключить базу данных

Создайте миграции для:
- Таблица дисциплин
- Таблица материалов
- Таблица тестов
- Таблица прогресса пользователей

### 4. Сделать контент динамическим

Замените статичные данные на данные из БД:
- Список дисциплин
- Карточки материалов
- Отзывы

## Полезные команды

```bash
# Просмотр всех роутов
php artisan route:list

# Просмотр роутов MedX
php artisan route:list --name=medx

# Очистка всех кешей
php artisan optimize:clear

# Создание символической ссылки для storage
php artisan storage:link
```

## Документация

Подробная документация:
- `docs/medx-structure.md` - Полная структура проекта
- `.kiro/steering/blade-directives-formatting.md` - Правила Blade
- `.kiro/steering/blade-scripts-styles.md` - Правила подключения скриптов

## Поддержка

При возникновении проблем:
1. Проверьте логи: `storage/logs/laravel.log`
2. Проверьте консоль браузера (F12)
3. Очистите все кеши
4. Проверьте права доступа к файлам

---

**Дата создания:** 2026-03-13  
**Версия:** 1.0.0
