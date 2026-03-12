# Спецификация: Система личных кабинетов для розничных и оптовых покупателей

**Дата создания**: 2026-03-05  
**Статус**: В разработке  
**Приоритет**: Высокий

## Обзор

Реализация двух типов личных кабинетов:
1. **Розничный покупатель** - физическое лицо
2. **Оптовый покупатель** - юридическое лицо (организация)

## Требования

### 1. Расширение таблицы users

Добавить поле для определения типа пользователя:
- `user_type` - тип пользователя ('retail' или 'wholesale')
- `org_id` - связь с организацией (для оптовых покупателей)

### 2. Создание таблицы orgs

Таблица для хранения данных организаций (создается через SQL, без миграций):

```sql
CREATE TABLE IF NOT EXISTS `orgs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `inn` varchar(12) NOT NULL COMMENT 'ИНН организации',
  `kpp` varchar(9) DEFAULT NULL COMMENT 'КПП организации',
  `ogrn` varchar(15) DEFAULT NULL COMMENT 'ОГРН организации',
  `name_full` varchar(500) NOT NULL COMMENT 'Полное наименование',
  `name_short` varchar(255) DEFAULT NULL COMMENT 'Краткое наименование',
  `legal_address` text DEFAULT NULL COMMENT 'Юридический адрес',
  `postal_address` text DEFAULT NULL COMMENT 'Почтовый адрес',
  `director_name` varchar(255) DEFAULT NULL COMMENT 'ФИО руководителя',
  `director_position` varchar(255) DEFAULT NULL COMMENT 'Должность руководителя',
  `phone` varchar(20) DEFAULT NULL COMMENT 'Телефон организации',
  `email` varchar(255) DEFAULT NULL COMMENT 'Email организации',
  `bank_name` varchar(255) DEFAULT NULL COMMENT 'Название банка',
  `bank_bik` varchar(9) DEFAULT NULL COMMENT 'БИК банка',
  `bank_account` varchar(20) DEFAULT NULL COMMENT 'Расчетный счет',
  `bank_corr_account` varchar(20) DEFAULT NULL COMMENT 'Корреспондентский счет',
  `opf` varchar(255) DEFAULT NULL COMMENT 'Организационно-правовая форма',
  `status` varchar(50) DEFAULT 'active' COMMENT 'Статус: active, inactive, liquidated',
  `dadata_json` text DEFAULT NULL COMMENT 'Полный JSON ответ от DaData',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orgs_inn_unique` (`inn`),
  KEY `orgs_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Организации (юридические лица)';
```

### 3. Форма регистрации

#### 3.1. Общие поля (для всех типов)
- Имя
- Email
- Телефон
- Пароль
- Подтверждение пароля

#### 3.2. Переключатель типа регистрации
- Радио-кнопки или табы: "Физическое лицо" / "Юридическое лицо"

#### 3.3. Дополнительные поля для юридических лиц
- **ИНН организации** (обязательное поле)
  - Валидация: 10 или 12 цифр
  - Кнопка "Проверить ИНН" → запрос к DaData API
  - После успешной проверки - автозаполнение полей организации
- **Название организации** (автозаполнение из DaData)
- **КПП** (автозаполнение из DaData)
- **Юридический адрес** (автозаполнение из DaData)
- **ФИО руководителя** (автозаполнение из DaData)
- **Должность в организации** (ручной ввод)

### 4. Интеграция с DaData

#### 4.1. API Endpoint
- URL: `https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party`
- Метод: POST
- Headers:
  - `Content-Type: application/json`
  - `Authorization: Token {DADATA_TOKEN}`

#### 4.2. Запрос
```json
{
  "query": "7707083893"
}
```

#### 4.3. Ответ (основные поля)
```json
{
  "suggestions": [{
    "value": "ПАО СБЕРБАНК",
    "data": {
      "inn": "7707083893",
      "kpp": "773601001",
      "ogrn": "1027700132195",
      "name": {
        "full_with_opf": "ПУБЛИЧНОЕ АКЦИОНЕРНОЕ ОБЩЕСТВО \"СБЕРБАНК РОССИИ\"",
        "short_with_opf": "ПАО СБЕРБАНК"
      },
      "address": {
        "value": "г Москва, ул Вавилова, д 19",
        "data": {
          "postal_code": "117997"
        }
      },
      "management": {
        "name": "Греф Герман Оскарович",
        "post": "ПРЕЗИДЕНТ, ПРЕДСЕДАТЕЛЬ ПРАВЛЕНИЯ"
      },
      "opf": {
        "full": "Публичное акционерное общество",
        "short": "ПАО"
      },
      "state": {
        "status": "ACTIVE"
      }
    }
  }]
}
```

### 5. Логика регистрации

#### 5.1. Для физических лиц (retail)
1. Валидация стандартных полей
2. Создание записи в `users` с `user_type = 'retail'`
3. Отправка на email подтверждения (опционально)
4. Редирект в личный кабинет розничного покупателя

#### 5.2. Для юридических лиц (wholesale)
1. Валидация стандартных полей + ИНН
2. Проверка ИНН через DaData API
3. Создание/обновление записи в `orgs`
4. Создание записи в `users` с `user_type = 'wholesale'` и `org_id`
5. Отправка на email подтверждения (опционально)
6. Редирект в личный кабинет оптового покупателя

### 6. Личные кабинеты

#### 6.1. Розничный покупатель (/profile)
- Личные данные (имя, email, телефон, пароль)
- История заказов
- Избранное
- Адреса доставки
- Бонусная программа (будущее)

#### 6.2. Оптовый покупатель (/profile/wholesale)
- Личные данные пользователя
- **Данные организации** (отдельная секция)
  - Просмотр всех данных организации
  - Возможность обновления контактных данных
  - Скачивание реквизитов (PDF)
- История заказов организации
- Счета и документы
- Специальные цены (будущее)
- Кредитный лимит (будущее)

### 7. Разграничение доступа

#### 7.1. Middleware
Создать middleware для проверки типа пользователя:
- `CheckRetailUser` - доступ только для retail
- `CheckWholesaleUser` - доступ только для wholesale

#### 7.2. Роуты
```php
// Розничный кабинет
Route::middleware(['auth', 'retail'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    // ...
});

// Оптовый кабинет
Route::middleware(['auth', 'wholesale'])->group(function () {
    Route::get('/profile/wholesale', [WholesaleProfileController::class, 'index'])->name('profile.wholesale');
    Route::get('/profile/wholesale/organization', [WholesaleProfileController::class, 'organization'])->name('profile.wholesale.organization');
    // ...
});
```

## Структура файлов

### Backend
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   └── RegisterController.php (расширить)
│   │   ├── ProfileController.php (существующий - для retail)
│   │   └── WholesaleProfileController.php (новый)
│   ├── Middleware/
│   │   ├── CheckRetailUser.php (новый)
│   │   └── CheckWholesaleUser.php (новый)
│   └── Requests/
│       ├── RegisterRetailRequest.php (новый)
│       └── RegisterWholesaleRequest.php (новый)
├── Models/
│   ├── User.php (расширить)
│   └── Organization.php (новый)
└── Services/
    └── DaDataService.php (новый)
```

### Frontend
```
resources/
├── views/
│   ├── auth/
│   │   └── register.blade.php (расширить)
│   ├── profile/
│   │   └── index.blade.php (существующий - для retail)
│   └── wholesale/
│       ├── index.blade.php (новый)
│       └── organization.blade.php (новый)
└── js/
    └── auth-register-wholesale.js (новый)

public/
└── assets/
    └── sfera/
        ├── js/
        │   └── dadata-inn-check.js (новый)
        └── css/
            └── wholesale-profile.css (новый)
```

## Этапы реализации

### Этап 1: Подготовка базы данных
- [ ] Создать таблицу `orgs` через SQL
- [ ] Добавить поля `user_type` и `org_id` в таблицу `users` через SQL
- [ ] Создать модель `Organization`
- [ ] Обновить модель `User` (добавить связь с Organization)

### Этап 2: Интеграция DaData
- [ ] Создать `DaDataService` для работы с API
- [ ] Добавить токен DaData в `.env`
- [ ] Создать API endpoint для проверки ИНН
- [ ] Создать JS-скрипт для автозаполнения формы

### Этап 3: Расширение регистрации
- [ ] Обновить форму регистрации (добавить переключатель типа)
- [ ] Добавить поля для юридических лиц
- [ ] Создать Request классы для валидации
- [ ] Обновить `RegisterController`

### Этап 4: Личные кабинеты ✅
- [x] Создать middleware для разграничения доступа
- [x] Создать `WholesaleProfileController`
- [x] Создать views для оптового кабинета
- [x] Обновить роуты
- [x] Адаптировать вёрстку из `legacy/room/`
- [x] Интегрировать данные организации из БД

### Этап 5: Тестирование (ТЕКУЩИЙ ЭТАП)
- [ ] Тестирование регистрации физ. лица
- [ ] Тестирование регистрации юр. лица
- [ ] Тестирование интеграции с DaData
- [ ] Тестирование доступа к кабинетам
- [ ] Тестирование отображения данных организации

## Конфигурация

### .env
```env
# DaData API
DADATA_TOKEN=your_dadata_token_here
DADATA_SECRET=your_dadata_secret_here
```

### config/services.php
```php
'dadata' => [
    'token' => env('DADATA_TOKEN'),
    'secret' => env('DADATA_SECRET'),
    'url' => 'https://suggestions.dadata.ru/suggestions/api/4_1/rs',
],
```

## Безопасность

1. **Валидация ИНН**: Проверка формата и контрольной суммы
2. **Rate limiting**: Ограничение запросов к DaData API
3. **Кеширование**: Сохранение результатов проверки ИНН
4. **Middleware**: Строгое разграничение доступа к кабинетам
5. **CSRF защита**: Для всех форм

## Будущие улучшения

1. Множественные пользователи от одной организации
2. Роли внутри организации (администратор, менеджер, бухгалтер)
3. Специальные цены для оптовых покупателей
4. Кредитный лимит и отсрочка платежа
5. Автоматическое формирование документов (счета, накладные)
6. История изменений данных организации
7. Верификация организации (модерация)

## Примечания

- Все изменения в базе данных выполняются через SQL (без миграций)
- Токен DaData уже есть в проекте
- Сохранять полный JSON ответ от DaData для возможного использования в будущем
- Предусмотреть возможность ручного ввода данных организации (если DaData не нашел)
