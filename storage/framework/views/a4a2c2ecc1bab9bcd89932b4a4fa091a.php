
<div class="city-modal-overlay" id="cityModalOverlay">
    <div class="city-modal" id="cityModal">
        
        <div class="city-modal-header">
            <h3>Выберите ваш город</h3>
            <button class="city-modal-close" id="cityModalClose" aria-label="Закрыть">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        
        <div class="city-modal-search">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" class="city-search-icon">
                <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="2"/>
                <path d="M12.5 12.5l4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <input 
                type="text" 
                id="citySearchInput" 
                class="city-search-input" 
                placeholder="Начните вводить название города..."
                autocomplete="off"
            >
        </div>

        
        <div class="city-modal-list" id="cityList">
            <div class="city-list-loading">Загрузка...</div>
        </div>
    </div>
</div>
<?php /**PATH C:\OS\home\sfera\resources\views/components/city-modal.blade.php ENDPATH**/ ?>