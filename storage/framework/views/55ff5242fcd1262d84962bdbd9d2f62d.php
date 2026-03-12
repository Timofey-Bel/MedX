<script>
/**
 * CartCounterViewModel - ViewModel для счетчиков корзины в header
 * 
 * Отдельная ViewModel для избежания конфликтов с множественными applyBindings
 * Подписывается на изменения model_cart.total_cart_amount() и обновляет счетчики
 */
function CartCounterViewModel() {
    var self = this;
    
    // Количество товаров в корзине
    self.count = ko.observable(0);
    
    // Форматированное количество (пустая строка если 0, иначе число)
    self.formattedCount = ko.pureComputed(function() {
        var count = self.count();
        if (count === 0) return '';
        return count.toString();
    });
    
    // Видимость счетчика (скрываем если 0)
    self.isVisible = ko.pureComputed(function() {
        return self.count() > 0;
    });
    
    // Метод для обновления счетчика из ответа сервера
    self.updateCount = function(cartData) {
        if (cartData && typeof cartData.total_cart_amount !== 'undefined') {
            self.count(cartData.total_cart_amount);
        }
    };
    
    // Инициализация счетчика из server_cart.data
    if (typeof server_cart !== 'undefined' && server_cart.data) {
        self.count(server_cart.data.total_cart_amount || 0);
    }
    
    // Подписка на изменения model_cart.total_cart_amount() если модель доступна
    // Используем setTimeout чтобы дождаться инициализации model_cart
    setTimeout(function() {
        if (typeof model_cart !== 'undefined' && model_cart.total_cart_amount) {
            model_cart.total_cart_amount.subscribe(function(newValue) {
                self.count(newValue);
            });
            // Синхронизируем начальное значение
            self.count(model_cart.total_cart_amount());
        }
    }, 100);
}

// Создаем глобальный экземпляр ViewModel
var cartCounterViewModel = new CartCounterViewModel();

// Применяем bindings к счетчикам корзины
$(document).ready(function() {
    // Применяем bindings к каждому счетчику отдельно (включая мобильный)
    $('.cart-counter, .mobile-cart-counter').each(function() {
        if (ko.dataFor(this) === undefined) {
            ko.applyBindings(cartCounterViewModel, this);
        }
    });
});
</script>
<?php /**PATH C:\OS\home\sfera\resources\views/js/models/cart_new/cart_counter_viewmodel.blade.php ENDPATH**/ ?>