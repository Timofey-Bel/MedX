-- Добавление полей для дополнительных контактов пользователя
-- Дата: 2026-03-11

ALTER TABLE `users` 
ADD COLUMN `phone_additional` VARCHAR(20) NULL COMMENT 'Дополнительный телефон' AFTER `phone`,
ADD COLUMN `telegram` VARCHAR(100) NULL COMMENT 'Telegram username или номер' AFTER `phone_additional`;
