-- Смотрим все поля для одной позиции заказа
SELECT *
FROM order_positions 
WHERE order_num = 7367827
LIMIT 1;
