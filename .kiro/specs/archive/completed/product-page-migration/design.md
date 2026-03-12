# Design Document: Миграция страницы товара

## Overview

Данный документ описывает технический дизайн миграции страницы товара из legacy Smarty системы в Laravel Blade. Миграция включает полную функциональную эквивалентность с legacy версией, сохранение совместимости с Knockout.js, создание переиспользуемых Blade компонентов и вынесение бизнес-логики в сервисы.

### Цели миграции

1. Перенести страницу товара из `legacy/site/modules/sfera/product/product.tpl` в Laravel Blade
2. Обеспечить полную функциональную эквивалентность с legacy версией
3. Сохранить совместимость с существующим JavaScript кодом (Knockout.js)
4. Создать переиспользуемые Blade компоненты для упрощения поддержки
5. Вынести бизнес-логику в ProductService для тестируемости
6. Обеспечить SEO-оптимизацию с Open Graph и Schema.org разметкой

### Scope

**В scope:**
- Миграция основного view страницы товара
- Создание 6 новых Blade компонентов
- Расширение ProductService 5 новыми методами
- Обновление ProductController для использования сервисов
- Сохранение всех CSS классов и ID из legacy системы
- Интеграция с Knockout.js для корзины
- SEO метаданные и Schema.org разметка
- Адаптивная верстка для мобильных устройств

**Вне scope:**
- Изменение логики работы корзины
- Модификация существующих JavaScript файлов
- Изменение структуры базы данных
- Создание новых API endpoints



## Architecture

### Общая архитектура

Миграция следует паттерну MVC с дополнительным слоем сервисов:

```
Request → Route → ProductController → ProductService → Database
                        ↓
                   View (Blade) → Components → Browser
                        ↓
                   Knockout.js (existing)
```

### Компонентная архитектура

Страница товара будет состоять из следующих компонентов:

```
resources/views/product/show.blade.php (главный view)
├── layouts/app.blade.php (layout с header/footer)
├── components/product/gallery.blade.php
├── components/product/price-block.blade.php
├── components/product/attributes.blade.php
├── components/product/reviews.blade.php
├── components/product/related-products.blade.php
└── components/product/product-card.blade.php (используется в related-products)
```

### Слой сервисов

ProductService будет расширен следующими методами:

```php
ProductService
├── getProductBySlug(string $slug): ?object
├── getProductAttributes(string $productId): array
├── getProductReviews(string $productId): array
├── getRelatedProducts(string $productId, int $limit = 12): array
├── getProductImages(string $productId): array
├── getProductRating(string $productId): array (существующий)
└── getProductImageUrl(string $productId): string (существующий)
```

### Интеграция с legacy системой

Для обеспечения совместимости с существующим JavaScript кодом:

1. Сохраняются все CSS классы из legacy системы
2. Сохраняются все HTML ID элементов с JavaScript обработчиками
3. Сохраняются data-атрибуты для Knockout.js bindings
4. Используется тот же порядок загрузки скриптов



## Components and Interfaces

### 1. ProductController

**Файл:** `app/Http/Controllers/ProductController.php`

**Обновленный метод:**

```php
/**
 * Отображение страницы товара
 * 
 * @param Request $request
 * @param string $slug ID или slug товара
 * @return \Illuminate\View\View
 */
public function show(Request $request, string $slug)
{
    // Получаем товар через сервис
    $product = $this->productService->getProductBySlug($slug);
    
    if (!$product) {
        abort(404);
    }
    
    // Получаем дополнительные данные через сервисы
    $attributes = $this->productService->getProductAttributes($product->id);
    $images = $this->productService->getProductImages($product->id);
    $reviews = $this->productService->getProductReviews($product->id);
    $relatedProducts = $this->productService->getRelatedProducts($product->id);
    $reviewsStats = $this->productService->getProductRating($product->id);
    
    // Формируем breadcrumbs
    $breadcrumbs = $this->buildBreadcrumbs($product);
    
    // Формируем SEO метаданные
    $seoData = $this->buildSeoData($product, $reviewsStats);
    
    return view('product.show', compact(
        'product',
        'attributes',
        'images',
        'reviews',
        'relatedProducts',
        'reviewsStats',
        'breadcrumbs',
        'seoData'
    ));
}
```

**Новые приватные методы:**

```php
/**
 * Формирование breadcrumbs для товара
 */
private function buildBreadcrumbs(object $product): array

/**
 * Формирование SEO метаданных
 */
private function buildSeoData(object $product, array $reviewsStats): array
```



### 2. ProductService

**Файл:** `app/Services/ProductService.php`

**Новые методы:**

```php
/**
 * Получить товар по slug или ID
 * 
 * @param string $slug ID или slug товара
 * @return object|null Объект товара или null если не найден
 */
public function getProductBySlug(string $slug): ?object
{
    // Получаем товар с ценой из таблиц products и prices
    // Возвращает объект с полями: id, name, description, category_id, price, etc.
}

/**
 * Получить характеристики товара
 * 
 * @param string $productId ID товара
 * @return array Массив объектов атрибутов
 */
public function getProductAttributes(string $productId): array
{
    // Получаем атрибуты из таблицы attributes
    // Исключаем служебные атрибуты: Новинка, Отгружать упаковками, Отображать, Стандарт, Тип обложки
    // Для атрибутов "Серия", "Тематика", "Тип товара" получаем ID из соответствующих view
    // Возвращает массив объектов с полями: name, value, seriya_id, topic_id, product_type_id
}

/**
 * Получить отзывы товара
 * 
 * @param string $productId ID товара
 * @return array Массив отзывов с форматированными данными
 */
public function getProductReviews(string $productId): array
{
    // Получаем SKU товара из v_products_o_products
    // Получаем отзывы из o_reviews с текстом и рейтингом
    // Форматируем даты на русском языке
    // Возвращает массив объектов с полями: review_id, rating, text, date, formatted_date, first_letter
}

/**
 * Получить похожие товары
 * 
 * @param string $productId ID товара
 * @param int $limit Максимальное количество товаров (по умолчанию 12)
 * @return array Массив похожих товаров
 */
public function getRelatedProducts(string $productId, int $limit = 12): array
{
    // Получаем категорию текущего товара
    // Выбираем товары из той же категории (исключая текущий)
    // Ограничиваем результат $limit товарами (минимум 4, максимум 12)
    // Возвращает массив объектов с полями: id, name, image, price
}

/**
 * Получить изображения товара
 * 
 * @param string $productId ID товара
 * @return array Массив URL изображений
 */
public function getProductImages(string $productId): array
{
    // Проверяем наличие товара в v_products_o_products
    // Если найден, получаем изображения из o_images упорядоченные по image_order
    // Формируем URL в формате /o_images/{product_id}/{image_order}.jpg
    // Если не найден, используем стандартный путь /import_files/{product_id}b.jpg
    // Возвращает массив объектов с полем url
}
```

**Обновленный метод getProductRating:**

```php
/**
 * Получить рейтинг и статистику отзывов для товара
 * 
 * @param string $productId ID товара
 * @return array Расширенная статистика отзывов
 */
public function getProductRating(string $productId): array
{
    // Существующая логика + добавляем:
    // - rating_distribution: массив с количеством и процентом отзывов для каждой звезды (1-5)
    // - total_count: общее количество отзывов
    // - average_rating: средний рейтинг
    // Возвращает: ['average_rating' => float, 'total_count' => int, 'rating_distribution' => array]
}
```



### 3. Blade Components

#### 3.1 Product Gallery Component

**Файл:** `resources/views/components/product/gallery.blade.php`

**Props:**
- `$images` (array): Массив изображений с полем url
- `$productName` (string): Название товара для alt текста

**Структура:**
```html
<div class="product-gallery">
    <div class="gallery-container">
        <div class="gallery-thumbnails-vertical">
            <!-- Вертикальные миниатюры -->
        </div>
        <div class="gallery-main-wrapper">
            <div class="gallery-main">
                <!-- Главное изображение -->
                <img id="mainImage" src="..." alt="...">
            </div>
        </div>
    </div>
</div>
```

**CSS классы из legacy:** `product-gallery`, `gallery-container`, `gallery-thumbnails-vertical`, `thumbnail-vertical`, `gallery-main-wrapper`, `gallery-main`

**JavaScript интеграция:** Использует существующий `public/assets/sfera/js/product.js` для переключения изображений

#### 3.2 Price Block Component

**Файл:** `resources/views/components/product/price-block.blade.php`

**Props:**
- `$product` (object): Объект товара с полями price, old_price, discount_percent, in_stock
- `$cartBindings` (string): Knockout.js data-bind атрибуты для кнопки корзины

**Структура:**
```html
<div class="product-purchase-sidebar">
    <div class="purchase-sticky-content">
        <div class="product-price-block">
            <div class="price-main">
                <span class="price-current">{{ $product->price }} ₽</span>
                @if($product->old_price)
                    <span class="price-old">{{ $product->old_price }} ₽</span>
                    <span class="discount-badge">-{{ $product->discount_percent }}%</span>
                @endif
            </div>
        </div>
        <div class="quantity-selector">
            <input type="number" id="productQuantity" value="1" min="1">
        </div>
        @if($product->in_stock)
            <button class="btn-add-to-cart" data-bind="{{ $cartBindings }}">
                Добавить в корзину
            </button>
            <div class="stock-status">В наличии</div>
        @else
            <div class="out-of-stock">Нет в наличии</div>
        @endif
    </div>
</div>
```

**CSS классы из legacy:** `product-purchase-sidebar`, `purchase-sticky-content`, `product-price-block`, `price-main`, `price-current`, `price-old`, `discount-badge`, `btn-add-to-cart`

**Knockout.js bindings:** `data-bind="click: addToCart, attr: { 'data-product-id': productId }"`



#### 3.3 Product Attributes Component

**Файл:** `resources/views/components/product/attributes.blade.php`

**Props:**
- `$attributes` (array): Массив атрибутов с полями name, value, unit (опционально)
- `$grouped` (bool): Группировать ли атрибуты по категориям (по умолчанию false)

**Структура:**
```html
<div class="product-attributes">
    <h3>Характеристики</h3>
    <div class="attributes-list">
        @foreach($attributes as $attribute)
            <div class="attribute-row">
                <span class="attribute-name">{{ $attribute->name }}</span>
                <span class="attribute-value">
                    {{ $attribute->value }}
                    @if(isset($attribute->unit))
                        {{ $attribute->unit }}
                    @endif
                </span>
            </div>
        @endforeach
    </div>
</div>
```

**CSS классы из legacy:** `product-attributes`, `attributes-list`, `attribute-row`, `attribute-name`, `attribute-value`

#### 3.4 Product Reviews Component

**Файл:** `resources/views/components/product/reviews.blade.php`

**Props:**
- `$reviews` (array): Массив отзывов
- `$reviewsStats` (array): Статистика отзывов (average_rating, total_count, rating_distribution)
- `$isAuthenticated` (bool): Авторизован ли пользователь

**Структура:**
```html
<div class="product-reviews">
    <div class="reviews-header">
        <h3>Отзывы ({{ $reviewsStats['total_count'] }})</h3>
        <div class="reviews-rating">
            <div class="rating-stars">
                <!-- Звезды рейтинга -->
            </div>
            <span class="rating-value">{{ $reviewsStats['average_rating'] }}</span>
        </div>
    </div>
    
    <div class="reviews-stats">
        @foreach([5,4,3,2,1] as $stars)
            <div class="rating-bar">
                <span>{{ $stars }} звезд</span>
                <div class="bar">
                    <div class="fill" style="width: {{ $reviewsStats['rating_distribution'][$stars]['percent'] }}%"></div>
                </div>
                <span>{{ $reviewsStats['rating_distribution'][$stars]['count'] }}</span>
            </div>
        @endforeach
    </div>
    
    <div class="reviews-list">
        @foreach($reviews as $review)
            <div class="review-item">
                <div class="review-avatar">{{ $review['first_letter'] }}</div>
                <div class="review-content">
                    <div class="review-rating">
                        <!-- Звезды рейтинга отзыва -->
                    </div>
                    <div class="review-date">{{ $review['formatted_date'] }}</div>
                    <div class="review-text">{{ $review['text'] }}</div>
                </div>
            </div>
        @endforeach
    </div>
    
    @if($isAuthenticated)
        <div class="review-form">
            <!-- Форма добавления отзыва -->
        </div>
    @else
        <div class="review-login-prompt">
            Войдите, чтобы оставить отзыв
        </div>
    @endif
</div>
```

**CSS классы из legacy:** `product-reviews`, `reviews-header`, `reviews-rating`, `rating-stars`, `reviews-stats`, `rating-bar`, `reviews-list`, `review-item`, `review-avatar`, `review-content`



#### 3.5 Related Products Component

**Файл:** `resources/views/components/product/related-products.blade.php`

**Props:**
- `$products` (array): Массив похожих товаров
- `$title` (string): Заголовок блока (по умолчанию "Похожие товары")

**Структура:**
```html
<div class="related-products">
    <h3>{{ $title }}</h3>
    <div class="products-carousel">
        <button class="carousel-btn carousel-prev">←</button>
        <div class="carousel-track">
            @foreach($products as $product)
                <x-product.product-card :product="$product" />
            @endforeach
        </div>
        <button class="carousel-btn carousel-next">→</button>
    </div>
    <div class="carousel-dots">
        <!-- Точки навигации -->
    </div>
</div>
```

**CSS классы из legacy:** `related-products`, `products-carousel`, `carousel-btn`, `carousel-prev`, `carousel-next`, `carousel-track`, `carousel-dots`

**JavaScript интеграция:** Использует существующий carousel JavaScript из showcase модулей

#### 3.6 Product Card Component

**Файл:** `resources/views/components/product/product-card.blade.php`

**Props:**
- `$product` (object): Объект товара с полями id, name, image, price, in_stock

**Структура:**
```html
<div class="product-card" data-product-id="{{ $product->id }}">
    <a href="/product/{{ $product->id }}" class="product-link">
        <div class="product-image">
            <img src="{{ $product->image }}" alt="{{ $product->name }}">
        </div>
        <div class="product-info">
            <h4 class="product-name">{{ $product->name }}</h4>
            <div class="product-price">{{ $product->price }} ₽</div>
        </div>
    </a>
    @if($product->in_stock)
        <button class="btn-add-to-cart-mini" 
                data-bind="click: addToCart" 
                data-product-id="{{ $product->id }}">
            В корзину
        </button>
    @endif
</div>
```

**CSS классы из legacy:** `product-card`, `product-link`, `product-image`, `product-info`, `product-name`, `product-price`, `btn-add-to-cart-mini`

**Knockout.js bindings:** Кнопка корзины использует те же bindings что и основная кнопка



### 4. Main View

**Файл:** `resources/views/product/show.blade.php`

**Структура:**

```blade
@extends('layouts.app')

@section('title', $seoData['title'])
@section('meta_description', $seoData['description'])

@push('head')
    <!-- Open Graph теги -->
    <meta property="og:title" content="{{ $seoData['og_title'] }}">
    <meta property="og:description" content="{{ $seoData['og_description'] }}">
    <meta property="og:image" content="{{ $seoData['og_image'] }}">
    <meta property="og:url" content="{{ $seoData['og_url'] }}">
    <meta property="og:type" content="product">
    
    <!-- Schema.org разметка -->
    <script type="application/ld+json">
    {!! json_encode($seoData['schema'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
    
    <!-- CSS файлы из legacy -->
    <link rel="stylesheet" href="/assets/sfera/css/product.css">
    <link rel="stylesheet" href="/assets/sfera/css/product-hashtags.css">
    <link rel="stylesheet" href="/assets/sfera/css/product/ui-kit-notification.css">
    <link rel="stylesheet" href="/assets/sfera/css/product/ui-kit-radio-group.css">
    <link rel="stylesheet" href="/assets/sfera/css/product/pdp-all-delivery-v7.css">
    <link rel="stylesheet" href="/assets/sfera/css/product/pdp-mobile-characteristics-v7.css">
    <link rel="stylesheet" href="/assets/sfera/css/product/common-all-tag-list.css">
    <link rel="stylesheet" href="/assets/sfera/css/product/pdp-all-relations.css">
    <link rel="stylesheet" href="/assets/sfera/css/product/product-info-column.css">
@endpush

@section('content')
<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <div class="container">
        @foreach($breadcrumbs as $crumb)
            @if($crumb['url'])
                <a href="{{ $crumb['url'] }}">{{ $crumb['title'] }}</a>
            @else
                <span>{{ $crumb['title'] }}</span>
            @endif
            @if(!$loop->last)
                <span>/</span>
            @endif
        @endforeach
    </div>
</div>

<!-- Product Page -->
<main class="product-page">
    <div class="container">
        <!-- Product Meta Actions -->
        <div class="product-meta-actions">
            <div class="product-article">Артикул: {{ $product->id }}</div>
        </div>

        <div class="product-layout">
            <!-- Gallery -->
            <x-product.gallery :images="$images" :productName="$product->name" />

            <!-- Product Info Column -->
            <div class="product-info-column">
                <h1 class="pdp_bg9 tsHeadline550Medium">{{ $product->name }}</h1>
                
                @if($reviewsStats['total_count'] > 0)
                    <div class="product-rating-summary">
                        <!-- Краткая информация о рейтинге -->
                    </div>
                @endif
                
                <!-- Краткие характеристики -->
                <div class="short-characteristics">
                    <!-- Отображение ключевых атрибутов: Автор, Издательство, Серия, Год -->
                </div>
            </div>

            <!-- Price Block (Sticky Sidebar) -->
            <x-product.price-block 
                :product="$product" 
                :cartBindings="'click: addToCart, attr: { \'data-product-id\': \'' . $product->id . '\' }'" 
            />
        </div>

        <!-- Tabs Section -->
        <div class="product-tabs">
            <div class="tabs-header">
                <button class="tab-btn active" data-tab="description">Описание</button>
                <button class="tab-btn" data-tab="attributes">Характеристики</button>
                <button class="tab-btn" data-tab="reviews">Отзывы</button>
            </div>
            
            <div class="tabs-content">
                <div class="tab-pane active" id="description">
                    <div class="product-description">
                        {!! $product->description !!}
                    </div>
                </div>
                
                <div class="tab-pane" id="attributes">
                    <x-product.attributes :attributes="$attributes" />
                </div>
                
                <div class="tab-pane" id="reviews">
                    <x-product.reviews 
                        :reviews="$reviews" 
                        :reviewsStats="$reviewsStats"
                        :isAuthenticated="auth()->check()"
                    />
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if(count($relatedProducts) > 0)
            <x-product.related-products 
                :products="$relatedProducts" 
                :title="'Похожие товары'" 
            />
        @endif
    </div>
</main>
@endsection

@push('scripts')
    <!-- JavaScript из legacy -->
    <script src="/assets/sfera/js/product.js"></script>
@endpush
```



## Data Models

### Product Model

```php
{
    id: string,              // ID товара (offer_id)
    name: string,            // Название товара
    description: string,     // Описание товара (HTML)
    category_id: string,     // ID категории
    price: float,            // Текущая цена
    old_price: float|null,   // Старая цена (если есть скидка)
    discount_percent: int|null, // Процент скидки
    in_stock: bool,          // Наличие на складе
    brand: string|null,      // Бренд товара
    sku: string|null         // SKU товара
}
```

### Product Attribute Model

```php
{
    name: string,            // Название атрибута
    value: string,           // Значение атрибута
    unit: string|null,       // Единица измерения (опционально)
    seriya_id: int|null,     // ID серии (для атрибута "Серия")
    topic_id: int|null,      // ID тематики (для атрибута "Тематика")
    product_type_id: int|null // ID типа товара (для атрибута "Тип товара")
}
```

### Product Image Model

```php
{
    url: string,             // URL изображения
    order: int               // Порядок отображения
}
```

### Product Review Model

```php
{
    review_id: int,          // ID отзыва
    rating: int,             // Рейтинг (1-5)
    text: string,            // Текст отзыва
    date: string,            // Дата отзыва (Y-m-d H:i:s)
    formatted_date: string,  // Форматированная дата (на русском)
    first_letter: string,    // Первая буква для аватара
    state: string            // Статус отзыва
}
```

### Reviews Stats Model

```php
{
    average_rating: float,   // Средний рейтинг
    total_count: int,        // Общее количество отзывов
    rating_distribution: [   // Распределение по звездам
        5: {count: int, percent: float},
        4: {count: int, percent: float},
        3: {count: int, percent: float},
        2: {count: int, percent: float},
        1: {count: int, percent: float}
    ]
}
```

### SEO Data Model

```php
{
    title: string,           // Title страницы
    description: string,     // Meta description
    og_title: string,        // Open Graph title
    og_description: string,  // Open Graph description
    og_image: string,        // Open Graph image URL
    og_url: string,          // Open Graph URL
    schema: array            // Schema.org разметка (JSON-LD)
}
```

### Schema.org Product Structure

```json
{
    "@context": "https://schema.org/",
    "@type": "Product",
    "name": "Название товара",
    "image": "URL изображения",
    "description": "Описание товара",
    "sku": "SKU товара",
    "brand": {
        "@type": "Brand",
        "name": "Название бренда"
    },
    "offers": {
        "@type": "Offer",
        "url": "URL товара",
        "priceCurrency": "RUB",
        "price": "Цена",
        "availability": "https://schema.org/InStock"
    },
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "Средний рейтинг",
        "reviewCount": "Количество отзывов"
    }
}
```



## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property Reflection

После анализа acceptance criteria выявлены следующие testable properties. Проведена проверка на избыточность:

**Группа 1: Отображение основной информации**
- 1.2, 1.3, 1.4 можно объединить в одно свойство о корректности базовой структуры страницы
- 2.1, 2.2 можно объединить в свойство о корректности галереи изображений

**Группа 2: Блок цены и корзины**
- 3.1, 3.3, 3.7 можно объединить в свойство о наличии всех элементов блока покупки
- 3.9, 3.10 - это два состояния одного свойства (наличие/отсутствие товара)

**Группа 3: Характеристики**
- 5.1, 5.3, 5.4, 5.5 можно объединить в одно свойство о корректности отображения атрибутов

**Группа 4: Отзывы**
- 6.1, 6.2, 6.3, 6.5 можно объединить в свойство о полноте отображения отзывов
- 6.6, 6.7 - это два состояния одного свойства (авторизован/не авторизован)

**Группа 5: SEO**
- 8.3, 8.4 можно объединить в одно свойство о наличии всех OG тегов
- 8.5, 8.6, 8.7 можно объединить в одно свойство о корректности Schema.org разметки

После устранения избыточности остается 25 уникальных свойств.



### Property 1: Базовая структура страницы

*For any* товар в системе, страница товара должна содержать breadcrumbs с путем от главной страницы, основной блок с названием и артикулом, и URL в формате `/product/{id}`

**Validates: Requirements 1.2, 1.3, 1.4**

### Property 2: Галерея изображений

*For any* товар с изображениями, галерея должна отображать главное изображение и миниатюры всех дополнительных изображений, где количество миниатюр равно количеству изображений товара

**Validates: Requirements 2.1, 2.2**

### Property 3: Навигация галереи

*For any* товар с более чем одним изображением, галерея должна предоставлять элементы навигации (кнопки вперед/назад)

**Validates: Requirements 2.5**

### Property 4: Отображение цены

*For any* товар в системе, страница должна отображать текущую цену товара

**Validates: Requirements 3.1**

### Property 5: Отображение скидки

*For any* товар со скидкой, страница должна отображать старую цену зачеркнутой и процент скидки

**Validates: Requirements 3.2**

### Property 6: Элементы блока покупки

*For any* товар в наличии, страница должна содержать кнопку "Добавить в корзину", поле выбора количества и статус "В наличии"

**Validates: Requirements 3.3, 3.7, 3.10**

### Property 7: Knockout.js bindings

*For any* товар в наличии, кнопка корзины должна содержать data-bind атрибуты для интеграции с Knockout.js

**Validates: Requirements 3.4, 10.3**

### Property 8: Отсутствие товара на складе

*For any* товар без наличия, страница должна отображать сообщение "Нет в наличии" вместо кнопки корзины

**Validates: Requirements 3.9**

### Property 9: Наличие табов

*For any* товар в системе, страница должна содержать три таба: "Описание", "Характеристики", "Отзывы"

**Validates: Requirements 4.1**

### Property 10: Активный таб по умолчанию

*For any* товар в системе, таб "Описание" должен быть активным по умолчанию (иметь класс active)

**Validates: Requirements 4.4**

### Property 11: Отображение всех атрибутов

*For any* товар с атрибутами, страница должна отображать все атрибуты из базы данных в формате "Название: Значение", с правильным форматированием по типу и единицами измерения где применимо

**Validates: Requirements 5.1, 5.3, 5.4, 5.5**

### Property 12: Группировка атрибутов

*For any* товар с категоризированными атрибутами, атрибуты должны быть сгруппированы по категориям

**Validates: Requirements 5.2**

### Property 13: Отображение отзывов

*For any* товар с отзывами, страница должна отображать все отзывы с полной информацией: рейтинг в виде звезд, общее количество отзывов, и каждый отзыв с именем автора, датой, рейтингом и текстом

**Validates: Requirements 6.1, 6.2, 6.3, 6.5**

### Property 14: Статистика отзывов

*For any* товар с отзывами, страница должна отображать статистику распределения отзывов по рейтингам (количество и процент для каждой звезды от 1 до 5)

**Validates: Requirements 6.4**

### Property 15: Форма отзыва для авторизованных

*For any* авторизованный пользователь, просматривающий страницу товара, должна отображаться форма добавления нового отзыва

**Validates: Requirements 6.6**

### Property 16: Сообщение для неавторизованных

*For any* неавторизованный пользователь, просматривающий страницу товара, должно отображаться сообщение с предложением войти для добавления отзыва

**Validates: Requirements 6.7**

### Property 17: Сортировка отзывов

*For any* товар с несколькими отзывами, отзывы должны быть отсортированы по дате (новые первыми)

**Validates: Requirements 6.8**

### Property 18: Блок похожих товаров

*For any* товар в системе, страница должна содержать блок похожих товаров с заголовком "Похожие товары" или "С этим товаром покупают"

**Validates: Requirements 7.1**

### Property 19: Карусель похожих товаров

*For any* товар в системе, блок похожих товаров должен быть реализован в виде карусели с элементами навигации (кнопки или точки)

**Validates: Requirements 7.2, 7.8**

### Property 20: Количество похожих товаров

*For any* товар в системе, должно отображаться от 4 до 12 похожих товаров

**Validates: Requirements 7.3**

### Property 21: Информация о похожих товарах

*For any* похожий товар в карусели, должны отображаться изображение, название, цена и кнопка корзины

**Validates: Requirements 7.4, 7.5**

### Property 22: Категория похожих товаров

*For any* товар в системе, все похожие товары должны быть из той же категории что и текущий товар

**Validates: Requirements 7.7**

### Property 23: SEO title и description

*For any* товар в системе, страница должна иметь title в формате "{Название товара} - {Название сайта}" и meta description длиной до 160 символов

**Validates: Requirements 8.1, 8.2**

### Property 24: Open Graph теги

*For any* товар в системе, страница должна содержать все обязательные Open Graph теги: og:title, og:description, og:image, og:url, og:type со значением "product"

**Validates: Requirements 8.3, 8.4**

### Property 25: Schema.org разметка

*For any* товар в системе, страница должна содержать Schema.org разметку типа "Product" с обязательными полями (name, image, description, sku, brand, offers), где offers содержит цену, валюту и availability, и для товаров с отзывами включает aggregateRating

**Validates: Requirements 8.5, 8.6, 8.7**

### Property 26: Сохранение legacy ID

*For any* товар в системе, ключевые элементы страницы (главное изображение, кнопка корзины, поле количества) должны иметь HTML ID идентичные legacy системе

**Validates: Requirements 10.2**

### Property 27: Сохранение data-атрибутов

*For any* товар в системе, элементы с JavaScript обработчиками должны сохранять data-атрибуты используемые legacy системой

**Validates: Requirements 10.4**

### Property 28: ProductService.getProductBySlug

*For any* валидный slug товара, метод getProductBySlug должен возвращать объект товара с полной информацией, и для невалидного slug должен возвращать null

**Validates: Requirements 12.1**

### Property 29: ProductService.getProductAttributes

*For any* товар в системе, метод getProductAttributes должен возвращать массив всех атрибутов товара, исключая служебные атрибуты

**Validates: Requirements 12.2**

### Property 30: ProductService.getProductReviews

*For any* товар в системе, метод getProductReviews должен возвращать массив отзывов с форматированными датами на русском языке

**Validates: Requirements 12.3**

### Property 31: ProductService.getRelatedProducts

*For any* товар в системе, метод getRelatedProducts должен возвращать массив товаров из той же категории, исключая текущий товар, в количестве от 4 до 12

**Validates: Requirements 12.4**

### Property 32: ProductService.getProductImages

*For any* товар в системе, метод getProductImages должен возвращать массив URL изображений, упорядоченных по image_order

**Validates: Requirements 12.5**



## Error Handling

### ProductController Error Handling

**404 Not Found:**
- Когда товар не найден по slug, возвращается стандартная 404 страница Laravel
- Логируется попытка доступа к несуществующему товару

**500 Server Error:**
- При ошибках базы данных возвращается стандартная 500 страница
- Ошибка логируется с полным stack trace
- Пользователю показывается дружественное сообщение

**Отсутствие данных:**
- Если у товара нет изображений, используется заглушка `/assets/img/product_empty.jpg`
- Если у товара нет атрибутов, блок характеристик скрывается
- Если у товара нет отзывов, показывается сообщение "Пока нет отзывов"
- Если нет похожих товаров, блок похожих товаров скрывается

### ProductService Error Handling

**Database Connection Errors:**
```php
try {
    // Database query
} catch (\Exception $e) {
    Log::error('ProductService error', [
        'method' => __METHOD__,
        'product_id' => $productId,
        'error' => $e->getMessage()
    ]);
    return []; // или null для методов возвращающих объект
}
```

**Invalid Input:**
- Пустые или null значения параметров обрабатываются gracefully
- Возвращаются пустые массивы или null вместо исключений
- Логируются предупреждения для отладки

**Data Integrity Issues:**
- Если товар есть в products но нет в v_products_o_products, используется fallback логика
- Если атрибут имеет некорректное значение, он пропускается с логированием
- Если изображение не найдено, используется заглушка

### Component Error Handling

**Missing Props:**
- Все компоненты имеют значения по умолчанию для опциональных props
- Обязательные props проверяются в начале компонента
- При отсутствии обязательных props показывается placeholder

**Invalid Data:**
- Компоненты проверяют типы данных перед рендерингом
- Некорректные данные заменяются на безопасные значения по умолчанию
- Ошибки логируются для отладки

### JavaScript Error Handling

**Knockout.js Integration:**
- Проверка наличия Knockout.js перед инициализацией bindings
- Graceful degradation если Knockout.js не загружен
- Fallback на стандартные формы если bindings не работают

**Image Loading Errors:**
- Использование onerror атрибута для замены на заглушку
- Предзагрузка критичных изображений
- Lazy loading для изображений ниже fold

**Carousel Errors:**
- Проверка наличия элементов перед инициализацией карусели
- Отключение навигации если товаров меньше минимума
- Graceful degradation до простого списка при ошибках



## Testing Strategy

### Dual Testing Approach

Тестирование будет включать как unit тесты для конкретных примеров и edge cases, так и property-based тесты для проверки универсальных свойств. Оба подхода дополняют друг друга и необходимы для комплексного покрытия.

**Unit тесты** фокусируются на:
- Конкретных примерах корректного поведения
- Edge cases (пустые данные, граничные значения)
- Интеграционных точках между компонентами
- Специфических сценариях ошибок

**Property-based тесты** фокусируются на:
- Универсальных свойствах, которые должны выполняться для всех входных данных
- Комплексном покрытии через рандомизацию
- Проверке инвариантов системы

### Unit Testing

**ProductController Tests** (`tests/Unit/ProductControllerTest.php`):

```php
// Тесты методов контроллера
test_show_returns_product_page_for_valid_slug()
test_show_returns_404_for_invalid_slug()
test_show_includes_all_required_data()
test_buildBreadcrumbs_creates_correct_path()
test_buildSeoData_formats_metadata_correctly()
```

**ProductService Tests** (`tests/Unit/ProductServiceTest.php`):

```php
// Тесты методов сервиса
test_getProductBySlug_returns_product_for_valid_slug()
test_getProductBySlug_returns_null_for_invalid_slug()
test_getProductAttributes_excludes_service_attributes()
test_getProductAttributes_includes_related_ids()
test_getProductReviews_formats_dates_in_russian()
test_getProductReviews_calculates_statistics_correctly()
test_getRelatedProducts_returns_same_category_products()
test_getRelatedProducts_excludes_current_product()
test_getRelatedProducts_respects_limit()
test_getProductImages_returns_ordered_images()
test_getProductImages_handles_missing_ozon_product()
```

**Component Tests** (`tests/Unit/Components/ProductComponentsTest.php`):

```php
// Тесты Blade компонентов
test_gallery_component_renders_with_images()
test_gallery_component_handles_empty_images()
test_price_block_component_shows_discount()
test_price_block_component_shows_out_of_stock()
test_attributes_component_formats_values_correctly()
test_reviews_component_displays_rating_distribution()
test_related_products_component_renders_carousel()
test_product_card_component_includes_cart_button()
```

### Property-Based Testing

Для property-based тестирования будет использоваться библиотека **Pest PHP** с плагином **Pest Property Testing**.

**Конфигурация:** Каждый property тест должен выполняться минимум 100 итераций для обеспечения достаточного покрытия через рандомизацию.

**Feature Tests** (`tests/Feature/ProductPagePropertiesTest.php`):

```php
/**
 * Feature: product-page-migration, Property 1: Базовая структура страницы
 * For any товар в системе, страница товара должна содержать breadcrumbs с путем 
 * от главной страницы, основной блок с названием и артикулом, и URL в формате /product/{id}
 */
test('product page has correct basic structure', function () {
    // Генерируем случайный товар
    $product = Product::factory()->create();
    
    $response = $this->get("/product/{$product->id}");
    
    $response->assertStatus(200);
    $response->assertSee($product->name);
    $response->assertSee("Артикул: {$product->id}");
    $response->assertSee('Главная');
    $response->assertSee('Каталог');
})->repeat(100);

/**
 * Feature: product-page-migration, Property 2: Галерея изображений
 */
test('product gallery displays all images', function () {
    $product = Product::factory()->create();
    $imageCount = rand(1, 10);
    ProductImage::factory()->count($imageCount)->create(['product_id' => $product->id]);
    
    $response = $this->get("/product/{$product->id}");
    
    // Проверяем что количество миниатюр соответствует количеству изображений
    $response->assertSee('thumbnail-vertical', false);
    // Дополнительная проверка через DOM parser
})->repeat(100);

/**
 * Feature: product-page-migration, Property 6: Элементы блока покупки
 */
test('product page shows purchase elements for in-stock products', function () {
    $product = Product::factory()->inStock()->create();
    
    $response = $this->get("/product/{$product->id}");
    
    $response->assertSee('Добавить в корзину');
    $response->assertSee('В наличии');
    $response->assertSee('productQuantity', false); // ID поля количества
})->repeat(100);

/**
 * Feature: product-page-migration, Property 8: Отсутствие товара на складе
 */
test('product page shows out of stock message for unavailable products', function () {
    $product = Product::factory()->outOfStock()->create();
    
    $response = $this->get("/product/{$product->id}");
    
    $response->assertSee('Нет в наличии');
    $response->assertDontSee('Добавить в корзину');
})->repeat(100);

/**
 * Feature: product-page-migration, Property 11: Отображение всех атрибутов
 */
test('product page displays all attributes correctly', function () {
    $product = Product::factory()->create();
    $attributeCount = rand(3, 15);
    $attributes = ProductAttribute::factory()->count($attributeCount)->create([
        'product_id' => $product->id
    ]);
    
    $response = $this->get("/product/{$product->id}");
    
    foreach ($attributes as $attr) {
        $response->assertSee($attr->name);
        $response->assertSee($attr->value);
    }
})->repeat(100);

/**
 * Feature: product-page-migration, Property 13: Отображение отзывов
 */
test('product page displays all reviews with complete information', function () {
    $product = Product::factory()->create();
    $reviewCount = rand(1, 20);
    $reviews = ProductReview::factory()->count($reviewCount)->create([
        'product_id' => $product->id
    ]);
    
    $response = $this->get("/product/{$product->id}");
    
    foreach ($reviews as $review) {
        $response->assertSee($review->text);
        // Проверяем наличие звезд рейтинга
        // Проверяем наличие даты
    }
})->repeat(100);

/**
 * Feature: product-page-migration, Property 20: Количество похожих товаров
 */
test('product page shows 4 to 12 related products', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id]);
    
    // Создаем случайное количество товаров в той же категории
    $relatedCount = rand(4, 20);
    Product::factory()->count($relatedCount)->create(['category_id' => $category->id]);
    
    $response = $this->get("/product/{$product->id}");
    
    // Проверяем что отображается от 4 до 12 товаров
    // Используем DOM parser для подсчета карточек товаров
})->repeat(100);

/**
 * Feature: product-page-migration, Property 24: Open Graph теги
 */
test('product page includes all required open graph tags', function () {
    $product = Product::factory()->create();
    
    $response = $this->get("/product/{$product->id}");
    
    $response->assertSee('og:title', false);
    $response->assertSee('og:description', false);
    $response->assertSee('og:image', false);
    $response->assertSee('og:url', false);
    $response->assertSee('og:type', false);
    $response->assertSee('content="product"', false);
})->repeat(100);

/**
 * Feature: product-page-migration, Property 25: Schema.org разметка
 */
test('product page includes valid schema.org markup', function () {
    $product = Product::factory()->create();
    
    $response = $this->get("/product/{$product->id}");
    
    $response->assertSee('application/ld+json', false);
    $response->assertSee('"@type":"Product"', false);
    $response->assertSee('"name":', false);
    $response->assertSee('"offers":', false);
    
    // Парсим JSON-LD и проверяем структуру
})->repeat(100);
```

**Service Tests** (`tests/Feature/ProductServicePropertiesTest.php`):

```php
/**
 * Feature: product-page-migration, Property 28: ProductService.getProductBySlug
 */
test('getProductBySlug returns product for valid slug and null for invalid', function () {
    $product = Product::factory()->create();
    $service = app(ProductService::class);
    
    // Валидный slug
    $result = $service->getProductBySlug($product->id);
    expect($result)->not->toBeNull();
    expect($result->id)->toBe($product->id);
    
    // Невалидный slug
    $result = $service->getProductBySlug('invalid-slug-' . rand(10000, 99999));
    expect($result)->toBeNull();
})->repeat(100);

/**
 * Feature: product-page-migration, Property 31: ProductService.getRelatedProducts
 */
test('getRelatedProducts returns products from same category excluding current', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id]);
    
    // Создаем товары в той же категории
    $relatedCount = rand(5, 15);
    Product::factory()->count($relatedCount)->create(['category_id' => $category->id]);
    
    $service = app(ProductService::class);
    $related = $service->getRelatedProducts($product->id);
    
    expect($related)->toBeArray();
    expect(count($related))->toBeGreaterThanOrEqual(4);
    expect(count($related))->toBeLessThanOrEqual(12);
    
    // Проверяем что текущий товар не включен
    foreach ($related as $relatedProduct) {
        expect($relatedProduct->id)->not->toBe($product->id);
        expect($relatedProduct->category_id)->toBe($category->id);
    }
})->repeat(100);
```

### Integration Testing

**Cart Integration Tests** (`tests/Feature/ProductCartIntegrationTest.php`):

```php
test_add_to_cart_button_has_knockout_bindings()
test_cart_counter_updates_after_adding_product()
test_multiple_products_can_be_added_to_cart()
```

**SEO Tests** (`tests/Feature/ProductSeoTest.php`):

```php
test_meta_description_length_is_under_160_characters()
test_schema_markup_is_valid_json()
test_schema_markup_includes_aggregate_rating_for_products_with_reviews()
```

### Browser Testing (Optional)

Для тестирования JavaScript взаимодействий можно использовать Laravel Dusk:

```php
test_clicking_thumbnail_updates_main_image()
test_tab_switching_works_correctly()
test_carousel_navigation_works()
test_add_to_cart_updates_header_counter()
```

### Test Coverage Goals

- Unit тесты: 80%+ покрытие кода
- Property тесты: 100% покрытие correctness properties
- Integration тесты: Все критичные пользовательские сценарии
- Browser тесты: Основные JavaScript взаимодействия



## Implementation Notes

### Migration Strategy

Миграция будет выполняться поэтапно:

1. **Фаза 1: Расширение ProductService**
   - Добавление новых методов в ProductService
   - Unit тесты для каждого метода
   - Обновление существующего метода getProductRating

2. **Фаза 2: Создание Blade компонентов**
   - Создание 6 новых компонентов
   - Тестирование каждого компонента изолированно
   - Проверка совместимости CSS классов с legacy

3. **Фаза 3: Обновление ProductController**
   - Рефакторинг метода show для использования сервисов
   - Добавление приватных методов для breadcrumbs и SEO
   - Unit тесты контроллера

4. **Фаза 4: Создание главного view**
   - Создание resources/views/product/show.blade.php
   - Интеграция всех компонентов
   - Добавление SEO метаданных

5. **Фаза 5: Тестирование и валидация**
   - Запуск всех unit тестов
   - Запуск property-based тестов
   - Ручное тестирование в браузере
   - Проверка совместимости с Knockout.js

6. **Фаза 6: Деплой и мониторинг**
   - Деплой на staging окружение
   - A/B тестирование с legacy версией
   - Мониторинг ошибок и производительности
   - Постепенный переход трафика

### CSS and JavaScript Compatibility

**Сохранение CSS классов:**
- Все CSS классы из legacy системы сохраняются без изменений
- Новые CSS классы добавляются только если необходимо
- CSS файлы из legacy подключаются в том же порядке

**Сохранение HTML ID:**
- `mainImage` - главное изображение в галерее
- `productQuantity` - поле выбора количества
- `favoriteBtn` - кнопка избранного (если будет реализована)

**Сохранение data-атрибутов:**
- `data-product-id` - ID товара на элементах
- `data-bind` - Knockout.js bindings
- `data-image` - URL изображения на миниатюрах
- `data-tab` - ID таба для переключения
- `data-carousel` - ID карусели для навигации

**JavaScript файлы:**
- `/assets/sfera/js/product.js` - основной JavaScript для страницы товара
- Подключается в конце страницы через @push('scripts')
- Инициализация происходит после загрузки DOM

### Performance Considerations

**Database Queries:**
- Использование eager loading для связанных данных
- Кэширование статических данных (категории, атрибуты)
- Индексы на часто используемых полях

**Image Optimization:**
- Lazy loading для изображений ниже fold
- Использование srcset для адаптивных изображений
- CDN для статических ресурсов

**Caching Strategy:**
- Кэширование данных товара на 5 минут
- Кэширование похожих товаров на 15 минут
- Инвалидация кэша при обновлении товара

**Frontend Performance:**
- Минификация CSS и JavaScript
- Отложенная загрузка некритичных скриптов
- Использование HTTP/2 для параллельной загрузки ресурсов

### Security Considerations

**XSS Protection:**
- Использование Blade экранирования `{{ }}` для всех пользовательских данных
- Использование `{!! !!}` только для проверенного HTML (описание товара)
- Санитизация HTML в описаниях товаров

**CSRF Protection:**
- CSRF токены для всех форм (форма отзыва)
- Проверка токенов на сервере

**SQL Injection Protection:**
- Использование prepared statements во всех запросах
- Валидация входных параметров
- Использование Eloquent ORM где возможно

**Access Control:**
- Проверка авторизации для форм отзывов
- Валидация прав доступа к административным функциям

### Accessibility Considerations

**Semantic HTML:**
- Использование правильных HTML5 тегов
- Структурированные заголовки (h1, h2, h3)
- Правильная иерархия контента

**ARIA Attributes:**
- aria-label для кнопок без текста
- aria-hidden для декоративных элементов
- role атрибуты для интерактивных элементов

**Keyboard Navigation:**
- Все интерактивные элементы доступны с клавиатуры
- Логичный порядок табуляции
- Видимый focus indicator

**Screen Reader Support:**
- Alt текст для всех изображений
- Описательные тексты ссылок
- Объявления изменений контента

### Monitoring and Logging

**Application Logging:**
- Логирование всех ошибок ProductService
- Логирование 404 ошибок для анализа
- Логирование медленных запросов (>1s)

**Performance Monitoring:**
- Мониторинг времени загрузки страницы
- Мониторинг времени выполнения запросов к БД
- Алерты при превышении пороговых значений

**Error Tracking:**
- Интеграция с Sentry или аналогом
- Группировка ошибок по типам
- Уведомления о критичных ошибках

**Analytics:**
- Отслеживание просмотров товаров
- Отслеживание кликов на кнопку корзины
- Отслеживание взаимодействий с галереей

