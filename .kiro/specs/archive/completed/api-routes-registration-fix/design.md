# API Routes Registration Fix Design

## Overview

This bugfix addresses a critical routing issue in Laravel 12 where API routes defined in `routes/api.php` are not being registered during application bootstrap. The `bootstrap/app.php` file only registers web routes, causing POST requests to `/api/cart` and `/api/favorites` to return 405 Method Not Allowed errors instead of JSON responses. The fix involves adding the `api` parameter to the `withRouting()` method call to explicitly register API routes with the `/api` prefix.

## Glossary

- **Bug_Condition (C)**: The condition that triggers the bug - when API routes are not registered during bootstrap, causing requests to API endpoints to fail
- **Property (P)**: The desired behavior when API routes are properly registered - requests to `/api/cart` and `/api/favorites` should return JSON responses with HTTP 200
- **Preservation**: Existing web routes, console commands, health check, and middleware configuration that must remain unchanged by the fix
- **withRouting()**: The method in `bootstrap/app.php` that configures route registration for the Laravel application
- **API Middleware Group**: The middleware group automatically applied to routes in `routes/api.php` when registered via the `api` parameter

## Bug Details

### Fault Condition

The bug manifests when the application bootstraps without registering API routes. The `withRouting()` method in `bootstrap/app.php` is missing the `api` parameter, which is required in Laravel 12 to explicitly register routes from `routes/api.php`.

**Formal Specification:**
```
FUNCTION isBugCondition(request)
  INPUT: request of type HttpRequest
  OUTPUT: boolean
  
  RETURN request.path STARTS_WITH '/api/'
         AND request.path IN ['/api/cart', '/api/favorites']
         AND request.method == 'POST'
         AND routeExists(request.path, request.method) == false
END FUNCTION
```

### Examples

- **Example 1**: POST request to `/api/cart` with `task=get_cart` returns 405 Method Not Allowed with HTML error page instead of JSON cart data with HTTP 200
- **Example 2**: POST request to `/api/favorites` with `task=get_favorites` returns 405 Method Not Allowed with HTML error page instead of JSON favorites data with HTTP 200
- **Example 3**: JavaScript fetch in `cart-counter.js` fails to parse response because it receives HTML instead of JSON
- **Edge Case**: GET request to `/api/user` with authentication should work after fix (this route uses auth:sanctum middleware)

## Expected Behavior

### Preservation Requirements

**Unchanged Behaviors:**
- Web routes defined in `routes/web.php` must continue to work exactly as before
- Console commands defined in `routes/console.php` must continue to load correctly
- Health check endpoint `/up` must continue to respond with health status
- Middleware configuration must continue to apply correctly to all route types
- Authenticated user endpoint `/api/user` must continue to return user data when properly authenticated
- Error handling for invalid task parameters must continue to return HTTP 400 responses

**Scope:**
All requests that do NOT target API routes (routes defined in `routes/api.php`) should be completely unaffected by this fix. This includes:
- All web routes (GET, POST, PUT, DELETE to non-API paths)
- Console commands executed via artisan
- Health check requests to `/up`
- Any custom middleware behavior

## Hypothesized Root Cause

Based on the bug description and code analysis, the root cause is:

1. **Missing API Route Registration**: The `withRouting()` method in `bootstrap/app.php` does not include the `api` parameter
   - Laravel 12 requires explicit registration of API routes via the `api` parameter
   - Without this parameter, routes defined in `routes/api.php` are never registered
   - The framework only registers the routes explicitly specified (web, commands, health)

2. **Laravel 12 Architecture Change**: In Laravel 12, the new application structure requires explicit route file registration
   - Previous Laravel versions may have auto-registered API routes
   - The new `Application::configure()` pattern requires explicit declaration

3. **Route Not Found**: When a POST request is made to `/api/cart` or `/api/favorites`, Laravel cannot find a matching route
   - Laravel's fallback behavior returns a 405 Method Not Allowed error
   - The error is rendered as HTML because the request doesn't match any route, including API routes

## Correctness Properties

Property 1: Fault Condition - API Routes Return JSON Responses

_For any_ HTTP request where the path starts with `/api/` and matches a route defined in `routes/api.php` (specifically `/api/cart` or `/api/favorites` with POST method), the fixed application SHALL register these routes during bootstrap and return JSON responses with appropriate HTTP status codes (200 for success, 400 for invalid parameters).

**Validates: Requirements 2.1, 2.2, 2.3, 2.4, 2.5**

Property 2: Preservation - Non-API Routes Behavior

_For any_ HTTP request that does NOT target API routes (web routes, health check, or any non-API path), the fixed application SHALL produce exactly the same routing behavior as the original application, preserving all existing web route functionality, console commands, health checks, and middleware application.

**Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5, 3.6**

## Fix Implementation

### Changes Required

Assuming our root cause analysis is correct:

**File**: `bootstrap/app.php`

**Function**: `Application::configure()` chain

**Specific Changes**:
1. **Add API Route Registration**: Add the `api` parameter to the `withRouting()` method call
   - Parameter name: `api`
   - Parameter value: `__DIR__.'/../routes/api.php'`
   - This will register all routes defined in `routes/api.php` with the `/api` prefix
   - Routes will automatically receive the `api` middleware group

2. **Maintain Existing Parameters**: Keep all existing parameters in the `withRouting()` call
   - `web: __DIR__.'/../routes/web.php'` - unchanged
   - `commands: __DIR__.'/../routes/console.php'` - unchanged
   - `health: '/up'` - unchanged

3. **Preserve Method Chaining**: Ensure the method chain remains intact
   - `->withRouting()` continues to chain to `->withMiddleware()`
   - `->withMiddleware()` continues to chain to `->withExceptions()`
   - `->withExceptions()` continues to chain to `->create()`

**Expected Code After Fix**:
```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

## Testing Strategy

### Validation Approach

The testing strategy follows a two-phase approach: first, surface counterexamples that demonstrate the bug on unfixed code, then verify the fix works correctly and preserves existing behavior.

### Exploratory Fault Condition Checking

**Goal**: Surface counterexamples that demonstrate the bug BEFORE implementing the fix. Confirm or refute the root cause analysis. If we refute, we will need to re-hypothesize.

**Test Plan**: Write tests that make HTTP requests to API endpoints and assert that routes are registered and return JSON responses. Run these tests on the UNFIXED code to observe failures and understand the root cause.

**Test Cases**:
1. **Cart API Test**: POST to `/api/cart` with `task=get_cart` (will fail with 405 on unfixed code)
2. **Favorites API Test**: POST to `/api/favorites` with `task=get_favorites` (will fail with 405 on unfixed code)
3. **Authenticated User API Test**: GET to `/api/user` with auth token (will fail with 404/405 on unfixed code)
4. **Invalid Task Test**: POST to `/api/cart` with invalid task (may fail with 405 instead of 400 on unfixed code)

**Expected Counterexamples**:
- HTTP 405 Method Not Allowed responses instead of JSON responses
- HTML error pages instead of JSON content-type
- Possible causes: missing route registration, incorrect route file path, missing middleware group

### Fix Checking

**Goal**: Verify that for all inputs where the bug condition holds, the fixed function produces the expected behavior.

**Pseudocode:**
```
FOR ALL request WHERE isBugCondition(request) DO
  response := handleRequest_fixed(request)
  ASSERT response.statusCode IN [200, 400]
  ASSERT response.contentType == 'application/json'
  ASSERT response.body IS valid JSON
END FOR
```

### Preservation Checking

**Goal**: Verify that for all inputs where the bug condition does NOT hold, the fixed function produces the same result as the original function.

**Pseudocode:**
```
FOR ALL request WHERE NOT isBugCondition(request) DO
  ASSERT handleRequest_original(request) = handleRequest_fixed(request)
END FOR
```

**Testing Approach**: Property-based testing is recommended for preservation checking because:
- It generates many test cases automatically across the input domain
- It catches edge cases that manual unit tests might miss
- It provides strong guarantees that behavior is unchanged for all non-API inputs

**Test Plan**: Observe behavior on UNFIXED code first for web routes and other endpoints, then write property-based tests capturing that behavior.

**Test Cases**:
1. **Web Routes Preservation**: Observe that web routes work correctly on unfixed code, then write test to verify this continues after fix
2. **Health Check Preservation**: Observe that `/up` endpoint works correctly on unfixed code, then write test to verify this continues after fix
3. **Console Commands Preservation**: Observe that artisan commands work correctly on unfixed code, then write test to verify this continues after fix
4. **Middleware Preservation**: Observe that middleware is applied correctly on unfixed code, then write test to verify this continues after fix

### Unit Tests

- Test that POST to `/api/cart` with valid task returns JSON with HTTP 200
- Test that POST to `/api/favorites` with valid task returns JSON with HTTP 200
- Test that POST to `/api/cart` with invalid task returns JSON error with HTTP 400
- Test that GET to `/api/user` with authentication returns user data
- Test that web routes continue to work correctly
- Test that health check endpoint continues to respond

### Property-Based Tests

- Generate random valid API requests and verify they return JSON responses with correct status codes
- Generate random web route requests and verify behavior is preserved from unfixed code
- Generate random task parameters and verify appropriate responses (200 for valid, 400 for invalid)

### Integration Tests

- Test full cart counter flow: JavaScript fetch to `/api/cart` receives JSON and updates counter
- Test full favorites counter flow: JavaScript fetch to `/api/favorites` receives JSON and updates counter
- Test that switching between web pages and API calls works correctly
- Test that authenticated API endpoints work with proper authentication
