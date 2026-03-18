# Настройка проекта MedX в OSPanel

## Требования

- PHP 8.5 или выше
- OSPanel с Apache/Nginx
- Composer

## Быстрая настройка

### Шаг 1: Проверка версии PHP

Убедитесь, что используется PHP 8.5:
```bash
php -v
```

Если версия ниже, обновите PHP в OSPanel:
1. Откройте OSPanel → Настройки → Модули
2. Выберите PHP 8.5
3. Перезапустите OSPanel

### Шаг 2: Настройка домена в OSPanel

1. Откройте OSPanel
2. Нажмите правой кнопкой на иконку OSPanel в трее
3. Выберите "Настройки" → "Домены"
4. Добавьте новый домен:
   - Имя домена: `medx`
   - Папка проекта: путь к папке `public` вашего Laravel проекта
   - Например: `C:\Users\Timofey\Documents\GitHub\MedX\public`

### Шаг 3: Перезапуск OSPanel

1. Остановите OSPanel
2. Запустите OSPanel снова
3. Убедитесь, что Apache и MySQL запущены

### Шаг 4: Проверка работы

Откройте браузер и перейдите по адресам:
- `http://medx` - главная страница
- `http://medx/login` - страница входа
- `http://medx/main_showcase` - база знаний

## Альтернативная настройка (ручная)

### Вариант 1: Через конфигурацию Apache

1. Откройте файл конфигурации виртуальных хостов OSPanel
2. Добавьте:

```apache
<VirtualHost *:80>
    ServerName medx
    DocumentRoot "C:/Users/Timofey/Documents/GitHub/MedX/public"
    
    <Directory "C:/Users/Timofey/Documents/GitHub/MedX/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

3. Добавьте в файл `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1 medx
```

### Вариант 2: Использование встроенного сервера PHP

Если не хотите настраивать OSPanel, можете использовать встроенный сервер Laravel:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Затем откройте: `http://localhost:8000`

## Проверка статических файлов

Убедитесь, что следующие файлы доступны:
- `http://medx/assets/medx/styles.css`
- `http://medx/site/modules/medx/showcase/style_fixed.css`
- `http://medx/site/modules/medx/showcase/img/logo@2x.png`

## Возможные проблемы

### Проблема: Страница не открывается
**Решение:** Проверьте, что Apache запущен в OSPanel и домен правильно настроен

### Проблема: CSS/JS не загружаются
**Решение:** Убедитесь, что папки `assets`, `js`, `site` скопированы в `public/`

### Проблема: Ошибка 404
**Решение:** Проверьте, что файл `.htaccess` существует в папке `public/`

### Проблема: Ошибка прав доступа
**Решение:** Дайте права на запись для папок `storage` и `bootstrap/cache`:
```bash
chmod -R 775 storage bootstrap/cache
```

## Структура URL

После настройки будут доступны:
- `/` → Главная страница (showcase)
- `/login` → Страница входа
- `/main_showcase` → База знаний

Все ссылки в шаблонах уже настроены и будут работать автоматически.
