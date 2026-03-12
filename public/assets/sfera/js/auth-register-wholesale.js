/**
 * Расширение функционала регистрации для юридических лиц
 * Подключается после auth-register.js
 */

document.addEventListener('DOMContentLoaded', function() {
    // Элементы формы
    const userTypeRadios = document.querySelectorAll('input[name="user_type"]');
    const wholesaleFields = document.getElementById('wholesale-fields');
    const innInput = document.getElementById('inn');
    const checkInnBtn = document.getElementById('check-inn-btn');
    
    // Поля организации для автозаполнения
    const orgFields = {
        name_full: document.getElementById('org_name_full'),
        name_short: document.getElementById('org_name_short'),
        kpp: document.getElementById('org_kpp'),
        ogrn: document.getElementById('org_ogrn'),
        legal_address: document.getElementById('org_legal_address'),
        director_name: document.getElementById('org_director_name'),
        director_position: document.getElementById('org_director_position'),
        opf: document.getElementById('org_opf'),
    };

    // Переключение между типами пользователей
    userTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'wholesale') {
                wholesaleFields.style.display = 'block';
                setWholesaleFieldsRequired(true);
            } else {
                wholesaleFields.style.display = 'none';
                setWholesaleFieldsRequired(false);
                clearWholesaleFields();
            }
        });
    });

    // Установка/снятие обязательности полей для юр. лиц
    function setWholesaleFieldsRequired(required) {
        if (innInput) {
            innInput.required = required;
        }
        if (orgFields.name_full) {
            orgFields.name_full.required = required;
        }
    }

    // Очистка полей организации
    function clearWholesaleFields() {
        Object.values(orgFields).forEach(field => {
            if (field) field.value = '';
        });
        if (innInput) innInput.value = '';
    }

    // Инициализация проверки ИНН
    if (innInput && checkInnBtn) {
        const dadataChecker = new DaDataInnChecker(
            '#inn',
            '#check-inn-btn',
            handleInnCheckSuccess
        );
    }

    // Обработчик успешной проверки ИНН
    function handleInnCheckSuccess(orgData) {
        console.log('Organization data received:', orgData);

        // Сохраняем полные данные в скрытое поле
        const dadataJsonField = document.getElementById('dadata_json');
        if (dadataJsonField) {
            dadataJsonField.value = JSON.stringify(orgData);
        }

        // Сохраняем статус
        const statusField = document.getElementById('org_status');
        if (statusField) {
            statusField.value = orgData.status || 'active';
        }

        // Автозаполнение полей
        if (orgFields.name_full && orgData.name_full) {
            orgFields.name_full.value = orgData.name_full;
            orgFields.name_full.readOnly = true;
        }

        if (orgFields.name_short && orgData.name_short) {
            orgFields.name_short.value = orgData.name_short;
            orgFields.name_short.readOnly = true;
        }

        if (orgFields.kpp && orgData.kpp) {
            orgFields.kpp.value = orgData.kpp;
            orgFields.kpp.readOnly = true;
        }

        if (orgFields.ogrn && orgData.ogrn) {
            orgFields.ogrn.value = orgData.ogrn;
            orgFields.ogrn.readOnly = true;
        }

        if (orgFields.legal_address && orgData.legal_address) {
            orgFields.legal_address.value = orgData.legal_address;
            orgFields.legal_address.readOnly = true;
        }

        if (orgFields.director_name && orgData.director_name) {
            orgFields.director_name.value = orgData.director_name;
            orgFields.director_name.readOnly = true;
        }

        if (orgFields.director_position && orgData.director_position) {
            orgFields.director_position.value = orgData.director_position;
            orgFields.director_position.readOnly = true;
        }

        if (orgFields.opf && orgData.opf) {
            orgFields.opf.value = orgData.opf;
            orgFields.opf.readOnly = true;
        }

        // Блокируем поле ИНН после успешной проверки
        if (innInput) {
            innInput.readOnly = true;
        }

        // Показываем кнопку "Изменить ИНН"
        showChangeInnButton();

        // Показываем уведомление
        showToast('Данные организации загружены успешно', 'success');
    }

    // Показать кнопку изменения ИНН
    function showChangeInnButton() {
        if (!checkInnBtn) return;

        // Скрываем кнопку "Проверить ИНН"
        checkInnBtn.style.display = 'none';

        // Создаем кнопку "Изменить ИНН"
        const changeBtn = document.createElement('button');
        changeBtn.type = 'button';
        changeBtn.className = 'btn-change-inn';
        changeBtn.textContent = 'Изменить ИНН';
        changeBtn.id = 'change-inn-btn';

        changeBtn.addEventListener('click', function() {
            resetInnCheck();
        });

        checkInnBtn.parentElement.appendChild(changeBtn);
    }

    // Сброс проверки ИНН
    function resetInnCheck() {
        // Разблокируем поле ИНН
        if (innInput) {
            innInput.readOnly = false;
            innInput.value = '';
            innInput.focus();
        }

        // Очищаем и разблокируем поля организации
        Object.values(orgFields).forEach(field => {
            if (field) {
                field.value = '';
                field.readOnly = false;
            }
        });

        // Показываем кнопку "Проверить ИНН"
        if (checkInnBtn) {
            checkInnBtn.style.display = 'inline-block';
        }

        // Удаляем кнопку "Изменить ИНН"
        const changeBtn = document.getElementById('change-inn-btn');
        if (changeBtn) {
            changeBtn.remove();
        }

        // Очищаем сообщения
        const messages = innInput?.parentElement.querySelectorAll('.dadata-message');
        messages?.forEach(msg => msg.remove());
        innInput?.classList.remove('error', 'success');
    }

    // Валидация ИНН при вводе
    if (innInput) {
        innInput.addEventListener('input', function(e) {
            // Только цифры
            e.target.value = e.target.value.replace(/\D/g, '');
            
            // Ограничение длины
            if (e.target.value.length > 12) {
                e.target.value = e.target.value.slice(0, 12);
            }
        });
    }

    // Toast notification (если не определена в auth-register.js)
    if (typeof showToast === 'undefined') {
        window.showToast = function(message, type = 'info') {
            const existing = document.querySelector('.toast-notification');
            if (existing) {
                existing.remove();
            }
            
            const toast = document.createElement('div');
            toast.className = `toast-notification ${type}`;
            toast.textContent = message;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        };
    }

    console.log('Wholesale registration initialized');
});
