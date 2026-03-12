# Исправление функционала выбора пункта выдачи после git reset

**Дата:** 2026-03-05  
**Проблема:** После `git reset --hard origin/dev` перестал работать функционал выбора пункта выдачи на странице checkout

---

## Симптомы

В консоли браузера появлялась ошибка:

```
КРИТИЧЕСКАЯ ОШИБКА: model_pickpoint не создан!
```

Кнопка "Выбрать адрес доставки" не работала.

---

## Причина

После синхронизации с GitHub файл `checkout-init.js` остался на месте, но изменился порядок загрузки скриптов. Inline скрипт в `checkout/index.blade.php` выполнялся раньше, чем успевал загрузиться и выполниться `checkout-init.js`, который создаёт `model_pickpoint`.

### Проблемный код

```javascript
$(document).ready(function(){
    if (typeof model_pickpoint === 'undefined') {
        console.error('КРИТИЧЕСКАЯ ОШИБКА: model_pickpoint не создан!');
        return; // Прерывание выполнения
    }
    // ... остальной код
});
```

**Проблема:** Проверка выполнялась синхронно, не давая времени на загрузку `checkout-init.js`.

---

## Решение

Добавлен механизм ожидания создания `model_pickpoint` с интервальной проверкой:

```javascript
$(document).ready(function(){
    console.log('=== Checkout page initialization ===');
    
    // Ждём создания model_pickpoint с таймаутом
    var checkPickpoint = setInterval(function() {
        if (typeof model_pickpoint !== 'undefined') {
            clearInterval(checkPickpoint);
            initializeCheckout();
        }
    }, 50); // Проверка каждые 50мс
    
    // Таймаут на случай, если model_pickpoint не создастся
    setTimeout(function() {
        clearInterval(checkPickpoint);
        if (typeof model_pickpoint === 'undefined') {
            console.error('КРИТИЧЕСКАЯ ОШИБКА: model_pickpoint не создан после ожидания!');
        }
    }, 3000); // Максимум 3 секунды ожидания
    
    function initializeCheckout() {
        console.log('✓ model_pickpoint найден, инициализация...');
        
        // Привязываем модель пункта выдачи к элементу .address-selector
        if($('.address-selector')[0]) {
            ko.applyBindings(model_pickpoint, $('.address-selector')[0]);
            console.log('✓ model_pickpoint bound to .address-selector');
        }
        
        // ... остальной код инициализации
    }
});
```

---

## Что изменилось

### До исправления
- ❌ Синхронная проверка `model_pickpoint`
- ❌ Немедленное прерывание при отсутствии
- ❌ Нет времени на загрузку скрипта

### После исправления
- ✅ Асинхронное ожидание с интервалом 50мс
- ✅ Таймаут 3 секунды для безопасности
- ✅ Инициализация в отдельной функции `initializeCheckout()`
- ✅ Логирование для отладки

---

## Файлы

### Изменённые файлы
- `resources/views/checkout/index.blade.php` - добавлен механизм ожидания

### Связанные файлы
- `public/assets/sfera/js/checkout-init.js` - создаёт `model_pickpoint`
- `public/assets/sfera/js/checkout.js` - обработчики событий

---

## Проверка

После исправления:

1. ✅ Очищен кеш: `php artisan view:clear`
2. ✅ Нет ошибок компиляции Blade
3. ✅ Страница checkout открывается без ошибок
4. ✅ `model_pickpoint` создаётся и находится скриптом
5. ✅ Кнопка "Выбрать адрес доставки" работает

---

## Тестирование

Для проверки работы функционала:

1. Открыть страницу `/checkout`
2. Открыть консоль браузера (F12)
3. Проверить логи:
   ```
   === checkout-init.js loaded ===
   ✓ model_pickpoint created: object
   === Checkout page initialization ===
   ✓ model_pickpoint найден, инициализация...
   ✓ model_pickpoint bound to .address-selector
   ```
4. Нажать кнопку "Выбрать адрес доставки"
5. Должна открыться карта с пунктами выдачи

---

## Альтернативные решения (не использованы)

### 1. Изменить порядок загрузки скриптов
```php
@push('head')
    <script src="{{ asset('assets/sfera/js/checkout-init.js') }}"></script>
@endpush
```
**Минус:** Скрипт загрузится раньше jQuery и Knockout

### 2. Использовать async/await
```javascript
async function waitForPickpoint() {
    while (typeof model_pickpoint === 'undefined') {
        await new Promise(resolve => setTimeout(resolve, 50));
    }
    initializeCheckout();
}
```
**Минус:** Более сложный код, нужна поддержка async/await

### 3. Объединить скрипты в один файл
**Минус:** Потеря модульности, сложнее поддерживать

---

## Выводы

Выбранное решение с `setInterval` и `setTimeout`:
- ✅ Простое и понятное
- ✅ Работает во всех браузерах
- ✅ Имеет защиту от бесконечного ожидания
- ✅ Не требует изменения структуры проекта
- ✅ Легко отлаживается через console.log

---

## История

- **2026-03-05:** Проблема обнаружена после `git reset --hard origin/dev`
- **2026-03-05:** Реализовано решение с асинхронным ожиданием
- **2026-03-05:** Проверка работы - успешно

---

## Связанные документы

- `docs/FINAL-VERIFICATION-REPORT.md` - Общая проверка системы
- `docs/git-sync-verification.md` - Синхронизация с GitHub
