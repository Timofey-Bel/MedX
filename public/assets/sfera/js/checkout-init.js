console.log('=== checkout-init.js loaded ===');

// Создаем model_pickpoint
var PointModel = {
    constructor: function(){
        var self = this;
        self.pickuppoint = ko.observable(false);
        self.PointWorkHours = ko.observable(false);
        self.Photo = ko.observable(false);
        self.WorkBreak = ko.observable(false);
        self.pickpoint = ko.pureComputed({
            read: function () {
                return self.pickuppoint;
            }
        });
        
        // Метод для открытия карты (триггерит клик на кнопке)
        self.openMap = function() {
            console.log('=== openMap called from model_pickpoint ===');
            // Находим кнопку и триггерим на ней событие клика
            // Это вызовет обработчик из checkout.js
            var btn = document.querySelector('.add-address-btn');
            if (btn) {
                // Создаем и диспатчим нативное событие клика
                var event = new MouseEvent('click', {
                    bubbles: true,
                    cancelable: true,
                    view: window
                });
                btn.dispatchEvent(event);
                console.log('✓ Click event dispatched on button');
            } else {
                console.error('Button .add-address-btn not found!');
            }
        };
        
        // Метод для очистки выбранного пункта выдачи
        self.clearPickpoint = function() {
            console.log('=== clearPickpoint called ===');
            self.pickuppoint(false);
            localStorage.removeItem('pickpoint_data');
            localStorage.removeItem('pickpoint_address');
            var pointIdInput = document.getElementById('point_id');
            if (pointIdInput) {
                pointIdInput.value = '';
            }
            console.log('Pickup point cleared');
        };
        
        ko.computed(function(){
            if (!ko.computedContext.isInitial())
                return;
            if (self.pickuppoint()) {
                console.log('Pickup point selected:', self.pickuppoint());
            }
        });
        
        return self;
    }
};

var model_pickpoint = Object.create(PointModel).constructor();
window.model_pickpoint = model_pickpoint;
console.log('✓ model_pickpoint created:', typeof model_pickpoint);
