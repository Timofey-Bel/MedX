-- Создание таблицы организаций для оптовых покупателей
-- Дата: 2026-03-05

-- Создание таблицы orgs
CREATE TABLE IF NOT EXISTS `orgs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `inn` varchar(12) NOT NULL COMMENT 'ИНН организации',
  `kpp` varchar(9) DEFAULT NULL COMMENT 'КПП организации',
  `ogrn` varchar(15) DEFAULT NULL COMMENT 'ОГРН организации',
  `name_full` varchar(500) NOT NULL COMMENT 'Полное наименование',
  `name_short` varchar(255) DEFAULT NULL COMMENT 'Краткое наименование',
  `legal_address` text DEFAULT NULL COMMENT 'Юридический адрес',
  `postal_address` text DEFAULT NULL COMMENT 'Почтовый адрес',
  `director_name` varchar(255) DEFAULT NULL COMMENT 'ФИО руководителя',
  `director_position` varchar(255) DEFAULT NULL COMMENT 'Должность руководителя',
  `phone` varchar(20) DEFAULT NULL COMMENT 'Телефон организации',
  `email` varchar(255) DEFAULT NULL COMMENT 'Email организации',
  `bank_name` varchar(255) DEFAULT NULL COMMENT 'Название банка',
  `bank_bik` varchar(9) DEFAULT NULL COMMENT 'БИК банка',
  `bank_account` varchar(20) DEFAULT NULL COMMENT 'Расчетный счет',
  `bank_corr_account` varchar(20) DEFAULT NULL COMMENT 'Корреспондентский счет',
  `opf` varchar(255) DEFAULT NULL COMMENT 'Организационно-правовая форма',
  `status` varchar(50) DEFAULT 'active' COMMENT 'Статус: active, inactive, liquidated',
  `dadata_json` text DEFAULT NULL COMMENT 'Полный JSON ответ от DaData',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orgs_inn_unique` (`inn`),
  KEY `orgs_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Организации (юридические лица)';

-- Добавление полей в таблицу users для поддержки типов пользователей
ALTER TABLE `users` 
ADD COLUMN `user_type` varchar(20) DEFAULT 'retail' COMMENT 'Тип пользователя: retail, wholesale' AFTER `phone`,
ADD COLUMN `org_id` bigint(20) unsigned DEFAULT NULL COMMENT 'ID организации для оптовых покупателей' AFTER `user_type`,
ADD KEY `users_user_type_index` (`user_type`),
ADD KEY `users_org_id_foreign` (`org_id`),
ADD CONSTRAINT `users_org_id_foreign` FOREIGN KEY (`org_id`) REFERENCES `orgs` (`id`) ON DELETE SET NULL;
