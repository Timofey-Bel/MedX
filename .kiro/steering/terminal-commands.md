---
inclusion: auto
---

# Инструкции по работе с терминалом для проекта Sfera

## Важно: Специфичные команды для этого проекта

### PHP команды

В этом проекте используется PHP 8.5, установленный в нестандартной директории.

**ВСЕГДА используйте полный путь к PHP с оператором `&` в PowerShell:**

```powershell
& "C:\OS\modules\PHP-8.5\php.exe"
```

**Примеры правильных команд:**

```powershell
# Проверка версии PHP
& "C:\OS\modules\PHP-8.5\php.exe" -v

# Запуск artisan команд
& "C:\OS\modules\PHP-8.5\php.exe" artisan migrate

# Запуск тестов
& "C:\OS\modules\PHP-8.5\php.exe" artisan test

# Очистка кэша
& "C:\OS\modules\PHP-8.5\php.exe" artisan cache:clear
& "C:\OS\modules\PHP-8.5\php.exe" artisan view:clear

# Создание контроллера
& "C:\OS\modules\PHP-8.5\php.exe" artisan make:controller ProductController

# Запуск сервера разработки
& "C:\OS\modules\PHP-8.5\php.exe" artisan serve
```

**НЕПРАВИЛЬНО (не используйте):**

```powershell
# ❌ НЕ РАБОТАЕТ - без оператора &
"C:\OS\modules\PHP-8.5\php.exe" artisan migrate

# ❌ НЕ РАБОТАЕТ - короткая команда
php artisan migrate
php artisan test
```

### Composer команды

Для Composer также используйте полный путь к PHP с оператором `&`:

```powershell
# Установка зависимостей
& "C:\OS\modules\PHP-8.5\php.exe" composer.phar install

# Обновление зависимостей
& "C:\OS\modules\PHP-8.5\php.exe" composer.phar update

# Добавление пакета
& "C:\OS\modules\PHP-8.5\php.exe" composer.phar require vendor/package
```

### NPM команды

NPM команды работают стандартно:

```bash
# Установка зависимостей
npm install

# Сборка фронтенда
npm run build

# Режим разработки
npm run dev
```

### GitHub CLI команды

GitHub CLI установлен в нестандартной директории. **ВСЕГДА используйте полный путь с оператором `&`:**

```powershell
& "C:\Program Files\GitHub CLI\gh.exe"
```

**Примеры правильных команд:**

```powershell
# Создание Pull Request
& "C:\Program Files\GitHub CLI\gh.exe" pr create --base dev --title "Feature: Title" --body "Description"

# Просмотр списка PR
& "C:\Program Files\GitHub CLI\gh.exe" pr list

# Просмотр статуса PR
& "C:\Program Files\GitHub CLI\gh.exe" pr status

# Merge PR
& "C:\Program Files\GitHub CLI\gh.exe" pr merge 123

# Просмотр issues
& "C:\Program Files\GitHub CLI\gh.exe" issue list

# Создание issue
& "C:\Program Files\GitHub CLI\gh.exe" issue create --title "Bug: Title" --body "Description"
```

**НЕПРАВИЛЬНО (не используйте):**

```powershell
# ❌ НЕ РАБОТАЕТ - короткая команда
gh pr create

# ❌ НЕ РАБОТАЕТ - без оператора &
"C:\Program Files\GitHub CLI\gh.exe" pr create
```

## Общие правила

1. **Всегда используйте оператор `&`** перед путем к PHP и GitHub CLI в PowerShell
2. **Всегда проверяйте путь к PHP** перед выполнением команд
3. **Используйте кавычки** вокруг пути к PHP и GitHub CLI (из-за пробелов в пути)
4. **Не используйте короткую команду `php` или `gh`** - они не будут работать
5. **Для artisan команд** всегда начинайте с `& "C:\OS\modules\PHP-8.5\php.exe"`
6. **Для GitHub CLI команд** всегда начинайте с `& "C:\Program Files\GitHub CLI\gh.exe"`

## Частые команды для этого проекта

### Миграции

```powershell
# Запуск миграций
& "C:\OS\modules\PHP-8.5\php.exe" artisan migrate

# Откат миграций
& "C:\OS\modules\PHP-8.5\php.exe" artisan migrate:rollback

# Создание миграции
& "C:\OS\modules\PHP-8.5\php.exe" artisan make:migration create_table_name
```

### Тестирование

```powershell
# Запуск всех тестов
& "C:\OS\modules\PHP-8.5\php.exe" artisan test

# Запуск конкретного теста
& "C:\OS\modules\PHP-8.5\php.exe" artisan test --filter TestName

# Запуск тестов с покрытием
& "C:\OS\modules\PHP-8.5\php.exe" artisan test --coverage
```

### Создание файлов

```powershell
# Создать контроллер
& "C:\OS\modules\PHP-8.5\php.exe" artisan make:controller ControllerName

# Создать модель
& "C:\OS\modules\PHP-8.5\php.exe" artisan make:model ModelName

# Создать сервис (вручную, artisan не поддерживает)
# Создайте файл app/Services/ServiceName.php вручную

# Создать middleware
& "C:\OS\modules\PHP-8.5\php.exe" artisan make:middleware MiddlewareName
```

### Очистка кэша

```powershell
# Очистить кэш приложения
& "C:\OS\modules\PHP-8.5\php.exe" artisan cache:clear

# Очистить кэш конфигурации
& "C:\OS\modules\PHP-8.5\php.exe" artisan config:clear

# Очистить кэш маршрутов
& "C:\OS\modules\PHP-8.5\php.exe" artisan route:clear

# Очистить кэш представлений
& "C:\OS\modules\PHP-8.5\php.exe" artisan view:clear
```

## Запуск сервера разработки

```powershell
# Запуск Laravel сервера
& "C:\OS\modules\PHP-8.5\php.exe" artisan serve

# Запуск на конкретном порту
& "C:\OS\modules\PHP-8.5\php.exe" artisan serve --port=8080
```

## Проверка версии PHP

```powershell
# Проверить версию PHP
& "C:\OS\modules\PHP-8.5\php.exe" -v

# Должно вывести: PHP 8.5.1 (cli) (built: Jan 15 2026 11:47:00)
```

## Troubleshooting

Если команда не работает:

1. Проверьте, что используете оператор `&` перед путем к PHP или GitHub CLI
2. Проверьте, что используете полный путь к исполняемому файлу
3. Проверьте, что путь заключен в кавычки
4. Проверьте, что файл существует:
   - PHP: `C:\OS\modules\PHP-8.5\php.exe`
   - GitHub CLI: `C:\Program Files\GitHub CLI\gh.exe`
5. Убедитесь, что вы находитесь в корневой директории проекта (c:\OS\home\sfera\)

## Частые ошибки

### Ошибка: "gh : Имя "gh" не распознано"

**Причина**: Используется короткая команда `gh` вместо полного пути.

**Решение**: Используйте `& "C:\Program Files\GitHub CLI\gh.exe"` вместо `gh`

### Ошибка: "php : Имя "php" не распознано"

**Причина**: Используется короткая команда `php` вместо полного пути.

**Решение**: Используйте `& "C:\OS\modules\PHP-8.5\php.exe"` вместо `php`
