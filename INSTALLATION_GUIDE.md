# Руководство по установке MedX

## Проблема с установкой

При попытке установить зависимости возникает ошибка прав доступа.

## Решение

### Вариант 1: Запуск от администратора

1. Откройте PowerShell от имени администратора
2. Перейдите в папку проекта:
   ```powershell
   cd C:\Users\Timofey\Documents\GitHub\MedX
   ```
3. Выполните установку:
   ```powershell
   php composer.phar update
   ```

### Вариант 2: Использование глобального Composer

Если у вас установлен Composer глобально:

```powershell
composer update
```

### Вариант 3: Очистка кеша Composer

```powershell
php composer.phar clear-cache
php composer.phar install
```

### Вариант 4: Установка без dev-зависимостей

Если нужно быстро запустить проект:

```powershell
php composer.phar install --no-dev --ignore-platform-reqs
```

## После успешной установки

```powershell
# 1. Скопировать .env файл
copy .env.example .env

# 2. Сгенерировать ключ приложения
php artisan key:generate

# 3. Очистить кеши
php artisan optimize:clear

# 4. Запустить сервер
php artisan serve
```

## Открыть MedX

После запуска сервера откройте в браузере:

```
http://localhost:8000/medx
```

## Альтернативный способ (без установки зависимостей)

Если установка не удаётся, вы можете:

1. Скопировать папку `vendor` из другого Laravel 12 проекта
2. Или скачать готовый проект с зависимостями

## Структура MedX уже создана

Все файлы MedX уже созданы и готовы к работе:

✅ Контроллер: `app/Http/Controllers/MedxController.php`  
✅ Views: `resources/views/medx/`  
✅ CSS: `public/assets/medx/css/`  
✅ JavaScript: `public/assets/medx/js/`  
✅ Роуты добавлены в `routes/web.php`  

Осталось только установить зависимости Laravel.

## Проверка версии PHP

Убедитесь, что используется PHP 8.4:

```powershell
php -v
```

Должно быть: `PHP 8.4.16` или выше.

## Помощь

Если проблемы продолжаются:

1. Проверьте права доступа к папке проекта
2. Отключите антивирус временно
3. Попробуйте запустить PowerShell от администратора
4. Проверьте, что папка `vendor` не заблокирована

---

**Важно:** Структура MedX полностью готова. Проблема только с установкой зависимостей Laravel, которая не связана с интеграцией MedX.
