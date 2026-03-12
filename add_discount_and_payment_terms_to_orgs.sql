-- Добавление полей для скидки и отсрочки платежа в таблицу orgs

-- Добавляем поле для персональной скидки (в процентах)
ALTER TABLE orgs ADD COLUMN discount_percent DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Персональная скидка в процентах';

-- Добавляем поле для отсрочки платежа (в днях)
ALTER TABLE orgs ADD COLUMN payment_delay_days INT DEFAULT 0 COMMENT 'Отсрочка платежа в днях';

-- Добавляем поле для описания условий отсрочки
ALTER TABLE orgs ADD COLUMN payment_terms TEXT NULL COMMENT 'Условия отсрочки платежа';
