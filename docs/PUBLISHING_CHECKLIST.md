# Чеклист публикации в GitHub

## ✅ Перед первым push

### 1. Проверьте .gitignore
- [x] `.env` исключен
- [x] `database/database.sqlite` исключен
- [x] `composer.phar` исключен
- [x] `/vendor` исключен
- [x] `/.idea` исключен

### 2. Проверьте конфиденциальные данные
- [x] Email пароли заменены на placeholders (`public/site/functions.php`)
- [x] API токены заменены на placeholders (`public/site/libs/dadata.php`)
- [x] `.env` файл не добавлен в git

### 3. Проверьте файлы
```bash
# Убедитесь, что .env не отслеживается
git status

# Проверьте, что нет секретов в staged файлах
git diff --cached
```

### 4. Создайте репозиторий на GitHub

1. Перейдите на https://github.com/new
2. Создайте новый репозиторий (например, `medx-platform`)
3. НЕ инициализируйте с README (у вас уже есть)

### 5. Подключите удаленный репозиторий

```bash
# Инициализируйте git (если еще не сделано)
git init

# Добавьте все файлы
git add .

# Создайте первый commit
git commit -m "Initial commit: MedX Educational Platform v2.0.0"

# Добавьте удаленный репозиторий
git remote add origin https://github.com/YOUR_USERNAME/medx-platform.git

# Отправьте код
git branch -M main
git push -u origin main
```

### 6. Настройте GitHub репозиторий

После публикации:

1. **Добавьте описание репозитория:**
   - "Образовательная платформа для студентов медицинских вузов | Medical Education Platform"

2. **Добавьте темы (topics):**
   - `laravel`
   - `php`
   - `medical-education`
   - `educational-platform`
   - `healthcare`
   - `medical-students`

3. **Настройте About:**
   - Website: (если есть)
   - Topics: добавьте релевантные теги

4. **Создайте Release:**
   - Tag: `v2.0.0`
   - Title: "MedX Platform v2.0.0 - Initial Release"
   - Description: Скопируйте из README.md раздел "Что включено"

## 🔒 Безопасность

### Проверьте, что НЕ попало в репозиторий:

```bash
# Проверьте историю на наличие .env
git log --all --full-history -- .env

# Если .env был добавлен по ошибке, удалите из истории:
git filter-branch --force --index-filter \
  "git rm --cached --ignore-unmatch .env" \
  --prune-empty --tag-name-filter cat -- --all
```

### Сканирование секретов (опционально)

Установите и запустите git-secrets:

```bash
# Установка (macOS)
brew install git-secrets

# Установка (Windows)
# Скачайте с https://github.com/awslabs/git-secrets

# Сканирование
git secrets --scan
```

## 📝 После публикации

1. Добавьте badges в README.md:
   - Build status
   - License
   - Version

2. Создайте GitHub Issues templates:
   - Bug report
   - Feature request

3. Добавьте CONTRIBUTING.md (если планируете принимать PR)

4. Настройте GitHub Actions для CI/CD (опционально)

## ⚠️ Важные напоминания

- ❌ НИКОГДА не коммитьте `.env` файл
- ❌ НИКОГДА не коммитьте пароли или API ключи
- ✅ ВСЕГДА проверяйте `git status` перед commit
- ✅ ВСЕГДА проверяйте `git diff` перед push
- ✅ Читайте [SECURITY.md](SECURITY.md) перед развертыванием

## 🎉 Готово!

Ваш проект опубликован! Не забудьте:
- Добавить ссылку на репозиторий в портфолио
- Поделиться с сообществом
- Следить за issues и pull requests

---

**Дата создания:** 2026-03-21
