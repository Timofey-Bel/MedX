# Модули "Серии", "Тематики", "Типы товаров"

## Обзор

Три модуля для отображения товаров по различным атрибутам:
- **Серии** - группировка товаров по сериям
- **Тематики** - группировка товаров по тематикам
- **Типы товаров** - группировка товаров по типам

**Миграция из legacy:**
- Серии: `site/modules/sfera/series/` и `site/modules/sfera/seriya/`
- Тематики: `site/modules/sfera/topics/` и `site/modules/sfera/topic/`
- Типы товаров: `site/modules/sfera/product_types/` и `site/modules/sfera/product_type/`

**Дата создания:** 2026-02-27

**Статус:** ✅ Реализовано

---

## Архитектура

Все три модуля следуют одинаковой архитектуре:

### Backend (Laravel)

#### Контроллеры

1. **SeriesController** (`app/Http/Controllers/SeriesController.php`)
   - `index()` - список всех серий из view `v_seriya`
   - `show(Request $request)` - товары серии по `seriya_id`
   - Route: `GET /series`, `GET /seriya?seriya_id={id}`

2. **TopicController** (`app/Http/Controllers/TopicController.php`)
   - `index()` - список всех тематик из view `v_tematika`
   - `show(Request $request)` - товары тематики по `topic_id`
   - Route: `GET /topics`, `GET /topic?topic_id={id}`

3. **ProductTypeController** (`app/Http/Controllers/ProductTypeController.php`)
   - `index()` - список всех типов товаров из view `v_tip_tovara`
   - `show(Request $request)` - товары типа по `product_type_id`
   - Route: `GET /product_types`, `GET /product_type?product_type_id={id}`

**Общие особенности:**
- Используют прямые SQL-запросы через `DB::select()`, `DB::selectOne()`
- Группировка по первой букве для удобного отображения
- Пагинация (20 товаров на страницу)
- Интеграция с ProductService для получения рейтингов
- Передают данные корзины и избранного из сессии в шаблоны
- Все комментарии на русском языке

---

## Frontend

### Blade Templates

Каждый модуль имеет два шаблона:

#### Списки (index.blade.php)
- `resources/views/series/index.blade.php`
- `resources/views/topics/index.blade.php`
- `resources/views/product-types/index.blade.php`

**Особенности:**
- Расширяют `layouts.app`
- Группировка по алфавиту
- Отображение количества товаров
- Breadcrumbs навигация

#### Страницы с товарами (show.blade.php)
- `resources/views/series/show.blade.php`
- `resources/views/topics/show.blade.php`
- `resources/views/product-types/show.blade.php`

**Особенности:**
- Расширяют `layouts.app`
- Карточки товаров с изображениями, ценами и рейтингами
- Интеграция с корзиной и избранным (отображение состояния из сессии)
- Иконки избранного с классом `favorite-filled` для товаров в избранном
- Пагинация (20 товаров на страницу)
- Подключают `catalog.js` для функциональности

---

## База данных

### Views

1. **v_seriya** - серии с количеством товаров
   - `id` - ID серии
   - `value` - название серии
   - `cnt` - количество товаров

2. **v_tematika** - тематики с количеством товаров
   - `id` - ID тематики
   - `value` - название тематики
   - `cnt` - количество товаров

3. **v_tip_tovara** - типы товаров с количеством товаров
   - `id` - ID типа товара
   - `value` - название типа товара
   - `cnt` - количество товаров

### Таблица attributes

Товары связаны с сериями/тематиками/типами через таблицу `attributes`:
- `product_id` - ID товара
- `name` - название атрибута ("Серия", "Тематика", "Тип товара")
- `value` - значение атрибута (название серии/тематики/типа)

---

## Роуты

```php
// Серии
GET /series → SeriesController@index
GET /seriya?seriya_id={id} → SeriesController@show

// Тематики
GET /topics → TopicController@index
GET /topic?topic_id={id} → TopicController@show

// Типы товаров
GET /product_types → ProductTypeController@index
GET /product_type?product_type_id={id} → ProductTypeController@show
```

---

## Требования

Модули реализуют следующие требования из спецификации:

- **1.1-1.7** - Процесс миграции модулей
- **2.1-2.4** - Сохранение SQL-запросов
- **3.1-3.5** - Конвертация Smarty шаблонов в Blade
- **24.4** - Комментарии на русском языке

---

## Интеграция

### Корзина
- Кнопки "В корзину" на карточках товаров
- Счетчик количества в корзине
- Синхронизация состояния через JavaScript

### Избранное
- Иконки избранного на карточках товаров
- Синхронизация состояния через JavaScript
- Интеграция с FavoritesViewModel

---

## Следующие шаги

1. ✅ Реализовать страницы серий - **ЗАВЕРШЕНО**
2. ✅ Реализовать страницы тематик - **ЗАВЕРШЕНО**
3. ✅ Реализовать страницы типов товаров - **ЗАВЕРШЕНО**
4. ⏭️ Завершить фильтры каталога (Этап 5)
5. ⏭️ Реализовать поиск товаров (Этап 9)
