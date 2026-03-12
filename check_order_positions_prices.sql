-- Проверка структуры таблицы order_positions для цен
DESCRIBE order_positions;

-- Проверка данных одного заказа
SELECT * FROM order_positions WHERE order_num = 7367827 LIMIT 3;
