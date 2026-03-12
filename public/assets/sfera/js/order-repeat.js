/**
 * Функционал повторения заказа с редактированием
 */

document.addEventListener('DOMContentLoaded', function() {
    // Получаем все элементы
    const orderItems = document.querySelectorAll('.order-item');
    const totalAmountElement = document.getElementById('total-amount');
    const totalWholesaleElement = document.getElementById('total-wholesale');
    const totalRetailElement = document.getElementById('total-retail');
    const totalDiscountElement = document.getElementById('total-discount');
    const discountPercentElement = document.getElementById('discount-percent');
    const placeOrderBtn = document.getElementById('btn-place-order');

    // Функция пересчета итоговой суммы
    function updateTotalAmount() {
        let totalWholesale = 0;
        let totalRetail = 0;
        
        orderItems.forEach(item => {
            if (!item.classList.contains('removing')) {
                const wholesalePrice = parseFloat(item.dataset.wholesalePrice);
                const retailPrice = parseFloat(item.dataset.retailPrice);
                const quantityInput = item.querySelector('.quantity-input');
                const quantity = parseInt(quantityInput.value) || 0;
                
                totalWholesale += wholesalePrice * quantity;
                totalRetail += retailPrice * quantity;
            }
        });

        const discount = totalRetail - totalWholesale;
        const discountPercent = totalRetail > 0 ? ((discount / totalRetail) * 100).toFixed(1) : 0;

        // Обновляем итоговую сумму в заголовке
        if (totalAmountElement) {
            totalAmountElement.textContent = formatPrice(totalWholesale);
        }

        totalWholesaleElement.textContent = formatPrice(totalWholesale);
        totalRetailElement.textContent = formatPrice(totalRetail);
        totalDiscountElement.textContent = '-' + formatPrice(discount);
        discountPercentElement.textContent = discountPercent;
    }

    // Функция обновления суммы товара
    function updateItemTotal(item) {
        const wholesalePrice = parseFloat(item.dataset.wholesalePrice);
        const retailPrice = parseFloat(item.dataset.retailPrice);
        const quantityInput = item.querySelector('.quantity-input');
        const quantity = parseInt(quantityInput.value) || 0;
        
        const wholesaleTotal = wholesalePrice * quantity;
        const retailTotal = retailPrice * quantity;
        
        const totalElement = item.querySelector('.item-total');
        const retailTotalElement = item.querySelector('.item-retail-total');
        
        totalElement.textContent = formatPrice(wholesaleTotal);
        if (retailTotalElement) {
            retailTotalElement.textContent = formatPrice(retailTotal);
        }
        
        updateTotalAmount();
    }

    // Форматирование цены
    function formatPrice(price) {
        return new Intl.NumberFormat('ru-RU', {
            style: 'decimal',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(price) + ' ₽';
    }

    // Обработчики для кнопок количества
    orderItems.forEach(item => {
        const quantityInput = item.querySelector('.quantity-input');
        const minusBtn = item.querySelector('.btn-quantity-minus');
        const plusBtn = item.querySelector('.btn-quantity-plus');
        const removeBtn = item.querySelector('.btn-remove-item');

        // Уменьшение количества
        minusBtn.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value) || 1;
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
                updateItemTotal(item);
            }
        });

        // Увеличение количества
        plusBtn.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value) || 1;
            if (currentValue < 999) {
                quantityInput.value = currentValue + 1;
                updateItemTotal(item);
            }
        });

        // Изменение вручную
        quantityInput.addEventListener('input', function() {
            let value = parseInt(this.value) || 1;
            
            // Ограничения
            if (value < 1) value = 1;
            if (value > 999) value = 999;
            
            this.value = value;
            updateItemTotal(item);
        });

        // Удаление товара
        removeBtn.addEventListener('click', function() {
            if (confirm('Удалить этот товар из заказа?')) {
                item.classList.add('fade-out');
                
                setTimeout(() => {
                    item.classList.add('removing');
                    item.style.display = 'none';
                    updateTotalAmount();
                    
                    // Проверяем, остались ли товары
                    const remainingItems = Array.from(orderItems).filter(
                        i => !i.classList.contains('removing')
                    );
                    
                    if (remainingItems.length === 0) {
                        alert('Все товары удалены. Заказ не может быть пустым.');
                        window.location.href = document.querySelector('a[href*="orders.show"]').href;
                    }
                }, 300);
            }
        });
    });

    // Оформление заказа
    placeOrderBtn.addEventListener('click', function() {
        // Собираем данные о товарах
        const orderData = [];
        
        orderItems.forEach(item => {
            if (!item.classList.contains('removing')) {
                const productId = item.querySelector('.file-info-text span').textContent.replace('ID: ', '').trim();
                const productName = item.querySelector('.file-info-text p').textContent.trim();
                const quantity = parseInt(item.querySelector('.quantity-input').value);
                const wholesalePrice = parseFloat(item.dataset.wholesalePrice);
                const retailPrice = parseFloat(item.dataset.retailPrice);
                
                if (productId !== 'N/A' && quantity > 0) {
                    orderData.push({
                        product_id: productId,
                        product_name: productName,
                        quantity: quantity,
                        wholesale_price: wholesalePrice,
                        retail_price: retailPrice
                    });
                }
            }
        });

        if (orderData.length === 0) {
            alert('Заказ не может быть пустым');
            return;
        }

        // Сохраняем в localStorage для передачи на страницу оформления
        localStorage.setItem('repeat_order_data', JSON.stringify(orderData));
        
        // Показываем уведомление
        alert(`Товары добавлены в корзину (${orderData.length} позиций). Переход к оформлению заказа...`);
        
        // TODO: Здесь должна быть логика добавления товаров в корзину и переход на checkout
        // Пока просто показываем данные в консоли
        console.log('Order data:', orderData);
        
        // Временно: возвращаемся к списку заказов
        // window.location.href = '/lk/orders';
        
        // В будущем: переход на страницу оформления заказа
        // window.location.href = '/checkout';
    });
});
