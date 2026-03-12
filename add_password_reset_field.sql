-- Добавление поля password_reset_required в таблицу users
-- Для функции восстановления пароля

-- Проверяем, существует ли поле
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'users' 
  AND COLUMN_NAME = 'password_reset_required'
  AND TABLE_SCHEMA = DATABASE();

-- Добавляем поле, если его нет
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS password_reset_required BOOLEAN DEFAULT FALSE 
COMMENT 'Флаг принудительной смены пароля после восстановления';

-- Проверяем результат
DESCRIBE users;