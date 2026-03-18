<?php /* Smarty version 2.6.11, created on 2026-03-04 14:21:11
         compiled from models/favorites/clear_favorites.js */ ?>
// Очистка избранного
self.clear_favorites = function(){
    var pars = {
        task: "clear_favorites"
    };

    $.ajax({
        type: "POST",
        url: '/favorites/',
        data: pars,
        dataType: "json",
        success: function(data, textStatus){
            // Обновляем глобальную переменную server_favorites
            if (typeof server_favorites === 'undefined') {
                window.server_favorites = {data: {}};
            }
            if (data) {
                server_favorites.data = data;
            }
            
            // Обновляем избранное из ответа сервера
            if (data) {
                self.favorites(ko.mapping.fromJS(data));
                self.update_favorites();
            }
            
            // Обновляем глобальный счетчик
            if (typeof favoritesCounterViewModel !== 'undefined' && favoritesCounterViewModel !== null) {
                favoritesCounterViewModel.updateCount(data);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error clearing favorites:', error);
        }
    });

    // Очищаем локальную модель
    self.favorites().items({});
    self.update_favorites();
};
