/**
 * Global Favorites Counter using KnockoutJS
 * Легкая модель для отображения счетчика избранного на всех страницах
 */

// console.log('=== Favorites Counter Script Loading ===');

// Глобальная модель счетчика избранного
function FavoritesCounterViewModel() {
    var self = this;
    
    // console.log('=== Initializing FavoritesCounterViewModel ===');
    
    // Количество товаров в избранном
    self.itemCount = ko.observable(0);
    
    // Форматированное количество
    self.formattedCount = ko.computed(function() {
        var count = self.itemCount();
        // console.log('Favorites counter computed:', count);
        return count > 0 ? count : '';
    });
    
    // Видимость счетчика
    self.isVisible = ko.computed(function() {
        return self.itemCount() > 0;
    });
    
    // Загрузка счетчика с сервера
    self.loadCount = function() {
        console.log('=== Loading favorites count from server ===');
        
        fetch('/api/favorites', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'task=get_favorites',
            credentials: 'same-origin' // Важно: отправляем cookies для сохранения сессии
        })
        .then(function(response) {
            console.log('Favorites count response status:', response.status);
            return response.json();
        })
        .then(function(data) {
            console.log('Favorites count data received:', data);
            
            var count = 0;
            if (data && data.count !== undefined) {
                count = parseInt(data.count);
            } else if (data && data.items) {
                // Если items это массив
                if (Array.isArray(data.items)) {
                    count = data.items.length;
                } else if (typeof data.items === 'object') {
                    // Если items это объект
                    count = Object.keys(data.items).length;
                }
            }
            
            console.log('Setting favorites count to:', count);
            self.itemCount(count);
        })
        .catch(function(error) {
            console.error('Error loading favorites count:', error);
        });
    };
    
    // Обновление счетчика (вызывается после операций)
    self.updateCount = function(favoritesData) {
        // console.log('=== Updating favorites count ===', favoritesData);
        
        if (favoritesData && favoritesData.count !== undefined) {
            self.itemCount(parseInt(favoritesData.count));
            // console.log('Favorites count updated to:', favoritesData.count);
        } else if (favoritesData && favoritesData.items) {
            var count = Object.keys(favoritesData.items).length;
            self.itemCount(count);
            // console.log('Favorites count updated to:', count);
        } else {
            // Если данных нет, перезагружаем с сервера
            self.loadCount();
        }
    };
    
    // Инициализация - загрузка счетчика
    // console.log('FavoritesCounterViewModel initialized, loading count...');
    self.loadCount();
}

// Глобальная переменная для доступа к счетчику избранного
var favoritesCounterViewModel = null;

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DOM Content Loaded - Initializing Favorites Counter ===');
    
    // Создаем ViewModel
    favoritesCounterViewModel = new FavoritesCounterViewModel();
    
    // Применяем bindings ко всем счетчикам избранного
    var favoritesBadges = document.querySelectorAll('.favorites-counter');
    console.log('Found ' + favoritesBadges.length + ' favorites counter elements');
    
    favoritesBadges.forEach(function(badge, index) {
        // Проверяем, не применены ли уже bindings
        if (ko.dataFor(badge) === undefined) {
            console.log('Applying bindings to favorites counter #' + index + ':', badge);
            try {
                ko.applyBindings(favoritesCounterViewModel, badge);
                console.log('Successfully applied bindings to counter #' + index);
            } catch (error) {
                console.error('Error applying bindings to counter #' + index + ':', error);
            }
        } else {
            console.log('Bindings already applied to favorites counter #' + index + ', skipping');
        }
    });
    
    if (favoritesBadges.length > 0) {
        console.log('Knockout bindings applied to all favorites counters');
        console.log('Initial count:', favoritesCounterViewModel.itemCount());
    } else {
        console.log('No favorites counter elements found');
    }
    
    // Обновление счетчика при переходе на вкладку (visibilitychange)
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden && favoritesCounterViewModel) {
            console.log('=== Tab became visible, refreshing favorites counter ===');
            favoritesCounterViewModel.loadCount();
        }
    });
    
    // Дополнительно: обновление при первом клике после возврата на вкладку
    var clickRefreshEnabled = false;
    document.addEventListener('click', function() {
        if (clickRefreshEnabled && favoritesCounterViewModel) {
            console.log('=== Page clicked after tab switch, refreshing favorites counter ===');
            favoritesCounterViewModel.loadCount();
            clickRefreshEnabled = false;
        }
    }, { once: false });
    
    // Включаем обновление по клику после скрытия вкладки
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clickRefreshEnabled = true;
        }
    });
});

console.log('=== Favorites Counter Script Loaded ===');


