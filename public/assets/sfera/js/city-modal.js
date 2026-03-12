// City Modal JavaScript

let cityModalState = {
    cities: [],
    selectedCityId: null,
    searchTimeout: null
};

/**
 * Открыть модальное окно выбора города
 */
function openCityModal() {
    const overlay = document.getElementById('cityModalOverlay');
    const searchInput = document.getElementById('citySearchInput');
    
    if (overlay) {
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Загружаем список городов
        loadCities();
        
        // Фокус на поле поиска
        setTimeout(() => {
            if (searchInput) {
                searchInput.focus();
            }
        }, 300);
    }
}

/**
 * Закрыть модальное окно
 */
function closeCityModal() {
    const overlay = document.getElementById('cityModalOverlay');
    const searchInput = document.getElementById('citySearchInput');
    
    if (overlay) {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
        
        // Очищаем поле поиска
        if (searchInput) {
            searchInput.value = '';
        }
    }
}

/**
 * Загрузить список городов
 */
function loadCities(query = '') {
    const cityList = document.getElementById('cityList');
    
    if (!cityList) return;
    
    // Показываем индикатор загрузки
    cityList.innerHTML = '<div class="city-list-loading">Загрузка...</div>';
    
    // Формируем URL
    const url = query 
        ? `/api/cities/search?query=${encodeURIComponent(query)}`
        : '/api/cities';
    
    // Загружаем города
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.cities) {
                cityModalState.cities = data.cities;
                renderCities(data.cities);
            } else {
                cityList.innerHTML = '<div class="city-list-empty">Ошибка загрузки городов</div>';
            }
        })
        .catch(error => {
            console.error('Error loading cities:', error);
            cityList.innerHTML = '<div class="city-list-empty">Ошибка загрузки городов</div>';
        });
}

/**
 * Отобразить список городов
 */
function renderCities(cities) {
    const cityList = document.getElementById('cityList');
    
    if (!cityList) return;
    
    if (cities.length === 0) {
        cityList.innerHTML = '<div class="city-list-empty">Города не найдены</div>';
        return;
    }
    
    // Получаем текущий выбранный город из сессии
    const selectedCityName = document.getElementById('selected-city')?.textContent || '';
    
    let html = '';
    cities.forEach(city => {
        const isSelected = city.name === selectedCityName;
        const selectedClass = isSelected ? ' selected' : '';
        
        html += `
            <button class="city-item${selectedClass}" data-city-id="${city.id}" data-city-name="${city.name}">
                ${city.name}
            </button>
        `;
    });
    
    cityList.innerHTML = html;
    
    // Добавляем обработчики клика
    cityList.querySelectorAll('.city-item').forEach(item => {
        item.addEventListener('click', () => {
            const cityId = item.dataset.cityId;
            const cityName = item.dataset.cityName;
            selectCity(cityId, cityName);
        });
    });
}

/**
 * Выбрать город
 */
function selectCity(cityId, cityName) {
    // Получаем CSRF токен
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Отправляем запрос на сервер
    fetch('/api/cities/select', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            city_id: cityId
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || 'Server error');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Обновляем отображаемый город в header
            const selectedCityElement = document.getElementById('selected-city');
            if (selectedCityElement) {
                selectedCityElement.textContent = cityName;
            }
            
            // Закрываем модальное окно
            closeCityModal();
            
            // Можно добавить уведомление об успешном выборе
            console.log('City selected:', cityName);
        } else {
            console.error('Error selecting city:', data.message);
            alert('Ошибка при выборе города: ' + (data.message || 'Неизвестная ошибка'));
        }
    })
    .catch(error => {
        console.error('Error selecting city:', error);
        alert('Ошибка при выборе города: ' + error.message);
    });
}

/**
 * Инициализация модального окна
 */
function initCityModal() {
    const overlay = document.getElementById('cityModalOverlay');
    const closeButton = document.getElementById('cityModalClose');
    const searchInput = document.getElementById('citySearchInput');
    
    // Закрытие по клику на overlay
    if (overlay) {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                closeCityModal();
            }
        });
    }
    
    // Закрытие по кнопке
    if (closeButton) {
        closeButton.addEventListener('click', closeCityModal);
    }
    
    // Закрытие по Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && overlay && overlay.classList.contains('active')) {
            closeCityModal();
        }
    });
    
    // Поиск городов при вводе
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            
            // Очищаем предыдущий таймаут
            if (cityModalState.searchTimeout) {
                clearTimeout(cityModalState.searchTimeout);
            }
            
            // Задержка перед поиском (debounce)
            cityModalState.searchTimeout = setTimeout(() => {
                loadCities(query);
            }, 300);
        });
    }
}

// Инициализация при загрузке страницы
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCityModal);
} else {
    initCityModal();
}
