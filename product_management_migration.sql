CREATE TABLE IF NOT EXISTS `products` (
  `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  `description` TEXT NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) NOT NULL DEFAULT 'Other',
  `created_at` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  PRIMARY KEY (`id`),
  CONSTRAINT `CK_products_price` CHECK (`price` >= 0)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

SET @category_column_exists = (
  SELECT COUNT(*)
  FROM information_schema.columns
  WHERE table_schema = DATABASE()
    AND table_name = 'products'
    AND column_name = 'category'
);
SET @category_migration = IF(
  @category_column_exists = 0,
  'ALTER TABLE products ADD COLUMN category VARCHAR(100) NOT NULL DEFAULT ''Other'' AFTER image',
  'SELECT 1'
);
PREPARE category_migration_statement FROM @category_migration;
EXECUTE category_migration_statement;
DEALLOCATE PREPARE category_migration_statement;
