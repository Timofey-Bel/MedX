---
title: Требования к данным карточек товаров
category: development-standards
tags: [products, controllers, data-consistency]
---

# Требования к данным карточек товаров

## Проблема

При разработке функционала поиска была обнаружена проблема: карточки товаров на странице результатов поиска не отображали состояние корзины и избранного, хотя данные передавались в view. Причина была в том, что метод `getProducts()` в `SearchController` не включал данные о рейтинге и количестве отзывов, которые есть в `CatalogController`.

## Обязательные данные для карточек товаров

Любой контроллер, который отображает карточки товаров (catalog, search, authors, series, topics, product_types и т.д.), ДОЛЖЕН возвращать следующий набор данных для каждого товара:

```php
[
    'id' => string,              // ID товара (offer_id)
    'name' => string,            // Название товара
    'description' => string,     // Описание товара
    'image' => string,           // URL изображения товара
    'price' => int,              // Цена товара (округленная)
    'quantity' => int,           // Количество товара на складе
    'rating' => float,           // Средний рейтинг (0 если нет отзывов)
    'reviews_count' => int       // Количество отзывов (0 если нет)
]
```

## Обязательные методы в контроллерах

Каждый контроллер, работающий с карточками товаров, ДОЛЖЕН иметь следующие приватные методы:

### 1. getProductImageUrl($productId)

```php
/**
 * Получение URL изображения товара
 * 
 * ЛОГИКА:
 * 1. Проверяем v_products_o_products (связь с Ozon)
 * 2. Если есть - ищем в o_images
 * 3. Если нет - используем /import_files/{id}b.jpg
 * 4. Fallback - product_empty.jpg
 * 
 * @param string $productId - ID товара
 * @return string - URL изображения
 */
private function getProductImageUrl($productId)
{
    if (empty($productId)) {
        return '/assets/img/product_empty.jpg';
    }
    
    $ozonProduct = DB::table('v_products_o_products')
        ->where('offer_id', $productId)
        ->first();
    
    if ($ozonProduct && !empty($ozonProduct->product_id)) {
        $oImage = DB::table('o_images')
            ->where('product_id', $ozonProduct->product_id)
            ->where('image_order', 0)
            ->first();
        
        if ($oImage) {
            return "/o_images/{$oImage->product_id}/0.jpg";
        }
    }
    
    return "/import_files/{$productId}b.jpg";
}
```

### 2. getProductRating($productId)

```php
/**
 * Получение рейтинга и количества отзывов для товара
 * 
 * ЛОГИКА:
 * 1. Проверяем v_products_o_products для получения SKU
 * 2. Если SKU найден - получаем средний рейтинг и количество отзывов из o_reviews
 * 3. Если данных нет - возвращаем 0
 * 
 * @param string $productId - ID товара (offer_id)
 * @return array - ['rating' => float, 'reviews_count' => int]
 */
private function getProductRating($productId)
{
    if (empty($productId)) {
        return ['rating' => 0, 'reviews_count' => 0];
    }
    
    $rating = 0;
    $reviewsCount = 0;
    
    $ozonProduct = DB::table('v_products_o_products')
        ->where('offer_id', $productId)
        ->first();
    
    if ($ozonProduct && !empty($ozonProduct->sku)) {
        $sku = intval($ozonProduct->sku);
        
        $ratingData = DB::table('o_reviews')
            ->where('sku', $sku)
            ->whereNotNull('rating')
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total_count')
            ->first();
        
        if ($ratingData) {
            $rating = round($ratingData->avg_rating, 1);
            $reviewsCount = intval($ratingData->total_count);
        }
    }
    
    return ['rating' => $rating, 'reviews_count' => $reviewsCount];
}
```

## Использование в методе getProducts()

В методе `getProducts()` (или аналогичном) ОБЯЗАТЕЛЬНО вызывать оба метода для каждого товара:

```php
private function getProducts($page, $limit, /* другие параметры */)
{
    // ... запрос к БД ...
    
    $products = [];
    foreach ($results as $row) {
        // ОБЯЗАТЕЛЬНО получаем рейтинг для каждого товара
        $ratingData = $this->getProductRating($row->id);
        
        $products[] = [
            'id' => $row->id,
            'name' => $row->name ?? '',
            'description' => $row->description ?? '',
            'image' => $this->getProductImageUrl($row->id),
            'price' => round($row->product_price ?? 0),
            'quantity' => intval($row->quantity ?? 99),
            'rating' => $ratingData['rating'],           // ОБЯЗАТЕЛЬНО
            'reviews_count' => $ratingData['reviews_count'] // ОБЯЗАТЕЛЬНО
        ];
    }
    
    return $products;
}
```

## Передача данных корзины и избранного в view

Каждый контроллер ДОЛЖЕН передавать в view данные о корзине и избранном:

```php
public function index(Request $request)
{
    // ... получение товаров ...
    
    // ОБЯЗАТЕЛЬНО получаем корзину и избранное из сессии
    $cart = session('cart', ['items' => []]);
    
    // КРИТИЧЕСКИ ВАЖНО: Преобразование формата избранного для неавторизованных пользователей
    // В FavoriteController::add() для неавторизованных избранное сохраняется как простой массив ID:
    // session(['favorites' => ['00-00006779', '00-00006780']])
    // 
    // Но в шаблонах проверяется структура $favorites['items'][$product['id']], то есть ожидается:
    // ['items' => ['00-00006779' => true, '00-00006780' => true]]
    //
    // Поэтому ОБЯЗАТЕЛЬНО преобразуем формат:
    $sessionFavorites = session('favorites', []);
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
    
    return view('some.view', [
        'products' => $products,
        'cart' => $cart,           // ОБЯЗАТЕЛЬНО
        'favorites' => $favorites, // ОБЯЗАТЕЛЬНО (с преобразованием формата)
        // ... другие данные ...
    ]);
}
```

### Почему нужно преобразование формата избранного?

**Проблема**: В `FavoriteController::add()` для неавторизованных пользователей избранное сохраняется как простой массив ID товаров:

```php
// В FavoriteController::add() для неавторизованных
$favorites = session('favorites', []);
$favorites[] = $productId;  // Результат: ['00-00006779', '00-00006780']
session(['favorites' => $favorites]);
```

**Ожидание в шаблонах**: Blade-шаблоны проверяют наличие товара в избранном через структуру с ключом `items`:

```blade
@if (isset($favorites['items'][$product['id']]))
    {{-- Товар в избранном --}}
@endif
```

**Решение**: Контроллеры ДОЛЖНЫ преобразовывать простой массив ID в структуру с ключом `items` перед передачей в view.

**Эталонная реализация**: См. `CatalogController::index()` и `SearchController::index()`

## Контроллеры, которые должны соответствовать этим требованиям

- ✅ `CatalogController` - эталонная реализация
- ✅ `SearchController` - исправлено (добавлен getProductRating)
- ✅ `AuthorController` - уже соответствует
- ✅ `SeriesController` - уже соответствует
- ✅ `TopicController` - уже соответствует
- ✅ `ProductTypeController` - уже соответствует
- ⚠️ Любые новые контроллеры с карточками товаров

## Почему это важно

1. **Консистентность UI**: Все карточки товаров должны выглядеть одинаково независимо от страницы
2. **Функциональность корзины/избранного**: JavaScript код ожидает полный набор данных для работы
3. **SEO и UX**: Рейтинги и отзывы важны для принятия решения о покупке
4. **Отладка**: Единообразная структура данных упрощает поиск проблем

## Проверка при code review

При добавлении нового контроллера или изменении существующего проверяйте:

1. ✅ Есть ли методы `getProductImageUrl()` и `getProductRating()`?
2. ✅ Вызываются ли эти методы в `getProducts()`?
3. ✅ Включены ли `rating` и `reviews_count` в возвращаемый массив?
4. ✅ Передаются ли `cart` и `favorites` в view?
5. ✅ **КРИТИЧНО**: Преобразуется ли формат избранного из простого массива в структуру с `items`?
6. ✅ Соответствует ли структура данных эталонной?

## Ссылки на эталонные реализации

- `app/Http/Controllers/CatalogController.php` - основной эталон
- `app/Http/Controllers/SearchController.php` - пример исправления
- `app/Http/Controllers/AuthorController.php` - пример для страниц авторов

## История изменений

- 2026-02-27: Создан документ после исправления SearchController
- 2026-02-27: Добавлена проблема с отсутствием рейтинга в SearchController
- 2026-02-27: Добавлена критически важная информация о преобразовании формата избранного для неавторизованных пользователей
