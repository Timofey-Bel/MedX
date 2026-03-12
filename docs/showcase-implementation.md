# Реализация главной страницы (Showcase)

## Дата: 2026-02-27

## Обзор

Главная страница интернет-магазина "Сфера" реализована с использованием ShowcaseController и набора Blade компонентов. Страница включает несколько блоков: главный карусель баннеров, боковые карусели товаров, популярные категории, TOP-10 товаров, обзоры продукции и случайные товары (новинки).

## Архитектура

### Контроллер: ShowcaseController

**Путь:** `app/Http/Controllers/ShowcaseController.php`

**Зависимости:**
- ProductService - для получения рейтингов и изображений товаров

**Методы:**

1. **index(Request $request)** - главный метод отображения страницы
   - Получает корзину из сессии
   - Преобразует формат избранного для неавторизованных пользователей
   - Вызывает все методы получения данных для блоков
   - Передает данные в view showcase/index.blade.php

2. **getMainCarouselBanners()** - получение баннеров для главного карусели
   - Таблица: `banners`
   - Фильтр: `active = 1`
   - Сортировка: `sort ASC`
   - Возвращает: Collection баннеров

3. **getProductCarouselData()** - получение товаров для карусели товаров
   - Таблицы: `products`, `prices`
   - Фильтр: `is_new = 1`
   - Выборка: случайная (inRandomOrder)
   - Лимит: 3 товара
   - Добавляет изображения через ProductService::getProductImageUrl()

4. **getPromoCarouselData()** - получение данных для промо-карусели
   - Аналогично getProductCarouselData()
   - Лимит: 3 товара

5. **getPopularCategories()** - получение популярных категорий
   - Таблицы: `popular_categories`, `tree`
   - Фильтр: `active = 1`
   - Сортировка: `sort ASC`
   - Преобразует в массивы для Blade

6. **getTop10Products()** - получение топ-10 товаров
   - Таблицы: `top10_products`, `products`, `prices`
   - Фильтр: `active = 1`
   - Сортировка: `sort ASC`
   - Лимит: 10 товаров
   - **Обогащает данные:**
     - Рейтинг через ProductService::getProductRating()
     - Изображения через ProductService::getProductImageUrl()
     - Округление цены
     - Fallback для quantity (99)

7. **getProductReviews()** - получение отзывов о товарах
   - Таблица: `product_reviews`
   - Фильтр: `active = 1`
   - Сортировка: `sort ASC`
   - Лимит: 10 отзывов

8. **getRandomProducts()** - получение случайных товаров (новинки)
   - Таблицы: `products`, `prices`
   - Фильтр: `is_new = 1`
   - Выборка: случайная (inRandomOrder)
   - Лимит: 12 товаров
   - **Обогащает данные:**
     - Рейтинг через ProductService::getProductRating()
     - Изображения через ProductService::getProductImageUrl()
     - Округление цены
     - Fallback для quantity (99)

### Шаблон: showcase/index.blade.php

**Путь:** `resources/views/showcase/index.blade.php`

**Структура:**
- Расширяет `layouts.app`
- Секции: title, styles, content, scripts

**Подключаемые CSS:**
- carousel.css
- categories.css
- top10-slider.css
- product-reviews.css
- catalog.css

**Блоки:**
1. Triple Carousel Section - тройной карусель (главный + 2 боковых)
2. Popular Categories - популярные категории
3. TOP-10 Slider - топ-10 товаров
4. Product Reviews - обзоры продукции
5. Product Grid - случайные товары (новинки)

**Подключаемые JavaScript:**
- carousel.js
- top10-slider.js
- catalog.js

## Blade компоненты

### 1. main-carousel.blade.php

**Путь:** `resources/views/components/showcase/main-carousel.blade.php`

**Назначение:** Главный карусель баннеров

**Props:**
- `$banners` (array) - массив баннеров

**Функционал:**
- Отображение баннеров
- Кнопки навигации (prev/next)
- Точки индикации
- Автоматическая прокрутка (через carousel.js)

### 2. product-carousel.blade.php

**Путь:** `resources/views/components/showcase/product-carousel.blade.php`

**Назначение:** Карусель товаров (верхняя правая часть)

**Props:**
- `$products` (array) - массив товаров
- `$carouselId` (string) - ID карусели (по умолчанию 'product')

**Функционал:**
- Отображение мини-карточек товаров
- Кнопки навигации
- Точки индикации

### 3. promo-carousel.blade.php

**Путь:** `resources/views/components/showcase/promo-carousel.blade.php`

**Назначение:** Промо-карусель (нижняя правая часть)

**Props:**
- `$products` (array) - массив товаров/промо
- `$carouselId` (string) - ID карусели (по умолчанию 'promo')

**Функционал:**
- Отображение промо-карточек
- Кнопки навигации
- Точки индикации

### 4. popular-categories.blade.php

**Путь:** `resources/views/components/showcase/popular-categories.blade.php`

**Назначение:** Блок популярных категорий

**Props:**
- `$categories` (array) - массив категорий

**Функционал:**
- Отображение сетки категорий
- Изображения категорий
- Ссылки на категории

### 5. top10-slider.blade.php

**Путь:** `resources/views/components/showcase/top10-slider.blade.php`

**Назначение:** TOP-10 товаров слайдер

**Props:**
- `$products` (array) - массив товаров
- `$cart` (array) - данные корзины
- `$favorites` (array) - данные избранного

**Функционал:**
- Отображение карточек товаров
- Кнопка избранного с состоянием
- Рейтинг и количество отзывов
- Цена товара
- Кнопка "В корзину"
- Контрол количества (для товаров в корзине)
- Кнопка "Купить всё"
- Синхронизация с корзиной и избранным

### 6. product-reviews.blade.php

**Путь:** `resources/views/components/showcase/product-reviews.blade.php`

**Назначение:** Обзоры продукции

**Props:**
- `$reviews` (array) - массив обзоров

**Функционал:**
- Отображение блоков обзоров
- Главная карточка обзора (4/6 ширины)
- Боковая сетка обзоров (2/6 ширины)
- Изображения обзоров
- Ссылки на страницы обзоров

### 7. random-products.blade.php

**Путь:** `resources/views/components/showcase/random-products.blade.php`

**Назначение:** Случайные товары (новинки)

**Props:**
- `$products` (array) - массив товаров
- `$cart` (array) - данные корзины
- `$favorites` (array) - данные избранного

**Функционал:**
- Отображение сетки товаров
- Кнопка избранного с состоянием
- Рейтинг и количество отзывов
- Цена товара
- Кнопка "В корзину"
- Контрол количества (для товаров в корзине)
- Кнопка "Купить всё"
- Ссылка на каталог
- Синхронизация с корзиной и избранным

## Соответствие требованиям к данным карточек товаров

ShowcaseController полностью соответствует требованиям документа `.kiro/steering/product-card-data-requirements.md`:

### Обязательные данные для карточек товаров

✅ **getTop10Products()** возвращает:
- id (product_id)
- name (product_name)
- image (image_url через ProductService)
- price (product_price, округленная)
- quantity (с fallback 99)
- rating (через ProductService::getProductRating())
- reviews_count (через ProductService::getProductRating())

✅ **getRandomProducts()** возвращает:
- id
- name
- image (через ProductService)
- price (округленная)
- quantity (с fallback 99)
- rating (через ProductService::getProductRating())
- reviews_count (через ProductService::getProductRating())

### Преобразование формата избранного

✅ **index()** метод преобразует формат избранного:
```php
$sessionFavorites = $request->session()->get('favorites', []);
$favorites = ['items' => []];

if (is_array($sessionFavorites)) {
    // Если это массив ID (для неавторизованных)
    if (isset($sessionFavorites[0]) && !isset($sessionFavorites['items'])) {
        foreach ($sessionFavorites as $productId) {
            $favorites['items'][$productId] = true;
        }
    } else {
        // Если уже в правильном формате (для авторизованных)
        $favorites = $sessionFavorites;
    }
}
```

### Передача данных в view

✅ **index()** передает в view:
- cart
- favorites (с преобразованием формата)
- Все данные для блоков

## Роуты

**Путь:** `routes/web.php`

```php
Route::get('/', [ShowcaseController::class, 'index'])->name('home');
```

## Миграция из legacy

**Legacy путь:** `legacy/site/modules/sfera/showcase/`

**Изменения:**
- Smarty модули (`~~mod~`) заменены на Blade компоненты (`@include`)
- SQL-запросы сохранены через DB::select()
- Добавлена интеграция с ProductService для рейтингов и изображений
- Добавлено преобразование формата избранного
- Сохранена структура HTML и CSS

## Тестирование

См. `docs/testing-checklist-showcase.md` для полного чек-листа тестирования.

### Основные проверки:

1. **Визуальная проверка:**
   - Все блоки отображаются корректно
   - Карусели работают
   - Изображения загружаются

2. **Функциональная проверка:**
   - Кнопки "В корзину" работают
   - Контрол количества работает
   - Кнопки избранного работают
   - Синхронизация состояния между страницами

3. **Проверка данных:**
   - Рейтинги отображаются
   - Количество отзывов отображается
   - Цены отображаются
   - Изображения корректные

## Известные проблемы

Нет известных проблем.

## Следующие шаги

1. Выполнить ручное тестирование по чек-листу
2. Проверить работу на production данных
3. Оптимизировать производительность (если нужно)
4. Создать PR в ветку dev

## Ссылки

- Контроллер: `app/Http/Controllers/ShowcaseController.php`
- Шаблон: `resources/views/showcase/index.blade.php`
- Компоненты: `resources/views/components/showcase/`
- Чек-лист тестирования: `docs/testing-checklist-showcase.md`
- Требования: `.kiro/specs/legacy-to-laravel-migration/requirements.md` (Requirement 20)
- Дизайн: `.kiro/specs/legacy-to-laravel-migration/design.md` (Этап 16)

## История изменений

- 2026-02-27: Создана документация после реализации ShowcaseController
- 2026-02-27: Добавлено преобразование формата избранного
- 2026-02-27: Создан шаблон showcase/index.blade.php
- 2026-02-27: Проверено соответствие требованиям к данным карточек товаров
