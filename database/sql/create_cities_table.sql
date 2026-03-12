-- Создание таблицы cities для выбора города
CREATE TABLE IF NOT EXISTS `cities` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Название города',
  `slug` varchar(255) NOT NULL COMMENT 'URL-friendly название',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Активен ли город',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Порядок сортировки',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cities_slug_unique` (`slug`),
  KEY `cities_is_active_index` (`is_active`),
  KEY `cities_sort_order_index` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Вставка городов
INSERT INTO `cities` (`name`, `slug`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
('Москва', 'moskva', 1, 1, NOW(), NOW()),
('Воронеж', 'voronezh', 1, 2, NOW(), NOW()),
('Россошь', 'rossosh', 1, 3, NOW(), NOW()),
('Поворино', 'povorino', 1, 4, NOW(), NOW()),
('Лиски', 'liski', 1, 5, NOW(), NOW()),
('Калач', 'kalach', 1, 6, NOW(), NOW()),
('Богучар', 'boguchar', 1, 7, NOW(), NOW());
