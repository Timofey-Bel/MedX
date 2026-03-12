/**
 * Модуль для проверки ИНН через DaData API
 * 
 * Использование:
 * const dadataChecker = new DaDataInnChecker('#inn-input', '#check-inn-btn', onSuccess);
 */

class DaDataInnChecker {
    constructor(innInputSelector, checkButtonSelector, onSuccessCallback) {
        this.innInput = document.querySelector(innInputSelector);
        this.checkButton = document.querySelector(checkButtonSelector);
        this.onSuccess = onSuccessCallback;
        this.isChecking = false;

        if (!this.innInput || !this.checkButton) {
            console.error('DaDataInnChecker: Required elements not found');
            return;
        }

        this.init();
    }

    init() {
        // Обработчик клика на кнопку проверки
        this.checkButton.addEventListener('click', (e) => {
            e.preventDefault();
            this.checkInn();
        });

        // Обработчик Enter в поле ИНН
        this.innInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.checkInn();
            }
        });

        // Форматирование ввода (только цифры)
        this.innInput.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    }

    async checkInn() {
        const inn = this.innInput.value.trim();

        // Валидация
        if (!inn) {
            this.showError('Введите ИНН');
            return;
        }

        if (inn.length !== 10 && inn.length !== 12) {
            this.showError('ИНН должен содержать 10 или 12 цифр');
            return;
        }

        // Блокируем кнопку
        this.setLoading(true);
        this.clearMessages();

        try {
            const response = await fetch('/api/dadata/check-inn', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({ inn }),
            });

            const data = await response.json();

            if (response.ok && data.success) {
                this.showSuccess(data.message);
                
                // Вызываем callback с данными организации
                if (this.onSuccess && typeof this.onSuccess === 'function') {
                    this.onSuccess(data.data);
                }
            } else {
                this.showError(data.message || 'Ошибка при проверке ИНН');
            }
        } catch (error) {
            console.error('DaData API Error:', error);
            this.showError('Ошибка соединения с сервером');
        } finally {
            this.setLoading(false);
        }
    }

    setLoading(isLoading) {
        this.isChecking = isLoading;
        this.checkButton.disabled = isLoading;
        this.innInput.disabled = isLoading;

        if (isLoading) {
            this.checkButton.textContent = 'Проверка...';
            this.checkButton.classList.add('loading');
        } else {
            this.checkButton.textContent = 'Проверить ИНН';
            this.checkButton.classList.remove('loading');
        }
    }

    showSuccess(message) {
        this.clearMessages();
        const messageEl = this.createMessage(message, 'success');
        this.innInput.parentElement.appendChild(messageEl);
        this.innInput.classList.remove('error');
        this.innInput.classList.add('success');
    }

    showError(message) {
        this.clearMessages();
        const messageEl = this.createMessage(message, 'error');
        this.innInput.parentElement.appendChild(messageEl);
        this.innInput.classList.add('error');
        this.innInput.classList.remove('success');
    }

    createMessage(text, type) {
        const div = document.createElement('div');
        div.className = `dadata-message dadata-message-${type}`;
        div.textContent = text;
        return div;
    }

    clearMessages() {
        const messages = this.innInput.parentElement.querySelectorAll('.dadata-message');
        messages.forEach(msg => msg.remove());
        this.innInput.classList.remove('error', 'success');
    }
}

// Экспорт для использования в других модулях
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DaDataInnChecker;
}
