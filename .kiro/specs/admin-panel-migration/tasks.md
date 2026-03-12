# План реализации: Миграция админ-панели

## Обзор

Этот план описывает пошаговую миграцию legacy админ-панели (ExtJS + Smarty + custom PHP) на Laravel с сохранением существующего Windows 10 Desktop интерфейса. Миграция модернизирует backend, заменяя Smarty на Blade, а custom PHP на Laravel, при этом ExtJS Desktop shell остается без изменений.

## Ключевые принципы

1. **База данных УЖЕ СУЩЕСТВУЕТ** - изучаем схему, создаем модели для существующих таблиц
2. **ExtJS Desktop shell ОСТАЕТСЯ БЕЗ ИЗМЕНЕНИЙ** - обновляем только API endpoints
3. **Iframe архитектура** - ExtJS окна содержат iframe с Laravel Blade
4. **Blade шаблоны** - ВСЕГДА используем @push/@stack для скриптов и стилей
5. **Минимум 80% покрытие кода** - включая property-based тесты

## Задачи

- [ ] 1. Фаза 1: Изучение legacy системы и подготовка фундамента
  - [x] 1.1 Изучить структуру базы данных legacy системы
    - Проанализировать таблицы: installed_apps, app_routes, app_desktop_shortcuts, admin_permissions, admin_roles, admin_user_roles, admin_role_permissions, app_installation_log
    - Документировать схему БД, связи между таблицами, индексы и ограничения
    - Создать диаграмму ER для понимания структуры
    - _Требования: 1.5_

  - [x] 1.2 Изучить архитектуру пакетной системы legacy
    - Проанализировать структуру .inst/ директории
    - Изучить формат manifest.json в существующих пакетах
    - Изучить install.php скрипты и логику установки
    - Документировать workflow установки пакетов (проверка требований, создание БД, копирование файлов, регистрация маршрутов, настройка ACL)
    - _Требования: 1.1, 1.2, 1.3_

  - [x] 1.3 Изучить ExtJS Desktop shell в legacy системе
    - Проанализировать legacy/site/modules/admin/desktop/desktop.tpl
    - Документировать компоненты: Desktop Area, Taskbar, Start Menu, Window Manager
    - Изучить JavaScript функции открытия окон (openProducts, openOrders и т.д.)
    - Определить API endpoints, которые нужно обновить
    - _Требования: 8.1-8.17_

  - [x] 1.4 Создать Laravel модели для существующих таблиц БД
    - Создать модель InstalledApp для таблицы installed_apps
    - Создать модель AppRoute для таблицы app_routes
    - Создать модель AppDesktopShortcut для таблицы app_desktop_shortcuts
    - Создать модель AdminPermission для таблицы admin_permissions
    - Создать модель AdminRole для таблицы admin_roles
    - Создать модель AppInstallationLog для таблицы app_installation_log
    - Определить relationships между моделями (hasMany, belongsTo, belongsToMany)
    - _Требования: 1.5_

  - [ ]* 1.5 Написать unit тесты для моделей
    - Тестировать relationships между моделями
    - Тестировать casts и accessors
    - Тестировать scopes и query builders
    - _Требования: 1.5_

  - [x] 1.6 Создать базовый admin layout (Blade шаблон)
    - Создать resources/views/layouts/admin.blade.php
    - Использовать @stack('styles') и @stack('scripts') (НЕ @yield!)
    - Подключить Material Icons
    - Создать базовые стили для admin контента
    - Добавить CSRF token meta tag
    - _Требования: 9.2, 9.3, 9.10_

  - [x] 1.7 Создать ManifestParser сервис
    - Создать App\Services\Admin\ManifestParser
    - Реализовать метод parse() для чтения manifest.json
    - Реализовать валидацию схемы манифеста
    - Реализовать парсинг секций: metadata, requirements, installation, routing, permissions, desktop
    - Вернуть структурированный объект Manifest
    - _Требования: 3.1-3.8_

  - [ ]* 1.8 Написать property тесты для ManifestParser
    - **Property 1: Manifest Parsing Completeness**
    - **Проверяет: Требования 3.1-3.7**
    - Для любого валидного manifest.json, parse() должен вернуть объект со всеми обязательными полями
    - **Property 2: Manifest Validation Consistency**
    - **Проверяет: Требования 3.7, 3.8**
    - Для любого невалидного manifest.json, parse() должен выбросить исключение с понятным сообщением

  - [ ] 1.9 Создать PackageInstaller сервис
    - Создать App\Services\Admin\PackageInstaller
    - Реализовать метод install() с полным workflow
    - Реализовать checkRequirements() для проверки PHP версии, расширений, таблиц БД
    - Реализовать checkDependencies() для проверки зависимостей пакетов
    - Реализовать installDatabase() для выполнения schema.sql и permissions.sql
    - Реализовать copyBackendFiles(), copyFrontendFiles(), copyPublicFiles()
    - Реализовать markAsInstalled() для записи в installed_apps
    - Реализовать rollback логику при ошибках (выполнение rollback.sql, удаление файлов)
    - Логировать все шаги установки в app_installation_log
    - _Требования: 4.1-4.14_

  - [ ]* 1.10 Написать property тесты для PackageInstaller
    - **Property 3: Installation Atomicity**
    - **Проверяет: Требования 4.13, 4.14**
    - Если install() выбрасывает исключение, то БД и файловая система должны быть в исходном состоянии
    - **Property 4: Installation Idempotency Check**
    - **Проверяет: Требования 4.3**
    - Повторная установка того же пакета должна выбросить PackageAlreadyInstalledException
    - **Property 5: Installation Completeness**
    - **Проверяет: Требования 4.4-4.12**
    - После успешной install(), пакет должен быть зарегистрирован во всех необходимых таблицах

  - [ ] 1.11 Создать PackageUninstaller сервис
    - Создать App\Services\Admin\PackageUninstaller
    - Реализовать метод uninstall() с полным workflow
    - Реализовать checkDependentPackages() для проверки зависимых пакетов
    - Реализовать executeRollbackSql() для выполнения database/rollback.sql
    - Реализовать removeFiles() для удаления backend, frontend, public файлов
    - Реализовать removeRoutes() для удаления из app_routes
    - Реализовать removeDesktopShortcuts() для удаления из app_desktop_shortcuts
    - Реализовать markAsInactive() для обновления installed_apps
    - Логировать все шаги удаления в app_installation_log
    - _Требования: 5.1-5.10_

  - [ ]* 1.12 Написать property тесты для PackageUninstaller
    - **Property 6: Uninstallation Completeness**
    - **Проверяет: Требования 5.2-5.9**
    - После успешной uninstall(), все записи пакета должны быть удалены из БД (кроме installed_apps.active=0)
    - **Property 7: Dependent Package Protection**
    - **Проверяет: Требования 5.1**
    - Попытка удалить пакет с зависимыми пакетами должна выбросить исключение

  - [ ] 1.13 Создать RouteManager сервис
    - Создать App\Services\Admin\RouteManager
    - Реализовать метод registerRoutes() для регистрации маршрутов из манифеста
    - Реализовать метод loadDynamicRoutes() для загрузки маршрутов из app_routes
    - Реализовать поддержку параметров маршрутов (/product/{id})
    - Реализовать поддержку middleware (auth, can:permission)
    - Реализовать кэширование маршрутов
    - Создать artisan команду admin:routes:rebuild для пересборки кэша
    - _Требования: 6.1-6.10_

  - [ ]* 1.14 Написать property тесты для RouteManager
    - **Property 8: Route Registration Completeness**
    - **Проверяет: Требования 6.1-6.3**
    - Все маршруты из манифеста должны быть зарегистрированы в app_routes
    - **Property 9: Route Removal Completeness**
    - **Проверяет: Требования 6.8**
    - При удалении пакета все его маршруты должны быть удалены из app_routes

  - [ ] 1.15 Интегрировать ACL систему с Laravel Gates
    - Обновить App\Providers\AuthServiceProvider
    - Реализовать динамическую регистрацию Gates из admin_permissions
    - Реализовать метод userHasPermission() для проверки прав через role_permissions
    - Создать middleware AdminPermissionMiddleware для защиты маршрутов
    - Создать Blade директивы @can/@cannot для проверки прав в шаблонах
    - _Требования: 7.1-7.10_

  - [ ]* 1.16 Написать property тесты для ACL системы
    - **Property 10: Permission Check Consistency**
    - **Проверяет: Требования 7.2-7.4**
    - Если пользователь имеет роль с правом X, то Gate::allows('X') должен вернуть true
    - **Property 11: Permission Removal Completeness**
    - **Проверяет: Требования 7.9**
    - При удалении пакета все его права должны быть удалены из admin_permissions

  - [ ] 1.17 Создать Migration Bridge для обратной совместимости
    - Создать App\Services\Admin\MigrationBridge
    - Реализовать legacy helper функции (noSQL, q, row, rows) как обертки Laravel DB
    - Реализовать адаптер legacy session структуры ($_SESSION['admin_user'] → Auth::user())
    - Реализовать адаптер legacy ACL (legacy ACL checks → Laravel Gates)
    - Добавить deprecation warnings для legacy кода
    - Логировать использование legacy compatibility features
    - _Требования: 12.1-12.7_

  - [ ] 1.18 Checkpoint - Убедиться, что все тесты проходят
    - Убедиться, что все тесты проходят, спросить пользователя, если возникли вопросы



- [ ] 2. Фаза 2: Миграция ExtJS Desktop и создание Package Manager UI
  - [x] 2.1 Создать Laravel маршрут для admin desktop
    - Создать AdminDesktopController с методом index()
    - Создать маршрут GET /admin/desktop
    - Добавить middleware auth и admin
    - _Требования: 8.18, 8.19_

  - [ ] 2.2 Портировать ExtJS Desktop shell на Laravel
    - Создать resources/views/admin/desktop/index.blade.php
    - Скопировать ExtJS код из legacy/site/modules/admin/desktop/desktop.tpl
    - Обновить API endpoints в JavaScript функциях (openProducts, openOrders и т.д.)
    - Обновить пути к модулям с /site/modules/admin/... на /admin/...
    - Сохранить все UI компоненты без изменений (Desktop Area, Taskbar, Start Menu, Window Manager)
    - Использовать @push('scripts') и @push('styles') для подключения ресурсов
    - _Требования: 8.1-8.19_

  - [ ] 2.3 Создать API endpoint для получения desktop shortcuts
    - Создать метод getDesktopShortcuts() в AdminDesktopController
    - Получать shortcuts из app_desktop_shortcuts для активных пакетов
    - Возвращать JSON с данными: title, icon, icon_color, function_name, show_on_desktop, show_in_quick_access
    - _Требования: 11.1-11.3_

  - [ ] 2.4 Создать API endpoint для получения списка приложений
    - Создать метод getInstalledApps() в AdminDesktopController
    - Получать приложения из installed_apps где active=1
    - Группировать по категориям для Start Menu
    - Возвращать JSON с данными: app_id, app_name, icon, icon_color, category
    - _Требования: 8.6, 8.7_

  - [ ]* 2.5 Написать integration тесты для desktop
    - Тестировать загрузку desktop страницы
    - Тестировать API endpoints (shortcuts, apps)
    - Тестировать авторизацию и права доступа
    - _Требования: 8.1-8.19_

  - [ ] 2.6 Создать модуль управления пакетами (Package Manager)
    - Создать PackageManagerController с методами: index, show, install, uninstall
    - Создать Blade view resources/views/admin/packages/index.blade.php
    - Использовать @push('scripts') и @push('styles') для подключения ресурсов (КРИТИЧНО!)
    - Отображать список доступных пакетов из .inst/ директории
    - Отображать список установленных пакетов из installed_apps
    - Реализовать форму установки с опциями (install_public_files, create_desktop_shortcut)
    - Реализовать real-time лог установки через AJAX
    - Реализовать подтверждение удаления пакета
    - Реализовать поиск и фильтрацию по категориям и статусу
    - Создать JavaScript функцию openPackageManager() для ExtJS окна
    - _Требования: 10.1-10.10_

  - [ ]* 2.7 Написать property тесты для Package Manager
    - **Property 12: Package List Consistency**
    - **Проверяет: Требования 10.1, 10.2**
    - Список доступных пакетов должен соответствовать содержимому .inst/ директории
    - **Property 13: Installation UI Feedback**
    - **Проверяет: Требования 10.6, 10.7**
    - После успешной установки UI должен показать success сообщение и обновить список

  - [ ] 2.8 Checkpoint - Убедиться, что desktop и Package Manager работают
    - Убедиться, что все тесты проходят, спросить пользователя, если возникли вопросы

- [ ] 3. Фаза 3: Реализация основных admin модулей (Products, Orders, Users)
  - [ ] 3.1 Создать модуль управления товарами (Product Manager)
    - Создать ProductController с методами: index, create, store, edit, update, destroy
    - Создать Blade views: resources/views/admin/products/index.blade.php, create.blade.php, edit.blade.php
    - Использовать @push('scripts') и @push('styles') для подключения ресурсов (КРИТИЧНО!)
    - Реализовать пагинированный список товаров с поиском и фильтрами
    - Реализовать форму создания/редактирования товара (name, description, price, quantity, images)
    - Реализовать загрузку изображений товара
    - Реализовать bulk операции (удаление, обновление цены, обновление количества)
    - Реализовать интеграцию с Ozon sync (v_products_o_products)
    - Реализовать валидацию данных товара
    - Добавить проверки авторизации (@authorize('products.view'), @authorize('products.create'))
    - Создать JavaScript функцию openProducts() для ExtJS окна
    - _Требования: 13.1-13.10_

  - [ ]* 3.2 Написать property тесты для Product Manager
    - **Property 14: Product CRUD Consistency**
    - **Проверяет: Требования 13.2, 13.3**
    - После создания товара через store(), товар должен быть доступен через index()
    - **Property 15: Product Validation**
    - **Проверяет: Требования 13.7**
    - Невалидные данные товара должны быть отклонены с понятными сообщениями об ошибках

  - [ ]* 3.3 Написать unit тесты для ProductController
    - Тестировать index() возвращает пагинированный список
    - Тестировать поиск и фильтры
    - Тестировать store() создает товар с валидными данными
    - Тестировать store() отклоняет невалидные данные
    - Тестировать update() обновляет товар
    - Тестировать destroy() удаляет товар
    - Тестировать проверки авторизации
    - _Требования: 13.1-13.10_

  - [ ] 3.4 Создать модуль управления заказами (Order Manager)
    - Создать OrderController с методами: index, show, updateStatus, generateInvoice
    - Создать Blade views: resources/views/admin/orders/index.blade.php, show.blade.php
    - Использовать @push('scripts') и @push('styles') для подключения ресурсов (КРИТИЧНО!)
    - Реализовать пагинированный список заказов с фильтрами (статус, дата, клиент)
    - Реализовать детальный просмотр заказа (клиент, товары, итого, статус, timeline)
    - Реализовать обновление статуса заказа с отправкой email уведомлений
    - Реализовать генерацию PDF счета
    - Реализовать интеграцию с 1C экспортом
    - Реализовать статистику заказов (total orders, revenue, average order value)
    - Добавить проверки авторизации (@authorize('orders.view'), @authorize('orders.update'))
    - Создать JavaScript функцию openOrders() для ExtJS окна
    - _Требования: 14.1-14.10_

  - [ ]* 3.5 Написать property тесты для Order Manager
    - **Property 16: Order Status Transition Validity**
    - **Проверяет: Требования 14.3**
    - Переходы статусов заказа должны быть валидными (pending → processing → shipped → delivered)
    - **Property 17: Order Email Notification**
    - **Проверяет: Требования 14.4**
    - При изменении статуса заказа должно быть отправлено email уведомление

  - [ ]* 3.6 Написать unit тесты для OrderController
    - Тестировать index() возвращает пагинированный список
    - Тестировать фильтры (статус, дата, клиент)
    - Тестировать show() отображает детали заказа
    - Тестировать updateStatus() изменяет статус и отправляет email
    - Тестировать generateInvoice() создает PDF
    - Тестировать проверки авторизации
    - _Требования: 14.1-14.10_

  - [ ] 3.7 Создать модуль управления пользователями (User Manager)
    - Создать UserController с методами: index, create, store, edit, update, destroy, resetPassword
    - Создать Blade views: resources/views/admin/users/index.blade.php, create.blade.php, edit.blade.php
    - Использовать @push('scripts') и @push('styles') для подключения ресурсов (КРИТИЧНО!)
    - Реализовать пагинированный список пользователей с поиском и фильтрами
    - Реализовать форму создания/редактирования пользователя
    - Реализовать назначение ролей (admin, manager, editor, viewer, customer)
    - Реализовать изменение статуса пользователя (active, inactive, banned)
    - Реализовать сброс пароля
    - Реализовать просмотр истории заказов пользователя
    - Добавить проверки авторизации (@authorize('users.view'), @authorize('users.create'))
    - Логировать действия управления пользователями для audit trail
    - Создать JavaScript функцию openUsers() для ExtJS окна
    - _Требования: 15.1-15.10_

  - [ ]* 3.8 Написать property тесты для User Manager
    - **Property 18: User Role Assignment**
    - **Проверяет: Требования 15.5**
    - После назначения роли пользователю, пользователь должен иметь права этой роли
    - **Property 19: User Data Validation**
    - **Проверяет: Требования 15.9**
    - Невалидные данные пользователя (email, phone) должны быть отклонены

  - [ ]* 3.9 Написать unit тесты для UserController
    - Тестировать index() возвращает пагинированный список
    - Тестировать поиск и фильтры
    - Тестировать store() создает пользователя с валидными данными
    - Тестировать store() отклоняет невалидные данные
    - Тестировать update() обновляет пользователя
    - Тестировать destroy() удаляет пользователя
    - Тестировать назначение ролей
    - Тестировать сброс пароля
    - Тестировать проверки авторизации
    - _Требования: 15.1-15.10_

  - [ ] 3.10 Checkpoint - Убедиться, что все основные модули работают
    - Убедиться, что все тесты проходят, спросить пользователя, если возникли вопросы

- [ ] 4. Фаза 4: Дополнительные модули (Content, Settings, Reports)
  - [ ] 4.1 Создать модуль управления контентом (Content Manager)
    - Создать ContentController с методами для страниц, баннеров, каруселей
    - Создать Blade views для редактирования страниц, баннеров, каруселей
    - Использовать @push('scripts') и @push('styles') для подключения ресурсов (КРИТИЧНО!)
    - Реализовать редактор страниц (WYSIWYG или markdown)
    - Реализовать систему shortcode для секций ([section guid="..."])
    - Реализовать управление баннерами (загрузка, title, link, order, active)
    - Реализовать управление каруселями (main carousel, promo carousel)
    - Реализовать загрузку изображений с автоматическим ресайзом
    - Реализовать preview функциональность
    - Реализовать scheduling (publish/unpublish dates)
    - Реализовать SEO поля (meta title, description, keywords)
    - Создать JavaScript функцию openContent() для ExtJS окна
    - _Требования: 16.1-16.10_

  - [ ]* 4.2 Написать property тесты для Content Manager
    - **Property 20: Content Scheduling**
    - **Проверяет: Требования 16.7**
    - Контент с publish_date в будущем не должен быть виден на публичном сайте
    - **Property 21: Shortcode Processing**
    - **Проверяет: Требования 16.2**
    - Shortcode [section guid="X"] должен быть заменен на содержимое секции X

  - [ ] 4.3 Создать модуль управления настройками (Settings Manager)
    - Создать SettingsController с методами для разных групп настроек
    - Создать Blade views для настроек (general, email, payment, shipping, tax, SEO)
    - Использовать @push('scripts') и @push('styles') для подключения ресурсов (КРИТИЧНО!)
    - Реализовать валидацию настроек (email format, URL format, numeric ranges)
    - Реализовать кэширование настроек для производительности
    - Реализовать import/export настроек (JSON format)
    - Создать artisan команду admin:settings:clear для очистки кэша
    - Создать JavaScript функцию openSettings() для ExtJS окна
    - _Требования: 17.1-17.10_

  - [ ]* 4.4 Написать property тесты для Settings Manager
    - **Property 22: Settings Cache Consistency**
    - **Проверяет: Требования 17.9, 17.10**
    - После обновления настройки, кэш должен быть инвалидирован
    - **Property 23: Settings Validation**
    - **Проверяет: Требования 17.7**
    - Невалидные настройки должны быть отклонены с понятными сообщениями

  - [ ] 4.5 Создать модуль статистики и отчетов (Statistics & Reports)
    - Создать ReportsController с методами для разных типов отчетов
    - Создать Blade views для dashboard, sales, products, customers
    - Использовать @push('scripts') и @push('styles') для подключения ресурсов (КРИТИЧНО!)
    - Реализовать статистику продаж (daily, weekly, monthly, yearly)
    - Реализовать графики выручки (line chart, bar chart)
    - Реализовать топ-продающиеся товары (по количеству, по выручке)
    - Реализовать статистику клиентов (новые, возвращающиеся)
    - Реализовать фильтрацию по датам
    - Реализовать экспорт отчетов (CSV, Excel, PDF)
    - Реализовать dashboard виджеты для ключевых метрик
    - Создать JavaScript функцию openReports() для ExtJS окна
    - _Требования: 18.1-18.10_

  - [ ]* 4.6 Написать property тесты для Reports
    - **Property 24: Report Data Consistency**
    - **Проверяет: Требования 18.1, 18.2**
    - Сумма продаж в отчете должна соответствовать сумме в таблице orders
    - **Property 25: Date Range Filtering**
    - **Проверяет: Требования 18.6**
    - Отчет с фильтром по датам должен включать только данные в указанном диапазоне

  - [ ] 4.7 Checkpoint - Убедиться, что дополнительные модули работают
    - Убедиться, что все тесты проходят, спросить пользователя, если возникли вопросы

- [ ] 5. Фаза 5: Package API, документация и финализация
  - [ ] 5.1 Создать Package API для кастомных модулей
    - Создать базовый AdminController для admin модулей
    - Создать базовый AdminModel с общей функциональностью
    - Создать helper методы для операций с БД
    - Создать helper методы для файловых операций
    - Создать helper методы для проверки ACL
    - Создать event систему для lifecycle пакетов (installed, uninstalled, updated)
    - Создать hook систему для расширения core функциональности
    - Создать artisan команду admin:package:make для генерации шаблона пакета
    - _Требования: 19.1-19.10_

  - [ ]* 5.2 Написать property тесты для Package API
    - **Property 26: Package Event Firing**
    - **Проверяет: Требования 19.7**
    - При установке пакета должен быть вызван event PackageInstalled

  - [ ] 5.3 Создать документацию для разработчиков
    - Создать руководство по архитектуре системы (system components, data flow)
    - Создать руководство по созданию пакетов (step-by-step tutorial)
    - Создать руководство по миграции legacy пакетов (legacy to Laravel conversion)
    - Создать API reference для всех сервисов и моделей
    - Создать примеры кода для типичных задач
    - Создать troubleshooting guide (частые проблемы и решения)
    - Создать сравнительную таблицу (legacy vs Laravel подходы)
    - _Требования: 21.1-21.10_

  - [ ] 5.4 Реализовать стратегию развертывания
    - Создать feature flags для включения/выключения новой админ-панели
    - Создать документацию по rollback процедуре
    - Создать smoke tests для post-deployment валидации
    - Создать мониторинг и алертинг для ошибок
    - Создать deployment checklist для operations команды
    - _Требования: 24.1-24.10_

  - [ ] 5.5 Провести тестирование производительности
    - Проверить загрузку dashboard (должна быть < 2 секунд)
    - Проверить загрузку списка товаров 100 items (должна быть < 1 секунды)
    - Проверить загрузку списка заказов 100 items (должна быть < 1 секунды)
    - Проверить пагинацию для больших датасетов (10,000+ записей)
    - Оптимизировать запросы с N+1 проблемами (использовать eager loading)
    - Проверить кэширование часто используемых данных
    - _Требования: 22.1-22.10_

  - [ ] 5.6 Провести проверку безопасности
    - Проверить аутентификацию для всех admin маршрутов
    - Проверить CSRF protection для всех форм
    - Проверить SQL injection prevention (parameterized queries)
    - Проверить XSS prevention (output escaping)
    - Проверить логирование всех admin действий для audit trail
    - _Требования: 23.1-23.10_

  - [ ] 5.7 Final checkpoint - Полная проверка системы
    - Убедиться, что все тесты проходят (unit, property, integration)
    - Проверить покрытие кода (минимум 80%)
    - Проверить производительность (dashboard < 2s, списки < 1s)
    - Проверить безопасность (CSRF, SQL injection, XSS protection)
    - Провести smoke тесты всех модулей
    - Спросить пользователя, если возникли вопросы

## Примечания

- Задачи, помеченные `*`, являются опциональными и могут быть пропущены для более быстрого MVP
- Каждая задача ссылается на конкретные требования для отслеживаемости
- Checkpoints обеспечивают инкрементальную валидацию
- Property тесты проверяют универсальные свойства корректности
- Unit тесты проверяют конкретные примеры и граничные случаи
- Минимальное покрытие кода: 80%

## КРИТИЧЕСКИЕ ПРАВИЛА

### Blade шаблоны
- **ВСЕГДА** используйте `@push('scripts')` и `@push('styles')`
- **НИКОГДА** не используйте `@section('scripts')` или `@section('styles')`
- Layout использует `@stack('scripts')` и `@stack('styles')`, поэтому `@section` не будет работать

### База данных
- **НЕ СОЗДАВАЙТЕ** новые таблицы - они уже существуют
- **ИЗУЧИТЕ** существующую схему БД в legacy системе
- **СОЗДАЙТЕ** Laravel модели для существующих таблиц

### ExtJS Desktop
- **НЕ ИЗМЕНЯЙТЕ** ExtJS Desktop shell - он уже реализован
- **ОБНОВИТЕ** только API endpoints и пути к модулям
- **СОХРАНИТЕ** все UI компоненты без изменений

## Технологический стек

- **Backend**: Laravel 12, PHP 8.5, MySQL 10.4+
- **Frontend (Desktop Shell)**: ExtJS 4.2.1, Material Icons (БЕЗ ИЗМЕНЕНИЙ)
- **Frontend (Window Content)**: Laravel Blade, HTML/CSS, jQuery
- **Архитектура**: Iframe (ExtJS окна содержат iframe с Laravel Blade)
- **Тестирование**: PHPUnit, Laravel Dusk (для browser тесты)
