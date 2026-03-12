# Implementation Plan: Checkout Order Implementation

## Overview

Реализация модуля оформления заказа для Laravel 12. Включает создание контроллера, сервиса, Blade шаблонов, JavaScript логики и интеграции с KnockoutJS и Яндекс.Картами. Все товары берутся из корзины, создается заказ в БД, корзина очищается, пользователь перенаправляется на страницу благодарности.

## Tasks

- [x] 1. Создать OrderService с базовой структурой
  - Создать файл `app/Services/OrderService.php`
  - Реализовать конструктор с инъекцией зависимостей
  - Добавить приватные методы-заглушки: `validateOrderData()`, `generateOrderNumber()`, `createOrderPositions()`, `createOrderRecord()`, `getClientIp()`
  - Все комментарии должны быть на русском языке
  - _Requirements: 3.1, 6.1, 13.3_

- [ ]* 1.1 Написать property-тест для генерации уникальных номеров заказов
  - **Property 1: Уникальность номеров заказов**
  - **Validates: Requirements 3.1, 3.2, 3.7**
  - Проверить, что при множественных вызовах `generateOrderNumber()` все номера уникальны

- [x] 2. Реализовать метод generateOrderNumber() в OrderService
  - [x] 2.1 Реализовать вычисление секунд с базовой даты (2025-12-10)
    - Использовать DateTime для вычисления интервала
    - Преобразовать интервал в секунды
    - _Requirements: 3.3, 13.4_
  
  - [x] 2.2 Реализовать генерацию order_code в формате base36
    - Преобразовать order_num в base36 через `base_convert()`
    - Разбить строку на группы по 3 символа с разделителем "-"
    - Преобразовать в верхний регистр
    - _Requirements: 3.4, 3.5, 3.6, 13.5_
  
  - [x] 2.3 Добавить случайную задержку для уникальности
    - Использовать `usleep(rand(1, 1000000))`
    - _Requirements: 3.7_

- [ ]* 2.4 Написать unit-тесты для generateOrderNumber()
  - Проверить формат order_code (паттерн с дефисами)
  - Проверить, что order_num положительное число
  - Проверить корректность преобразования base36
  - _Requirements: 3.1, 3.2, 3.4, 3.5, 3.6_

- [x] 3. Реализовать метод validateOrderData() в OrderService
  - Проверить, что name не пустое
  - Проверить, что phone не пустое
  - Проверить формат email (если указан)
  - Выбрасывать ValidationException с русскими сообщениями об ошибках
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6_

- [ ]* 3.1 Написать unit-тесты для validateOrderData()
  - Тест: пустое имя выбрасывает исключение
  - Тест: пустой телефон выбрасывает исключение
  - Тест: некорректный email выбрасывает исключение
  - Тест: валидные данные проходят без исключений
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6_

- [x] 4. Реализовать метод createOrderPositions() в OrderService
  - [x] 4.1 Получить данные товара из БД (products + prices)
    - Использовать LEFT JOIN с таблицей prices (price_type_id = '000000002')
    - Если товар не найден - пропустить и продолжить
    - _Requirements: 4.2, 4.3, 12.4_
  
  - [x] 4.2 Создать запись в order_positions для каждого товара
    - Сохранить order_num, order_code, guid, title, art
    - Сохранить pieces, amount (количество)
    - Сохранить cost, piece_cost, bill (цены)
    - Рассчитать sum = cost * pieces
    - Сохранить weight, piece_weight
    - _Requirements: 4.1, 4.4, 4.5, 4.6, 4.7, 4.8, 4.9_
  
  - [x] 4.3 Накапливать totalSum для всех позиций
    - Возвращать итоговую сумму всех позиций
    - _Requirements: 5.1_

- [ ]* 4.4 Написать property-тест для createOrderPositions()
  - **Property 2: Корректность расчета сумм позиций**
  - **Validates: Requirements 4.7, 5.1**
  - Проверить, что sum = cost * pieces для каждой позиции
  - Проверить, что totalSum = сумма всех позиций

- [x] 5. Реализовать метод createOrderRecord() в OrderService
  - [x] 5.1 Рассчитать итоговые суммы заказа
    - full_sum = totalSum из createOrderPositions()
    - discount_sum = SESSION['cart_discount'] ?? 0
    - pay_sum = full_sum - discount_sum (минимум 0)
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6_
  
  - [x] 5.2 Создать запись в таблице orders
    - Сохранить id (order_num), order_code
    - Сохранить full_sum, discount_sum, pay_sum
    - Сохранить name, phone, email, comment_user
    - Сохранить user_id (из сессии или 0 для гостей)
    - Сохранить ip (через getClientIp()), user_agent
    - Установить created_at, updated_at
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 6.6, 6.7, 6.8, 6.9_

- [ ]* 5.3 Написать unit-тесты для createOrderRecord()
  - Тест: pay_sum не может быть отрицательной
  - Тест: user_id = 0 для неавторизованных пользователей
  - Тест: корректное сохранение всех полей
  - _Requirements: 5.5, 6.8_

- [x] 6. Реализовать метод getClientIp() в OrderService
  - Проверить заголовки HTTP_X_FORWARDED_FOR, HTTP_CLIENT_IP
  - Использовать REMOTE_ADDR как fallback
  - Обеспечить безопасность (защита от подделки IP)
  - _Requirements: 6.5, 10.4_

- [x] 7. Реализовать основной метод createOrder() в OrderService
  - [x] 7.1 Вызвать validateOrderData() для проверки данных
    - _Requirements: 2.1, 2.2, 2.3_
  
  - [x] 7.2 Обернуть создание заказа в транзакцию БД
    - Использовать DB::transaction()
    - _Requirements: 11.5, 12.3_
  
  - [x] 7.3 Вызвать generateOrderNumber()
    - _Requirements: 3.1, 3.2_
  
  - [x] 7.4 Вызвать createOrderPositions() для создания позиций
    - Передать cartItems с фильтрацией по selected = true
    - _Requirements: 4.1_
  
  - [x] 7.5 Вызвать createOrderRecord() для создания заказа
    - Передать totalSum из createOrderPositions()
    - _Requirements: 6.1_
  
  - [x] 7.6 Вернуть результат с success, order_num, redirect
    - _Requirements: 8.3, 8.4, 8.5_
  
  - [x] 7.7 Обработать исключения и логировать ошибки
    - Использовать Log::error() для записи деталей
    - Откатить транзакцию при ошибке
    - _Requirements: 11.3, 11.4, 11.5_

- [ ]* 7.8 Написать integration-тест для createOrder()
  - Тест: полный цикл создания заказа с корзиной из 3 товаров
  - Проверить создание записей в orders и order_positions
  - Проверить корректность всех сумм
  - _Requirements: 3.1, 4.1, 5.1, 6.1_

- [x] 8. Checkpoint - Убедиться, что OrderService работает корректно
  - Ensure all tests pass, ask the user if questions arise.

- [x] 9. Создать OrderController с методами checkout() и placeOrder()
  - [x] 9.1 Создать файл `app/Http/Controllers/OrderController.php`
    - Добавить конструктор с инъекцией OrderService и CartService
    - Все комментарии на русском языке
    - _Requirements: 1.1_
  
  - [x] 9.2 Реализовать метод checkout() для GET /checkout
    - Получить данные корзины через CartService::getCartData()
    - Проверить, что корзина не пуста (редирект на /cart если пуста)
    - Вернуть view 'checkout.index' с данными корзины
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 12.1_
  
  - [x] 9.3 Реализовать метод placeOrder() для POST /checkout
    - Валидировать входные данные через Request::validate()
    - Проверить, что корзина не пуста
    - Вызвать OrderService::createOrder()
    - Вызвать CartService::clearCart() после успешного создания
    - Вернуть JSON ответ с success, order_num, redirect
    - Обработать исключения и вернуть JSON с ошибкой
    - _Requirements: 8.1, 8.3, 8.4, 8.5, 8.6, 8.7, 11.1, 11.2, 11.3, 12.2_

- [ ]* 9.4 Написать unit-тесты для OrderController
  - Тест: checkout() редиректит на /cart если корзина пуста
  - Тест: placeOrder() возвращает ошибку при невалидных данных
  - Тест: placeOrder() возвращает success при корректных данных
  - _Requirements: 1.3, 2.4, 2.5, 8.3, 8.6_

- [x] 10. Добавить маршруты в routes/web.php
  - Добавить GET /checkout -> OrderController@checkout
  - Добавить POST /checkout -> OrderController@placeOrder
  - Применить middleware 'web' для CSRF защиты
  - _Requirements: 1.1, 8.1, 10.1_

- [x] 11. Создать Blade шаблон checkout/index.blade.php
  - [x] 11.1 Создать файл `resources/views/checkout/index.blade.php`
    - Использовать @extends('layouts.app')
    - Добавить @section('content')
    - _Requirements: 1.1_
  
  - [x] 11.2 Добавить форму с полями получателя
    - Поле "ФИО получателя" (recipientName) - обязательное
    - Поле "Телефон получателя" (recipientPhone) - обязательное
    - Поле "Email" (recipientEmail) - необязательное
    - Поле "Комментарий к заказу" (orderComment) - необязательное
    - _Requirements: 1.6_
  
  - [x] 11.3 Добавить радио-кнопки выбора доставки
    - Опции: pickup (Самовывоз), courier (Курьер), express (Экспресс)
    - _Requirements: 1.7_
  
  - [x] 11.4 Добавить радио-кнопки выбора оплаты
    - Опции: card (Картой), cash (Наличными), sberpay (СберПэй)
    - _Requirements: 1.8_
  
  - [x] 11.5 Добавить контейнер для отображения корзины через KnockoutJS
    - Использовать data-bind для интеграции с cart-viewmodel.js
    - Отобразить список товаров с названиями, ценами, количеством
    - Отобразить итоговую сумму
    - _Requirements: 1.5, 9.1, 9.2, 9.3, 9.4_
  
  - [x] 11.6 Добавить кнопку "Оформить заказ" с id="checkoutBtn"
    - _Requirements: 8.1_
  
  - [x] 11.7 Добавить скрытое поле с CSRF токеном
    - Использовать @csrf или meta-тег
    - _Requirements: 10.1_
  
  - [x] 11.8 Добавить контейнер для карты Яндекс.Карт (скрыт по умолчанию)
    - Показывать только при выборе доставки "pickup"
    - _Requirements: 14.1, 14.2_

- [x] 12. Создать CSS стили для страницы оформления заказа
  - Создать файл `public/assets/sfera/css/checkout.css`
  - Добавить стили для формы, полей ввода, кнопок
  - Добавить адаптивные стили для мобильных устройств (медиа-запросы для <768px)
  - Увеличить размер кнопок на мобильных устройствах
  - Расположить поля вертикально на узких экранах
  - _Requirements: 15.1, 15.2, 15.3_

- [x] 13. Подключить стили в checkout/index.blade.php
  - Использовать @push('styles') для подключения checkout.css
  - НЕ использовать @section('styles') - только @push!
  - _Requirements: 1.1_

- [x] 14. Создать JavaScript файл checkout.js с AJAX логикой
  - [x] 14.1 Создать файл `public/assets/sfera/js/checkout.js`
    - Все комментарии на русском языке
    - _Requirements: 8.1_
  
  - [x] 14.2 Добавить обработчик клика на кнопку "Оформить заказ"
    - Собрать данные формы (recipientName, recipientPhone, recipientEmail, orderComment, delivery, payment)
    - Получить CSRF токен из meta-тега
    - _Requirements: 8.1, 8.2_
  
  - [x] 14.3 Реализовать отправку AJAX POST запроса на /checkout
    - Использовать fetch() API
    - Добавить заголовок X-Requested-With: XMLHttpRequest
    - Добавить CSRF токен в FormData
    - _Requirements: 8.1, 8.2_
  
  - [x] 14.4 Обработать успешный ответ (success: true)
    - Выполнить редирект на URL из response.redirect
    - _Requirements: 8.3, 8.4, 8.5, 8.8_
  
  - [x] 14.5 Обработать ошибку (success: false)
    - Показать уведомление с сообщением об ошибке
    - Использовать существующую функцию showNotification()
    - _Requirements: 8.6, 8.7, 11.1_
  
  - [x] 14.6 Обработать сетевые ошибки (catch)
    - Показать общее сообщение "Ошибка при оформлении заказа"
    - _Requirements: 11.3_

- [x] 15. Добавить интеграцию с Яндекс.Картами
  - [x] 15.1 Добавить обработчик изменения способа доставки
    - При выборе "pickup" - показать карту
    - При выборе "courier" или "express" - скрыть карту, показать поле адреса
    - _Requirements: 14.1, 14.5_
  
  - [x] 15.2 Инициализировать карту Яндекс.Карт при выборе "pickup"
    - Загрузить API Яндекс.Карт
    - Отобразить пункты выдачи на карте
    - _Requirements: 14.1, 14.2_
  
  - [x] 15.3 Добавить обработчик выбора пункта выдачи
    - Сохранить ID выбранного пункта
    - Отобразить адрес и время работы
    - _Requirements: 14.3, 14.4_
  
  - [x] 15.4 Адаптировать размер карты для мобильных устройств
    - _Requirements: 15.4_

- [x] 16. Подключить скрипты в checkout/index.blade.php
  - Использовать @push('scripts') для подключения checkout.js
  - Использовать @push('scripts') для подключения cart-viewmodel.js (если еще не подключен)
  - Использовать @push('head') для подключения API Яндекс.Карт
  - НЕ использовать @section('scripts') - только @push!
  - _Requirements: 8.1, 9.1, 14.1_

- [x] 17. Обновить CartService::clearCart() для очистки всех данных корзины
  - Очистить SESSION['cart']['items']
  - Очистить SESSION['cart_promocode']
  - Очистить SESSION['cart_discount']
  - Очистить SESSION['cart_selected']
  - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_

- [ ]* 17.1 Написать unit-тест для CartService::clearCart()
  - Проверить, что все ключи сессии удалены
  - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_

- [x] 18. Создать страницу благодарности thankyoupage/index.blade.php
  - Создать файл `resources/views/thankyoupage/index.blade.php`
  - Отобразить сообщение "Спасибо за заказ!"
  - Отобразить номер заказа (если передан в сессии)
  - Добавить кнопку "Вернуться на главную"
  - _Requirements: 8.8_

- [x] 19. Добавить маршрут для страницы благодарности
  - Добавить GET /thankyoupage -> контроллер для отображения thankyoupage
  - _Requirements: 8.8_

- [x] 20. Checkpoint - Проверить полную интеграцию
  - Ensure all tests pass, ask the user if questions arise.

- [x] 21. Добавить экранирование пользовательских данных
  - Проверить, что все данные экранируются перед сохранением в БД
  - Использовать параметризованные запросы (Laravel Query Builder делает это автоматически)
  - Ограничить длину User Agent до 255 символов
  - _Requirements: 10.3, 10.5_

- [ ]* 21.1 Написать security-тест для проверки экранирования
  - Тест: SQL injection в поле name не выполняется
  - Тест: XSS в поле comment_user экранируется
  - _Requirements: 10.3_

- [x] 22. Добавить логирование ошибок
  - В OrderService::createOrder() добавить try-catch с Log::error()
  - Логировать детали ошибки (message, stack trace, input data)
  - _Requirements: 11.4_

- [x] 23. Финальная проверка производительности
  - Проверить, что страница /checkout загружается за <2 секунд
  - Проверить, что POST /checkout обрабатывается за <3 секунд
  - Оптимизировать запросы к БД при необходимости
  - _Requirements: 12.1, 12.2, 12.3, 12.4_

## Notes

- Задачи, помеченные `*`, являются опциональными и могут быть пропущены для быстрого MVP
- Все комментарии в коде должны быть на русском языке
- Использовать @push для подключения скриптов и стилей (НЕ @section)
- Сохранить интеграцию с KnockoutJS для отображения корзины
- Использовать существующую структуру БД без изменений
- Все SQL запросы через Laravel Query Builder для безопасности
- Checkpoint задачи обеспечивают инкрементальную валидацию
