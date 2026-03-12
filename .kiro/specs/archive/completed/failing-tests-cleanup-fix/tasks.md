# Implementation Plan

- [x] 1. Exploratory testing - понимание причин падения тестов
  - **Property 1: Fault Condition** - Test Suite Failures Analysis
  - **КРИТИЧЕСКИ ВАЖНО**: Эти тесты ДОЛЖНЫ ПАДАТЬ на неисправленном коде - падение подтверждает наличие проблем
  - **НЕ ПЫТАЙТЕСЬ исправлять тесты или код когда они падают**
  - **ПРИМЕЧАНИЕ**: Эти тесты кодируют ожидаемое поведение - они будут валидировать исправления когда пройдут после реализации
  - **ЦЕЛЬ**: Выявить конкретные причины падения 63 тестов и понять паттерны ошибок
  - **Подход**: Запустить тесты в разных комбинациях для изоляции проблем
  - Запустить полный набор тестов: `php vendor/bin/phpunit --colors=always` (ожидается 63 падения: 33 errors + 30 failures)
  - Запустить только ExampleTest: `php vendor/bin/phpunit tests/Feature/ExampleTest.php tests/Unit/ExampleTest.php` (ожидается падение из-за несоответствия архитектуре)
  - Запустить только CSRF тесты: `php vendor/bin/phpunit tests/Feature/CartCsrfExceptionTest.php tests/Feature/CartCsrfExemptionTest.php` (может падать или проходить)
  - Запустить только Session тест: `php vendor/bin/phpunit tests/Feature/CartSessionPersistenceTest.php` (ожидается падение из-за устаревшего API формата)
  - Запустить только Cart Functionality тест: `php vendor/bin/phpunit tests/Feature/CartFunctionalityTest.php` (ожидается падение, нужно понять причину)
  - Запустить только Product тесты: `php vendor/bin/phpunit tests/Feature/ProductPageTest.php tests/Unit/ProductServiceTest.php tests/Unit/ProductControllerTest.php` (ожидается падение, нужно понять причину)
  - Запустить с отображением deprecations: `php vendor/bin/phpunit --display-deprecations` (ожидается 50 warnings)
  - **ОЖИДАЕМЫЙ РЕЗУЛЬТАТ**: Тесты ПАДАЮТ (это правильно - это доказывает наличие проблем)
  - Документировать конкретные ошибки для каждой категории тестов
  - Подтвердить или опровергнуть гипотезы из design документа
  - Отметить задачу выполненной когда тесты запущены, падения задокументированы и причины поняты
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6_

- [x] 2. Preservation testing - проверка текущей функциональности ПЕРЕД исправлениями
  - **Property 2: Preservation** - Existing Functionality Baseline
  - **ВАЖНО**: Следуем методологии observation-first
  - Протестировать функциональность корзины в браузере на НЕИСПРАВЛЕННОМ коде:
    - Открыть страницу товара, добавить в корзину - записать поведение
    - Открыть корзину, изменить количество - записать поведение
    - Открыть корзину, удалить товар - записать поведение
    - Открыть корзину, выбрать/снять выбор товара - записать поведение
    - Проверить обновление счетчика корзины в header - записать поведение
  - Протестировать функциональность страницы товара в браузере:
    - Открыть страницу товара - записать отображение
    - Проверить отображение цены - записать поведение
    - Проверить отображение отзывов - записать поведение
    - Проверить адаптивность на мобильных - записать поведение
  - Протестировать интеграцию с Knockout.js:
    - Проверить динамическое обновление корзины без перезагрузки - записать поведение
    - Проверить биндинги Knockout.js - записать поведение
  - Протестировать сохранение в сессию:
    - Добавить товар в корзину, перезагрузить страницу - записать поведение
    - Проверить сохранение выбранных товаров - записать поведение
  - Проверить AJAX запросы к `/api/cart` через DevTools - записать формат запросов/ответов
  - **ОЖИДАЕМЫЙ РЕЗУЛЬТАТ**: Функциональность РАБОТАЕТ (это подтверждает baseline поведение для сохранения)
  - Создать документ с записанным baseline поведением для последующего сравнения
  - Отметить задачу выполненной когда baseline поведение задокументировано
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

- [x] 3. Phase 1: Удаление устаревших Laravel default тестов

  - [x] 3.1 Удалить ExampleTest файлы
    - Удалить `tests/Feature/ExampleTest.php`
    - Удалить `tests/Unit/ExampleTest.php`
    - _Bug_Condition: isBugCondition(testSuite) where testSuite.hasExampleTests() = true_
    - _Expected_Behavior: Тесты удалены, количество тестов уменьшилось на 2_
    - _Preservation: Production код не затронут, только тестовые файлы_
    - _Requirements: 1.3, 2.3_

  - [x] 3.2 Проверить что ExampleTest удалены
    - **Property 1: Expected Behavior** - ExampleTest Removal Validation
    - **ВАЖНО**: Повторно запустить ТОТ ЖЕ тест из задачи 1 - НЕ писать новый тест
    - Запустить полный набор тестов: `php vendor/bin/phpunit --colors=always`
    - **ОЖИДАЕМЫЙ РЕЗУЛЬТАТ**: Количество тестов уменьшилось на 2 (было 90, стало 88)
    - Подтвердить что ExampleTest больше не выполняются
    - _Requirements: 2.3_

- [x] 4. Phase 2: Аудит и обновление CSRF тестов

  - [x] 4.1 Проанализировать текущую архитектуру CSRF
    - Изучить `bootstrap/app.php` - как настроено CSRF исключение для `/api/cart`
    - Изучить `routes/api.php` - какие middleware используются для маршрута `/api/cart`
    - Изучить `app/Http/Controllers/CartController.php` - как обрабатываются AJAX запросы
    - Определить актуальный способ тестирования CSRF исключения
    - _Requirements: 1.4, 2.4_

  - [x] 4.2 Принять решение по CSRF тестам
    - Если тесты дублируют функциональность - выбрать один для сохранения
    - Если тесты устарели - пометить для удаления
    - Если тесты актуальны но падают - пометить для исправления
    - Документировать решение и обоснование
    - _Requirements: 1.4, 2.4_

  - [x] 4.3 Обновить или удалить CSRF тесты
    - Выполнить действия согласно решению из 4.2
    - Если обновляем - привести в соответствие с текущей архитектурой
    - Если удаляем - удалить файлы тестов
    - _Bug_Condition: isBugCondition(testSuite) where testSuite.hasOutdatedCsrfTests() = true_
    - _Expected_Behavior: CSRF тесты актуальны и проходят, или удалены если неактуальны_
    - _Preservation: CSRF исключение для /api/cart продолжает работать_
    - _Requirements: 1.4, 2.4_

  - [x] 4.4 Проверить что CSRF тесты работают корректно
    - **Property 1: Expected Behavior** - CSRF Tests Validation
    - **ВАЖНО**: Повторно запустить ТОТ ЖЕ тест из задачи 1 - НЕ писать новый тест
    - Если тесты обновлены: запустить `php vendor/bin/phpunit tests/Feature/CartCsrf*`
    - **ОЖИДАЕМЫЙ РЕЗУЛЬТАТ**: Тесты ПРОХОДЯТ (подтверждает что CSRF тесты исправлены)
    - Если тесты удалены: подтвердить что файлы отсутствуют
    - _Requirements: 2.4_

- [x] 5. Phase 3: Аудит и обновление Session теста

  - [x] 5.1 Проанализировать текущий API корзины
    - Изучить `app/Http/Controllers/CartController.php` метод `handleAjax`
    - Определить текущий формат данных для операций с корзиной
    - Определить как данные сохраняются в сессию
    - Сравнить с форматом в `CartSessionPersistenceTest`
    - _Requirements: 1.5, 2.5_

  - [x] 5.2 Принять решение по Session тесту
    - Если тест использует устаревший формат - пометить для обновления
    - Если функциональность уже покрыта CartFunctionalityTest - пометить для удаления
    - Если тест актуален но падает - пометить для исправления
    - Документировать решение и обоснование
    - _Requirements: 1.5, 2.5_

  - [x] 5.3 Обновить или удалить Session тест
    - Выполнить действия согласно решению из 5.2
    - Если обновляем - привести в соответствие с текущим API
    - Если удаляем - удалить файл теста
    - _Bug_Condition: isBugCondition(testSuite) where testSuite.hasOutdatedSessionTests() = true_
    - _Expected_Behavior: Session тест актуален и проходит, или удален если неактуален_
    - _Preservation: Сохранение данных корзины в сессию продолжает работать_
    - _Requirements: 1.5, 2.5_

  - [x] 5.4 Проверить что Session тест работает корректно
    - **Property 1: Expected Behavior** - Session Test Validation
    - **ВАЖНО**: Повторно запустить ТОТ ЖЕ тест из задачи 1 - НЕ писать новый тест
    - Если тест обновлен: запустить `php vendor/bin/phpunit tests/Feature/CartSessionPersistenceTest.php`
    - **ОЖИДАЕМЫЙ РЕЗУЛЬТАТ**: Тест ПРОХОДИТ (подтверждает что Session тест исправлен)
    - Если тест удален: подтвердить что файл отсутствует
    - _Requirements: 2.5_

- [x] 6. Phase 4: Исправление новых функциональных тестов

  - [x] 6.1 Проанализировать падения CartFunctionalityTest
    - Запустить `php vendor/bin/phpunit tests/Feature/CartFunctionalityTest.php --verbose`
    - Классифицировать ошибки: errors (проблемы окружения) vs failures (проблемы assertions)
    - Проверить наличие тестовых данных в БД (product_id)
    - Проверить настройку middleware и сессий в тестовом окружении
    - Документировать конкретные проблемы для каждого падающего теста
    - _Requirements: 1.6, 2.6, 2.7_

  - [x] 6.2 Исправить проблемы CartFunctionalityTest
    - Для errors - исправить настройки тестового окружения или добавить тестовые данные
    - Для failures - исправить код CartController/CartService или assertions в тестах
    - Применить исправления систематически, проверяя каждое изменение
    - _Bug_Condition: isBugCondition(testSuite) where testSuite.hasBrokenFunctionalTests() = true для CartFunctionalityTest_
    - _Expected_Behavior: Все 29 тестов CartFunctionalityTest проходят успешно_
    - _Preservation: Функциональность корзины продолжает работать корректно_
    - _Requirements: 1.6, 2.6, 2.7, 3.1, 3.2_

  - [x] 6.3 Проверить что CartFunctionalityTest проходит
    - **Property 1: Expected Behavior** - CartFunctionalityTest Validation
    - **ВАЖНО**: Повторно запустить ТОТ ЖЕ тест из задачи 1 - НЕ писать новый тест
    - Запустить `php vendor/bin/phpunit tests/Feature/CartFunctionalityTest.php`
    - **ОЖИДАЕМЫЙ РЕЗУЛЬТАТ**: Все 29 тестов ПРОХОДЯТ (подтверждает что Cart тесты исправлены)
    - _Requirements: 2.6, 3.1_

  - [x] 6.4 Проанализировать падения Product тестов
    - Запустить `php vendor/bin/phpunit tests/Feature/ProductPageTest.php --verbose`
    - Запустить `php vendor/bin/phpunit tests/Unit/ProductServiceTest.php --verbose`
    - Запустить `php vendor/bin/phpunit tests/Unit/ProductControllerTest.php --verbose`
    - Классифицировать ошибки: errors vs failures
    - Проверить наличие тестовых данных в БД
    - Документировать конкретные проблемы для каждого падающего теста
    - _Requirements: 1.6, 2.6, 2.7_

  - [x] 6.5 Исправить проблемы Product тестов
    - Для errors - исправить настройки тестового окружения или добавить тестовые данные
    - Для failures - исправить код ProductController/ProductService или assertions в тестах
    - Применить исправления систематически, проверяя каждое изменение
    - _Bug_Condition: isBugCondition(testSuite) where testSuite.hasBrokenFunctionalTests() = true для Product тестов_
    - _Expected_Behavior: Все 35 Product тестов проходят успешно (15 ProductPageTest + 15 ProductServiceTest + 5 ProductControllerTest)_
    - _Preservation: Функциональность страницы товара продолжает работать корректно_
    - _Requirements: 1.6, 2.6, 2.7, 3.1, 3.3_

  - [x] 6.6 Проверить что Product тесты проходят
    - **Property 1: Expected Behavior** - Product Tests Validation
    - **ВАЖНО**: Повторно запустить ТОТ ЖЕ тест из задачи 1 - НЕ писать новый тест
    - Запустить `php vendor/bin/phpunit tests/Feature/ProductPageTest.php tests/Unit/ProductServiceTest.php tests/Unit/ProductControllerTest.php`
    - **ОЖИДАЕМЫЙ РЕЗУЛЬТАТ**: Все 35 тестов ПРОХОДЯТ (подтверждает что Product тесты исправлены)
    - _Requirements: 2.6, 3.1_

- [x] 7. Phase 5: Устранение PHPUnit deprecation warnings

  - [x] 7.1 Проанализировать deprecation warnings
    - Запустить `php vendor/bin/phpunit --display-deprecations > deprecations.log`
    - Классифицировать warnings по типам:
      - Устаревшие assertion методы PHPUnit
      - Устаревшие PHPUnit features
      - Устаревшие Laravel testing helpers
    - Определить какие методы нужно заменить
    - Проверить документацию PHPUnit и Laravel для актуальных методов
    - _Requirements: 1.2, 2.2_

  - [x] 7.2 Устранить deprecation warnings
    - Заменить устаревшие assertion методы на актуальные
    - Обновить использование PHPUnit features
    - Обновить использование Laravel testing helpers
    - Применить изменения систематически по всем тестовым файлам
    - _Bug_Condition: isBugCondition(testSuite) where testSuite.hasDeprecationWarnings() = true_
    - _Expected_Behavior: 0 deprecation warnings при запуске тестов_
    - _Preservation: Логика тестов не изменяется, только синтаксис методов_
    - _Requirements: 1.2, 2.2_

  - [x] 7.3 Проверить что deprecation warnings устранены
    - **Property 1: Expected Behavior** - Deprecation Warnings Validation
    - **ВАЖНО**: Повторно запустить ТОТ ЖЕ тест из задачи 1 - НЕ писать новый тест
    - Запустить `php vendor/bin/phpunit --display-deprecations`
    - **ОЖИДАЕМЫЙ РЕЗУЛЬТАТ**: 0 deprecation warnings (подтверждает что warnings устранены)
    - _Requirements: 2.2_

- [x] 8. Final Validation: Полная проверка всех исправлений

  - [x] 8.1 Запустить полный набор тестов
    - **Property 1: Expected Behavior** - Full Test Suite Validation
    - **ВАЖНО**: Повторно запустить ТОТ ЖЕ тест из задачи 1 - НЕ писать новый тест
    - Запустить `php vendor/bin/phpunit --colors=always`
    - **ОЖИДАЕМЫЙ РЕЗУЛЬТАТ**: Все тесты ПРОХОДЯТ (0 failures, 0 errors, 0 deprecations)
    - Подтвердить что количество тестов соответствует ожидаемому (после удаления устаревших)
    - _Requirements: 2.1, 2.2_

  - [x] 8.2 Проверить preservation - функциональность не сломалась
    - **Property 2: Preservation** - Existing Functionality Validation
    - **ВАЖНО**: Повторно запустить ТЕ ЖЕ тесты из задачи 2 - НЕ писать новые тесты
    - Протестировать функциональность корзины в браузере - сравнить с baseline из задачи 2
    - Протестировать функциональность страницы товара в браузере - сравнить с baseline
    - Протестировать интеграцию с Knockout.js - сравнить с baseline
    - Протестировать сохранение в сессию - сравнить с baseline
    - Проверить AJAX запросы к `/api/cart` через DevTools - сравнить с baseline
    - **ОЖИДАЕМЫЙ РЕЗУЛЬТАТ**: Все функциональность РАБОТАЕТ идентично baseline (подтверждает отсутствие регрессий)
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

- [x] 9. Checkpoint - Убедиться что все тесты проходят и функциональность работает
  - Подтвердить что все 8 фаз выполнены успешно
  - Подтвердить что полный набор тестов проходит без ошибок
  - Подтвердить что production функциональность работает корректно
  - Если возникают вопросы - обратиться к пользователю
