-- Проверяем структуру таблицы order_positions
DESCRIBE order_positions;

-- Смотрим пример данных
SELECT * FROM order_positions WHERE order_num = 7284007 LIMIT 5;
