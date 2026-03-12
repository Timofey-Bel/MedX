<script>

// Инициализация server_cart если не определена (для страниц, где она не инициализирована)
if (typeof server_cart === 'undefined') {
    var server_cart = {
        data: {items: {}, total_cart_sum: 0, total_cart_amount: 0, cart_sum: 0, cart_discount: 0, promocode: ''}
    };
}

var CartModel = {
    constructor: function(){
        var self = this;
        
        // Инициализация корзины через ko.mapping для автоматического преобразования
        var cartData = (typeof server_cart !== 'undefined' && server_cart.data) ? server_cart.data : {items: {}, total_cart_sum: 0, total_cart_amount: 0, cart_sum: 0, cart_discount: 0, promocode: ''};
        // console.log('CartModel constructor - initializing with cartData:', cartData);
        // console.log('Items in cartData:', Object.keys(cartData.items || {}).length);
        self.cart = ko.observable(ko.mapping.fromJS(cartData)).extend({ deferred: true });
        // console.log('Cart initialized. Items in cart:', Object.keys(ko.unwrap(self.cart().items) || {}).length);
        
        // Флаг для предотвращения бесконечного цикла при обновлении selected с сервера
        self._updatingSelected = {};
        
        // Промокод
        self.promocode = ko.observable(ko.unwrap(self.cart().promocode) || '');
        self.promocode_input = ko.observable('');
        // Алиас для совместимости с шаблонами (promoCode вместо promocode_input)
        self.promoCode = self.promocode_input;
        self.promocode_applied = ko.observable(false);
        self.promocode_message = ko.observable('');
        
        // Доставка (для checkout)
        self.delivery_price = ko.observable(0);
        self.delivery_method = ko.observable('pickup');
        
        // Вычисляемые свойства - общая сумма товаров
        // Используем значение из сервера, если оно есть, иначе вычисляем
        self.cart_sum = ko.pureComputed({
            read: function () {
                var cartData = self.cart();
                // Сначала пытаемся получить значение из сервера
                var serverCartSum = parseFloat(ko.unwrap(cartData.cart_sum) || 0) || 0;
                
                // Если значение с сервера есть и больше 0, используем его
                if (serverCartSum > 0) {
                    return serverCartSum;
                }
                
                // Иначе вычисляем вручную
                var sum = 0;
                var itemsObj = null;
                
                // Получаем items из cart (ko.mapping создает observable объект, не функцию)
                if (cartData && typeof cartData === 'object') {
                    if (typeof cartData.items === 'function') {
                        itemsObj = cartData.items();
                    } else if (cartData.items && typeof cartData.items === 'object') {
                        itemsObj = ko.unwrap(cartData.items) || cartData.items;
                    } else if (cartData.items !== undefined) {
                        itemsObj = cartData.items;
                    }
                }
                
                if (itemsObj && typeof itemsObj === 'object' && !Array.isArray(itemsObj)) {
                    for (var key in itemsObj) {
                        if (itemsObj.hasOwnProperty(key)) {
                            var item = itemsObj[key];
                            var itemUnwrapped = ko.unwrap(item) || item;
                            // Проверяем, выбран ли товар (по умолчанию true, если свойство не определено)
                            var isSelected = itemUnwrapped.selected !== undefined ? (typeof itemUnwrapped.selected === 'function' ? itemUnwrapped.selected() : itemUnwrapped.selected) : true;
                            if (isSelected) {
                                var cost = parseFloat(itemUnwrapped.cost || itemUnwrapped.price || 0) || 0;
                                var amount = parseInt(itemUnwrapped.product_amount || 1) || 1;
                                sum = sum + (cost * amount);
                            }
                        }
                    }
                }
                return sum;
            }
        });
        
        // Вычисляемые свойства - скидка
        self.cart_discount = ko.pureComputed({
            read: function () {
                var discount = parseFloat(ko.unwrap(self.cart().cart_discount) || 0) || 0;
                return discount;
            }
        });
        
        // Алиас для совместимости с шаблонами
        self.discountTotal = self.cart_discount;
        
        // Вычисляемые свойства - общее количество товаров
        self.total_cart_amount = ko.pureComputed({
            read: function () {
                var total_amount = 0;
                var cartData = self.cart();
                var itemsObj = null;
                
                // Получаем items из cart
                if (cartData && typeof cartData === 'object') {
                    if (typeof cartData.items === 'function') {
                        itemsObj = cartData.items();
                    } else if (cartData.items && typeof cartData.items === 'object') {
                        itemsObj = ko.unwrap(cartData.items) || cartData.items;
                    } else if (cartData.items !== undefined) {
                        itemsObj = cartData.items;
                    }
                }
                
                if (itemsObj && typeof itemsObj === 'object' && !Array.isArray(itemsObj)) {
                    for (var key in itemsObj) {
                        if (itemsObj.hasOwnProperty(key)) {
                            var item = itemsObj[key];
                            var itemUnwrapped = ko.unwrap(item) || item;
                            // Проверяем, выбран ли товар (по умолчанию true, если свойство не определено)
                            var isSelected = itemUnwrapped.selected !== undefined ? (typeof itemUnwrapped.selected === 'function' ? itemUnwrapped.selected() : itemUnwrapped.selected) : true;
                            if (isSelected) {
                                var amount = parseInt(itemUnwrapped.product_amount || 1) || 1;
                                total_amount = total_amount + amount;
                            }
                        }
                    }
                }
                return total_amount;
            }
        });
        
        // Вычисляемые свойства - итоговая сумма
        self.total_cart_sum = ko.pureComputed({
            read: function () {
                var total_sum = self.cart_sum() - self.cart_discount();
                return Math.round(total_sum * 100) / 100;
            }
        });
        
        // Форматированная итоговая сумма
        self.formatted_total_cart_sum = ko.pureComputed({
            read: function () {
                return self.total_cart_sum().toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + ' ₽';
            }
        });
        
        // Итоговая сумма с доставкой (для checkout)
        self.grandTotal = ko.pureComputed({
            read: function () {
                var total = self.total_cart_sum() + parseFloat(self.delivery_price() || 0);
                return Math.round(total * 100) / 100;
            }
        });
        
        // Форматированная итоговая сумма с доставкой
        self.formattedGrandTotal = ko.pureComputed({
            read: function () {
                return self.grandTotal().toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + ' ₽';
            }
        });
        
        // Форматированное количество товаров
        self.formatted_total_cart_amount = ko.pureComputed({
            read: function () {
                var count = self.total_cart_amount();
                if (count === 0) return '';
                if (count === 1) return '1';
                return count.toString();
            }
        });
        
        // Форматированное количество товаров для отображения в корзине
        self.formattedItemsCount = ko.pureComputed({
            read: function () {
                var count = self.total_cart_amount();
                if (count === 0) return '(0 товаров)';
                if (count === 1) return '(1 товар)';
                if (count >= 2 && count <= 4) return '(' + count + ' товара)';
                return '(' + count + ' товаров)';
            }
        });
        
        // Форматированная сумма товаров (без скидки)
        self.formattedItemsTotal = ko.pureComputed({
            read: function () {
                return self.cart_sum().toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + ' ₽';
            }
        });
        
        // Форматированная скидка
        self.formattedDiscountTotal = ko.pureComputed({
            read: function () {
                var discount = self.cart_discount();
                if (discount > 0) {
                    return '−' + discount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + ' ₽';
                }
                return '0 ₽';
            }
        });
        
        // Форматированная цена доставки
        self.formattedDeliveryPrice = ko.pureComputed({
            read: function () {
                var price = parseFloat(self.delivery_price()) || 0;
                if (price > 0) {
                    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + ' ₽';
                }
                return 'Бесплатно';
            }
        });
        
        // Можно ли оформить заказ (есть ли выбранные товары)
        self.canCheckout = ko.pureComputed({
            read: function () {
                return self.total_cart_amount() > 0;
            }
        });
        
        // Преобразование items из объекта в массив для работы с foreach
        // Это computed observable, который преобразует объект items в массив
        // Для checkout страницы фильтруем только selected товары
        // Используем ko.computed вместо ko.pureComputed, чтобы он всегда пересчитывался при изменении структуры объекта
        self.items = ko.computed({
            read: function () {
                console.log('=== items computed called ===');
                var cartData = self.cart();
                console.log('cartData:', cartData);
                console.log('cartData.items:', cartData ? cartData.items : 'N/A');
                var itemsObj = null;
                var isCheckoutPage = $('.checkout-sidebar').length > 0;
                
                // ko.mapping.fromJS() для объекта {items: {}} создает observable объект для items
                // НО это НЕ observable функция, поэтому items() не работает
                // Нужно использовать ko.unwrap() или обращаться напрямую
                if (cartData && typeof cartData === 'object') {
                    // Если items - это observable функция (старый подход с массивом)
                    if (typeof cartData.items === 'function') {
                        itemsObj = cartData.items();
                    } 
                    // Если items - это observable объект (ko.mapping создает observable для объектов)
                    // ko.unwrap правильно обработает и observable, и обычное значение
                    else if (cartData.items !== undefined) {
                        itemsObj = ko.unwrap(cartData.items);
                    }
                }
                
                var itemsArray = [];
                
                // Если items - это объект, преобразуем в массив
                if (itemsObj && typeof itemsObj === 'object' && !Array.isArray(itemsObj)) {
                    for (var key in itemsObj) {
                        if (itemsObj.hasOwnProperty(key)) {
                            var item = itemsObj[key];
                            // Добавляем id в объект товара
                            var itemData = ko.unwrap(item) || item;
                            if (typeof itemData === 'object' && itemData !== null) {
                                // Копируем данные товара
                                var itemCopy = {};
                                for (var prop in itemData) {
                                    if (itemData.hasOwnProperty(prop)) {
                                        var propValue = itemData[prop];
                                        // Если свойство - это observable, получаем его значение
                                        itemCopy[prop] = ko.unwrap(propValue) !== undefined ? ko.unwrap(propValue) : propValue;
                                    }
                                }
                                itemCopy.id = key;
                                itemCopy.guid = key;
                                // Создаем observable для каждого товара через ko.mapping
                                var mappedItem = ko.mapping.fromJS(itemCopy);
                                
                                // Добавляем computed observables для каждого элемента
                                // Используем замыкание для правильной работы с mappedItem
                                (function(item) {
                                    // Сохраняем исходные значения для доступа без рекурсии
                                    var itemName = itemCopy.name || itemCopy.title || '';
                                    var itemImgUrl = itemCopy.img_url || itemCopy.image || '/assets/img/product_empty.jpg';
                                    var itemCost = parseFloat(itemCopy.cost || itemCopy.price || 0) || 0;
                                    var itemAmount = parseInt(itemCopy.product_amount || 1) || 1;
                                    var itemOldPrice = parseFloat(itemCopy.old_price || 0) || 0;
                                    
                                    // productUrl - URL страницы товара
                                    item.productUrl = ko.pureComputed(function() {
                                        var itemId = ko.unwrap(item.id) || ko.unwrap(item.guid) || key;
                                        return '/product/' + itemId + '/';
                                    });
                                    
                                    // image - URL изображения товара (используем сохраненное значение)
                                    item.image = ko.observable(itemImgUrl);
                                    
                                    // name - название товара (используем сохраненное значение)
                                    item.name = ko.observable(itemName);
                                    
                                    // formattedPrice - отформатированная цена за единицу
                                    item.formattedPrice = ko.pureComputed(function() {
                                        // Получаем актуальное значение cost из observable
                                        var price = parseFloat(ko.unwrap(item.cost) || ko.unwrap(item.price) || itemCost) || 0;
                                        return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + ' ₽';
                                    });
                                    
                                    // formattedTotal - отформатированная общая цена (цена × количество)
                                    item.formattedTotal = ko.pureComputed(function() {
                                        var price = parseFloat(ko.unwrap(item.cost) || ko.unwrap(item.price) || itemCost) || 0;
                                        var amount = parseInt(ko.unwrap(item.product_amount) || itemAmount) || 1;
                                        var total = price * amount;
                                        return total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + ' ₽';
                                    });
                                    
                                    // hasDiscount - есть ли скидка
                                    item.hasDiscount = ko.pureComputed(function() {
                                        var oldPrice = parseFloat(ko.unwrap(item.old_price) || itemOldPrice) || 0;
                                        var price = parseFloat(ko.unwrap(item.cost) || ko.unwrap(item.price) || itemCost) || 0;
                                        return oldPrice > 0 && oldPrice > price;
                                    });
                                    
                                    // formattedOldPrice - отформатированная старая цена
                                    item.formattedOldPrice = ko.pureComputed(function() {
                                        var oldPrice = parseFloat(ko.unwrap(item.old_price) || itemOldPrice) || 0;
                                        if (oldPrice > 0) {
                                            return oldPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + ' ₽';
                                        }
                                        return '';
                                    });
                                    
                                    // formattedQuantity - отформатированное количество (для checkout)
                                    item.formattedQuantity = ko.pureComputed(function() {
                                        var amount = parseInt(ko.unwrap(item.product_amount) || itemAmount) || 1;
                                        return amount + ' шт.';
                                    });
                                    
                                    // max_quantity - максимальное количество товара (для валидации input)
                                    // Используем observable, чтобы оно обновлялось при изменении данных
                                    var itemMaxQuantity = parseInt(itemCopy.quantity || itemCopy.max_quantity || 99) || 99;
                                    if (!item.max_quantity || typeof item.max_quantity !== 'function') {
                                        item.max_quantity = ko.observable(itemMaxQuantity);
                                    } else {
                                        item.max_quantity(itemMaxQuantity);
                                    }
                                    
                                    // selected - выбран ли товар для заказа (по умолчанию true)
                                    // Если товар не выбран, он не учитывается в расчетах и не попадает в заказ
                                    var itemSelected = itemCopy.selected !== undefined ? itemCopy.selected : true;
                                    if (!item.selected || typeof item.selected !== 'function') {
                                        item.selected = ko.observable(itemSelected);
                                    } else {
                                        // Обновляем значение только если оно отличается от текущего
                                        var currentValue = ko.unwrap(item.selected);
                                        if (currentValue !== itemSelected) {
                                            item.selected(itemSelected);
                                        }
                                    }
                                    
                                    // Подписываемся на изменение selected, чтобы обновлять состояние на сервере
                                    // Важно: subscribe срабатывает только при изменении значения observable
                                    // Если мы обновляем значение программно (из ответа сервера), 
                                    // subscribe тоже сработает, поэтому нужно проверять, не идет ли уже обновление
                                    var itemGuid = ko.unwrap(item.id) || ko.unwrap(item.guid) || key;
                                    if (itemGuid) {
                                        // Инициализируем флаг для этого товара, если его еще нет
                                        if (!self._updatingSelected) {
                                            self._updatingSelected = {};
                                        }
                                        
                                        item.selected.subscribe(function(newValue) {
                                            // Пропускаем, если обновление идет программно (из ответа сервера)
                                            if (self._updatingSelected[itemGuid]) {
                                                return;
                                            }
                                            
                                            // Вызываем обновление на сервере только при ручном изменении пользователем
                                            self.update_item_selected(itemGuid, newValue);
                                        });
                                    }
                                })(mappedItem);
                                itemsArray.push(mappedItem);
                            }
                        }
                    }
                }
                
                // Если это страница оформления заказа, фильтруем только выбранные товары
                if (isCheckoutPage) {
                    return itemsArray.filter(function(item) {
                        return ko.unwrap(item.selected);
                    });
                }
                
                return itemsArray;
            },
            deferEvaluation: true // Откладываем вычисление до первого запроса
        });
        
        // Включение модульных методов через Blade includes
        <?php echo $__env->make('js.models.cart_new.put_into_cart', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('js.models.cart_new.remove_from_cart', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('js.models.cart_new.clear_cart', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('js.models.cart_new.update_amount', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('js.models.cart_new.amount_plus', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('js.models.cart_new.amount_minus', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('js.models.cart_new.validate_amount', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('js.models.cart_new.update_items', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('js.models.cart_new.update_cart', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('js.models.cart_new.try2apply_promocode', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('js.models.cart_new.try2cancel_promocode', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('js.models.cart_new.update_delivery_price', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('js.models.cart_new.refresh_cart', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('js.models.cart_new.update_item_selected', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        
        // Алиасы для методов (для совместимости с разными именами в шаблонах)
        self.decreaseAmount = self.amount_minus;
        self.increaseAmount = self.amount_plus;
        self.removeItem = self.remove_from_cart;
        self.applyPromoCode = self.try2apply_promocode;
        self.cancelPromoCode = self.try2cancel_promocode;
        self.clearCart = self.clear_cart;
        self.updateAmount = self.update_amount;
        
        return self; // Возвращаем self для правильной инициализации
    }
};

// Глобальная инициализация модели корзины
var model_cart = Object.create(CartModel);
model_cart.constructor();
console.log('✓ model_cart created:', typeof model_cart, model_cart);

// Алиас для удобства
var cart = model_cart;
console.log('✓ cart alias created:', typeof cart);

// Применяем биндинги KnockoutJS
$(document).ready(function(){
    // Функция для проверки, не является ли элемент счетчиком
    function isCounterElement(element) {
        var $el = $(element);
        if ($el.hasClass('cart-counter') || 
            $el.hasClass('mobile-cart-counter') || 
            $el.hasClass('favorites-counter')) {
            return true;
        }
        if ($el.find('.cart-counter, .mobile-cart-counter, .favorites-counter').length > 0) {
            return true;
        }
        return false;
    }
    
    // Функция для безопасного применения bindings
    function safeApplyBindings(viewModel, element) {
        // Check if bindings are already applied
        var existingData = ko.dataFor(element);
        if (existingData !== undefined) {
            // console.log('Bindings already applied, skipping for element:', element);
            return;
        }
        
        // Verify viewModel is defined before applying
        if (typeof viewModel === 'undefined' || viewModel === null) {
            console.error('Cannot apply bindings: viewModel is undefined or null');
            console.error('Element:', element);
            return;
        }
        
        // Apply bindings with error handling
        try {
            ko.applyBindings(viewModel, element);
            // console.log('Bindings applied successfully to element:', element);
        } catch (error) {
            console.error('Error applying Knockout bindings:', error);
            console.error('Element:', element);
            console.error('ViewModel:', viewModel);
        }
    }
    
    // Функция для применения bindings с проверкой готовности модели
    function applyBindingsWhenReady() {
        console.log('=== applyBindingsWhenReady called ===');
        console.log('typeof model_cart:', typeof model_cart);
        console.log('model_cart value:', model_cart);
        
        // Проверяем, что model_cart определен и инициализирован
        if (typeof model_cart === 'undefined' || model_cart === null) {
            console.error('model_cart is not defined, cannot apply bindings');
            console.error('Available global variables:', Object.keys(window).filter(k => k.includes('cart') || k.includes('model')));
            return;
        }
        
        // Проверяем, что у model_cart есть необходимые свойства
        if (typeof model_cart.cart === 'undefined' || typeof model_cart.items === 'undefined') {
            console.error('model_cart is not properly initialized, cannot apply bindings');
            return;
        }
        
        // console.log('Applying Knockout bindings to cart elements...');
        
        // Применяем bindings к разным элементам
        $('.cart-page').each(function(){
            if (!isCounterElement(this)) {
                safeApplyBindings(model_cart, this);
                console.log('=== Bindings applied to .cart-page ===');
                console.log('model_cart.items():', model_cart.items());
                console.log('model_cart.items().length:', model_cart.items().length);
            }
        });
        
        $('.checkout-sidebar').each(function(){
            if (!isCounterElement(this)) {
                safeApplyBindings(model_cart, this);
            }
        });
                        
        $('.cart').each(function(){
            if (!isCounterElement(this)) {
                safeApplyBindings(model_cart, this);
            }
        });
        
        $('.total_cart_sum').each(function(){
            if (!isCounterElement(this)) {
                          safeApplyBindings(model_cart, this);
            }        });                
            
        $('.total_cart_amount').each(function(){
            if (!isCounterElement(this)) {
                safeApplyBindings(model_cart, this);
             }
        });
        
        $('.order-summary').each(function(){
            if (!isCounterElement(this)) {
                safeApplyBindings(model_cart, this);
            }
        });

        // Применяем биндинги для избранного, если модель существует
        if (typeof model_favorites !== 'undefined') {

            $('.favorites').each(function(){
                safeApplyBindings(model_favorites, this);
            });
            $('.favorites-counter').each(function(){
                safeApplyBindings(model_favorites, this);
            });
        }
    }
    
    console.log('=== Setting up setTimeout for bindings ===');
    console.log('model_cart exists before setTimeout:', typeof model_cart !== 'undefined');
    console.log('jQuery ready state:', document.readyState);
    
    // Сначала загружаем данные с сервера, затем применяем bindings
    // Это гарантирует, что Knockout увидит актуальные данные при инициализации
    setTimeout(function() {
        if (typeof model_cart !== 'undefined' && typeof model_cart.refresh_cart === 'function') {
            console.log('Auto-refreshing cart data from server...');
            // Передаем applyBindingsWhenReady как callback, чтобы он вызвался ПОСЛЕ загрузки данных
            model_cart.refresh_cart(applyBindingsWhenReady);
        } else {
            // Если refresh_cart недоступен, применяем bindings сразу
            setTimeout(applyBindingsWhenReady, 200);
        }
    }, 100);
});

 </script>                       
<?php /**PATH C:\OS\home\sfera\resources\views/js/models/cart_new/model_cart.blade.php ENDPATH**/ ?>