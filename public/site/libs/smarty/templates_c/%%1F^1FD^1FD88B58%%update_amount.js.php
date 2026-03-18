<?php /* Smarty version 2.6.11, created on 2026-03-04 14:21:11
         compiled from models/cart_new/update_amount.js */ ?>
// Обновление количества товара
self.update_amount = function (guid, amount) {
    var pars = {
        task: "update_amount",
        guid: guid,
        amount: amount
    };

    $.ajax({
        type: "POST",
        url: '/cart/',
        data: pars,
        dataType: "json",
        success: function (data, textStatus) {
            // Обновляем глобальную переменную server_cart
            if (typeof server_cart === 'undefined') {
                window.server_cart = {data: {}};
            }
            if (data) {
                server_cart.data = data;
            }
            
            // Обновляем корзину из ответа сервера
            if (data) {
                // Используем ko.mapping.fromJS с target для обновления существующего объекта
                var currentCart = self.cart();
                var updatedCart = ko.mapping.fromJS(data, {}, currentCart);
                self.cart(updatedCart);
                self.update_cart();
            }
            
            // Обновляем глобальный счетчик
            if (typeof cartCounterViewModel !== 'undefined' && cartCounterViewModel !== null) {
                cartCounterViewModel.updateCount(data);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error updating amount:', error);
        }
    });
};
