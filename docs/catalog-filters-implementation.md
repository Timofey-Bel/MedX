# Реализация фильтров каталога

## Обзор

Добавлена функциональность фильтрации товаров в каталоге по следующим параметрам:
- Авторы
- Возраст
- Серии
- Типы товаров
- Тематики

## Технические детали

### Архитектура

Фильтрация реализована через GET-запросы (не AJAX) для обеспечения:
- SEO-оптимизации (индексируемые URL)
- Возможности отладки через DBForge
- Простоты тестирования

### Подход к фильтрации

1. **Получение ID товаров** - сначала получаем список ID товаров, соответствующих фильтрам
2. **Получение деталей** - затем получаем дополнительные данные (рейтинги, изображения) только для отфильтрованных товаров
3. **EXISTS подзапросы** - используются для эффективной фильтрации без лишних JOIN

### Файлы изменены

#### 1. `app/Services/FilterService.php`

Добавлены методы для получения данных фильтров:

```php
// Получение списка серий с количеством товаров
public function getSeries(int $limit = 0): array

// Получение списка типов товаров с количеством товаров
public function getProductTypes(int $limit = 0): array

// Получение списка тематик с количеством товаров
public function getTopics(int $limit = 0): array
```

Эти методы используют представления базы данных:
- `v_seriya` - для серий
- `v_tip_tovara` - для типов товаров
- `v_tematika` - для тематик

#### 2. `app/Http/Controllers/CatalogController.php`

**Добавлена обработка фильтров:**

```php
// Получение параметров фильтров из GET-запроса
$filter_authors = $request->input('author', []);
$filter_ages = $request->input('age', []);
$filter_series_ids = $request->input('seriya', []);
$filter_product_type_ids = $request->input('product_type', []);
$filter_topic_ids = $request->input('topic', []);

// Конвертация ID в значения для фильтрации
$filter_series = $this->convertIdsToValues($filter_series_ids, 'v_seriya');
$filter_product_types = $this->convertIdsToValues($filter_product_type_ids, 'v_tip_tovara');
$filter_topics = $this->convertIdsToValues($filter_topic_ids, 'v_tematika');
```

**Добавлен вспомогательный метод:**

```php
/**
 * Конвертация массива ID в массив значений из представления
 */
private function convertIdsToValues(array $ids, string $viewName): array
```

**Реализована фильтрация в методах `getProducts()` и `getProductsCount()`:**

```php
// Фильтр по авторам
if (!empty($filter_authors) && is_array($filter_authors)) {
    $where_clauses[] = "EXISTS (SELECT 1 FROM authors a 
                        WHERE a.product_id = p.id 
                        AND a.author_name IN (...))";
}

// Фильтр по возрасту
if (!empty($filter_ages) && is_array($filter_ages)) {
    $where_clauses[] = "EXISTS (SELECT 1 FROM ages ag 
                        WHERE ag.product_id = p.id 
                        AND ag.age IN (...))";
}

// Фильтр по сериям
if (!empty($filter_series) && is_array($filter_series)) {
    $where_clauses[] = "EXISTS (SELECT 1 FROM attributes a 
                        WHERE a.product_id = p.id 
                        AND BINARY a.name = 'Серия' 
                        AND BINARY a.value IN (...))";
}

// Аналогично для типов товаров и тематик
```

#### 3. `resources/views/catalog/index.blade.php`

**Добавлены секции фильтров:**

```blade
{{-- Фильтр по авторам --}}
@if (!empty($authors))
    <div class="filter-section">
        <h4>Автор</h4>
        @foreach ($authors as $author)
            <label class="filter-checkbox">
                <input type="checkbox" name="author[]" value="{{ $author['name'] }}" 
                       @if(in_array($author['name'], $filter_authors)) checked @endif>
                {{ $author['name'] }} ({{ $author['count'] }})
            </label>
        @endforeach
    </div>
@endif

{{-- Аналогично для возраста, серий, типов товаров и тематик --}}
```

**Обновлена пагинация для сохранения фильтров:**

```blade
@php
    // Формируем параметры для пагинации с сохранением всех фильтров
    $paginationParams = [];
    if (isset($category['id']) && $category['id']) {
        $paginationParams['category_id'] = $category['id'];
    }
    if (!empty($filter_authors)) {
        $paginationParams['author'] = $filter_authors;
    }
    // ... и так далее для всех фильтров
@endphp

<a href="{{ route('catalog.index', array_merge(['page' => $i], $paginationParams)) }}" 
   class="pagination-btn">{{ $i }}</a>
```

### Структура базы данных

**Таблицы для фильтрации:**

1. **authors** - авторы товаров
   - `product_id` - ID товара
   - `author_name` - имя автора

2. **ages** - возрастные категории
   - `product_id` - ID товара
   - `age` - значение возраста

3. **attributes** - атрибуты товаров (серии, типы, тематики)
   - `product_id` - ID товара
   - `name` - название атрибута ('Серия', 'Тип товара', 'Тематика')
   - `value` - значение атрибута

**Представления для фильтров:**

1. **v_seriya** - список серий с количеством товаров
   - `id` - ID серии
   - `value` - название серии
   - `cnt` - количество товаров

2. **v_tip_tovara** - список типов товаров
   - `id` - ID типа
   - `value` - название типа
   - `cnt` - количество товаров

3. **v_tematika** - список тематик
   - `id` - ID тематики
   - `value` - название тематики
   - `cnt` - количество товаров

## Примеры использования

### URL с фильтрами

```
# Фильтр по одному автору
/catalog?author[]=Пушкин+А.С.

# Фильтр по нескольким авторам
/catalog?author[]=Пушкин+А.С.&author[]=Толстой+Л.Н.

# Комбинированные фильтры
/catalog?author[]=Пушкин+А.С.&age[]=7-11+лет&seriya[]=123

# С пагинацией
/catalog?author[]=Пушкин+А.С.&page=2
```

### SQL-запросы для отладки

Все запросы можно легко скопировать и выполнить в DBForge для отладки:

```sql
-- Получение товаров с фильтром по автору
SELECT DISTINCT p.*, pr.price as product_price, p.quantity
FROM products p
LEFT JOIN prices pr ON p.id = pr.product_id AND pr.price_type_id = '000000002'
WHERE EXISTS (
    SELECT 1 FROM authors a 
    WHERE a.product_id = p.id 
    AND a.author_name IN ('Пушкин А.С.')
)
ORDER BY p.name ASC
LIMIT 0, 20;
```

## Производительность

### Оптимизации

1. **EXISTS подзапросы** вместо JOIN - избегаем дублирования строк
2. **Индексы** на таблицах authors, ages, attributes по product_id
3. **Представления** v_seriya, v_tip_tovara, v_tematika предварительно агрегируют данные
4. **Ленивая загрузка** - сначала ID товаров, потом детали

### Рекомендации

- Для больших каталогов (>10000 товаров) рассмотреть кэширование списков фильтров
- Мониторить производительность запросов через slow query log
- При необходимости добавить составные индексы на часто используемые комбинации фильтров

## Тестирование

### Ручное тестирование

1. Открыть каталог: http://sfera/catalog
2. Выбрать несколько фильтров
3. Нажать "Применить фильтр"
4. Проверить:
   - Отображаются только отфильтрованные товары
   - Количество товаров корректно
   - Пагинация работает с сохранением фильтров
   - URL содержит все параметры фильтров

### Тестирование в DBForge

1. Скопировать SQL-запрос из лога Laravel
2. Выполнить в DBForge
3. Проверить результаты и производительность
4. При необходимости оптимизировать запрос

## Известные ограничения

1. Фильтр по возрасту работает по точному совпадению (не по диапазону)
2. Максимум 4 автора отображается в фильтре (можно изменить параметр `$limit` в `FilterService::getAuthors()`)
3. Фильтры применяются через AND (все условия должны выполняться)

## Дальнейшие улучшения

1. Добавить фильтр по цене (min/max)
2. Реализовать фильтрацию по диапазону возраста
3. Добавить сортировку товаров (по цене, рейтингу, новизне)
4. Реализовать "умные" фильтры (показывать только доступные комбинации)
5. Добавить счетчики товаров для каждого значения фильтра
6. Кэширование списков фильтров

## Коммиты

1. `14cec43` - Add series, product types, and topics filters to catalog view
2. `4e558b1` - Add series, product types, and topics filter sections to catalog view with pagination support
3. `1dff059` - Fix filter parameter handling: convert IDs to values for series, product types, and topics
4. `ff952c5` - Implement age filtering using ages table

## Ветка

`feature/catalog-filters` - готова к мержу в `dev`
