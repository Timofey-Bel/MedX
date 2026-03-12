-- Проверяем позиции заказа 7367827
SELECT 
    id,
    art as product_id,
    title as product_name,
    pieces as quantity,
    piece_cost as price
FROM order_positions 
WHERE order_num = 7367827
LIMIT 10;
