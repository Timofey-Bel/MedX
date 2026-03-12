# Implementation Plan

- [ ] 1. Write bug condition exploration test
  - **Property 1: Fault Condition** - API Routes Return JSON Responses
  - **CRITICAL**: This test MUST FAIL on unfixed code - failure confirms the bug exists
  - **DO NOT attempt to fix the test or the code when it fails**
  - **NOTE**: This test encodes the expected behavior - it will validate the fix when it passes after implementation
  - **GOAL**: Surface counterexamples that demonstrate the bug exists
  - **Scoped PBT Approach**: Scope the property to concrete failing cases: POST requests to `/api/cart` and `/api/favorites`
  - Test that POST to `/api/cart` with `task=get_cart` returns JSON response with HTTP 200 (from Fault Condition in design)
  - Test that POST to `/api/favorites` with `task=get_favorites` returns JSON response with HTTP 200 (from Fault Condition in design)
  - Test that response content-type is `application/json`
  - Test that response body is valid JSON
  - Run test on UNFIXED code
  - **EXPECTED OUTCOME**: Test FAILS with HTTP 405 Method Not Allowed (this is correct - it proves the bug exists)
  - Document counterexamples found: HTTP 405 responses, HTML error pages instead of JSON
  - Mark task complete when test is written, run, and failure is documented
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

- [ ] 2. Write preservation property tests (BEFORE implementing fix)
  - **Property 2: Preservation** - Non-API Routes Behavior
  - **IMPORTANT**: Follow observation-first methodology
  - Observe behavior on UNFIXED code for non-API routes (web routes, health check, console commands)
  - Test that GET to `/` (home page) returns expected web response
  - Test that GET to `/up` (health check) returns health status
  - Test that web routes continue to route through `routes/web.php` correctly
  - Test that middleware is applied correctly to web routes
  - Write property-based tests capturing observed behavior patterns from Preservation Requirements
  - Property-based testing generates many test cases for stronger guarantees
  - Run tests on UNFIXED code
  - **EXPECTED OUTCOME**: Tests PASS (this confirms baseline behavior to preserve)
  - Mark task complete when tests are written, run, and passing on unfixed code
  - _Requirements: 3.1, 3.2, 3.3, 3.4_

- [ ] 3. Fix for API routes registration

  - [x] 3.1 Implement the fix in bootstrap/app.php
    - Add `api` parameter to `withRouting()` method call
    - Set parameter value to `__DIR__.'/../routes/api.php'`
    - Maintain existing parameters: `web`, `commands`, `health`
    - Preserve method chaining: `->withRouting()` -> `->withMiddleware()` -> `->withExceptions()` -> `->create()`
    - _Bug_Condition: isBugCondition(request) where request.path starts with '/api/' and matches routes in routes/api.php_
    - _Expected_Behavior: API routes return JSON responses with HTTP 200 for valid requests, HTTP 400 for invalid parameters_
    - _Preservation: Web routes, console commands, health check, and middleware configuration remain unchanged_
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 3.1, 3.2, 3.3, 3.4, 3.5, 3.6_

  - [ ] 3.2 Verify bug condition exploration test now passes
    - **Property 1: Expected Behavior** - API Routes Return JSON Responses
    - **IMPORTANT**: Re-run the SAME test from task 1 - do NOT write a new test
    - The test from task 1 encodes the expected behavior
    - When this test passes, it confirms the expected behavior is satisfied
    - Run bug condition exploration test from step 1
    - **EXPECTED OUTCOME**: Test PASSES (confirms bug is fixed)
    - Verify POST to `/api/cart` returns JSON with HTTP 200
    - Verify POST to `/api/favorites` returns JSON with HTTP 200
    - Verify response content-type is `application/json`
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_

  - [ ] 3.3 Verify preservation tests still pass
    - **Property 2: Preservation** - Non-API Routes Behavior
    - **IMPORTANT**: Re-run the SAME tests from task 2 - do NOT write new tests
    - Run preservation property tests from step 2
    - **EXPECTED OUTCOME**: Tests PASS (confirms no regressions)
    - Confirm web routes still work correctly
    - Confirm health check endpoint still responds
    - Confirm middleware is still applied correctly
    - Confirm all tests still pass after fix (no regressions)

- [ ] 4. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.
