---
inclusion: auto
---

# Использование GitHub CLI в Windows

## Критически важно: Полный путь обязателен

В этом проекте GitHub CLI установлен в стандартную директорию Windows, которая содержит пробелы в пути. **Всегда используйте полный путь с оператором вызова PowerShell `&`**.

```powershell
# ✅ ПРАВИЛЬНО
& "C:\Program Files\GitHub CLI\gh.exe" pr create --title "My PR"

# ❌ НЕПРАВИЛЬНО - не будет работать
gh pr create --title "My PR"
```

## Расположение установки

GitHub CLI установлен по адресу:
```
C:\Program Files\GitHub CLI\gh.exe
```

## Почему нужен оператор вызова `&`

PowerShell требует специальный синтаксис для выполнения команд с пробелами в пути:

1. **Пробелы в пути**: `Program Files` содержит пробел
2. **Парсинг PowerShell**: Без `&` PowerShell интерпретирует это как строку, а не команду
3. **Кавычки**: Путь должен быть в двойных кавычках
4. **Оператор `&`**: Указывает PowerShell выполнить команду

### Пример проблемы

```powershell
# Это не работает:
"C:\Program Files\GitHub CLI\gh.exe" --version
# PowerShell просто выведет строку, не выполнит команду

# Это работает:
& "C:\Program Files\GitHub CLI\gh.exe" --version
# PowerShell выполнит команду
```

## Первоначальная настройка

### Аутентификация

Перед первым использованием необходимо авторизоваться:

```powershell
& "C:\Program Files\GitHub CLI\gh.exe" auth login
```

Следуйте интерактивным инструкциям:
1. Выберите `GitHub.com`
2. Выберите протокол: `HTTPS` или `SSH`
3. Выберите метод аутентификации: `Login with a web browser` (рекомендуется)
4. Скопируйте код и откройте браузер
5. Введите код на GitHub

### Проверка аутентификации

```powershell
& "C:\Program Files\GitHub CLI\gh.exe" auth status
```

## Основные команды

### Проверка версии

```powershell
& "C:\Program Files\GitHub CLI\gh.exe" --version
```

### Информация о репозитории

```powershell
# Просмотр информации о текущем репозитории
& "C:\Program Files\GitHub CLI\gh.exe" repo view

# Открыть репозиторий в браузере
& "C:\Program Files\GitHub CLI\gh.exe" repo view --web
```

## Работа с Pull Requests

### Создание PR

```powershell
# Базовая команда
& "C:\Program Files\GitHub CLI\gh.exe" pr create --base dev --title "feat: Новая функция" --body "Описание изменений"

# С указанием файла для описания
& "C:\Program Files\GitHub CLI\gh.exe" pr create --base dev --title "feat: Новая функция" --body-file pr-description.md

# Интерактивное создание (с промптами)
& "C:\Program Files\GitHub CLI\gh.exe" pr create --base dev

# С метками и назначением
& "C:\Program Files\GitHub CLI\gh.exe" pr create --base dev --title "fix: Исправление бага" --body "Описание" --label bug --assignee @me
```

### Просмотр PR

```powershell
# Список всех PR
& "C:\Program Files\GitHub CLI\gh.exe" pr list

# Список PR с фильтрами
& "C:\Program Files\GitHub CLI\gh.exe" pr list --state open
& "C:\Program Files\GitHub CLI\gh.exe" pr list --author "@me"
& "C:\Program Files\GitHub CLI\gh.exe" pr list --label bug

# Просмотр конкретного PR
& "C:\Program Files\GitHub CLI\gh.exe" pr view 123

# Просмотр PR в браузере
& "C:\Program Files\GitHub CLI\gh.exe" pr view 123 --web

# Статус PR для текущей ветки
& "C:\Program Files\GitHub CLI\gh.exe" pr status
```

### Управление PR

```powershell
# Checkout PR локально
& "C:\Program Files\GitHub CLI\gh.exe" pr checkout 123

# Слияние PR
& "C:\Program Files\GitHub CLI\gh.exe" pr merge 123

# Закрытие PR
& "C:\Program Files\GitHub CLI\gh.exe" pr close 123

# Повторное открытие PR
& "C:\Program Files\GitHub CLI\gh.exe" pr reopen 123

# Добавление комментария
& "C:\Program Files\GitHub CLI\gh.exe" pr comment 123 --body "Отличная работа!"

# Проверка CI статуса
& "C:\Program Files\GitHub CLI\gh.exe" pr checks
```

## Работа с Issues

```powershell
# Список issues
& "C:\Program Files\GitHub CLI\gh.exe" issue list

# Создание issue
& "C:\Program Files\GitHub CLI\gh.exe" issue create --title "Bug: Описание" --body "Детали"

# Просмотр issue
& "C:\Program Files\GitHub CLI\gh.exe" issue view 456

# Закрытие issue
& "C:\Program Files\GitHub CLI\gh.exe" issue close 456
```

## Работа с ветками

```powershell
# Просмотр текущей ветки
git branch --show-current

# Создание ветки и PR одной командой
git checkout -b feature/new-feature
git add .
git commit -m "feat: Новая функция"
git push -u origin feature/new-feature
& "C:\Program Files\GitHub CLI\gh.exe" pr create --base dev --title "feat: Новая функция" --body "Описание"
```

## Типичный workflow

### 1. Создание feature ветки и PR

```powershell
# Создать ветку от dev
git checkout dev
git pull origin dev
git checkout -b feature/my-feature

# Внести изменения
# ... редактирование файлов ...

# Коммит и push
git add .
git commit -m "feat: Описание изменений"
git push -u origin feature/my-feature

# Создать PR
& "C:\Program Files\GitHub CLI\gh.exe" pr create --base dev --title "feat: Описание изменений" --body "Детальное описание"
```

### 2. Проверка статуса PR

```powershell
# Проверить статус текущей ветки
& "C:\Program Files\GitHub CLI\gh.exe" pr status

# Проверить CI/CD статус
& "C:\Program Files\GitHub CLI\gh.exe" pr checks

# Просмотреть PR в браузере
& "C:\Program Files\GitHub CLI\gh.exe" pr view --web
```

### 3. Обновление PR после ревью

```powershell
# Внести изменения
# ... редактирование файлов ...

# Коммит и push
git add .
git commit -m "fix: Исправления по ревью"
git push

# PR автоматически обновится
```

## Полезные команды для ежедневной работы

### Быстрый просмотр активности

```powershell
# Мои открытые PR
& "C:\Program Files\GitHub CLI\gh.exe" pr list --author "@me" --state open

# PR, требующие моего ревью
& "C:\Program Files\GitHub CLI\gh.exe" pr list --search "review-requested:@me"

# Недавно обновленные PR
& "C:\Program Files\GitHub CLI\gh.exe" pr list --limit 10
```

### Работа с черновиками

```powershell
# Создать PR как черновик
& "C:\Program Files\GitHub CLI\gh.exe" pr create --base dev --draft --title "WIP: Работа в процессе"

# Пометить PR как готовый к ревью
& "C:\Program Files\GitHub CLI\gh.exe" pr ready 123
```

### Просмотр изменений

```powershell
# Diff PR
& "C:\Program Files\GitHub CLI\gh.exe" pr diff 123

# Просмотр файлов в PR
& "C:\Program Files\GitHub CLI\gh.exe" pr view 123 --json files --jq '.files[].path'
```

## Использование в скриптах

Для автоматизации можно создать PowerShell функцию:

```powershell
# Добавить в профиль PowerShell ($PROFILE)
function gh {
    & "C:\Program Files\GitHub CLI\gh.exe" $args
}

# Теперь можно использовать короткую команду:
gh pr list
gh pr create --base dev --title "My PR"
```

### Создание алиаса (не рекомендуется для AI агентов)

```powershell
# В профиле PowerShell
Set-Alias -Name gh -Value "C:\Program Files\GitHub CLI\gh.exe"
```

**Примечание**: Алиасы не работают с аргументами в PowerShell, поэтому функция предпочтительнее.

## Troubleshooting

### Проблема: "gh не является командой"

**Причина**: Путь не добавлен в PATH или используется неправильный синтаксис.

**Решение**: Используйте полный путь с оператором `&`:
```powershell
& "C:\Program Files\GitHub CLI\gh.exe" --version
```

### Проблема: "authentication required"

**Причина**: Не выполнена аутентификация.

**Решение**: 
```powershell
& "C:\Program Files\GitHub CLI\gh.exe" auth login
```

### Проблема: "could not create pull request"

**Причины**:
1. Нет изменений в ветке
2. PR уже существует
3. Нет прав на создание PR

**Решение**:
```powershell
# Проверить статус
& "C:\Program Files\GitHub CLI\gh.exe" pr status

# Проверить существующие PR
& "C:\Program Files\GitHub CLI\gh.exe" pr list --head $(git branch --show-current)
```

### Проблема: "not in a git repository"

**Причина**: Команда выполняется не в директории git репозитория.

**Решение**: Перейдите в корень проекта:
```powershell
cd C:\path\to\your\project
```

## Для AI агентов

При использовании GitHub CLI в автоматизированных задачах:

1. **Всегда используйте полный путь**: `& "C:\Program Files\GitHub CLI\gh.exe"`
2. **Проверяйте аутентификацию**: Перед созданием PR убедитесь, что пользователь авторизован
3. **Используйте флаг `--json`**: Для парсинга вывода в скриптах
4. **Обрабатывайте ошибки**: Проверяйте код возврата команды

### Пример для AI агента

```powershell
# Проверка аутентификации
$authStatus = & "C:\Program Files\GitHub CLI\gh.exe" auth status 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Error "GitHub CLI не авторизован. Выполните: gh auth login"
    exit 1
}

# Создание PR с обработкой ошибок
$prResult = & "C:\Program Files\GitHub CLI\gh.exe" pr create --base dev --title "feat: New feature" --body "Description" 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "PR успешно создан: $prResult"
} else {
    Write-Error "Ошибка создания PR: $prResult"
    exit 1
}
```

## Полезные ссылки

- [GitHub CLI Documentation](https://cli.github.com/manual/)
- [GitHub CLI Repository](https://github.com/cli/cli)
- [PowerShell Call Operator Documentation](https://docs.microsoft.com/en-us/powershell/module/microsoft.powershell.core/about/about_operators#call-operator-)

## История изменений

- 2026-02-28: Создан документ с инструкциями по использованию GitHub CLI в Windows
- Основная проблема: Необходимость использования полного пути с оператором `&`
- Целевая аудитория: Разработчики и AI агенты, работающие с проектом
