/**
 * Скрипт для страницы оформления заказа
 * 
 * Обеспечивает:
 * - Интеграцию с Яндекс.Картами для выбора пункта выдачи
 * - Загрузку пунктов выдачи из БД
 * - AJAX отправку формы заказа
 * - Валидацию полей формы
 */

(function() {
    'use strict';
    
    console.log('=== External checkout.js loaded ===');

    // Получаем CSRF токен из meta-тега
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Флаг выбора пункта выдачи
    var pickpoint_choosen = false;

    /**
     * Объект для работы с картой
     */
    var Map = {
        map: null,
        _init: false,
        placemark: null,
        blueCollection: null,
        clusterer: null,
        points: null,
        geoObjects: null,
        geolocation: null,

        /**
         * Инициализация карты
         */
        init: function () {
            console.log('=== Map.init() called ===');
            console.log('this._init:', this._init);
            
            if (this._init) {
                console.log('Map already initialized, skipping');
                return false;
            }
            
            console.log('Initializing map...');
            
            let defPoint = [55.755814, 37.617635]; // Москва по умолчанию
            
            // Пытаемся получить геолокацию пользователя
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;
                    defPoint = [lat, lng];
                }, function(err) {
                    console.warn(`ERROR(${err.code}): ${err.message}`);
                });
            }

            // Проверяем сохраненную точку в localStorage
            let selectPoint = JSON.parse(localStorage.getItem('pickpoint_data'));
            
            // Создаем карту
            this.map = new ymaps.Map("map", {
                center: selectPoint || defPoint,
                zoom: 11,
                controls: ['zoomControl', 'fullscreenControl', 'searchControl', 'geolocationControl']
            }, {
                minZoom: 7,
                maxZoom: 16
            });

            // Создаем кластеризатор
            this.clusterer = new ymaps.Clusterer({
                preset: 'islands#invertedBlackClusterIcons',
                hasBalloon: false,
                groupByCoordinates: false,
                clusterDisableClickZoom: true,
                clusterHideIconOnBalloonOpen: false,
                geoObjectHideIconOnBalloonOpen: false
            });

            // Функция для получения опций точки
            const getPointOptions = function () {
                return {
                    preset: 'islands#redDotIcon',  // Изменили на красный для видимости
                    openBalloonOnClick: false,
                    visible: true
                };
            };

            this.geolocation = ymaps.geolocation;
            this.points = [];
            this.geoObjects = [];

            // Шаблон вывода количества точек в кластере
            const MyIconContentLayout = ymaps.templateLayoutFactory.createClass(
                "<span style='color: #FFF;'>{{ properties.geoObjects.length }}</span>"
            );

            this.clusterer.options.set({
                gridSize: 80,
                mapAutoPan: false,
                clusterDisableClickZoom: true,
                clusterIconContentLayout: MyIconContentLayout,
                clusterBalloonContentLayout: "cluster#balloonAccordion"
            });

            // Обработчик изменения границ карты
            this.map.events.add('boundschange', function (event) {
                console.log('=== boundschange event fired ===');
                Map.loadPoints();
            });

            this._init = true;
            console.log('Map initialized successfully');
            
            // Загружаем точки при первой инициализации
            console.log('Loading initial points...');
            this.loadPoints();
        },

        /**
         * Загрузка пунктов выдачи для текущих границ карты
         */
        loadPoints: function() {
            console.log('=== loadPoints called ===');
            if (!this.map) {
                console.error('Map not initialized');
                return;
            }
            
            var bounds = this.map.getBounds();
            console.log('Map bounds:', bounds);
            
            // Функция для получения опций точки
            const getPointOptions = function () {
                return {
                    preset: 'islands#redDotIcon',  // Изменили на красный для видимости
                    openBalloonOnClick: false,
                    visible: true
                };
            };
            
            // Загружаем точки в пределах видимой области карты
            console.log('Sending AJAX request to /checkout/get-pickpoint-list');
            $.ajax({
                type: 'POST',
                url: '/checkout/get-pickpoint-list',
                dataType: 'json',
                data: {
                    task: 'get_pickpoint_list',
                    bounds: bounds,
                    _token: csrfToken
                },
                async: true,
                cache: false,
                error: function (jqXHR, textStatus) {
                    console.error("Ошибка загрузки пунктов выдачи: " + textStatus);
                    console.error("Response:", jqXHR.responseText);
                },
                success: function (data) {
                    console.log('=== AJAX response received ===');
                    console.log('data.result:', data.result);
                    console.log('data.points length:', data.points ? data.points.length : 0);
                    
                    if (!data.result) {
                        console.error("Ошибка: ", data.message);
                        return;
                    }

                    Map.points.length = 0;

                    // Преобразуем точки в массив координат
                    for (var i = 0, len = data.points.length; i < len; i++) {
                        Map.points.push([parseFloat(data.points[i].la), parseFloat(data.points[i].lo)]);
                    }
                    
                    console.log('Map.points after conversion:', Map.points.length, 'points');

                    Map.geoObjects = [];
                    let pointOptions = getPointOptions();
                    
                    console.log('Creating placemarks for', Map.points.length, 'points');
                    
                    // Создаем метки для каждой точки
                    for(var i = 0, len = Map.points.length; i < len; i++) {
                        var placemark = new ymaps.Placemark(Map.points[i], null, pointOptions);

                        // Обработчик клика по метке
                        placemark.events.add('click', function (e) {
                            var coords = e.get('target').geometry.getCoordinates();

                            // Получаем данные о пункте выдачи
                            $.ajax({
                                type: 'POST',
                                url: '/checkout/get-pickpoint-data',
                                dataType: 'json',
                                data: {
                                    task: 'get_pickpoint_data',
                                    coords: coords,
                                    _token: csrfToken
                                },
                                async: false,
                                cache: false,
                                error: function (jqXHR, textStatus) {
                                    console.error("Ошибка получения данных пункта: " + textStatus);
                                },
                                success: function (data) {
                                    console.log('=== Pickpoint data received ===');
                                    console.log('data:', data);
                                    console.log('data.point_data:', data.point_data);
                                    
                                    if (!data.result) {
                                        console.error("Ошибка: ", data.message);
                                        return;
                                    }

                                    // Обновляем модель KnockoutJS
                                    if (typeof model_pickpoint !== 'undefined') {
                                        console.log('Updating model_pickpoint.pickuppoint with:', data.point_data);
                                        model_pickpoint.pickuppoint(data.point_data);
                                        console.log('model_pickpoint.pickuppoint():', model_pickpoint.pickuppoint());
                                        console.log('model_pickpoint.pickpoint():', model_pickpoint.pickpoint());
                                    } else {
                                        console.error('model_pickpoint is undefined!');
                                    }

                                    // Сохраняем в localStorage
                                    localStorage.setItem('pickpoint_data', JSON.stringify(coords));
                                    localStorage.setItem('pickpoint_address', data.point_data.address.full_address);

                                    // Показываем информацию в балуне
                                    var myPlacemark = e.get('target');
                                    myPlacemark.properties.set('balloonContent', 
                                        'Выбран пункт выдачи:<br>' + 
                                        data.point_data.address.full_address + 
                                        ', тел: ' + data.point_data.contact.phone + 
                                        '<br><span>' + data.point_data.address.comment + '</span>'
                                    );
                                    myPlacemark.balloon.open();
                                    pickpoint_choosen = true;

                                    // Закрываем попап карты
                                    closeMapPopup();
                                }
                            });
                        });

                        Map.geoObjects[i] = placemark;
                    }

                    // Обновляем кластеризатор
                    console.log('Updating clusterer with', Map.geoObjects.length, 'geoObjects');
                    Map.clusterer.removeAll();
                    Map.clusterer.add(Map.geoObjects);
                    
                    console.log('Clusterer state after add:');
                    console.log('  - clusterer.getGeoObjects().length:', Map.clusterer.getGeoObjects().length);
                    console.log('  - clusterer.getClusters().length:', Map.clusterer.getClusters().length);

                    Map.map.geoObjects.removeAll();
                    Map.map.geoObjects.add(Map.clusterer);
                    
                    console.log('Map geoObjects after add:');
                    console.log('  - map.geoObjects.getLength():', Map.map.geoObjects.getLength());
                    
                    console.log('=== Points loaded and added to map ===');
                }
            });
        }
    };

    /**
     * Открыть попап с картой
     */
    function openMapPopup() {
        console.log('openMapPopup called');
        const mapPopup = document.getElementById('map-popup');
        console.log('Map popup element:', mapPopup);
        
        if (mapPopup) {
            mapPopup.style.display = 'flex';
            console.log('Map popup display set to flex');
            
            // Инициализируем карту при первом открытии
            if (typeof ymaps !== 'undefined') {
                console.log('Yandex Maps API available, initializing...');
                ymaps.ready(function() {
                    Map.init();
                });
            } else {
                console.error('Yandex Maps API not loaded');
            }
        } else {
            console.error('Map popup element not found');
        }
    }

    /**
     * Закрыть попап с картой
     */
    function closeMapPopup() {
        const mapPopup = document.getElementById('map-popup');
        if (mapPopup) {
            mapPopup.style.display = 'none';
        }
    }

    /**
     * Показать уведомление
     */
    function showNotification(message, type = 'error') {
        // Удаляем предыдущее уведомление если есть
        const existing = document.querySelector('.checkout-notification');
        if (existing) {
            existing.remove();
        }
        
        const notification = document.createElement('div');
        notification.className = `checkout-notification checkout-notification-${type}`;
        notification.textContent = message;
        
        const colors = {
            error: '#dc3545',
            success: '#28a745',
            info: '#17a2b8',
            warning: '#ffc107'
        };
        
        Object.assign(notification.style, {
            position: 'fixed',
            top: '20px',
            left: '50%',
            transform: 'translateX(-50%)',
            backgroundColor: colors[type] || colors.error,
            color: '#fff',
            padding: '16px 24px',
            borderRadius: '8px',
            boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
            zIndex: '10000',
            fontSize: '15px',
            fontWeight: '500',
            maxWidth: '500px',
            animation: 'slideDown 0.3s ease',
            textAlign: 'center'
        });
        
        document.body.appendChild(notification);
        
        // Автоматически удаляем через 4 секунды
        setTimeout(() => {
            notification.style.animation = 'slideUp 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 4000);
    }

    /**
     * Валидация формы
     */
    function validateForm() {
        let isValid = true;
        let errors = [];

        // Проверка ФИО
        const recipientName = document.getElementById('recipientName');
        if (!recipientName || !recipientName.value.trim()) {
            errors.push('Укажите ФИО получателя');
            if (recipientName) {
                recipientName.style.borderColor = '#dc3545';
            }
            isValid = false;
        } else if (recipientName) {
            recipientName.style.borderColor = '';
        }

        // Проверка телефона
        const recipientPhone = document.getElementById('recipientPhone');
        if (!recipientPhone || !recipientPhone.value.trim()) {
            errors.push('Укажите телефон получателя');
            if (recipientPhone) {
                recipientPhone.style.borderColor = '#dc3545';
            }
            isValid = false;
        } else if (recipientPhone) {
            recipientPhone.style.borderColor = '';
        }

        // Показываем первую ошибку
        if (errors.length > 0) {
            showNotification(errors[0], 'error');
        }

        return isValid;
    }

    /**
     * Отправить заказ на сервер
     */
    async function submitOrder() {
        // Валидация формы
        if (!validateForm()) {
            return;
        }

        const checkoutBtn = document.getElementById('checkoutBtn');
        
        // Блокируем кнопку
        if (checkoutBtn) {
            checkoutBtn.disabled = true;
            checkoutBtn.textContent = 'Оформление...';
        }

        // Собираем данные формы
        const formData = new FormData();
        formData.append('recipientName', document.getElementById('recipientName').value.trim());
        formData.append('recipientPhone', document.getElementById('recipientPhone').value.trim());
        formData.append('recipientEmail', document.getElementById('recipientEmail')?.value.trim() || '');
        formData.append('orderComment', document.getElementById('orderComment')?.value.trim() || '');
        formData.append('delivery', document.querySelector('input[name="delivery"]:checked')?.value || 'pickup');
        formData.append('payment', document.querySelector('input[name="payment"]:checked')?.value || 'card');
        formData.append('_token', csrfToken);

        try {
            // Отправляем AJAX запрос
            const response = await fetch('/checkout', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                // Успешное создание заказа
                showNotification('Заказ успешно оформлен!', 'success');
                
                // Редирект на страницу благодарности через 1 секунду
                setTimeout(() => {
                    window.location.href = data.redirect || '/thankyoupage';
                }, 1000);
            } else {
                // Ошибка при создании заказа
                showNotification(data.message || 'Ошибка при оформлении заказа', 'error');
                
                if (checkoutBtn) {
                    checkoutBtn.disabled = false;
                    checkoutBtn.textContent = 'Оформить заказ';
                }
            }
        } catch (error) {
            console.error('Ошибка при отправке заказа:', error);
            showNotification('Ошибка при оформлении заказа. Попробуйте позже.', 'error');
            
            if (checkoutBtn) {
                checkoutBtn.disabled = false;
                checkoutBtn.textContent = 'Оформить заказ';
            }
        }
    }

    /**
     * Обработчики выбора способа доставки
     */
    function initDeliveryHandlers() {
        const deliveryCards = document.querySelectorAll('.delivery-card');
        deliveryCards.forEach(card => {
            card.addEventListener('click', function() {
                // Убираем active у всех карточек
                deliveryCards.forEach(c => c.classList.remove('active'));
                
                // Добавляем active к выбранной
                this.classList.add('active');
                
                // Отмечаем radio
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                }
            });
        });
    }

    /**
     * Обработчики выбора способа оплаты
     */
    function initPaymentHandlers() {
        const paymentCards = document.querySelectorAll('.payment-card');
        paymentCards.forEach(card => {
            card.addEventListener('click', function() {
                // Убираем active у всех карточек
                paymentCards.forEach(c => c.classList.remove('active'));
                
                // Добавляем active к выбранной
                this.classList.add('active');
                
                // Отмечаем radio
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                }
            });
        });
    }

    /**
     * Инициализация обработчиков событий
     */
    function initEventHandlers() {
        // Кнопка "Выбрать адрес доставки" - используем делегирование через document
        document.addEventListener('click', function(e) {
            if (e.target.closest('#add-address-btn')) {
                console.log('Add address button clicked');
                e.preventDefault();
                openMapPopup();
            }
        });

        // Кнопка закрытия попапа карты
        const mapPopupClose = document.getElementById('map-popup-close');
        if (mapPopupClose) {
            mapPopupClose.addEventListener('click', closeMapPopup);
        }

        // Закрытие попапа по клику на overlay
        const mapPopupOverlay = document.querySelector('.map-popup-overlay');
        if (mapPopupOverlay) {
            mapPopupOverlay.addEventListener('click', closeMapPopup);
        }

        // Кнопка "Оформить заказ"
        const checkoutBtn = document.getElementById('checkoutBtn');
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', submitOrder);
        }

        // Сброс подсветки ошибок при вводе
        const recipientName = document.getElementById('recipientName');
        const recipientPhone = document.getElementById('recipientPhone');
        
        if (recipientName) {
            recipientName.addEventListener('input', function() {
                this.style.borderColor = '';
            });
        }
        
        if (recipientPhone) {
            recipientPhone.addEventListener('input', function() {
                this.style.borderColor = '';
            });
        }

        // Обработчики выбора доставки и оплаты
        initDeliveryHandlers();
        initPaymentHandlers();
    }

    /**
     * Инициализация при загрузке страницы
     */
    function init() {
        initEventHandlers();
    }

    // Запускаем инициализацию после загрузки DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
