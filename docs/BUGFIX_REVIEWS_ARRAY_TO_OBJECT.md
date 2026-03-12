# Исправление ошибки "Attempt to read property on array" в ProductService::getProductReviews()

## Проблема
Метод `ProductService::getProductReviews()` возвращал массив массивов, но в Blade-шаблоне `show.blade.php` использовался объектный синтаксис для доступа к свойствам отзывов:

```php
$review->review_id
$review->first_letter
$review->formatted_date
$review->rating
$review->text
```

Это приводило к ошибке: **"Attempt to read property 'review_id' on array"**

## Решение
Изменена строка 352 в `app/Services/ProductService.php`:

**Было:**
```php
$reviews[] = [
    'review_id' => $review->review_id,
    ...
];
```

**Стало:**
```php
$reviews[] = (object)[
    'review_id' => $review->review_id,
    ...
];
```

Добавление `(object)` преобразует каждый элемент массива в объект `stdClass`, что позволяет использовать объектный синтаксис в Blade.

## Единообразие
Это решение соответствует подходу, уже используемому в методе `getProductImages()` (строки 479, 487), который также возвращает массив объектов.

## Тестирование
Создан unit-тест `tests/Unit/ProductServiceReviewsTest.php`, который проверяет, что метод возвращает массив объектов, а не массив массивов.

Тест успешно пройден ✓

## Файлы изменены
- `app/Services/ProductService.php` - исправлен метод `getProductReviews()`
- `tests/Unit/ProductServiceReviewsTest.php` - добавлен тест для проверки типа возвращаемых данных
