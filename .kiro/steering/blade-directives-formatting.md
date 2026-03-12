# Форматирование Blade-директив

## Критическое правило

**ВСЕГДА размещайте Blade-директивы на отдельных строках, как HTML-теги.**

**НЕ используйте inline директивы (несколько директив на одной строке)!**

## Почему это важно

Парсер Blade может запутаться при обработке inline директив, особенно когда они вложены друг в друга. Это приводит к синтаксическим ошибкам типа:

- `syntax error, unexpected token "endforeach", expecting "elseif" or "else" or "endif"`
- `unexpected end of file, expecting endif`

## Правильное форматирование

### ✅ ПРАВИЛЬНО - Каждая директива на отдельной строке

```php
{{-- Пример 1: Простое условие --}}
@if($user->isAdmin())
    <p>Вы администратор</p>
@else
    <p>Вы обычный пользователь</p>
@endif

{{-- Пример 2: Цикл с условием --}}
@foreach($items as $item)
    @if($item->isActive())
        <div>{{ $item->name }}</div>
    @endif
@endforeach

{{-- Пример 3: JavaScript объект с Blade --}}
<script>
var config = {
    enabled: 
@if($config['enabled'])
true
@else
false
@endif
    ,
    name: '{{ $config['name'] }}'
};
</script>
```

### ❌ НЕПРАВИЛЬНО - Inline директивы

```php
{{-- НЕПРАВИЛЬНО! Парсер запутается --}}
@if($user->isAdmin())Admin@else User@endif

{{-- НЕПРАВИЛЬНО! Несколько директив на одной строке --}}
enabled: @if($enabled)true@else false@endif,

{{-- НЕПРАВИЛЬНО! Inline foreach --}}
@foreach($items as $item){{ $item->name }},@endforeach
```

## Отступы для Blade-директив

**Важно:** Blade-директивы (`@if`, `@foreach`, `@endif` и т.д.) должны начинаться с начала строки БЕЗ отступов.

Отступы используются только для HTML-контента внутри директив.

```php
{{-- ПРАВИЛЬНО --}}
<div>
@if($condition)
    <p>Контент с отступом</p>
@endif
</div>

{{-- НЕПРАВИЛЬНО - отступы у директив --}}
<div>
    @if($condition)
        <p>Контент</p>
    @endif
</div>
```

**Исключение:** Если Blade-директива находится внутри HTML-тега или JavaScript кода, можно использовать отступы для читаемости, но лучше всё равно размещать директивы на отдельных строках.

## Сложные случаи

### JavaScript объекты с динамическими свойствами

```php
{{-- ПРАВИЛЬНО --}}
<script>
var moduleAccess = {
    permissions: 
@if(isset($moduleAccess['permissions']) && $moduleAccess['permissions'])
true
@else
false
@endif
    ,
    users: 
@if(isset($moduleAccess['users']) && $moduleAccess['users'])
true
@else
false
@endif
@foreach($installed_apps as $app)
    ,
    {{ $app['app_id'] }}: 
@if(isset($moduleAccess[$app['app_id']]) && $moduleAccess[$app['app_id']])
true
@else
false
@endif
@endforeach
};
</script>
```

### Вложенные условия

```php
{{-- ПРАВИЛЬНО --}}
@if($user)
    @if($user->isAdmin())
        <p>Администратор</p>
    @elseif($user->isModerator())
        <p>Модератор</p>
    @else
        <p>Пользователь</p>
    @endif
@else
    <p>Гость</p>
@endif
```

## Отладка синтаксических ошибок

### Симптомы проблемы

1. `syntax error, unexpected token "endforeach"`
2. `unexpected end of file, expecting endif`
3. Blade не компилируется

### Решение

1. Найдите все inline директивы в файле
2. Разделите каждую директиву на отдельную строку
3. Убедитесь, что директивы начинаются с начала строки
4. Очистите кеш view: `php artisan view:clear`

### Поиск проблемных мест

```bash
# Найти inline @if...@endif
grep -n "@if.*@endif" resources/views/**/*.blade.php

# Найти inline @foreach...@endforeach
grep -n "@foreach.*@endforeach" resources/views/**/*.blade.php

# Найти @endif@foreach (директивы без разделения)
grep -n "@endif@foreach" resources/views/**/*.blade.php
```

## Пример из реального проекта

### Проблема (2026-03-02)

В файле `resources/views/admin/desktop/index.blade.php` была inline директива:

```php
{{-- БЫЛО (неправильно): --}}
app_installer: @if(isset($moduleAccess['app_installer']))true@else false@endif@foreach($apps as $app),
{{ $app['id'] }}: @if(isset($moduleAccess[$app['id']]))true@else false@endif@endforeach
```

**Ошибка:** `syntax error, unexpected token "endforeach", expecting "elseif" or "else" or "endif"`

**Решение:** Разделили все директивы на отдельные строки:

```php
{{-- СТАЛО (правильно): --}}
app_installer: 
@if(isset($moduleAccess['app_installer']))
true
@else
false
@endif
@foreach($apps as $app)
    ,
    {{ $app['id'] }}: 
@if(isset($moduleAccess[$app['id']]))
true
@else
false
@endif
@endforeach
```

**Результат:** Ошибка исчезла, шаблон скомпилировался успешно.

## Чек-лист для разработчиков

При создании или редактировании Blade-шаблонов:

- [ ] Каждая Blade-директива на отдельной строке
- [ ] Директивы начинаются с начала строки (без отступов)
- [ ] Нет inline директив типа `@if(...)...@else...@endif`
- [ ] Парные директивы правильно закрыты (`@if` → `@endif`, `@foreach` → `@endforeach`)
- [ ] После изменений очищен кеш: `php artisan view:clear`
- [ ] Шаблон компилируется без ошибок

## Связанные правила

- [blade-scripts-styles.md](blade-scripts-styles.md) - Правила подключения скриптов и стилей

## История

- 2026-03-02: Создан документ после исправления синтаксических ошибок в admin desktop шаблоне
- Проблема: Inline Blade-директивы вызывали ошибки парсера
- Решение: Разделение всех директив на отдельные строки
