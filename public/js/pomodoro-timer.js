// Система уведомлений помодоро
window.PomodoroNotification = {
    currentNotification: null,
    
    show(title, message, actionText, onAction) {
        // Удаляем предыдущее уведомление если есть
        this.hide();
        
        // Создаем новое уведомление
        const notification = document.createElement('div');
        notification.className = 'pomodoro-notification';
        
        notification.innerHTML = `
            <div class="notification-header">
                <h3 class="notification-title">${title}</h3>
                <button class="notification-close" aria-label="Закрыть">×</button>
            </div>
            <p class="notification-message">${message}</p>
            ${actionText ? `<button class="notification-action">${actionText}</button>` : ''}
        `;
        
        // Добавляем в body
        document.body.appendChild(notification);
        this.currentNotification = notification;
        
        // Обработчик закрытия
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => {
            this.hide();
        });
        
        // Обработчик действия
        if (actionText && onAction) {
            const actionBtn = notification.querySelector('.notification-action');
            actionBtn.addEventListener('click', () => {
                onAction();
                this.hide();
            });
        }
        
        // Автоматическое закрытие через 10 секунд
        setTimeout(() => {
            this.hide();
        }, 10000);
        
        return notification;
    },
    
    hide() {
        if (this.currentNotification) {
            this.currentNotification.classList.add('hiding');
            
            setTimeout(() => {
                if (this.currentNotification && this.currentNotification.parentNode) {
                    this.currentNotification.parentNode.removeChild(this.currentNotification);
                }
                this.currentNotification = null;
            }, 300);
        }
    }
};

// Помодоро таймер
(function() {
    'use strict';
    
    console.log('Загрузка скрипта помодоро таймера');
    
    function initPomodoroTimer() {
        console.log('Инициализация помодоро таймера');
        
        const pomodoroTimer = {
            workTime: 25 * 60, // 25 минут в секундах
            shortBreak: 5 * 60, // 5 минут
            longBreak: 15 * 60, // 15 минут
            currentTime: 25 * 60,
            round: 1,
            isRunning: false,
            isBreak: false,
            interval: null,
            isEditable: true,
            lastTick: null,
            
            elements: {
                timerMinutes: document.getElementById('timerMinutes'),
                timerSeconds: document.getElementById('timerSeconds'),
                timerValueDisplay: document.getElementById('timerValue'),
                timerValueContainer: document.querySelector('.timer-value-container'),
                timerRound: document.getElementById('timerRound'),
                playBtn: document.getElementById('playBtn'),
                pauseBtn: document.getElementById('pauseBtn'),
                resetBtn: document.getElementById('resetBtn'),
                timerCard: document.querySelector('.timer-card'),
                timerBackground: document.querySelector('.timer-background')
            },
            
            init() {
                console.log('Инициализация таймера');
                console.log('Элементы:', this.elements);
                
                // Загружаем состояние из localStorage
                this.loadState();
                
                // Если элементы UI не найдены, запускаем фоновый режим
                if (!this.elements.playBtn) {
                    console.log('UI элементы не найдены, запуск в фоновом режиме');
                    this.startBackgroundMode();
                    return true;
                }
                
                // События для кнопок
                if (this.elements.playBtn) {
                    this.elements.playBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        console.log('Клик на play');
                        this.start();
                    });
                }
                
                if (this.elements.pauseBtn) {
                    this.elements.pauseBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        console.log('Клик на pause');
                        this.pause();
                    });
                }
                
                if (this.elements.resetBtn) {
                    this.elements.resetBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        console.log('Клик на reset');
                        this.reset();
                    });
                }
                
                // События для редактирования времени
                if (this.elements.timerMinutes) {
                    this.elements.timerMinutes.addEventListener('input', (e) => {
                        this.handleTimeInput(e, 'minutes');
                    });
                    
                    this.elements.timerMinutes.addEventListener('blur', () => {
                        this.updateTimeFromInputs();
                    });
                    
                    this.elements.timerMinutes.addEventListener('keypress', (e) => {
                        if (!/[0-9]/.test(e.key)) {
                            e.preventDefault();
                        }
                    });
                }
                
                if (this.elements.timerSeconds) {
                    this.elements.timerSeconds.addEventListener('input', (e) => {
                        this.handleTimeInput(e, 'seconds');
                    });
                    
                    this.elements.timerSeconds.addEventListener('blur', () => {
                        this.updateTimeFromInputs();
                    });
                    
                    this.elements.timerSeconds.addEventListener('keypress', (e) => {
                        if (!/[0-9]/.test(e.key)) {
                            e.preventDefault();
                        }
                    });
                }
                
                this.updateDisplay();
                
                console.log('Таймер успешно инициализирован');
                return true;
            },
            
            handleTimeInput(e, type) {
                let value = e.target.value.replace(/\D/g, '');
                
                if (type === 'minutes') {
                    if (parseInt(value) > 99) value = '99';
                } else {
                    if (parseInt(value) > 59) value = '59';
                }
                
                e.target.value = value;
            },
            
            updateTimeFromInputs() {
                if (!this.isEditable) return;
                
                if (!this.elements.timerMinutes || !this.elements.timerSeconds) return;
                
                const minutes = parseInt(this.elements.timerMinutes.value) || 0;
                const seconds = parseInt(this.elements.timerSeconds.value) || 0;
                
                this.currentTime = minutes * 60 + seconds;
                
                if (!this.isBreak) {
                    this.workTime = this.currentTime;
                }
                
                this.updateDisplay();
            },
            
            switchToDisplayMode() {
                this.isEditable = false;
                if (this.elements.timerValueContainer) {
                    this.elements.timerValueContainer.classList.add('hidden');
                }
                if (this.elements.timerValueDisplay) {
                    this.elements.timerValueDisplay.classList.remove('hidden');
                }
                if (this.elements.timerMinutes) {
                    this.elements.timerMinutes.disabled = true;
                }
                if (this.elements.timerSeconds) {
                    this.elements.timerSeconds.disabled = true;
                }
            },
            
            switchToEditMode() {
                this.isEditable = true;
                if (this.elements.timerValueContainer) {
                    this.elements.timerValueContainer.classList.remove('hidden');
                }
                if (this.elements.timerValueDisplay) {
                    this.elements.timerValueDisplay.classList.add('hidden');
                }
                if (this.elements.timerMinutes) {
                    this.elements.timerMinutes.disabled = false;
                }
                if (this.elements.timerSeconds) {
                    this.elements.timerSeconds.disabled = false;
                }
            },
            
            start() {
                console.log('Запуск таймера');
                if (!this.isRunning) {
                    // Обновляем время из полей ввода только если они есть и таймер не был восстановлен
                    if (this.elements.timerMinutes && this.elements.timerSeconds && this.currentTime === this.workTime) {
                        this.updateTimeFromInputs();
                    }
                    this.switchToDisplayMode();
                    
                    this.isRunning = true;
                    this.lastTick = Date.now();
                    
                    if (this.elements.playBtn) {
                        this.elements.playBtn.classList.add('hidden');
                    }
                    if (this.elements.pauseBtn) {
                        this.elements.pauseBtn.classList.remove('hidden');
                    }
                    if (this.elements.resetBtn) {
                        this.elements.resetBtn.classList.remove('hidden');
                    }
                    
                    this.saveState();
                    
                    this.interval = setInterval(() => {
                        this.currentTime--;
                        this.lastTick = Date.now();
                        this.updateDisplay();
                        this.saveState();
                        
                        if (this.currentTime <= 0) {
                            this.complete();
                        }
                    }, 1000);
                }
            },
            
            pause() {
                console.log('Пауза таймера');
                this.isRunning = false;
                clearInterval(this.interval);
                this.saveState();
                
                if (this.elements.playBtn) {
                    this.elements.playBtn.classList.remove('hidden');
                    this.elements.pauseBtn.classList.add('hidden');
                }
            },
            
            reset() {
                console.log('Сброс таймера');
                this.pause();
                this.switchToEditMode();
                
                this.currentTime = this.isBreak ? 
                    (this.round % 4 === 0 ? this.longBreak : this.shortBreak) : 
                    this.workTime;
                
                this.updateDisplay();
                this.saveState();
                
                if (this.elements.resetBtn) {
                    this.elements.resetBtn.classList.add('hidden');
                }
            },
            
            complete() {
                console.log('Таймер завершен');
                this.pause();
                
                if (!this.isBreak) {
                    // Завершился рабочий период
                    this.isBreak = true;
                    
                    if (this.round % 4 === 0) {
                        // Длинный перерыв после 4 кругов
                        this.currentTime = this.longBreak;
                        this.showCustomNotification(
                            'Ура, отдыхать!',
                            'Поздравляю, ты закончил круг помодоро. У тебя есть 15 минут, чтобы размяться, заварить чай или просто выдохнуть :)',
                            'ЗАПУСТИТЬ ТАЙМЕР ОТДЫХА'
                        );
                    } else {
                        // Короткий перерыв
                        this.currentTime = this.shortBreak;
                        this.showCustomNotification(
                            'Ура, отдыхать!',
                            'Поздравляю, ты закончил круг помодоро. У тебя есть 5 минут, чтобы размяться, заварить чай или просто выдохнуть :)',
                            'ЗАПУСТИТЬ ТАЙМЕР ОТДЫХА'
                        );
                    }
                    
                    if (this.elements.timerCard) {
                        this.elements.timerCard.classList.add('break-mode');
                    }
                } else {
                    // Завершился перерыв
                    this.isBreak = false;
                    this.round++;
                    this.currentTime = this.workTime;
                    
                    if (this.elements.timerCard) {
                        this.elements.timerCard.classList.remove('break-mode');
                    }
                    
                    this.showCustomNotification(
                        'Время работать!',
                        'Перерыв закончился. Пора вернуться к работе и продолжить движение к цели!',
                        'ЗАПУСТИТЬ ТАЙМЕР РАБОТЫ'
                    );
                    
                    // Сброс после 4 кругов
                    if (this.round > 4) {
                        this.round = 1;
                    }
                }
                
                if (this.elements.timerRound) {
                    this.elements.timerRound.textContent = `#${this.round}`;
                }
                
                this.switchToEditMode();
                this.updateDisplay();
                this.saveState();
                
                if (this.elements.resetBtn) {
                    this.elements.resetBtn.classList.add('hidden');
                }
            },
            
            updateDisplay() {
                const minutes = Math.floor(this.currentTime / 60);
                const seconds = this.currentTime % 60;
                const timeString = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                // Обновляем поля ввода
                if (this.elements.timerMinutes) {
                    this.elements.timerMinutes.value = minutes.toString().padStart(2, '0');
                }
                if (this.elements.timerSeconds) {
                    this.elements.timerSeconds.value = seconds.toString().padStart(2, '0');
                }
                
                // Обновляем дисплей
                if (this.elements.timerValueDisplay) {
                    this.elements.timerValueDisplay.textContent = timeString;
                }
            },
            
            saveState() {
                const state = {
                    workTime: this.workTime,
                    currentTime: this.currentTime,
                    round: this.round,
                    isRunning: this.isRunning,
                    isBreak: this.isBreak,
                    lastTick: this.lastTick
                };
                localStorage.setItem('pomodoroState', JSON.stringify(state));
            },
            
            loadState() {
                const saved = localStorage.getItem('pomodoroState');
                if (saved) {
                    try {
                        const state = JSON.parse(saved);
                        this.workTime = state.workTime || this.workTime;
                        this.round = state.round || 1;
                        this.isBreak = state.isBreak || false;
                        
                        // Обновляем UI для break-mode
                        if (this.isBreak && this.elements.timerCard) {
                            this.elements.timerCard.classList.add('break-mode');
                        }
                        
                        // Обновляем номер раунда
                        if (this.elements.timerRound) {
                            this.elements.timerRound.textContent = `#${this.round}`;
                        }
                        
                        // Если таймер был запущен, корректируем время
                        if (state.isRunning && state.lastTick) {
                            const elapsed = Math.floor((Date.now() - state.lastTick) / 1000);
                            this.currentTime = Math.max(0, state.currentTime - elapsed);
                            
                            if (this.currentTime <= 0) {
                                // Время вышло, завершаем
                                this.currentTime = 0;
                                this.complete();
                            } else {
                                // Продолжаем таймер - НЕ вызываем start(), а запускаем напрямую
                                this.isRunning = true;
                                this.lastTick = Date.now();
                                
                                // Обновляем UI если элементы есть
                                if (this.elements.playBtn) {
                                    this.elements.playBtn.classList.add('hidden');
                                }
                                if (this.elements.pauseBtn) {
                                    this.elements.pauseBtn.classList.remove('hidden');
                                }
                                if (this.elements.resetBtn) {
                                    this.elements.resetBtn.classList.remove('hidden');
                                }
                                
                                this.switchToDisplayMode();
                                
                                // Запускаем интервал
                                this.interval = setInterval(() => {
                                    this.currentTime--;
                                    this.lastTick = Date.now();
                                    this.updateDisplay();
                                    this.saveState();
                                    
                                    if (this.currentTime <= 0) {
                                        this.complete();
                                    }
                                }, 1000);
                            }
                        } else {
                            // Таймер был на паузе
                            this.currentTime = state.currentTime || this.currentTime;
                        }
                    } catch (e) {
                        console.error('Ошибка загрузки состояния таймера:', e);
                    }
                }
            },
            
            startBackgroundMode() {
                console.log('Запуск фонового режима таймера');
                
                // Проверяем состояние каждую секунду
                setInterval(() => {
                    const saved = localStorage.getItem('pomodoroState');
                    if (!saved) return;
                    
                    try {
                        const state = JSON.parse(saved);
                        
                        if (state.isRunning && state.lastTick) {
                            const elapsed = Math.floor((Date.now() - state.lastTick) / 1000);
                            const timeLeft = state.currentTime - elapsed;
                            
                            // Если время вышло (с небольшим допуском для точности)
                            if (timeLeft <= 0 && state.currentTime > 0) {
                                console.log('Фоновый режим: время вышло, показываем уведомление');
                                
                                // Обновляем локальное состояние
                                this.currentTime = 0;
                                this.round = state.round;
                                this.isBreak = state.isBreak;
                                this.workTime = state.workTime;
                                
                                // Завершаем круг
                                this.complete();
                            }
                        }
                    } catch (e) {
                        console.error('Ошибка проверки состояния:', e);
                    }
                }, 1000);
            },
            
            showCustomNotification(title, message, actionText) {
                console.log('Уведомление:', title, message);
                
                // Используем кастомную систему уведомлений
                if (window.PomodoroNotification) {
                    window.PomodoroNotification.show(title, message, actionText, () => {
                        // При клике на кнопку запускаем таймер
                        this.start();
                    });
                }
                
                // Также пробуем браузерные уведомления
                if ('Notification' in window && Notification.permission === 'granted') {
                    new Notification(title, { body: message });
                }
            }
        };
        
        // Инициализация таймера
        const initialized = pomodoroTimer.init();
        
        if (initialized) {
            console.log('Помодоро таймер готов к работе');
            
            // Сохраняем глобально для доступа с других страниц
            window.pomodoroTimer = pomodoroTimer;
            
            // Запрос разрешения на уведомления
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission().then(permission => {
                    console.log('Разрешение на уведомления:', permission);
                });
            }
        } else {
            console.error('Не удалось инициализировать таймер');
        }
        
        return pomodoroTimer;
    }
    
    // Ждем загрузки DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPomodoroTimer);
    } else {
        // DOM уже загружен
        initPomodoroTimer();
    }
})();
