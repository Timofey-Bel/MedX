# Требования: Миграция интернет-магазина "Сфера" с Legacy PHP на Laravel 12

## Введение

Данный документ описывает требования к миграции интернет-магазина издательства "Сфера" с самописного PHP-движка (Smarty 2.6.11) на Laravel 12 с сохранением существующей базы данных и SQL-запросов.

## Приоритеты требований

Требования разделены на три уровня приоритета:

### 🔴 КРИТИЧНЫЕ (Блокируют работу интернет-магазина)

- **Requirement 36-39**: Обмен данными с 1С - без этого нет синхронизации товаров, цен, остатков и заказов
- **Requirement 31-35**: Административная панель - без этого невозможно управлять сайтом
- **Requirement 25-27**: Список и детали заказов пользователя - базовый функционал интернет-магазина

### 🟡 ВАЖНЫЕ (Улучшают UX и функциональность)

- **Requirement 28**: Добавление отзывов - важно для доверия и конверсии
- **Requirement 29**: Динамическое меню - улучшает навигацию
- **Requirement 30**: Breadcrumbs - улучшает UX и SEO

### 🟢 РЕАЛИЗОВАННЫЕ (Уже работают)

- **Requirement 1-24**: Базовый функционал интернет-магазина (каталог, корзина, оформление заказа, авторизация, поиск, избранное, главная страница, статические страницы)

## Зависимости между требованиями

### Административная панель (Requirements 31-35)

- **Requirement 31** (Управление товарами) зависит от:
  - Requirement 4 (Работа с базой данных)
  - Requirement 5 (Каталог товаров)
  
- **Requirement 32** (Управление заказами) зависит от:
  - Requirement 16 (Оформление заказа)
  - Requirement 25-27 (Список и детали заказов)
  
- **Requirement 33** (Управление пользователями) зависит от:
  - Requirement 14 (Авторизация и регистрация)
  
- **Requirement 34** (Управление контентом) зависит от:
  - Requirement 21 (Статические страницы)
  
- **Requirement 35** (Статистика и отчеты) зависит от:
  - Requirement 32 (Управление заказами)

### Обмен с 1С (Requirements 36-39)

- **Requirement 36** (Импорт каталога) зависит от:
  - Requirement 4 (Работа с базой данных)
  - Requirement 39 (Аутентификация 1С)
  
- **Requirement 37** (Импорт предложений) зависит от:
  - Requirement 36 (Импорт каталога) - товары должны существовать
  - Requirement 39 (Аутентификация 1С)
  
- **Requirement 38** (Экспорт заказов) зависит от:
  - Requirement 16 (Оформление заказа)
  - Requirement 39 (Аутентификация 1С)

### Заказы пользователя (Requirements 25-27)

- **Requirement 25** (Список заказов) зависит от:
  - Requirement 14 (Авторизация)
  - Requirement 16 (Оформление заказа)
  
- **Requirement 26** (Детали заказа) зависит от:
  - Requirement 25 (Список заказов)
  
- **Requirement 27** (Счет на оплату) зависит от:
  - Requirement 26 (Детали заказа)

### Отзывы и навигация (Requirements 28-30)

- **Requirement 28** (Добавление отзывов) зависит от:
  - Requirement 14 (Авторизация)
  - Requirement 19 (Отображение отзывов)
  
- **Requirement 29** (Динамическое меню) зависит от:
  - Requirement 5 (Каталог товаров)
  
- **Requirement 30** (Breadcrumbs) зависит от:
  - Requirement 5 (Каталог товаров)
  - Requirement 7 (Страница товара)

## Глоссарий

- **System** - Laravel-приложение интернет-магазина "Сфера"
- **Legacy_System** - существующий PHP-движок на Smarty 2.6.11
- **Migration_Module** - функциональный модуль, переносимый из Legacy_System в System
- **Controller** - Laravel контроллер, обрабатывающий HTTP-запросы
- **Service** - сервисный класс с бизнес-логикой
- **Blade_Template** - шаблон Laravel Blade
- **Smarty_Template** - шаблон Smarty (.tpl файл)
- **SQL_Query** - прямой SQL-запрос через DB::select() или DB::selectOne()
- **Exchange1C** - модуль обмена данными с 1С:Предприятие по протоколу CommerceML
- **CommerceML** - XML-формат обмена данными с 1С:Предприятие
- **AppInstaller** - система установки приложений
- **Cart** - корзина покупок
- **Product** - товар в каталоге
- **Category** - категория товаров
- **Filter** - фильтр каталога товаров
- **User** - пользователь интернет-магазина
- **Order** - заказ пользователя
- **Admin_Panel** - административная панель для управления интернет-магазином
- **Review** - отзыв пользователя на товар
- **Menu** - навигационное меню категорий
- **Breadcrumbs** - хлебные крошки навигации
- **Invoice** - счет на оплату заказа

## Требования

### Requirement 1: Процесс миграции модуля

**User Story:** Как разработчик, я хочу следовать стандартному процессу миграции модулей, чтобы обеспечить единообразие и качество переноса функционала.

#### Acceptance Criteria

1. WHEN начинается миграция Migration_Module, THE System SHALL изучить исходный код в site/modules/sfera/{module}/
2. WHEN изучается Legacy_System модуль, THE System SHALL идентифицировать SQL-запросы, бизнес-логику и структуру шаблонов
3. WHEN создается Controller, THE System SHALL перенести логику из execute() метода Legacy_System
4. WHEN переносятся SQL-запросы, THE System SHALL сохранить их без изменений используя DB::select() или DB::selectOne()
5. WHEN создается Service, THE System SHALL вынести сложную бизнес-логику из Controller
6. WHEN конвертируется Smarty_Template в Blade_Template, THE System SHALL сохранить структуру HTML и CSS максимально точно
7. WHEN добавляются роуты, THE System SHALL проверить отсутствие дублирующих маршрутов в routes/web.php

### Requirement 2: Сохранение SQL-запросов

**User Story:** Как разработчик, я хочу использовать существующие SQL-запросы, чтобы избежать ошибок при переводе на Eloquent ORM и упростить проверку запросов.

#### Acceptance Criteria

1. THE System SHALL использовать прямые SQL-запросы через DB::select() и DB::selectOne()
2. THE System SHALL NOT переводить SQL-запросы на Eloquent ORM
3. WHEN выполняется SQL_Query, THE System SHALL использовать параметризованные запросы для защиты от SQL-инъекций
4. WHEN требуется проверка SQL_Query, THE System SHALL позволить копирование запроса в DBForge без изменений

### Requirement 3: Конвертация шаблонов

**User Story:** Как разработчик, я хочу конвертировать Smarty шаблоны в Blade, чтобы использовать современный шаблонизатор Laravel с сохранением верстки.

#### Acceptance Criteria

1. WHEN конвертируется переменная Smarty, THE System SHALL заменить ~~$var~ на {{ $var }}
2. WHEN конвертируется условие Smarty, THE System SHALL заменить ~~if~ на @if и ~~/if~ на @endif
3. WHEN конвертируется цикл Smarty, THE System SHALL заменить ~~foreach~ на @foreach
4. WHEN конвертируется Smarty_Template, THE System SHALL сохранить структуру HTML, классы CSS и атрибуты элементов
5. THE System SHALL создать Blade_Template в директории resources/views/{module}/

### Requirement 4: Работа с базой данных

**User Story:** Как система, я хочу использовать существующую базу данных без изменений, чтобы обеспечить совместимость с Legacy_System и минимизировать риски.

#### Acceptance Criteria

1. THE System SHALL использовать существующую структуру базы данных MariaDB 10.4
2. THE System SHALL NOT изменять схему существующих таблиц
3. THE System SHALL использовать 66 существующих моделей в app/Models/ только для определения структуры
4. WHEN выполняются запросы к БД, THE System SHALL использовать прямые SQL-запросы вместо Eloquent методов

### Requirement 5: Каталог товаров

**User Story:** Как пользователь, я хочу просматривать каталог товаров, чтобы найти интересующие меня продукты.

#### Acceptance Criteria

1. WHEN пользователь открывает /catalog, THE System SHALL отобразить список товаров
2. WHEN пользователь открывает /catalog/{category_id}, THE System SHALL отобразить товары выбранной категории
3. WHEN отображается Product, THE System SHALL показать изображение, название, цену и рейтинг
4. WHEN отображается список товаров, THE System SHALL использовать CatalogController::index()
5. THE System SHALL получать данные товаров через SQL_Query к таблицам products, prices, o_images
6. WHEN Product находится в Cart, THE System SHALL отобразить на карточке товара количество с кнопками +/- вместо кнопки "В корзину"
7. WHEN пользователь изменяет количество Product на карточке, THE System SHALL обновить количество в Cart через AJAX
8. WHEN Product находится в избранном, THE System SHALL отобразить на карточке товара активную иконку избранного
9. WHEN пользователь кликает на иконку избранного на карточке, THE System SHALL добавить/удалить Product из избранного через AJAX
10. THE System SHALL синхронизировать состояние карточек товаров с Cart и избранным на всех страницах (каталог, поиск, главная)

### Requirement 6: Фильтрация товаров

**User Story:** Как пользователь, я хочу фильтровать товары по различным параметрам, чтобы быстро найти нужные продукты.

#### Acceptance Criteria

1. WHEN пользователь применяет Filter, THE System SHALL обновить список товаров через AJAX
2. THE System SHALL поддерживать фильтрацию по авторам
3. THE System SHALL поддерживать фильтрацию по возрастам
4. THE System SHALL поддерживать фильтрацию по сериям
5. THE System SHALL поддерживать фильтрацию по типам товаров
6. THE System SHALL поддерживать фильтрацию по тематикам
7. WHEN применяется Filter, THE System SHALL использовать FilterService для построения SQL_Query

### Requirement 7: Страница товара

**User Story:** Как пользователь, я хочу видеть детальную информацию о товаре, чтобы принять решение о покупке.

#### Acceptance Criteria

1. WHEN пользователь открывает /product/{slug}, THE System SHALL отобразить детальную информацию о Product
2. WHEN отображается Product, THE System SHALL показать название, описание, цену, изображения, атрибуты и отзывы
3. WHEN отображается Product, THE System SHALL показать breadcrumbs навигацию
4. WHEN отображается Product, THE System SHALL установить SEO метаданные (title, description, keywords)
5. THE System SHALL использовать ProductController::show() для отображения страницы товара
6. THE System SHALL использовать ProductService для получения рейтинга, изображений и атрибутов

### Requirement 8: Корзина покупок

**User Story:** Как пользователь, я хочу добавлять товары в корзину, чтобы оформить заказ.

#### Acceptance Criteria

1. WHEN пользователь добавляет Product в Cart, THE System SHALL увеличить количество товара в сессии
2. WHEN пользователь удаляет Product из Cart, THE System SHALL удалить товар из сессии
3. WHEN пользователь изменяет количество Product в Cart, THE System SHALL обновить количество в сессии
4. WHEN пользователь открывает /cart, THE System SHALL отобразить содержимое Cart
5. THE System SHALL использовать CartService для управления Cart
6. THE System SHALL хранить Cart в сессии Laravel
7. WHEN выполняются операции с Cart, THE System SHALL возвращать JSON-ответ для AJAX-запросов
8. WHEN отображается Product в Cart, THE System SHALL показать галочку выбора товара к покупке
9. WHEN пользователь снимает галочку с Product, THE System SHALL исключить товар из расчета итоговой суммы
10. WHEN пользователь снимает галочку с Product, THE System SHALL сохранить товар в Cart для последующих покупок
11. WHEN рассчитывается итоговая сумма Cart, THE System SHALL учитывать только Product с установленной галочкой

### Requirement 9: Обмен данными с 1С

**User Story:** Как система, я хочу синхронизировать данные с 1С:Предприятие, чтобы поддерживать актуальность товаров, цен и заказов.

#### Acceptance Criteria

1. WHEN 1С отправляет запрос на /exchange1c, THE System SHALL аутентифицировать запрос через HTTP Basic Auth (см. Requirement 39)
2. WHEN 1С отправляет каталог товаров, THE System SHALL импортировать товары и категории из CommerceML XML (см. Requirement 36)
3. WHEN 1С отправляет предложения, THE System SHALL импортировать цены и остатки товаров (см. Requirement 37)
4. WHEN требуется экспорт заказов, THE System SHALL отправить заказы в 1С в формате CommerceML (см. Requirement 38)
5. THE System SHALL использовать Exchange1cService для обработки запросов обмена
6. WHEN происходит импорт товаров, THE System SHALL использовать batch insert для производительности
7. WHEN происходит обмен с 1С, THE System SHALL логировать все операции в exchange1c_logs
8. THE System SHALL поддерживать режимы обмена: checkauth, init, file, import, query, success
9. WHEN 1С отправляет файлы, THE System SHALL сохранять их во временную директорию storage/app/1c/
10. WHEN обработка файлов завершена, THE System SHALL удалять временные файлы

### Requirement 10: Установка приложений

**User Story:** Как администратор, я хочу устанавливать приложения из пакетов, чтобы расширять функционал системы.

#### Acceptance Criteria

1. WHEN администратор устанавливает приложение, THE System SHALL выполнить SQL-миграции из install.sql
2. WHEN администратор устанавливает приложение, THE System SHALL скопировать файлы из .inst/{app_id}_app_v{version}/ в public/apps/{app_id}/
3. WHEN администратор устанавливает приложение, THE System SHALL зарегистрировать приложение в таблице installed_apps
4. WHEN администратор удаляет приложение, THE System SHALL удалить файлы и запись из installed_apps
5. THE System SHALL использовать AppInstallerService для управления приложениями
6. WHEN запрашивается список приложений, THE System SHALL вернуть список из таблицы installed_apps

### Requirement 11: Обработка ошибок

**User Story:** Как пользователь, я хочу видеть понятные сообщения об ошибках, чтобы понимать что произошло.

#### Acceptance Criteria

1. WHEN происходит ошибка 404, THE System SHALL отобразить страницу errors/404.blade.php
2. WHEN происходит ошибка 404, THE System SHALL залогировать запрос в таблицу log_404
3. WHEN происходит ошибка 500, THE System SHALL отобразить страницу errors/500.blade.php
4. WHEN происходит ошибка 500, THE System SHALL залогировать ошибку в Laravel log
5. WHEN происходит ошибка обмена с 1С, THE System SHALL вернуть XML-ответ с описанием ошибки
6. WHEN происходит ошибка в Cart, THE System SHALL вернуть JSON-ответ с полем error

### Requirement 12: Тестирование

**User Story:** Как разработчик, я хочу безопасно тестировать функционал, чтобы не повредить рабочую базу данных.

#### Acceptance Criteria

1. WHEN выполняется тест, THE System SHALL использовать Database Transactions для отката изменений
2. WHEN выполняется тест, THE System SHALL NOT удалять или очищать таблицы полностью
3. WHEN создаются тестовые данные, THE System SHALL использовать уникальные идентификаторы (например, test_product_xxx)
4. WHEN тест завершается, THE System SHALL откатить транзакцию или удалить только тестовые записи
5. THE System SHALL NOT использовать RefreshDatabase trait в тестах
6. WHEN выполняются юнит-тесты, THE System SHALL тестировать CartService, ProductService, FilterService
7. WHEN выполняются интеграционные тесты, THE System SHALL тестировать HTTP-маршруты и AJAX-endpoints

### Requirement 13: Технические требования

**User Story:** Как система, я должна соответствовать техническим требованиям, чтобы обеспечить стабильность и производительность.

#### Acceptance Criteria

1. THE System SHALL использовать PHP версии 8.5
2. THE System SHALL использовать Laravel версии 12
3. THE System SHALL использовать MariaDB версии 10.4
4. THE System SHALL использовать Blade в качестве шаблонизатора
5. THE System SHALL сохранить использование Knockout.js на фронтенде
6. WHEN загружаются assets, THE System SHALL использовать файлы из директории public/assets/
7. WHEN создается Knockout.js ViewModel, THE System SHALL следовать единому стандарту: function NameViewModel() с observables и computed
8. WHEN создается Knockout.js модель данных, THE System SHALL следовать паттерну: function ModelName(data) с маппингом полей
9. WHEN применяются Knockout bindings, THE System SHALL использовать ko.applyBindings(viewModel, element) для конкретного элемента
10. WHEN создается глобальная ViewModel, THE System SHALL объявить переменную var nameViewModel = null перед инициализацией
11. THE System SHALL использовать data-bind атрибуты в HTML для связывания с ViewModel
12. THE System SHALL сохранить структуру Knockout биндингов из legacy системы (click, value, text, foreach, if)

### Requirement 24: Комментарии в коде

**User Story:** Как разработчик legacy системы, я хочу видеть подробные комментарии на русском языке в новом коде, чтобы понимать логику работы Laravel приложения.

#### Acceptance Criteria

1. WHEN создается Controller, THE System SHALL добавить комментарии на русском языке для каждого метода
2. WHEN создается Service, THE System SHALL добавить комментарии на русском языке описывающие логику работы
3. WHEN переносится SQL-запрос, THE System SHALL добавить комментарий объясняющий что делает запрос
4. WHEN конвертируется Smarty_Template в Blade_Template, THE System SHALL добавить комментарий в начале файла с указанием источника (legacy путь)
5. WHEN создается Knockout.js ViewModel, THE System SHALL добавить комментарии на русском языке для observables и computed свойств
6. WHEN создается сложная бизнес-логика, THE System SHALL добавить пошаговые комментарии на русском языке
7. THE System SHALL использовать формат комментариев PHPDoc для методов классов
8. THE System SHALL добавлять комментарии в формате JSDoc для JavaScript функций
9. WHEN добавляются комментарии, THE System SHALL объяснять "почему" а не только "что" делает код

### Requirement 14: Авторизация и регистрация

**User Story:** Как пользователь, я хочу зарегистрироваться и войти в систему, чтобы получить доступ к персональным функциям.

#### Acceptance Criteria

1. WHEN пользователь открывает /register, THE System SHALL отобразить форму регистрации
2. WHEN пользователь отправляет форму регистрации, THE System SHALL валидировать данные (email, пароль, имя)
3. WHEN данные валидны, THE System SHALL создать User в таблице users
4. WHEN пользователь открывает /login, THE System SHALL отобразить форму входа
5. WHEN пользователь отправляет форму входа, THE System SHALL аутентифицировать User
6. WHEN аутентификация успешна, THE System SHALL создать сессию для User

### Requirement 15: Профиль пользователя

**User Story:** Как пользователь, я хочу управлять своими данными и видеть историю заказов, чтобы контролировать свой аккаунт.

#### Acceptance Criteria

1. WHEN пользователь открывает /profile, THE System SHALL отобразить данные User
2. WHEN пользователь редактирует данные, THE System SHALL валидировать и сохранить изменения в таблице users
3. WHEN пользователь открывает историю заказов, THE System SHALL отобразить список Order пользователя
4. THE System SHALL требовать аутентификацию для доступа к /profile

### Requirement 16: Оформление заказа

**User Story:** Как пользователь, я хочу оформить заказ, чтобы приобрести товары из корзины.

#### Acceptance Criteria

1. WHEN пользователь открывает /checkout, THE System SHALL отобразить форму оформления заказа
2. WHEN пользователь отправляет форму заказа, THE System SHALL валидировать данные доставки
3. WHEN данные валидны, THE System SHALL создать Order в таблице orders
4. WHEN Order создан, THE System SHALL очистить Cart
5. WHEN Order создан, THE System SHALL перенаправить пользователя на /thankyoupage
6. THE System SHALL использовать OrderController::checkout() для оформления заказа
7. WHEN создается Order, THE System SHALL включить в заказ только Product с установленной галочкой выбора
8. WHEN создается Order, THE System SHALL сохранить в БД только выбранные товары из Cart
9. WHEN создается Order, THE System SHALL оставить в Cart невыбранные товары для последующих покупок

### Requirement 17: Поиск товаров

**User Story:** Как пользователь, я хочу искать товары, чтобы быстро найти нужный продукт.

#### Acceptance Criteria

1. WHEN пользователь вводит запрос в поиск, THE System SHALL предложить автодополнение через AJAX
2. WHEN пользователь отправляет поисковый запрос, THE System SHALL отобразить результаты на /search
3. WHEN выполняется поиск, THE System SHALL искать по названию, автору и описанию Product
4. THE System SHALL использовать SearchController для обработки поисковых запросов
5. THE System SHALL предоставить API endpoint /api/search/autocomplete для автодополнения

### Requirement 18: Избранное

**User Story:** Как пользователь, я хочу добавлять товары в избранное, чтобы быстро находить интересующие меня продукты.

#### Acceptance Criteria

1. WHEN пользователь добавляет Product в избранное, THE System SHALL сохранить связь в таблице favorites
2. WHEN пользователь удаляет Product из избранного, THE System SHALL удалить связь из таблицы favorites
3. WHEN пользователь открывает /favorites, THE System SHALL отобразить список избранных Product
4. THE System SHALL использовать FavoriteService для управления избранным
5. THE System SHALL отображать счетчик избранного в header
6. WHEN отображается карточка Product, THE System SHALL показать активную иконку избранного если товар в избранном
7. WHEN пользователь кликает на иконку избранного, THE System SHALL обновить состояние через AJAX
8. THE System SHALL синхронизировать состояние избранного на всех карточках товаров (каталог, поиск, главная, страница товара)

### Requirement 19: Отзывы на товары

**User Story:** Как пользователь, я хочу читать отзывы на товары, чтобы принимать обоснованные решения о покупке.

#### Acceptance Criteria

1. WHEN отображается Product, THE System SHALL показать отзывы из таблицы o_reviews
2. WHEN отображается Review, THE System SHALL показать рейтинг, текст, автора и дату
3. WHEN отображается список отзывов, THE System SHALL отсортировать их по дате (новые первыми)
4. THE System SHALL показывать средний рейтинг Product на основе всех отзывов
5. THE System SHALL показывать количество отзывов на карточке Product
6. WHEN у Product нет отзывов, THE System SHALL показать сообщение "Отзывов пока нет"
7. THE System SHALL использовать ProductService::getProductRating() для получения рейтинга

**Примечание:** Добавление отзывов описано в Requirement 28.

### Requirement 20: Главная страница

**User Story:** Как пользователь, я хочу видеть привлекательную главную страницу с актуальными предложениями, чтобы быстро ориентироваться в магазине.

#### Acceptance Criteria

1. WHEN пользователь открывает главную страницу, THE System SHALL отобразить карусели товаров
2. WHEN отображается главная страница, THE System SHALL показать баннеры
3. WHEN отображается главная страница, THE System SHALL показать популярные категории
4. WHEN отображается главная страница, THE System SHALL показать топ-10 товаров
5. THE System SHALL использовать ShowcaseController для отображения главной страницы
6. WHEN отображается главная страница, THE System SHALL подключить модули через Blade компоненты (аналог ~~mod~ в Smarty)
7. THE System SHALL использовать существующий showcase.tpl как основу для showcase.blade.php
8. WHEN дорабатывается главная страница, THE System SHALL сохранить уже реализованные блоки и компоненты
9. THE System SHALL отображать на главной странице модули: main_carousel, promo_carousel, top10_products, random_products, popular_categories, banners

### Requirement 21: Статические страницы

**User Story:** Как пользователь, я хочу читать информационные страницы, чтобы узнать больше о магазине и условиях работы.

#### Acceptance Criteria

1. WHEN пользователь открывает статическую страницу, THE System SHALL получить контент из таблицы pages
2. WHEN отображается статическая страница, THE System SHALL отобразить заголовок и содержимое
3. THE System SHALL использовать PageController для отображения статических страниц
4. THE System SHALL поддерживать страницы: О нас, Контакты, Доставка, Оплата

### Requirement 22: Производительность

**User Story:** Как пользователь, я хочу, чтобы сайт работал быстро, чтобы комфортно совершать покупки.

#### Acceptance Criteria

1. WHEN загружается страница каталога, THE System SHALL отобразить её за время не более 2 секунд
2. WHEN загружается страница товара, THE System SHALL отобразить её за время не более 1.5 секунд
3. WHEN выполняется AJAX-запрос, THE System SHALL ответить за время не более 500 миллисекунд
4. WHEN выполняется импорт из 1С, THE System SHALL использовать batch операции для оптимизации

### Requirement 23: Безопасность

**User Story:** Как система, я должна обеспечивать безопасность данных пользователей, чтобы защитить их от угроз.

#### Acceptance Criteria

1. THE System SHALL использовать параметризованные SQL-запросы для защиты от SQL-инъекций
2. THE System SHALL хешировать пароли пользователей перед сохранением в БД
3. THE System SHALL использовать CSRF-токены для защиты форм
4. THE System SHALL использовать HTTPS для передачи данных
5. WHEN происходит аутентификация 1С, THE System SHALL использовать HTTP Basic Auth
6. THE System SHALL валидировать все входные данные от пользователей

### Requirement 25: Список заказов пользователя

**User Story:** Как пользователь, я хочу видеть список своих заказов, чтобы отслеживать их статус и историю покупок.

#### Acceptance Criteria

1. WHEN пользователь открывает /orders, THE System SHALL отобразить список всех Order пользователя
2. WHEN отображается список заказов, THE System SHALL показать номер заказа, дату, статус и сумму
3. WHEN отображается список заказов, THE System SHALL отсортировать Order по дате создания (новые первыми)
4. THE System SHALL использовать OrderController::index() для отображения списка заказов
5. THE System SHALL требовать аутентификацию для доступа к /orders
6. WHEN пользователь кликает на Order, THE System SHALL перенаправить на страницу детального просмотра заказа

### Requirement 26: Детальный просмотр заказа

**User Story:** Как пользователь, я хочу видеть детали своего заказа, чтобы проверить состав и информацию о доставке.

#### Acceptance Criteria

1. WHEN пользователь открывает /order/{id}, THE System SHALL отобразить детальную информацию об Order
2. WHEN отображается Order, THE System SHALL показать список товаров с ценами и количеством
3. WHEN отображается Order, THE System SHALL показать данные доставки (адрес, получатель, телефон)
4. WHEN отображается Order, THE System SHALL показать статус заказа и дату создания
5. WHEN отображается Order, THE System SHALL показать итоговую сумму заказа
6. THE System SHALL использовать OrderController::show() для отображения деталей заказа
7. THE System SHALL требовать аутентификацию и проверять что Order принадлежит текущему User
8. WHEN пользователь открывает чужой Order, THE System SHALL вернуть ошибку 403

### Requirement 27: Счет на оплату заказа

**User Story:** Как пользователь, я хочу скачать счет на оплату, чтобы оплатить заказ через банк.

#### Acceptance Criteria

1. WHEN пользователь открывает /invoice/{id}, THE System SHALL сгенерировать счет на оплату в формате PDF или HTML
2. WHEN генерируется счет, THE System SHALL включить реквизиты организации
3. WHEN генерируется счет, THE System SHALL включить список товаров с ценами
4. WHEN генерируется счет, THE System SHALL включить итоговую сумму к оплате
5. THE System SHALL использовать OrderController::invoice() для генерации счета
6. THE System SHALL требовать аутентификацию и проверять что Order принадлежит текущему User
7. WHEN пользователь открывает счет чужого Order, THE System SHALL вернуть ошибку 403

### Requirement 28: Добавление отзывов на товары

**User Story:** Как пользователь, я хочу добавлять отзывы на товары, чтобы делиться своим опытом использования.

#### Acceptance Criteria

1. WHEN пользователь открывает страницу Product, THE System SHALL отобразить форму добавления отзыва
2. WHEN пользователь отправляет отзыв, THE System SHALL валидировать данные (рейтинг от 1 до 5, текст отзыва)
3. WHEN данные валидны, THE System SHALL сохранить отзыв в таблице o_reviews
4. WHEN отзыв сохранен, THE System SHALL обновить средний рейтинг Product
5. WHEN отзыв сохранен, THE System SHALL отобразить сообщение об успешном добавлении
6. THE System SHALL использовать ProductController::addReview() для обработки отзыва
7. THE System SHALL требовать аутентификацию для добавления отзыва
8. WHEN неавторизованный пользователь пытается добавить отзыв, THE System SHALL перенаправить на страницу входа
9. WHEN пользователь уже оставил отзыв на Product, THE System SHALL предложить редактировать существующий отзыв

### Requirement 29: Динамическое меню категорий

**User Story:** Как пользователь, я хочу видеть структурированное меню категорий, чтобы легко находить нужные разделы каталога.

#### Acceptance Criteria

1. WHEN загружается страница, THE System SHALL отобразить меню категорий в header
2. WHEN генерируется меню, THE System SHALL получить список Category из таблицы categories
3. WHEN генерируется меню, THE System SHALL построить иерархическую структуру категорий
4. WHEN отображается Category в меню, THE System SHALL показать название и количество товаров
5. THE System SHALL использовать MenuService для генерации структуры меню
6. THE System SHALL кешировать структуру меню на 1 час для производительности
7. WHEN изменяются Category в БД, THE System SHALL очистить кеш меню

### Requirement 30: Breadcrumbs навигация

**User Story:** Как пользователь, я хочу видеть хлебные крошки, чтобы понимать где я нахожусь на сайте и быстро переходить на верхние уровни.

#### Acceptance Criteria

1. WHEN отображается страница Product, THE System SHALL показать breadcrumbs с путем от главной до товара
2. WHEN отображается страница Category, THE System SHALL показать breadcrumbs с путем от главной до категории
3. WHEN отображается страница Author, THE System SHALL показать breadcrumbs с путем от главной до автора
4. WHEN отображается страница Series, THE System SHALL показать breadcrumbs с путем от главной до серии
5. THE System SHALL использовать BreadcrumbsService для генерации breadcrumbs
6. WHEN генерируются breadcrumbs, THE System SHALL включить микроразметку Schema.org для SEO
7. THE System SHALL отображать breadcrumbs на всех страницах кроме главной

### Requirement 31: Административная панель - Управление товарами

**User Story:** Как администратор, я хочу управлять товарами через административную панель, чтобы добавлять, редактировать и удалять товары.

#### Acceptance Criteria

1. WHEN администратор открывает /admin/products, THE System SHALL отобразить список всех Product
2. WHEN администратор кликает "Добавить товар", THE System SHALL отобразить форму создания Product
3. WHEN администратор сохраняет новый Product, THE System SHALL валидировать данные и сохранить в таблицу products
4. WHEN администратор кликает "Редактировать" на Product, THE System SHALL отобразить форму редактирования
5. WHEN администратор сохраняет изменения Product, THE System SHALL обновить данные в таблице products
6. WHEN администратор кликает "Удалить" на Product, THE System SHALL запросить подтверждение и удалить товар
7. THE System SHALL использовать Admin\ProductController для управления товарами
8. THE System SHALL требовать роль администратора для доступа к /admin/*
9. WHEN неавторизованный пользователь пытается открыть /admin/*, THE System SHALL перенаправить на страницу входа
10. WHEN пользователь без роли администратора пытается открыть /admin/*, THE System SHALL вернуть ошибку 403

### Requirement 32: Административная панель - Управление заказами

**User Story:** Как администратор, я хочу управлять заказами через административную панель, чтобы обрабатывать заказы клиентов.

#### Acceptance Criteria

1. WHEN администратор открывает /admin/orders, THE System SHALL отобразить список всех Order
2. WHEN отображается список заказов, THE System SHALL показать номер, дату, клиента, статус и сумму
3. WHEN администратор кликает на Order, THE System SHALL отобразить детальную информацию о заказе
4. WHEN администратор изменяет статус Order, THE System SHALL обновить статус в таблице orders
5. WHEN администратор изменяет статус Order, THE System SHALL отправить уведомление клиенту (email)
6. THE System SHALL использовать Admin\OrderController для управления заказами
7. THE System SHALL поддерживать фильтрацию заказов по статусу, дате и клиенту
8. WHEN администратор кликает "Печать", THE System SHALL сгенерировать печатную форму заказа

### Requirement 33: Административная панель - Управление пользователями

**User Story:** Как администратор, я хочу управлять пользователями через административную панель, чтобы контролировать доступ к системе.

#### Acceptance Criteria

1. WHEN администратор открывает /admin/users, THE System SHALL отобразить список всех User
2. WHEN отображается список пользователей, THE System SHALL показать имя, email, роль и дату регистрации
3. WHEN администратор кликает на User, THE System SHALL отобразить детальную информацию о пользователе
4. WHEN администратор изменяет роль User, THE System SHALL обновить роль в таблице users
5. WHEN администратор блокирует User, THE System SHALL установить флаг is_blocked в таблице users
6. WHEN заблокированный User пытается войти, THE System SHALL отклонить аутентификацию
7. THE System SHALL использовать Admin\UserController для управления пользователями
8. THE System SHALL поддерживать поиск пользователей по имени и email

### Requirement 34: Административная панель - Управление контентом

**User Story:** Как администратор, я хочу управлять контентом сайта через административную панель, чтобы редактировать страницы и баннеры.

#### Acceptance Criteria

1. WHEN администратор открывает /admin/pages, THE System SHALL отобразить список всех статических страниц
2. WHEN администратор редактирует страницу, THE System SHALL предоставить WYSIWYG редактор для контента
3. WHEN администратор сохраняет страницу, THE System SHALL обновить данные в таблице pages
4. WHEN администратор открывает /admin/banners, THE System SHALL отобразить список всех баннеров
5. WHEN администратор добавляет баннер, THE System SHALL загрузить изображение и сохранить данные в таблицу banners
6. THE System SHALL использовать Admin\PageController и Admin\BannerController
7. THE System SHALL поддерживать загрузку изображений для баннеров

### Requirement 35: Административная панель - Статистика и отчеты

**User Story:** Как администратор, я хочу видеть статистику и отчеты, чтобы анализировать работу интернет-магазина.

#### Acceptance Criteria

1. WHEN администратор открывает /admin/dashboard, THE System SHALL отобразить основные метрики (продажи, заказы, пользователи)
2. WHEN администратор открывает /admin/reports/sales, THE System SHALL отобразить отчет по продажам за период
3. WHEN администратор выбирает период, THE System SHALL обновить данные отчета
4. WHEN отображается отчет, THE System SHALL показать графики и таблицы с данными
5. THE System SHALL использовать Admin\DashboardController и Admin\ReportController
6. THE System SHALL поддерживать экспорт отчетов в Excel и PDF

### Requirement 36: Обмен данными с 1С - Импорт каталога

**User Story:** Как система, я хочу импортировать каталог товаров из 1С, чтобы синхронизировать данные о товарах и категориях.

#### Acceptance Criteria

1. WHEN 1С отправляет файл import.xml, THE System SHALL распарсить XML в формате CommerceML
2. WHEN парсится каталог, THE System SHALL извлечь категории и товары
3. WHEN импортируются категории, THE System SHALL создать или обновить записи в таблице categories
4. WHEN импортируются товары, THE System SHALL создать или обновить записи в таблице products
5. THE System SHALL использовать Exchange1cService::importCatalog() для обработки каталога
6. WHEN происходит импорт, THE System SHALL использовать batch insert для производительности
7. WHEN происходит ошибка импорта, THE System SHALL залогировать ошибку в exchange1c_logs
8. WHEN импорт завершен, THE System SHALL вернуть XML-ответ "success"

### Requirement 37: Обмен данными с 1С - Импорт предложений

**User Story:** Как система, я хочу импортировать предложения из 1С, чтобы синхронизировать цены и остатки товаров.

#### Acceptance Criteria

1. WHEN 1С отправляет файл offers.xml, THE System SHALL распарсить XML в формате CommerceML
2. WHEN парсятся предложения, THE System SHALL извлечь цены и остатки для каждого товара
3. WHEN импортируются цены, THE System SHALL обновить записи в таблице prices
4. WHEN импортируются остатки, THE System SHALL обновить поле quantity в таблице products
5. THE System SHALL использовать Exchange1cService::importOffers() для обработки предложений
6. WHEN происходит импорт, THE System SHALL использовать batch update для производительности
7. WHEN товар не найден в БД, THE System SHALL залогировать предупреждение в exchange1c_logs
8. WHEN импорт завершен, THE System SHALL вернуть XML-ответ "success"

### Requirement 38: Обмен данными с 1С - Экспорт заказов

**User Story:** Как система, я хочу экспортировать заказы в 1С, чтобы передавать информацию о заказах в учетную систему.

#### Acceptance Criteria

1. WHEN 1С запрашивает заказы, THE System SHALL получить список новых Order из таблицы orders
2. WHEN формируется экспорт, THE System SHALL сгенерировать XML в формате CommerceML
3. WHEN формируется XML заказа, THE System SHALL включить данные клиента, товары и суммы
4. WHEN заказ экспортирован, THE System SHALL установить флаг exported_to_1c в таблице orders
5. THE System SHALL использовать Exchange1cService::exportOrders() для экспорта заказов
6. WHEN происходит экспорт, THE System SHALL залогировать операцию в exchange1c_logs
7. WHEN происходит ошибка экспорта, THE System SHALL вернуть XML-ответ с описанием ошибки

### Requirement 39: Обмен данными с 1С - Аутентификация

**User Story:** Как система, я хочу безопасно аутентифицировать запросы от 1С, чтобы защитить данные от несанкционированного доступа.

#### Acceptance Criteria

1. WHEN 1С отправляет запрос на /exchange1c, THE System SHALL проверить HTTP Basic Auth заголовок
2. WHEN заголовок отсутствует, THE System SHALL вернуть HTTP 401 Unauthorized
3. WHEN учетные данные неверны, THE System SHALL вернуть HTTP 401 Unauthorized
4. WHEN учетные данные верны, THE System SHALL обработать запрос обмена
5. THE System SHALL использовать middleware для проверки аутентификации 1С
6. THE System SHALL хранить учетные данные 1С в конфигурации (.env)
7. WHEN происходит попытка неавторизованного доступа, THE System SHALL залогировать попытку в exchange1c_logs
