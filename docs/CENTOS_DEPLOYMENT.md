# Развертывание проекта MedX на CentOS 9

> Конфигурация: Apache + MariaDB + PHP 8.4

## Этап 1: Обновление системы

```bash
# Обновить все пакеты
sudo dnf update -y

# Установить базовые утилиты
sudo dnf install -y wget curl git unzip nano vim
```

## Этап 2: Установка PHP 8.4

```bash
# Добавить репозиторий Remi
sudo dnf install -y https://rpms.remirepo.net/enterprise/remi-release-9.rpm

# Включить модуль PHP 8.4
sudo dnf module reset php -y
sudo dnf module enable php:remi-8.4 -y

# Установить PHP и необходимые расширения
sudo dnf install -y php php-cli php-fpm php-mysqlnd php-pdo php-mbstring \
    php-xml php-bcmath php-json php-zip php-gd php-curl php-intl \
    php-opcache php-tokenizer php-fileinfo php-sqlite3

# Проверить версию PHP (должна быть 8.4.x)
php -v
```

## Этап 3: Установка Composer

```bash
# Скачать установщик Composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

# Установить Composer глобально
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Удалить установщик
php -r "unlink('composer-setup.php');"

# Проверить установку
composer --version
```

## Этап 4: Установка Apache

```bash
# Установить Apache
sudo dnf install -y httpd

# Запустить и добавить в автозагрузку
sudo systemctl start httpd
sudo systemctl enable httpd

# Проверить статус
sudo systemctl status httpd
```

## Этап 5: Установка MariaDB

```bash
# Установить MariaDB
sudo dnf install -y mariadb-server

# Запустить и добавить в автозагрузку
sudo systemctl start mariadb
sudo systemctl enable mariadb

# Безопасная настройка MariaDB
sudo mysql_secure_installation

# Войти в MariaDB
sudo mysql -u root -p

# Создать базу данных и пользователя
CREATE DATABASE medx CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'medx_user'@'localhost' IDENTIFIED BY 'ваш_пароль';
GRANT ALL PRIVILEGES ON medx.* TO 'medx_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## Этап 6: Настройка firewall

```bash
# Установить firewalld (если не установлен)
sudo dnf install -y firewalld

# Запустить и добавить в автозагрузку
sudo systemctl start firewalld
sudo systemctl enable firewalld

# Проверить статус
sudo systemctl status firewalld

# Открыть порты HTTP и HTTPS
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --reload

# Проверить открытые порты
sudo firewall-cmd --list-all
```

## Этап 7: Настройка SELinux

```bash
# Временно отключить SELinux (для тестирования)
sudo setenforce 0

# Или настроить правильно для production
sudo setsebool -P httpd_can_network_connect 1
sudo setsebool -P httpd_can_network_connect_db 1
sudo setsebool -P httpd_unified 1

# Для постоянного отключения (не рекомендуется для production)
# sudo nano /etc/selinux/config
# Изменить SELINUX=enforcing на SELINUX=disabled
```

## Этап 8: Клонирование проекта

```bash
# Создать директорию для проекта
sudo mkdir -p /var/www/medx
cd /var/www/medx

# Клонировать репозиторий (если используете Git)
sudo git clone https://github.com/ваш-репозиторий/medx.git .

# Или загрузить файлы через SCP/FTP
# scp -r /путь/к/проекту/* user@server:/var/www/medx/

# Установить права владельца
sudo chown -R $USER:$USER /var/www/medx
```

## Этап 9: Установка зависимостей

```bash
cd /var/www/MedX

# Если composer.json требует PHP ^8.5, измените на ^8.4
nano composer.json
# Найдите "php": "^8.5" и измените на "php": "^8.4"

# Обновить lock файл
composer update --lock

# Установить зависимости Composer
composer install --optimize-autoloader --no-dev

# Если возникают проблемы с памятью
# COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader --no-dev
```

## Этап 10: Настройка окружения

```bash
# Скопировать файл окружения
cp .env.example .env

# Отредактировать .env
nano .env
```

Настройте следующие параметры в `.env`:

```env
APP_NAME=MedX
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://212.193.62.143

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=medx
DB_USERNAME=medx_user
DB_PASSWORD=ваш_пароль

# Для SQLite используйте:
# DB_CONNECTION=sqlite
# DB_DATABASE=/var/www/MedX/database/database.sqlite

SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

```bash
# Сгенерировать ключ приложения
php artisan key:generate

# Создать символическую ссылку для storage
php artisan storage:link
```

## Этап 11: Миграция базы данных

```bash
# Выполнить миграции
php artisan migrate --force

# Если нужны сиды (тестовые данные)
# php artisan db:seed
```

## Этап 12: Настройка прав доступа

```bash
# Установить правильные права (для Apache)
sudo chown -R apache:apache /var/www/MedX

# Установить права на директории
sudo find /var/www/MedX -type d -exec chmod 755 {} \;
sudo find /var/www/MedX -type f -exec chmod 644 {} \;

# Дать права на запись для storage и bootstrap/cache
sudo chmod -R 775 /var/www/MedX/storage
sudo chmod -R 775 /var/www/MedX/bootstrap/cache
sudo chmod -R 775 /var/www/MedX/public/avatars

# Для SELinux
sudo chcon -R -t httpd_sys_rw_content_t /var/www/MedX/storage
sudo chcon -R -t httpd_sys_rw_content_t /var/www/MedX/bootstrap/cache
sudo chcon -R -t httpd_sys_rw_content_t /var/www/MedX/public/avatars
```

## Этап 13: Настройка Apache

```bash
# Создать конфигурацию сайта
sudo nano /etc/httpd/conf.d/medx.conf
```

Добавьте следующую конфигурацию:

```apache
<VirtualHost *:80>
    ServerName 212.193.62.143
    DocumentRoot /var/www/MedX/public

    <Directory /var/www/MedX/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog /var/log/httpd/medx-error.log
    CustomLog /var/log/httpd/medx-access.log combined
</VirtualHost>
```

```bash
# Проверить конфигурацию Apache
sudo apachectl configtest

# Перезапустить Apache
sudo systemctl restart httpd
```

## Этап 14: Оптимизация для production

```bash
cd /var/www/MedX

# Кэшировать конфигурацию
php artisan config:cache

# Кэшировать маршруты
php artisan route:cache

# Кэшировать представления
php artisan view:cache

# Оптимизировать автозагрузку
composer dump-autoload --optimize
```

## Этап 15: Настройка SSL (опционально)

> **Примечание:** SSL для IP-адреса невозможен. Если у вас будет доменное имя, выполните:

```bash
# Установить Certbot для Apache
sudo dnf install -y certbot python3-certbot-apache

# Получить SSL-сертификат
sudo certbot --apache -d ваш-домен.ru -d www.ваш-домен.ru

# Автоматическое обновление сертификата
sudo systemctl enable --now certbot-renew.timer
```

Пока используется IP-адрес (212.193.62.143), SSL недоступен.

## Этап 16: Настройка планировщика задач (Cron)

```bash
# Открыть crontab
sudo crontab -e

# Добавить строку для Laravel Scheduler
* * * * * cd /var/www/MedX && php artisan schedule:run >> /dev/null 2>&1
```

## Этап 17: Настройка очередей (опционально)

```bash
# Создать systemd service для queue worker
sudo nano /etc/systemd/system/medx-queue.service
```

Добавьте:

```ini
[Unit]
Description=MedX Queue Worker
After=network.target

[Service]
Type=simple
User=apache
WorkingDirectory=/var/www/MedX
ExecStart=/usr/bin/php /var/www/MedX/artisan queue:work --sleep=3 --tries=3
Restart=always

[Install]
WantedBy=multi-user.target
```

```bash
# Запустить и включить service
sudo systemctl daemon-reload
sudo systemctl start medx-queue
sudo systemctl enable medx-queue
```

## Этап 18: Проверка работоспособности

```bash
# Проверить логи Apache
sudo tail -f /var/log/httpd/error_log
sudo tail -f /var/log/httpd/medx-error.log

# Проверить логи Laravel
tail -f /var/www/MedX/storage/logs/laravel.log

# Проверить статус служб
sudo systemctl status httpd
sudo systemctl status mariadb
```

**Откройте в браузере:** http://212.193.62.143

## Этап 19: Мониторинг и обслуживание

```bash
# Очистка кэша (при необходимости)
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Просмотр логов
php artisan log:clear

# Резервное копирование БД
mysqldump -u medx_user -p medx > backup_$(date +%Y%m%d).sql

# Автоматическое резервное копирование (добавить в cron)
# 0 2 * * * mysqldump -u medx_user -pпароль medx > /backup/medx_$(date +\%Y\%m\%d).sql
```

## Возможные проблемы и решения

### Ошибка 500

```bash
# Проверить права доступа
ls -la /var/www/MedX/storage
ls -la /var/www/MedX/bootstrap/cache

# Проверить логи
tail -f /var/www/MedX/storage/logs/laravel.log
sudo tail -f /var/log/httpd/medx-error.log
```

### Ошибка подключения к БД

```bash
# Проверить статус MariaDB
sudo systemctl status mariadb

# Проверить подключение
mysql -u medx_user -p -h 127.0.0.1 medx

# Проверить настройки в .env
cat /var/www/MedX/.env | grep DB_
```

### Проблемы с Apache

```bash
# Проверить статус
sudo systemctl status httpd

# Проверить логи
sudo tail -f /var/log/httpd/error_log

# Перезапустить
sudo systemctl restart httpd
```

## Полезные команды

```bash
# Проверить версии
php -v
composer --version
httpd -v
mysql --version

# Проверить открытые порты
sudo ss -tulpn | grep LISTEN

# Проверить использование ресурсов
top
df -h
free -h

# Обновление проекта
cd /var/www/MedX
git pull origin main
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo systemctl restart httpd
```

## Безопасность

```bash
# Отключить листинг директорий (Nginx)
# Уже настроено в конфигурации выше

# Скрыть версию PHP
sudo nano /etc/php.ini
# Найти и изменить: expose_php = Off

# Настроить fail2ban (опционально)
sudo dnf install -y fail2ban
sudo systemctl enable --now fail2ban

# Регулярно обновлять систему
sudo dnf update -y
```

## Готово!

Ваш проект должен быть доступен по адресу: **http://212.193.62.143**

Для проверки откройте браузер и перейдите на http://212.193.62.143

> **Рекомендация:** Для production лучше использовать доменное имя вместо IP-адреса. Это позволит настроить SSL-сертификат и улучшит SEO.
