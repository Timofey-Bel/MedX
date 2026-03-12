# Failing Tests Cleanup Fix - Bugfix Design

## Overview

Проект имеет 63 падающих теста из 90 (33 errors + 30 failures) плюс 50 PHPUnit deprecation warnings. Проблема возникла в процессе миграции с legacy Smarty системы на Laravel 12. Основные причины падения тестов:

1. Устаревшие Laravel default тесты (ExampleTest.php)
2. Старые CSRF-тесты, написанные для предыдущей архитектуры
3. Старые тесты сессий корзины, не соответствующие текущей реализации
4. Возможные проблемы в новых функциональных тестах (65 тестов для корзины и страницы товара)
5. PHPUnit deprecation warnings

Стратегия исправления: провести аудит всех тестов, удалить устаревшие, обновить или удалить неактуальные CSRF/session тесты, исправить проблемы в новых тестах, устранить deprecation warnings.

## Glossary

- **Bug_Condition (C)**: Условие, при котором тесты падают - запуск полного набора тестов командой `php vendor/bin/phpunit --colors=always`
- **Property (P)**: Желаемое поведение - все актуальные тесты проходят успешно (0 failures, 0 errors, 0 deprecations)
- **Preservation**: Существующая функциональность корзины, страницы товара и интеграция с Knockout.js должны продолжать работать
- **ExampleTest**: Стандартные тестовые файлы Laravel, создаваемые при инициализации проекта
- **CSRF Tests**: Тесты проверки исключения `/api/cart` из CSRF защиты (CartCsrfExceptionTest, CartCsrfExemptionTest)
- **Session Tests**: Тесты проверки сохранения данных корзины в сессию (CartSessionPersistenceTest)
- **Functional Tests**: Новые тесты функциональности корзины и страницы товара (CartFunctionalityTest - 29 тестов, ProductPageTest - 15 тестов, ProductServiceTest - 15 тестов, ProductControllerTest - 5 тестов)
- **CartController**: Контроллер в `app/Http/Controllers/CartController.php`, обрабатывающий AJAX запросы к `/api/cart`
- **API Route**: Маршрут `/api/cart` определен в `routes/api.php` с middleware `web` и исключением CSRF в `bootstrap/app.php`

## Bug Details

### Fault Condition

Баг проявляется при запуске полного набора тестов. Тестовый набор содержит смесь устаревших, неактуальных и новых тестов, что приводит к массовым падениям.

**Formal Specification:**
```
FUNCTION isBugCondition(testSuite)
  INPUT: testSuite of type PHPUnitTestSuite
  OUTPUT: boolean
  
  RETURN testSuite.hasExampleTests() 
         OR testSuite.hasOutdatedCsrfTests()
         OR testSuite.hasOutdatedSessionTests()
         OR testSuite.hasBrokenFunctionalTests()
         OR testSuite.hasDeprecationWarnings()
END FUNCTION
```

### Examples

- **Example 1**: `tests/Feature/ExampleTest.php` - Laravel default тест, проверяет GET `/` возвращает 200, но может быть неактуален для текущей архитектуры
- **Example 2**: `tests/Unit/ExampleTest.php` - Laravel default тест, проверяет `true === true`, не несет практической ценности
- **Example 3**: `tests/Feature/CartCsrfExceptionTest.php` - тест CSRF исключения, может дублировать функциональность CartCsrfExemptionTest или быть устаревшим
- **Example 4**: `tests/Feature/CartCsrfExemptionTest.php` - тест CSRF исключения с использованием `withoutMiddleware()`, может не соответствовать текущей архитектуре где CSRF исключение настроено в `bootstrap/app.php`
- **Example 5**: `tests/Feature/CartSessionPersistenceTest.php` - тест сессий корзины, использует устаревший формат данных (`put_item` с `guid` и `product_amount` вместо текущего API)
- **Example 6**: `tests/Feature/CartFunctionalityTest.php` - 29 новых тестов, могут падать из-за проблем в коде или тестовом окружении
- **Edge Case**: PHPUnit deprecation warnings (50 штук) - указывают на использование устаревших методов PHPUnit или Laravel testing helpers

## Expected Behavior

### Preservation Requirements

**Unchanged Behaviors:**
- Функциональность корзины (добавление, удаление, изменение количества, выбор товаров) должна продолжать работать
- Функциональность страницы товара (отображение, отзывы, цены) должна продолжать работать
- Интеграция с Knockout.js для динамического обновления корзины должна продолжать работать
- API endpoint `/api/cart` должен продолжать работать с middleware `web` и исключением CSRF
- Тестовое окружение должно использовать реальную БД (согласно phpunit.xml)

**Scope:**
Все изменения касаются только тестового кода и не должны затрагивать production код. Исключения:
- Если новые функциональные тесты выявляют реальные баги в коде, эти баги должны быть исправлены
- Если тестовое окружение настроено неправильно, настройки должны быть исправлены

## Hypothesized Root Cause

Based on the bug description, the most likely issues are:

1. **Устаревшие Example Tests**: Laravel создает default тесты при инициализации проекта
   - `tests/Feature/ExampleTest.php` проверяет GET `/`, который может не существовать или требовать аутентификации
   - `tests/Unit/ExampleTest.php` проверяет `true === true`, не несет практической ценности
   - Решение: удалить оба файла

2. **Дублирование CSRF Tests**: Два теста проверяют одно и то же - исключение `/api/cart` из CSRF
   - `CartCsrfExceptionTest` использует `postJson()` без CSRF токена
   - `CartCsrfExemptionTest` использует `withoutMiddleware()` для отключения CSRF
   - Текущая архитектура: CSRF исключение настроено в `bootstrap/app.php`, маршрут использует middleware `web`
   - Решение: оставить один актуальный тест или обновить оба под текущую архитектуру

3. **Устаревший Session Test**: `CartSessionPersistenceTest` использует старый формат API
   - Тест использует `put_item` с `guid` и `product_amount`
   - Текущий API может использовать другой формат (нужно проверить CartController)
   - Решение: обновить тест под текущий API или удалить, если функциональность покрыта CartFunctionalityTest

4. **Проблемы в новых функциональных тестах**: 65 новых тестов могут падать по разным причинам
   - Неправильная настройка тестового окружения (БД, сессии, middleware)
   - Отсутствие тестовых данных в БД (product_id)
   - Проблемы в коде CartController или CartService
   - Несоответствие между тестами и реальным API
   - Решение: запустить тесты, проанализировать ошибки, исправить код или тесты

5. **PHPUnit Deprecation Warnings**: 50 предупреждений о deprecation
   - Использование устаревших assertion методов (например, `assertArrayHasKey` вместо `assertArrayHasKey`)
   - Использование устаревших PHPUnit features
   - Использование устаревших Laravel testing helpers
   - Решение: обновить код тестов под актуальные PHPUnit/Laravel методы

## Correctness Properties

Property 1: Fault Condition - All Relevant Tests Pass

_For any_ test suite where устаревшие тесты удалены, неактуальные тесты обновлены или удалены, и проблемы в новых тестах исправлены, команда `php vendor/bin/phpunit --colors=always` SHALL завершаться успешно с 0 failures, 0 errors, и 0 deprecation warnings.

**Validates: Requirements 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7**

Property 2: Preservation - Existing Functionality Unchanged

_For any_ production код (контроллеры, сервисы, модели, views), исправление тестов SHALL NOT изменять поведение существующей функциональности корзины, страницы товара, и интеграции с Knockout.js.

**Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5**

## Fix Implementation

### Changes Required

Предполагая, что наш анализ корректен:

**Phase 1: Удаление устаревших тестов**

**Files**: 
- `tests/Feature/ExampleTest.php`
- `tests/Unit/ExampleTest.php`

**Action**: Удалить оба файла

**Rationale**: Эти тесты не несут практической ценности и могут падать из-за несоответствия текущей архитектуре

**Phase 2: Аудит и обновление CSRF тестов**

**Files**:
- `tests/Feature/CartCsrfExceptionTest.php`
- `tests/Feature/CartCsrfExemptionTest.php`

**Action**: 
1. Запустить оба теста отдельно и проанализировать результаты
2. Проверить, соответствуют ли тесты текущей архитектуре (CSRF исключение в `bootstrap/app.php`, middleware `web`)
3. Если тесты дублируют функциональность - оставить один, удалить другой
4. Если тесты устарели - обновить или удалить оба
5. Если тесты актуальны - исправить проблемы

**Phase 3: Аудит и обновление Session теста**

**File**: `tests/Feature/CartSessionPersistenceTest.php`

**Action**:
1. Изучить текущий API CartController (метод `handleAjax`)
2. Проверить, какой формат данных ожидается
3. Обновить тест под текущий формат API
4. Если функциональность уже покрыта CartFunctionalityTest - удалить тест

**Phase 4: Исправление новых функциональных тестов**

**Files**:
- `tests/Feature/CartFunctionalityTest.php` (29 тестов)
- `tests/Feature/ProductPageTest.php` (15 тестов)
- `tests/Unit/ProductServiceTest.php` (15 тестов)
- `tests/Unit/ProductControllerTest.php` (5 тестов)

**Action**:
1. Запустить каждый тестовый файл отдельно
2. Проанализировать ошибки (errors vs failures)
3. Для errors - исправить проблемы в тестовом окружении или коде
4. Для failures - исправить assertions или код
5. Проверить наличие тестовых данных в БД (product_id)
6. Проверить правильность настройки middleware и сессий

**Phase 5: Устранение PHPUnit Deprecation Warnings**

**Action**:
1. Запустить тесты с флагом `--display-deprecations`
2. Проанализировать список deprecation warnings
3. Обновить код тестов:
   - Заменить устаревшие assertion методы
   - Обновить использование PHPUnit features
   - Обновить использование Laravel testing helpers
4. Проверить документацию PHPUnit и Laravel для актуальных методов



## Testing Strategy

### Validation Approach

Стратегия тестирования следует поэтапному подходу: сначала провести exploratory testing для понимания причин падения тестов на UNFIXED коде, затем систематически исправлять проблемы и проверять, что исправления работают корректно и не ломают существующую функциональность.

### Exploratory Fault Condition Checking

**Goal**: Понять причины падения тестов ПЕРЕД внесением исправлений. Подтвердить или опровергнуть гипотезы о root cause. Если опровергнем - нужно пересмотреть гипотезы.

**Test Plan**: Запустить тесты на UNFIXED коде в разных комбинациях для понимания паттернов падений:

**Test Cases**:
1. **Full Test Suite Run**: `php vendor/bin/phpunit --colors=always` (ожидается 63 падения)
2. **Example Tests Only**: `php vendor/bin/phpunit tests/Feature/ExampleTest.php tests/Unit/ExampleTest.php` (ожидается падение из-за несоответствия архитектуре)
3. **CSRF Tests Only**: `php vendor/bin/phpunit tests/Feature/CartCsrfExceptionTest.php tests/Feature/CartCsrfExemptionTest.php` (может падать или проходить)
4. **Session Test Only**: `php vendor/bin/phpunit tests/Feature/CartSessionPersistenceTest.php` (ожидается падение из-за устаревшего API формата)
5. **Cart Functionality Test Only**: `php vendor/bin/phpunit tests/Feature/CartFunctionalityTest.php` (ожидается падение, нужно понять причину)
6. **Product Tests Only**: `php vendor/bin/phpunit tests/Feature/ProductPageTest.php tests/Unit/ProductServiceTest.php tests/Unit/ProductControllerTest.php` (ожидается падение, нужно понять причину)
7. **Deprecation Warnings**: `php vendor/bin/phpunit --display-deprecations` (ожидается 50 warnings)

**Expected Counterexamples**:
- ExampleTest падает из-за несуществующего маршрута `/` или требования аутентификации
- CSRF тесты могут падать из-за несоответствия текущей архитектуре или дублирования
- Session тест падает из-за использования устаревшего формата API (`guid`, `product_amount`)
- Cart Functionality тесты падают из-за:
  - Отсутствия тестовых данных в БД (product_id не существует)
  - Неправильной настройки сессий в тестовом окружении
  - Проблем в CartController или CartService
- Product тесты падают из-за аналогичных причин
- Deprecation warnings указывают на конкретные устаревшие методы

### Fix Checking

**Goal**: Проверить, что для всех случаев, где условие бага выполняется (тесты падают), исправленный тестовый набор производит ожидаемое поведение (все тесты проходят).

**Pseudocode:**
```
FOR ALL testSuite WHERE isBugCondition(testSuite) DO
  result := runTests_fixed(testSuite)
  ASSERT result.failures == 0
  ASSERT result.errors == 0
  ASSERT result.deprecations == 0
  ASSERT result.allTestsPass == true
END FOR
```

**Validation Steps**:
1. После удаления ExampleTest: запустить `php vendor/bin/phpunit` - количество тестов должно уменьшиться на 2
2. После обновления CSRF тестов: запустить CSRF тесты отдельно - должны проходить
3. После обновления Session теста: запустить Session тест отдельно - должен проходить
4. После исправления Cart Functionality тестов: запустить CartFunctionalityTest - все 29 тестов должны проходить
5. После исправления Product тестов: запустить Product тесты - все тесты должны проходить
6. После устранения deprecations: запустить с `--display-deprecations` - должно быть 0 warnings
7. Финальная проверка: запустить полный набор тестов - все должны проходить

### Preservation Checking

**Goal**: Проверить, что для всех случаев, где условие бага НЕ выполняется (production код), исправленный код производит тот же результат, что и оригинальный код.

**Pseudocode:**
```
FOR ALL productionCode WHERE NOT isBugCondition(productionCode) DO
  ASSERT behavior_original(productionCode) = behavior_fixed(productionCode)
END FOR
```

**Testing Approach**: Manual testing и integration testing рекомендуются для preservation checking потому что:
- Нужно проверить реальное поведение в браузере (Knockout.js интеграция)
- Нужно проверить AJAX запросы к `/api/cart`
- Нужно проверить сохранение данных в сессию
- Нужно проверить визуальное отображение корзины и страницы товара

**Test Plan**: Перед началом исправлений протестировать функциональность в браузере, затем после всех исправлений повторить тесты и убедиться, что поведение не изменилось.

**Test Cases**:
1. **Cart Functionality Preservation**: 
   - Открыть страницу товара, добавить в корзину - должно работать
   - Открыть корзину, изменить количество - должно работать
   - Открыть корзину, удалить товар - должно работать
   - Открыть корзину, выбрать/снять выбор товара - должно работать
   - Проверить обновление счетчика корзины в header - должно работать

2. **Product Page Preservation**:
   - Открыть страницу товара - должна отображаться корректно
   - Проверить отображение цены - должно работать
   - Проверить отображение отзывов - должно работать
   - Проверить адаптивность на мобильных - должно работать

3. **Knockout.js Integration Preservation**:
   - Проверить динамическое обновление корзины без перезагрузки страницы - должно работать
   - Проверить биндинги Knockout.js - должны работать

4. **Session Persistence Preservation**:
   - Добавить товар в корзину, перезагрузить страницу - товар должен остаться
   - Проверить сохранение выбранных товаров - должно работать

### Unit Tests

- Тесты ExampleTest будут удалены (не несут ценности)
- CSRF тесты будут обновлены или удалены в зависимости от актуальности
- Session тест будет обновлен под текущий API или удален
- Cart Functionality тесты (29) будут исправлены для прохождения
- Product тесты (35) будут исправлены для прохождения

### Property-Based Tests

Property-based testing не применяется в данном bugfix, так как:
- Проблема связана с тестовым кодом, а не с логикой production кода
- Нужно исправить конкретные тесты, а не генерировать случайные входные данные
- Preservation checking лучше выполнять через manual/integration testing

### Integration Tests

- Запуск полного набора тестов после каждого этапа исправлений
- Manual testing функциональности корзины в браузере
- Manual testing функциональности страницы товара в браузере
- Проверка интеграции с Knockout.js
- Проверка AJAX запросов к `/api/cart` через DevTools
- Проверка сохранения данных в сессию
