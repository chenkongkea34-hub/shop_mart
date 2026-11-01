CREATE DATABASE IF NOT EXISTS `shopmate`;
USE `shopmate`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(191) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(20) NOT NULL DEFAULT 'customer',
  `created_at` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_users_email` (`email`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

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

CREATE TABLE IF NOT EXISTS `orders` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `order_number` VARCHAR(60) NOT NULL,
  `order_date` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `total_amount` DECIMAL(12, 2) NOT NULL,
  `status` VARCHAR(60) NOT NULL DEFAULT 'Pending',
  `customer_name` VARCHAR(100) NULL,
  `customer_phone` VARCHAR(30) NULL,
  `shipping_address` VARCHAR(1000) NOT NULL,
  `payment_method` VARCHAR(100) NULL,
  `payment_status` VARCHAR(60) NOT NULL DEFAULT 'Unpaid',
  `created_at` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_orders_order_number` (`order_number`),
  CONSTRAINT `FK_orders_users` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
  CONSTRAINT `CK_orders_total_amount` CHECK (`total_amount` >= 0),
  CONSTRAINT `CK_orders_status` CHECK (
    `status` IN (
      'Pending',
      'Processing',
      'Shipped',
      'Delivered',
      'Cancelled'
    )
  ),
  CONSTRAINT `CK_orders_payment_status` CHECK (
    `payment_status` IN ('Unpaid', 'Paid', 'Failed', 'Refunded')
  )
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `product_id` INT NOT NULL,
  `product_name` VARCHAR(200) NOT NULL,
  `quantity` INT NOT NULL,
  `unit_price` DECIMAL(12, 2) NOT NULL,
  `subtotal` DECIMAL(12, 2) GENERATED ALWAYS AS ((`quantity` * `unit_price`)) STORED,
  `created_at` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_order_items_orders` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`),
  CONSTRAINT `CK_order_items_quantity` CHECK (`quantity` > 0),
  CONSTRAINT `CK_order_items_unit_price` CHECK (`unit_price` >= 0)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Demo login: demo@shopmate.test / DemoPassword123!
INSERT INTO
  `users` (`name`, `email`, `password`)
SELECT
  *
FROM
  (
    SELECT
      'Demo Customer' AS `name`,
      'demo@shopmate.test' AS `email`,
      '$2y$10$dXulG2xRrO4fJhm/4ydzReu2eZhYwbMyKQsBkktncHayH56yAZjG2' AS `password`
  ) AS `tmp`
WHERE
  NOT EXISTS (
    SELECT
      1
    FROM
      `users`
    WHERE
      `email` = 'demo@shopmate.test'
  )
LIMIT
  1;