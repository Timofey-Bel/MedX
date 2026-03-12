// Обновление данных корзины с сервера
self.refresh_cart = function(callback) {
    $.ajax({
        type: "POST",
        url: '/api/cart',
        data: {
            task: "get_cart"
        },
        dataType: "json",
        success: function(data, textStatus){
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
                console.log('=== cart() updated ===');
                console.log('self.cart():', self.cart());
                console.log('self.items():', self.items());
                console.log('self.items().length:', self.items().length);
                self.update_cart();
            }
            
            // Обновляем глобальный счетчик
            if (typeof cartCounterViewModel !== 'undefined' && cartCounterViewModel !== null) {
                cartCounterViewModel.updateCount(data);
            }
            
            // Вызываем callback, если он передан
            if (typeof callback === 'function') {
                callback();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error refreshing cart:', error);
            console.error('Response:', xhr.responseText);
        }
    });
};

// Обновление при возврате на вкладку
// Инициализация переменной page_focus если её нет
if (typeof page_focus === 'undefined') {
    var page_focus = false;
}

// Функция addEvent если её нет
if (typeof addEvent === 'undefined') {
    function addEvent(element, event, handler) {
        if (element.addEventListener) {
            element.addEventListener(event, handler, false);
        } else if (element.attachEvent) {
            element.attachEvent('on' + event, handler);
        }
    }
}

addEvent(window, 'focus', function(){
    if(page_focus == false) {
        page_focus = true;
        self.refresh_cart();
    }
});

addEvent(window, 'blur', function(){
    page_focus = false;
});

