# Обновление проекта на PHP 8.5

## Что изменено

Проект теперь требует PHP 8.5 или выше. Обновлен файл `composer.json`:

```json
"require": {
    "php": "^8.5",
    ...
}
```

## Установка PHP 8.5 в OSPanel

### Вариант 1: Через интерфейс OSPanel

1. Откройте OSPanel
2. Нажмите правой кнопкой на иконку в трее
3. Выберите "Настройки" → "Модули"
4. В разделе "PHP" выберите версию 8.5
5. Нажмите "Применить"
6. Перезапустите OSPanel

### Вариант 2: Ручная установка

1. Скачайте PHP 8.5 для Windows с официального сайта: https://windows.php.net/download/
2. Распакуйте в папку `C:\OSPanel\modules\PHP-8.5\`
3. Скопируйте `php.ini-development` в `php.ini`
4. Настройте расширения в `php.ini`
5. Перезапустите OSPanel

## Проверка версии PHP

После установки проверьте версию:

```bash
php -v
```

Должно вывести:
```
PHP 8.5.x (cli) ...
```

## Обновление зависимостей Composer

После установки PHP 8.5 обновите зависимости:

```bash
composer update
```

Или полная переустановка:

```bash
composer install
```

## Возможные проблемы

### Проблема: Composer требует более старую версию PHP

**Решение:** Обновите Composer до последней версии:
```bash
composer self-update
```

### Проблема: Некоторые пакеты несовместимы с PHP 8.5

**Решение:** Проверьте совместимость пакетов:
```bash
composer why-not php 8.5
```

Обновите несовместимые пакеты или найдите альтернативы.

### Проблема: OSPanel не видит PHP 8.5

**Решение:** 
1. Убедитесь, что папка `C:\OSPanel\modules\PHP-8.5\` существует
2. Проверьте наличие файла `php.exe` в этой папке
3. Перезапустите OSPanel с правами администратора

## Настройка php.ini для Laravel

Убедитесь, что в `php.ini` включены необходимые расширения:

```ini
extension=curl
extension=fileinfo
extension=gd
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=pdo_sqlite
extension=zip
```

## Проверка работы проекта

После обновления PHP проверьте работу проекта:

```bash
php artisan --version
```

Запустите встроенный сервер:

```bash
php artisan serve
```

Откройте в браузере: `http://localhost:8000`

## Преимущества PHP 8.5

- Улучшенная производительность
- Новые возможности языка
- Лучшая типизация
- Исправления безопасности
- Оптимизация памяти

## Дополнительная информация

- [PHP 8.5 Release Notes](https://www.php.net/releases/8.5/)
- [Laravel 12 Requirements](https://laravel.com/docs/12.x/deployment#server-requirements)
- [OSPanel Documentation](https://ospanel.io/)
