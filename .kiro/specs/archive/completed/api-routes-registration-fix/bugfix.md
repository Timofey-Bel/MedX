# Bugfix Requirements Document

## Introduction

This bugfix addresses a critical routing issue in Laravel 12 where API routes defined in `routes/api.php` are not being registered, causing POST requests to `/api/cart` and `/api/favorites` to return 405 Method Not Allowed errors with HTML error pages instead of JSON responses. This breaks the cart and favorites counter functionality that relies on these API endpoints.

The root cause is that `bootstrap/app.php` only registers web routes but does not explicitly register API routes, which is required in Laravel 12's new application structure.

## Bug Analysis

### Current Behavior (Defect)

1.1 WHEN a POST request is made to `/api/cart` with task `get_cart` THEN the system returns a 405 Method Not Allowed error with an HTML error page instead of JSON

1.2 WHEN a POST request is made to `/api/favorites` with task `get_favorites` THEN the system returns a 405 Method Not Allowed error with an HTML error page instead of JSON

1.3 WHEN the application bootstraps THEN the system does not register routes defined in `routes/api.php`

1.4 WHEN JavaScript code in `cart-counter.js` attempts to fetch cart data THEN the system returns non-JSON content causing the fetch to fail

1.5 WHEN JavaScript code in `favorites-counter.js` attempts to fetch favorites data THEN the system returns non-JSON content causing the fetch to fail

### Expected Behavior (Correct)

2.1 WHEN a POST request is made to `/api/cart` with task `get_cart` THEN the system SHALL return a JSON response with cart data and HTTP status 200

2.2 WHEN a POST request is made to `/api/favorites` with task `get_favorites` THEN the system SHALL return a JSON response with favorites data and HTTP status 200

2.3 WHEN the application bootstraps THEN the system SHALL register all routes defined in `routes/api.php` with the `/api` prefix

2.4 WHEN JavaScript code in `cart-counter.js` attempts to fetch cart data THEN the system SHALL return valid JSON with content-type `application/json`

2.5 WHEN JavaScript code in `favorites-counter.js` attempts to fetch favorites data THEN the system SHALL return valid JSON with content-type `application/json`

### Unchanged Behavior (Regression Prevention)

3.1 WHEN web routes are accessed THEN the system SHALL CONTINUE TO route requests through `routes/web.php` correctly

3.2 WHEN console commands are executed THEN the system SHALL CONTINUE TO load commands from `routes/console.php` correctly

3.3 WHEN the health check endpoint `/up` is accessed THEN the system SHALL CONTINUE TO respond with health status

3.4 WHEN middleware is configured THEN the system SHALL CONTINUE TO apply middleware correctly to all route types

3.5 WHEN the authenticated user endpoint `/api/user` is accessed with valid authentication THEN the system SHALL CONTINUE TO return the authenticated user data

3.6 WHEN invalid task parameters are sent to `/api/cart` or `/api/favorites` THEN the system SHALL CONTINUE TO return appropriate error responses with HTTP status 400
