<?php /* Smarty version 2.6.11, created on 2026-03-04 14:21:11
         compiled from models/cart_new/update_delivery_price.js */ ?>
// Обновление цены доставки (для checkout)
self.update_delivery_price = function(method) {
    self.delivery_method(method);
    
    if (method === 'pickup') {
        self.delivery_price(0);
    } else if (method === 'courier') {
        self.delivery_price(200);
    } else if (method === 'express') {
        self.delivery_price(500);
    }
};
