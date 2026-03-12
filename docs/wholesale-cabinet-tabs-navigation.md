# Навигационные табы в оптовом кабинете

## Обзор

Реализована система навигационных табов для оптового кабинета, которая обеспечивает удобную навигацию между основными разделами.

## Структура

### Разделы навигации

1. **Обзор** (`/lk/`) - Главная страница с статистикой и последними заказами
2. **Заказы** (`/lk/orders`) - Полный список всех заказов организации
3. **Организация** (`/lk/organization`) - Реквизиты и данные организации

### Компоненты

Навигационные табы состоят из двух частей:

```blade
<div class="tabs-header">
    <div class="tabs-nav">
        {{-- Навигационные ссылки --}}
    </div>
    <div class="tabs-actions">
        {{-- Кнопки для будущего использования --}}
    </div>
</div>
```

## Реализация

### HTML структура

Каждая страница содержит блок с табами:

```blade
<div class="section">
    <div class="tabs-header">
        <div class="tabs-nav">
            <a href="{{ route('lk.index') }}" class="tab-link {{ request()->routeIs('lk.index') ? 'active' : '' }}">
                <svg>...</svg>
                Обзор
            </a>
            <a href="{{ route('lk.orders') }}" class="tab-link {{ request()->routeIs('lk.orders') ? 'active' : '' }}">
                <svg>...</svg>
                Заказы
            </a>
            <a href="{{ route('lk.organization') }}" class="tab-link {{ request()->routeIs('lk.organization') ? 'active' : '' }}">
                <svg>...</svg>
                Организация
            </a>
        </div>
        <div class="tabs-actions">
            {{-- Кнопки для будущего использования --}}
        </div>
    </div>
</div>
```

### CSS стили

Стили находятся в `public/assets/sfera/css/wholesale-cabinet.css`:

```css
/* Контейнер табов */
.tabs-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  padding: 0;
  background: var(--card);
  border-radius: var(--radius-xl);
  border: 1px solid var(--border);
  overflow: hidden;
}

/* Навигация */
.tabs-nav {
  display: flex;
  align-items: center;
  gap: 0;
  flex: 1;
}

/* Ссылка таба */
.tab-link {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 16px 24px;
  color: var(--muted-fg);
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s;
  border-bottom: 2px solid transparent;
  position: relative;
}

.tab-link:hover {
  color: var(--fg);
  background: var(--muted);
}

.tab-link.active {
  color: var(--primary);
  border-bottom-color: var(--primary);
  background: var(--accent);
}

/* Блок для кнопок */
.tabs-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-left: 1px solid var(--border);
}
```

### Адаптивность

На мобильных устройствах (< 768px):
- Табы располагаются вертикально
- Навигация прокручивается горизонтально
- Блок кнопок перемещается вниз

```css
@media (max-width: 768px) {
  .tabs-header {
    flex-direction: column;
    align-items: stretch;
  }

  .tabs-nav {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .tab-link {
    padding: 12px 16px;
    font-size: 13px;
    white-space: nowrap;
  }

  .tabs-actions {
    border-left: none;
    border-top: 1px solid var(--border);
    justify-content: center;
  }
}
```

## Иконки

Каждый таб имеет SVG иконку:

- **Обзор**: Иконка дома (home)
- **Заказы**: Иконка пользователей (users)
- **Организация**: Иконка здания (building)

Иконки взяты из библиотеки Lucide Icons.

## Будущие возможности

Блок `tabs-actions` зарезервирован для будущих кнопок:

```blade
<div class="tabs-actions">
    {{-- Примеры для будущего использования: --}}
    {{-- <button class="btn btn-ghost">Экспорт</button> --}}
    {{-- <button class="btn btn-primary">Новый заказ</button> --}}
    {{-- <button class="btn btn-ghost">Фильтры</button> --}}
    {{-- <button class="btn btn-ghost">Редактировать</button> --}}
    {{-- <button class="btn btn-primary">Сохранить</button> --}}
</div>
```

### Возможные кнопки по разделам

**Обзор:**
- "Экспорт" - экспорт статистики
- "Новый заказ" - быстрое создание заказа

**Заказы:**
- "Фильтры" - фильтрация заказов
- "Новый заказ" - создание нового заказа
- "Экспорт" - экспорт списка заказов

**Организация:**
- "Редактировать" - редактирование данных
- "Сохранить" - сохранение изменений

## Файлы

### View файлы
- `resources/views/wholesale/index.blade.php` - Главная страница
- `resources/views/wholesale/orders.blade.php` - Страница заказов
- `resources/views/wholesale/organization.blade.php` - Страница организации

### Стили
- `public/assets/sfera/css/wholesale-cabinet.css` - Основные стили кабинета (включая табы)

## Использование

### Добавление нового раздела

1. Добавьте роут в `routes/web.php`:
```php
Route::get('/lk/new-section', [WholesaleProfileController::class, 'newSection'])
    ->name('lk.new-section');
```

2. Добавьте метод в контроллер `WholesaleProfileController`:
```php
public function newSection()
{
    $organization = Auth::user()->organization;
    return view('wholesale.new-section', compact('organization'));
}
```

3. Создайте view `resources/views/wholesale/new-section.blade.php`:
```blade
@extends('wholesale.layout')

@section('page-title', 'Новый раздел')

@section('content')
<div class="section">
    <div class="tabs-header">
        <div class="tabs-nav">
            <a href="{{ route('lk.index') }}" class="tab-link">
                <svg>...</svg>
                Обзор
            </a>
            <a href="{{ route('lk.orders') }}" class="tab-link">
                <svg>...</svg>
                Заказы
            </a>
            <a href="{{ route('lk.organization') }}" class="tab-link">
                <svg>...</svg>
                Организация
            </a>
            <a href="{{ route('lk.new-section') }}" class="tab-link active">
                <svg>...</svg>
                Новый раздел
            </a>
        </div>
        <div class="tabs-actions">
            {{-- Кнопки --}}
        </div>
    </div>
</div>

{{-- Контент раздела --}}
@endsection
```

4. Обновите табы на всех остальных страницах, добавив новую ссылку

## История изменений

- **2026-03-05**: Создана система навигационных табов
  - Добавлены табы на все три страницы оптового кабинета
  - Реализованы стили с адаптивностью
  - Добавлен блок `tabs-actions` для будущих кнопок
  - Добавлены SVG иконки для каждого раздела

## Связанные документы

- [Реализация оптового кабинета](wholesale-cabinet-implementation.md)
- [Регистрация оптовых покупателей](wholesale-registration-implementation.md)
