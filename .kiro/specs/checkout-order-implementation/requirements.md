# Requirements Document: Checkout Order Implementation

## Introduction

Данный документ описывает требования к модулю оформления заказа (checkout) для Laravel 12 приложения. Модуль обеспечивает полный цикл оформления заказа: отображение формы с товарами из корзины, валидацию данных получателя, создание заказа в БД, очистку корзины и редирект на страницу благодарности. Сохраняется интеграция с KnockoutJS для отображения корзины и Яндекс.Картами для выбора пункта выдачи.

## Glossary

- **Order_System** - система оформления заказов
- **Checkout_Controller** - HTTP контроллер для обработки запросов оформления заказа
- **Order_Service** - сервис бизнес-логики создания заказа
- **Cart_Service** - сервис работы с корзиной пользователя
- **Order** - заказ в системе (запись в таблице orders)
- **Order_Position** - позиция заказа (товар в заказе, запись в таблице order_positions)
- **Order_Number** - уникальный числовой идентификатор заказа (order_num)
- **Order_Code** - строковый код заказа в формате base36 (например, "1A2-B3C-D4E")
- **Cart** - корзина товаров пользователя (хранится в сессии)
- **Recipient** - получатель заказа
- **Guest_User** - неавторизованный пользователь
- **Registered_User** - авторизованный пользователь
- **CSRF_Token** - токен защиты от CSRF атак
- **KnockoutJS_ViewModel** - клиентская модель данных для отображения корзины
- **Yandex_Maps_API** - API Яндекс.Карт для выбора пункта выдачи

## Requirements

### Requirement 1: Отображение формы оформления заказа

**User Story:** Как пользователь, я хочу видеть форму оформления заказа с товарами из корзины, чтобы заполнить данные получателя и завершить покупку.

#### Acceptance Criteria

1. WHEN пользователь переходит на страницу /checkout, THE Checkout_Controller SHALL отобразить форму оформления заказа
2. WHEN форма отображается, THE Order_System SHALL загрузить данные корзины из сессии
3. WHEN корзина пуста, THE Order_System SHALL перенаправить пользователя на страницу корзины с сообщением "Корзина пуста"
4. WHEN форма отображается, THE Order_System SHALL передать данные корзины в формате JSON для KnockoutJS_ViewModel
5. WHEN форма отображается, THE Order_System SHALL отобразить список товаров с названиями, ценами, количеством и итоговой суммой
6. WHEN форма отображается, THE Order_System SHALL отобразить поля для ввода данных получателя (ФИО, телефон, email, комментарий)
7. WHEN форма отображается, THE Order_System SHALL отобразить опции выбора способа доставки (pickup, courier, express)
8. WHEN форма отображается, THE Order_System SHALL отобразить опции выбора способа оплаты (card, cash, sberpay)

### Requirement 2: Валидация данных формы

**User Story:** Как система, я хочу валидировать данные формы заказа, чтобы предотвратить создание заказов с некорректными данными.

#### Acceptance Criteria

1. WHEN пользователь отправляет форму, THE Order_System SHALL проверить, что поле "ФИО получателя" заполнено
2. WHEN пользователь отправляет форму, THE Order_System SHALL проверить, что поле "Телефон получателя" заполнено
3. WHEN пользователь указывает email, THE Order_System SHALL проверить, что email имеет корректный формат
4. WHEN поле "ФИО получателя" пустое, THE Order_System SHALL вернуть ошибку валидации "ФИО получателя обязательно"
5. WHEN поле "Телефон получателя" пустое, THE Order_System SHALL вернуть ошибку валидации "Телефон получателя обязателен"
6. WHEN email имеет некорректный формат, THE Order_System SHALL вернуть ошибку валидации "Некорректный формат email"
7. WHEN пользователь отправляет форму, THE Order_System SHALL проверить, что выбран способ доставки
8. WHEN пользователь отправляет форму, THE Order_System SHALL проверить, что выбран способ оплаты

### Requirement 3: Генерация уникального номера заказа

**User Story:** Как система, я хочу генерировать уникальные номера заказов, чтобы каждый заказ имел уникальный идентификатор.

#### Acceptance Criteria

1. WHEN создается новый заказ, THE Order_Service SHALL сгенерировать уникальный Order_Number
2. WHEN создается новый заказ, THE Order_Service SHALL сгенерировать уникальный Order_Code
3. WHEN генерируется Order_Number, THE Order_Service SHALL вычислить количество секунд с базовой даты (2025-12-10)
4. WHEN генерируется Order_Code, THE Order_Service SHALL преобразовать Order_Number в base36 формат
5. WHEN генерируется Order_Code, THE Order_Service SHALL разбить base36 строку на группы по 3 символа с разделителем "-"
6. WHEN генерируется Order_Code, THE Order_Service SHALL преобразовать все символы в верхний регистр
7. WHEN генерируется Order_Number, THE Order_Service SHALL добавить случайную задержку (1-1000000 микросекунд) для обеспечения уникальности

### Requirement 4: Создание позиций заказа

**User Story:** Как система, я хочу создавать записи позиций заказа для каждого товара в корзине, чтобы сохранить информацию о заказанных товарах.

#### Acceptance Criteria

1. WHEN создается заказ, THE Order_Service SHALL создать запись Order_Position для каждого выбранного товара в корзине
2. WHEN создается Order_Position, THE Order_Service SHALL получить данные товара из таблицы products
3. WHEN товар не найден в БД, THE Order_Service SHALL пропустить этот товар и продолжить обработку
4. WHEN создается Order_Position, THE Order_Service SHALL сохранить Order_Number и Order_Code
5. WHEN создается Order_Position, THE Order_Service SHALL сохранить количество товара (pieces, amount)
6. WHEN создается Order_Position, THE Order_Service SHALL сохранить цену за единицу (cost, piece_cost, bill)
7. WHEN создается Order_Position, THE Order_Service SHALL рассчитать сумму позиции (sum = cost * pieces)
8. WHEN создается Order_Position, THE Order_Service SHALL сохранить артикул (art), ID товара (guid), название (title)
9. WHEN создается Order_Position, THE Order_Service SHALL сохранить вес единицы товара (weight, piece_weight)

### Requirement 5: Расчет итоговых сумм заказа

**User Story:** Как система, я хочу корректно рассчитывать итоговые суммы заказа, чтобы пользователь видел правильную сумму к оплате.

#### Acceptance Criteria

1. WHEN создается заказ, THE Order_Service SHALL рассчитать полную сумму товаров (full_sum) как сумму всех позиций
2. WHEN создается заказ, THE Order_Service SHALL получить сумму скидки (discount_sum) из сессии
3. WHEN сумма скидки не указана в сессии, THE Order_Service SHALL установить discount_sum равной 0
4. WHEN создается заказ, THE Order_Service SHALL рассчитать сумму к оплате (pay_sum = full_sum - discount_sum)
5. WHEN pay_sum получается отрицательной, THE Order_Service SHALL установить pay_sum равной 0
6. WHEN создается заказ, THE Order_Service SHALL сохранить все три суммы (full_sum, discount_sum, pay_sum) в таблице orders

### Requirement 6: Создание записи заказа

**User Story:** Как система, я хочу создавать запись заказа в БД, чтобы сохранить информацию о заказе для дальнейшей обработки.

#### Acceptance Criteria

1. WHEN создается заказ, THE Order_Service SHALL создать запись в таблице orders с уникальным Order_Number
2. WHEN создается запись заказа, THE Order_Service SHALL сохранить Order_Code
3. WHEN создается запись заказа, THE Order_Service SHALL сохранить данные получателя (name, phone, email, comment_user)
4. WHEN создается запись заказа, THE Order_Service SHALL сохранить способ доставки и оплаты
5. WHEN создается запись заказа, THE Order_Service SHALL сохранить IP адрес клиента
6. WHEN создается запись заказа, THE Order_Service SHALL сохранить User Agent браузера
7. WHEN пользователь авторизован, THE Order_Service SHALL сохранить user_id из сессии
8. WHEN пользователь не авторизован (Guest_User), THE Order_Service SHALL установить user_id равным 0
9. WHEN создается запись заказа, THE Order_Service SHALL установить временные метки created_at и updated_at

### Requirement 7: Очистка корзины после создания заказа

**User Story:** Как пользователь, я хочу, чтобы корзина очищалась после успешного оформления заказа, чтобы не видеть уже заказанные товары.

#### Acceptance Criteria

1. WHEN заказ успешно создан, THE Cart_Service SHALL очистить корзину в сессии
2. WHEN корзина очищается, THE Cart_Service SHALL удалить все товары из SESSION['cart']['items']
3. WHEN корзина очищается, THE Cart_Service SHALL удалить промокод из SESSION['cart_promocode']
4. WHEN корзина очищается, THE Cart_Service SHALL удалить скидку из SESSION['cart_discount']
5. WHEN корзина очищается, THE Cart_Service SHALL удалить выбранные товары из SESSION['cart_selected']

### Requirement 8: AJAX обработка создания заказа

**User Story:** Как пользователь, я хочу оформлять заказ без перезагрузки страницы, чтобы процесс был быстрым и удобным.

#### Acceptance Criteria

1. WHEN пользователь нажимает кнопку "Оформить заказ", THE Order_System SHALL отправить AJAX POST запрос на /checkout
2. WHEN AJAX запрос отправляется, THE Order_System SHALL включить CSRF_Token в заголовки запроса
3. WHEN заказ успешно создан, THE Checkout_Controller SHALL вернуть JSON ответ с success: true
4. WHEN заказ успешно создан, THE Checkout_Controller SHALL включить Order_Number в JSON ответ
5. WHEN заказ успешно создан, THE Checkout_Controller SHALL включить URL редиректа (/thankyoupage/) в JSON ответ
6. WHEN произошла ошибка при создании заказа, THE Checkout_Controller SHALL вернуть JSON ответ с success: false
7. WHEN произошла ошибка, THE Checkout_Controller SHALL включить сообщение об ошибке в JSON ответ
8. WHEN получен успешный JSON ответ, THE Order_System SHALL перенаправить пользователя на страницу благодарности

### Requirement 9: Интеграция с KnockoutJS

**User Story:** Как пользователь, я хочу видеть динамическое обновление корзины на странице оформления заказа, чтобы видеть актуальные данные.

#### Acceptance Criteria

1. WHEN форма оформления заказа загружается, THE Order_System SHALL передать данные корзины в формате JSON
2. WHEN данные корзины передаются, THE Order_System SHALL включить информацию о каждом товаре (id, name, cost, quantity)
3. WHEN данные корзины передаются, THE Order_System SHALL включить итоговую сумму корзины
4. WHEN KnockoutJS_ViewModel инициализируется, THE Order_System SHALL отобразить список товаров с актуальными данными
5. WHEN пользователь изменяет количество товара, THE KnockoutJS_ViewModel SHALL пересчитать итоговую сумму

### Requirement 10: Безопасность и защита от атак

**User Story:** Как система, я хочу защитить процесс оформления заказа от атак, чтобы обеспечить безопасность данных пользователей.

#### Acceptance Criteria

1. WHEN пользователь отправляет форму, THE Order_System SHALL проверить наличие валидного CSRF_Token
2. WHEN CSRF_Token невалиден, THE Order_System SHALL отклонить запрос с ошибкой 419
3. WHEN создается запись заказа, THE Order_Service SHALL экранировать все пользовательские данные перед сохранением в БД
4. WHEN получается IP адрес клиента, THE Order_Service SHALL использовать безопасный метод получения IP (учитывая прокси)
5. WHEN сохраняется User Agent, THE Order_Service SHALL ограничить длину строки до 255 символов

### Requirement 11: Обработка ошибок

**User Story:** Как пользователь, я хочу видеть понятные сообщения об ошибках, чтобы понимать, что пошло не так при оформлении заказа.

#### Acceptance Criteria

1. WHEN происходит ошибка валидации, THE Order_System SHALL вернуть JSON с описанием ошибки
2. WHEN корзина пуста при попытке создания заказа, THE Order_System SHALL вернуть ошибку "Корзина пуста"
3. WHEN происходит ошибка БД при создании заказа, THE Order_System SHALL вернуть ошибку "Ошибка при создании заказа"
4. WHEN происходит любая ошибка, THE Order_System SHALL логировать детали ошибки для отладки
5. WHEN происходит ошибка, THE Order_System SHALL откатить транзакцию БД для сохранения целостности данных

### Requirement 12: Производительность

**User Story:** Как пользователь, я хочу, чтобы процесс оформления заказа был быстрым, чтобы не ждать долго.

#### Acceptance Criteria

1. WHEN пользователь открывает страницу /checkout, THE Order_System SHALL загрузить страницу за время не более 2 секунд
2. WHEN пользователь отправляет форму заказа, THE Order_System SHALL обработать запрос за время не более 3 секунд
3. WHEN создается заказ, THE Order_Service SHALL использовать транзакции БД для минимизации времени блокировки
4. WHEN загружаются данные товаров, THE Order_Service SHALL использовать JOIN запросы вместо множественных SELECT

### Requirement 13: Совместимость с существующей системой

**User Story:** Как разработчик, я хочу, чтобы новый модуль был совместим с существующей структурой БД, чтобы не нарушить работу других модулей.

#### Acceptance Criteria

1. THE Order_System SHALL использовать существующую таблицу orders без изменения структуры
2. THE Order_System SHALL использовать существующую таблицу order_positions без изменения структуры
3. WHEN создается заказ, THE Order_Service SHALL сохранять данные в том же формате, что и legacy система
4. WHEN генерируется Order_Number, THE Order_Service SHALL использовать тот же алгоритм, что и legacy система (секунды с 2025-12-10)
5. WHEN генерируется Order_Code, THE Order_Service SHALL использовать тот же формат base36, что и legacy система

### Requirement 14: Интеграция с Яндекс.Картами

**User Story:** Как пользователь, я хочу выбирать пункт выдачи на карте, чтобы удобно указать место получения заказа.

#### Acceptance Criteria

1. WHERE способ доставки "pickup" выбран, THE Order_System SHALL отобразить карту Яндекс.Карт
2. WHERE способ доставки "pickup" выбран, THE Order_System SHALL отобразить список доступных пунктов выдачи на карте
3. WHEN пользователь выбирает пункт выдачи на карте, THE Order_System SHALL сохранить ID пункта выдачи
4. WHEN пользователь выбирает пункт выдачи, THE Order_System SHALL отобразить адрес и время работы пункта
5. WHERE способ доставки "courier" или "express" выбран, THE Order_System SHALL скрыть карту и показать поле для ввода адреса

### Requirement 15: Адаптивность интерфейса

**User Story:** Как пользователь мобильного устройства, я хочу удобно оформлять заказ с телефона, чтобы не испытывать трудностей с интерфейсом.

#### Acceptance Criteria

1. WHEN пользователь открывает страницу /checkout на мобильном устройстве, THE Order_System SHALL отобразить адаптивную версию формы
2. WHEN форма отображается на экране шириной менее 768px, THE Order_System SHALL расположить поля формы вертикально
3. WHEN форма отображается на мобильном устройстве, THE Order_System SHALL увеличить размер кнопок для удобства нажатия
4. WHEN карта Яндекс.Карт отображается на мобильном устройстве, THE Order_System SHALL адаптировать размер карты под экран

