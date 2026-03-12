-- Создание таблицы favorites для хранения избранных товаров пользователей
-- Дата: 2026-03-06

CREATE TABLE IF NOT EXISTS `favorites` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'ID пользователя',
  `product_id` varchar(50) NOT NULL COMMENT 'ID товара (offer_id)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `favorites_user_product_unique` (`user_id`, `product_id`),
  KEY `favorites_user_id_index` (`user_id`),
  KEY `favorites_product_id_index` (`product_id`),
  KEY `favorites_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Избранные товары пользователей';