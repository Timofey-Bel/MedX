# Design Document: Checkout Order Migration

## Overview

Миграция критичного функционала оформления заказа с legacy PHP/Smarty на Laravel 12. Включает форму оформления заказа, создание заказа с транзакционной целостностью, и страницу благодарности. Без этого функционала интернет-магазин "Сфера" не работает.

## Architecture

```mermaid
graph TD
    A[User Browser] -->|GET /checkout| B[OrderController::checkout]
    B -->|Получить корзину| C[CartService]
    C -->|Данные корзины| B
    B -->|Render| D[checkout/index.blade.php]
    
    A -->|POST /checkout| E[OrderController::placeOrder]
    E -->|Валидация| F[PlaceOrderRequest]
    F -->|Валидные данные| E
    E -->|BEGIN TRANSACTION| G[Database]
    E -->|Создать заказ| G
    E -->|Создать позиции| G
    E -->|COMMIT| G
    E -->|Очистить корзину| C
    E -->|Сохранить в сессию| H[Session]
    E -->|Redirect| I[/thankyoupage]
    
    A -->|GET /thankyoupage| J[OrderController::thankyoupage]
    J -->|Получить из сессии| H
    J -->|Render| K[thankyoupage/index.blade.php]
```

## Main Workflow

```mermaid
sequenceDiagram
    participant User
    participant Browser
    participant OrderController
    participant PlaceOrderRequest
    participant CartService
    participant Database
    participant Session
    
    User->>Browser: Открыть /checkout
    Browser->>OrderController: GET /checkout
    OrderController->>CartService: getCart()
    CartService-->>OrderController: cart data
    OrderController->>Session: get('cart')
    OrderController-->>Browser: checkout view
    Browser-->>User: Форма оформления
    
    User->>Browser: Заполнить форму + Submit
    Browser->>OrderController: POST /checkout
    OrderController->>PlaceOrderRequest: validate()
    PlaceOrderRequest-->>OrderController: validated data
    OrderController->>Database: BEGIN TRANSACTION
    OrderController->>Database: INSERT orders
    OrderController->>Database: INSERT order_positions (loop)
    OrderController->>Database: COMMIT
    OrderController->>CartService: clearSelectedItems()
    OrderController->>Session: put('order', data)
    OrderController-->>Browser: redirect /thankyoupage
    Browser->>OrderController: GET /thankyoupage
    OrderController->>Session: get('order')
    OrderController-->>Browser: thankyoupage view
    Browser-->>User: Страница благодарности
