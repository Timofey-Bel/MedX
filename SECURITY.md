# Безопасность / Security

## 🔒 Важная информация перед развертыванием

### Конфиденциальные данные

Этот проект содержит файлы-примеры с placeholder-значениями. Перед развертыванием в production необходимо заменить следующие данные:

### 1. Файл `.env`

**ВАЖНО:** Файл `.env` НЕ включен в репозиторий (находится в `.gitignore`).

После клонирования проекта:

```bash
# Скопируйте файл-пример
cp .env.example .env

# Сгенерируйте новый ключ приложения
php artisan key:generate
```

### 2. Email настройки

Файл: `public/site/functions.php` (строки 786-794)

Замените placeholder-значения на реальные:

```php
$mail->Username = 'your-email@example.com'; // Ваш email
$mail->Password = 'your-password-here';     // Ваш пароль
$mail->setFrom('your-email@example.com', 'Your Name');
```

**Рекомендация:** Используйте переменные окружения вместо хардкода:

```php
$mail->Username = getenv('MAIL_USERNAME');
$mail->Password = getenv('MAIL_PASSWORD');
```

### 3. DaData API Token

Файл: `public/site/libs/dadata.php` (строка 241)

Замените placeholder на ваш токен:

```php
$token = "YOUR_DADATA_API_TOKEN_HERE"; // Получите токен на https://dadata.ru
```

### 4. База данных

Файл `database/database.sqlite` исключен из репозитория.

После клонирования создайте новую базу:

```bash
touch database/database.sqlite
php artisan migrate
```

## ✅ Чеклист перед публикацией

- [ ] `.env` файл в `.gitignore`
- [ ] `database/database.sqlite` в `.gitignore`
- [ ] Все пароли заменены на placeholders
- [ ] Все API ключи заменены на placeholders
- [ ] `composer.phar` в `.gitignore`
- [ ] Проверены логи на наличие чувствительных данных

## 🚨 Что НЕ должно попасть в репозиторий

- ❌ `.env` файл с реальными данными
- ❌ Пароли от email, баз данных, API
- ❌ API токены и ключи
- ❌ Файлы базы данных (`.sqlite`, `.sql` дампы с реальными данными)
- ❌ Приватные ключи SSH/SSL
- ❌ Файлы с персональными данными пользователей

## 📝 Рекомендации

1. **Используйте переменные окружения** для всех конфиденциальных данных
2. **Регулярно обновляйте зависимости**: `composer update`
3. **Включите HTTPS** в production
4. **Отключите debug режим** в production: `APP_DEBUG=false`
5. **Используйте сильные пароли** для всех сервисов

## 🔍 Проверка перед commit

Перед каждым commit проверяйте:

```bash
# Проверьте, что .env не добавлен
git status

# Проверьте содержимое файлов на наличие секретов
git diff
```

## 📞 Сообщить о проблеме безопасности

Если вы обнаружили уязвимость безопасности, пожалуйста, НЕ создавайте публичный issue. Свяжитесь с разработчиками напрямую.

---

**Последнее обновление:** 2026-03-21
