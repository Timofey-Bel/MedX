# 🚀 Быстрый старт MedX (PHP 8.5)

## Минимальные шаги для запуска

### 1️⃣ Проверка PHP
```bash
php -v
```
Должно быть: **PHP 8.5.x**

❌ Если версия ниже → см. [PHP_8.5_UPGRADE.md](PHP_8.5_UPGRADE.md)

### 2️⃣ Установка зависимостей
```bash
composer install
```

### 3️⃣ Настройка окружения
```bash
cp .env.example .env
php artisan key:generate
```

### 4️⃣ Настройка OSPanel

**Вариант A: Через интерфейс**
1. OSPanel → Настройки → Домены
2. Добавить домен: `medx`
3. Путь: `C:\путь\к\проекту\public`
4. Перезапустить OSPanel

**Вариант B: Встроенный сервер**
```bash
php artisan serve
```
Откройте: `http://localhost:8000`

### 5️⃣ Готово! 🎉

Откройте в браузере:
- `http://medx` → Главная
- `http://medx/login` → Вход
- `http://medx/main_showcase` → База знаний

---

## 🆘 Проблемы?

### PHP версия не 8.5
```bash
# В OSPanel:
# Настройки → Модули → PHP 8.5 → Применить
```

### Ошибка Composer
```bash
composer self-update
composer update
```

### Ошибка прав доступа
```bash
chmod -R 775 storage bootstrap/cache
```

### CSS/JS не загружаются
Проверьте, что папки скопированы:
- `public/assets/`
- `public/js/`
- `public/site/`

---

## 📚 Полная документация

- [README_PHP85.md](README_PHP85.md) - Подробная инструкция
- [OSPANEL_SETUP.md](OSPANEL_SETUP.md) - Настройка OSPanel
- [PHP_8.5_UPGRADE.md](PHP_8.5_UPGRADE.md) - Обновление PHP

---

**Время установки:** ~5 минут  
**Сложность:** ⭐⭐☆☆☆
