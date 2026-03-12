<?php $__env->startSection('title', 'Оформление заказа'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/sfera/css/checkout.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('head'); ?>
    <!-- Инициализация данных корзины с сервера ДО загрузки других скриптов -->
    <script>
        var server_cart = {};
<?php if(isset($cart) && isset($cart['items']) && count($cart['items']) > 0): ?>
        server_cart.data = <?php echo json_encode($cart); ?>;
<?php else: ?>
        server_cart.data = {total_cart_sum:0, total_cart_amount:0, cart_sum:0, items:{}};
<?php endif; ?>
    </script>
    <script src="https://api-maps.yandex.ru/2.1/?apikey=71dcace5-2bc2-42e5-8e82-62aa21e52541&lang=ru_RU"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<!-- Main Content -->
<main class="checkout-container">
    <div class="container">
        <div class="checkout-layout">
            <!-- Left Column -->
            <div class="checkout-main">
                <h1 class="checkout-title">Оформление заказа</h1>

                <!-- Delivery Address -->
                <section class="checkout-section">
                    <div class="section-header">
                        <h2 class="section-title">1. Адрес доставки</h2>
                    </div>
                    <div class="address-selector">
                        <input type="hidden" name="point_id" id="point_id">

                        <!-- ko if: pickpoint()  -->
                        <div class="address-card active" >
                            <div class="address-content">
                                <h4 data-bind="text: pickuppoint()['address']['full_address']"></h4>
                                <p data-bind="text: 'тел: ' + pickuppoint()['contact']['phone']"></p>
                                <p data-bind="text: pickuppoint()['name']"></p>
                                <p data-bind="text: pickuppoint()['address']['comment']"></p>
                            </div>
                            <button type="button" class="address-edit" data-bind="click: clearPickpoint">Удалить</button>
                        </div>
                        <!-- /ko -->

                        <!-- ko ifnot: pickpoint() -->
                        <button type="button" id="add-address-btn" class="add-address-btn">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            Выбрать адрес доставки
                        </button>
                        <!-- /ko -->
                    </div>
                </section>

                <!-- Delivery Method -->
                <section class="checkout-section">
                    <div class="section-header">
                        <h2 class="section-title">2. Способ доставки</h2>
                    </div>
                    <div class="delivery-methods">
                        <div class="delivery-card active" data-method="pickup">
                            <div class="delivery-radio">
                                <input type="radio" name="delivery" id="pickup" value="pickup" checked>
                                <label for="pickup"></label>
                            </div>
                            <div class="delivery-icon">
                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
                                    <rect x="5" y="10" width="20" height="20" rx="2" stroke="currentColor" stroke-width="2"/>
                                    <path d="M12 25v-5M18 25v-5M15 10v5" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="delivery-content">
                                <h4>Пункт выдачи Яндекс Доставка</h4>
                                <p class="delivery-price">Бесплатно</p>
                                <p class="delivery-date">Завтра, 12 ноября</p>
                            </div>
                        </div>
                        <div class="delivery-card" data-method="courier">
                            <div class="delivery-radio">
                                <input type="radio" name="delivery" id="courier" value="courier">
                                <label for="courier"></label>
                            </div>
                            <div class="delivery-icon">
                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
                                    <rect x="5" y="12" width="22" height="16" rx="2" stroke="currentColor" stroke-width="2"/>
                                    <path d="M27 18h5l3 4v6h-3" stroke="currentColor" stroke-width="2"/>
                                    <circle cx="12" cy="31" r="3" stroke="currentColor" stroke-width="2"/>
                                    <circle cx="30" cy="31" r="3" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="delivery-content">
                                <h4>Курьером до двери</h4>
                                <p class="delivery-price">200 ₽</p>
                                <p class="delivery-date">Завтра, 12 ноября, 10:00 - 18:00</p>
                            </div>
                        </div>
                        <div class="delivery-card" data-method="express">
                            <div class="delivery-radio">
                                <input type="radio" name="delivery" id="express" value="express">
                                <label for="express"></label>
                            </div>
                            <div class="delivery-icon">
                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
                                    <path d="M8 20l5-5 5 5" stroke="currentColor" stroke-width="2"/>
                                    <path d="M13 15v12" stroke="currentColor" stroke-width="2"/>
                                    <circle cx="25" cy="20" r="10" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="delivery-content">
                                <h4>Экспресс-доставка</h4>
                                <p class="delivery-price">500 ₽</p>
                                <p class="delivery-date">Сегодня, 14:00 - 18:00</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Payment Method -->
                <section class="checkout-section">
                    <div class="section-header">
                        <h2 class="section-title">3. Способ оплаты</h2>
                    </div>
                    <div class="payment-methods">
                        <div class="payment-card active" data-payment="card">
                            <div class="payment-radio">
                                <input type="radio" name="payment" id="card" value="card" checked>
                                <label for="card"></label>
                            </div>
                            <div class="payment-icon">
                                <svg width="32" height="24" viewBox="0 0 32 24" fill="none">
                                    <rect x="1" y="1" width="30" height="22" rx="3" stroke="currentColor" stroke-width="2"/>
                                    <path d="M1 7h30" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="payment-content">
                                <h4>Банковская карта</h4>
                                <p>Visa, MasterCard, Мир</p>
                            </div>
                        </div>
                        <div class="payment-card" data-payment="cash">
                            <div class="payment-radio">
                                <input type="radio" name="payment" id="cash" value="cash">
                                <label for="cash"></label>
                            </div>
                            <div class="payment-icon">
                                <svg width="32" height="24" viewBox="0 0 32 24" fill="none">
                                    <rect x="1" y="5" width="30" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
                                    <circle cx="16" cy="12" r="4" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="payment-content">
                                <h4>Наличными при получении</h4>
                                <p>Оплата курьеру или в пункте выдачи</p>
                            </div>
                        </div>
                        <div class="payment-card" data-payment="sberpay">
                            <div class="payment-radio">
                                <input type="radio" name="payment" id="sberpay" value="sberpay">
                                <label for="sberpay"></label>
                            </div>
                            <div class="payment-icon">
                                <svg width="32" height="24" viewBox="0 0 32 24" fill="none">
                                    <circle cx="16" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                    <path d="M16 7v5l3 3" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="payment-content">
                                <h4>SberPay</h4>
                                <p>Быстрая оплата</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Recipient -->
                <section class="checkout-section">
                    <div class="section-header">
                        <h2 class="section-title">4. Получатель</h2>
                    </div>
                    <div class="recipient-form">
                        <div class="form-group">
                            <label for="recipientName">ФИО <span style="color: red;">*</span></label>
                            <input type="text" id="recipientName" name="recipientName" required>
                        </div>
                        <div class="form-group">
                            <label for="recipientPhone">Телефон <span style="color: red;">*</span></label>
                            <input type="tel" id="recipientPhone" name="recipientPhone" required>
                        </div>
                        <div class="form-group">
                            <label for="recipientEmail">Email (необязательно)</label>
                            <input type="email" id="recipientEmail" name="recipientEmail" placeholder="example@mail.ru">
                        </div>
                    </div>
                </section>

                <!-- Comment -->
                <section class="checkout-section">
                    <div class="section-header">
                        <h2 class="section-title">Комментарий к заказу</h2>
                    </div>
                    <textarea class="order-comment" id="orderComment" name="orderComment" placeholder="Укажите пожелания по доставке или другую важную информацию"></textarea>
                </section>
            </div>

            <!-- Right Column - Order Summary -->
            <aside class="checkout-sidebar">
                <div class="order-summary">
                    <h3 class="summary-title">Ваш заказ</h3>

                    <div class="order-items" data-bind="foreach: items()">
                        <div class="order-item">
                            <img data-bind="attr: {src: image(), alt: name()}" onerror="this.onerror=null; this.src='/assets/img/product_empty.jpg';">
                            <div class="item-info">
                                <h4 data-bind="text: name()"></h4>
                                <p class="item-quantity" data-bind="text: formattedQuantity()"></p>
                            </div>
                            <div class="item-price" data-bind="text: formattedTotal()"></div>
                        </div>
                    </div>
                    
                    <!-- ko if: items().length === 0 -->
                    <div class="order-item">
                        <p>Корзина пуста</p>
                    </div>
                    <!-- /ko -->

                    <div class="promo-code">
                        <input type="text" placeholder="Промокод или сертификат" id="promoInput" data-bind="value: promocode_input, valueUpdate: 'afterkeydown', enable: !promocode_applied()">
                        <button class="btn-apply" data-bind="click: applyPromoCode, enable: promocode_input().length > 0 && !promocode_applied()">Применить</button>
                        <!-- ko if: promocode_message -->
                        <div class="promo-message" data-bind="text: promocode_message, css: {success: promocode_applied(), error: !promocode_applied()}"></div>
                        <!-- /ko -->
                        <!-- ko if: promocode_applied -->
                        <div class="promo-applied">
                            <span>Промокод применен</span>
                            <button class="btn-remove-promo" data-bind="click: removePromoCode">×</button>
                        </div>
                        <!-- /ko -->
                    </div>

                    <div class="summary-details">
                        <div class="summary-row">
                            <span data-bind="text: 'Товары ' + formattedItemsCount()"></span>
                            <span class="summary-value" data-bind="text: formattedItemsTotal()"></span>
                        </div>
                        <!-- ko if: discountTotal() > 0 -->
                        <div class="summary-row">
                            <span>Скидка</span>
                            <span class="summary-value discount" data-bind="text: formattedDiscountTotal()"></span>
                        </div>
                        <!-- /ko -->
                        <div class="summary-row">
                            <span>Доставка</span>
                            <span class="summary-value free" data-bind="text: formattedDeliveryPrice()"></span>
                        </div>
                        <div class="summary-row total">
                            <span>Итого</span>
                            <span class="summary-value" data-bind="text: formattedGrandTotal()"></span>
                        </div>
                    </div>

                    <button class="checkout-btn" id="checkoutBtn" data-bind="enable: canCheckout">Оформить заказ</button>

                    <p class="checkout-agreement">
                        Нажимая кнопку, вы соглашаетесь с <a href="#">условиями обработки персональных данных</a> и <a href="#">правилами продажи</a>
                    </p>
                </div>
            </aside>
        </div>
    </div>
</main>

<!-- Map Popup -->
<div id="map-popup" class="map-popup">
    <div class="map-popup-overlay"></div>
    <div class="map-popup-content">
        <button class="map-popup-close" id="map-popup-close">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
        <div id="map"></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/sfera/js/checkout-init.js')); ?>?v=<?php echo e(time()); ?>&bust=<?php echo e(rand()); ?>"></script>
    <script src="<?php echo e(asset('assets/sfera/js/checkout.js')); ?>?v=<?php echo e(time()); ?>&bust=<?php echo e(rand()); ?>"></script>
    
    <script>
        $(document).ready(function(){
            console.log('=== Checkout page initialization ===');
            
            // Ждём создания model_pickpoint с таймаутом
            var checkPickpoint = setInterval(function() {
                if (typeof model_pickpoint !== 'undefined') {
                    clearInterval(checkPickpoint);
                    initializeCheckout();
                }
            }, 50);
            
            // Таймаут на случай, если model_pickpoint не создастся
            setTimeout(function() {
                clearInterval(checkPickpoint);
                if (typeof model_pickpoint === 'undefined') {
                    console.error('КРИТИЧЕСКАЯ ОШИБКА: model_pickpoint не создан после ожидания!');
                }
            }, 3000);
            
            function initializeCheckout() {
                console.log('✓ model_pickpoint найден, инициализация...');
            
            // Привязываем модель пункта выдачи к элементу .address-selector
            if($('.address-selector')[0]) {
                ko.applyBindings(model_pickpoint, $('.address-selector')[0]);
                console.log('✓ model_pickpoint bound to .address-selector');
            }
            
            // Проверяем, что model_cart определен
            if (typeof model_cart === 'undefined' || model_cart === null) {
                console.error('ОШИБКА: model_cart не определен!');
                return;
            }
            
            console.log('✓ model_cart доступен');
            
            // Привязываем model_cart к элементу .checkout-sidebar (если еще не применены bindings)
            var checkoutSidebar = $('.checkout-sidebar')[0];
            if (checkoutSidebar && ko.dataFor(checkoutSidebar) === undefined) {
                ko.applyBindings(model_cart, checkoutSidebar);
                console.log('✓ model_cart bound to .checkout-sidebar');
            }
            } // Закрываем initializeCheckout
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OS\home\sfera\resources\views/checkout/index.blade.php ENDPATH**/ ?>