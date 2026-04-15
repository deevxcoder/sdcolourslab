-- ============================================================
-- SD Colours Photobook Lab — MySQL Database Setup
-- Database: sdcolourslab  |  MySQL port: 3306
-- Run this file once to create all tables and seed data
-- ============================================================

CREATE DATABASE IF NOT EXISTS `sdcolourslab`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `sdcolourslab`;

-- --------------------------------------------------------
-- Table: users
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(150)    NOT NULL,
  `email`         VARCHAR(200)    NOT NULL UNIQUE,
  `password_hash` VARCHAR(255)    NOT NULL,
  `role`          ENUM('admin','photographer') NOT NULL DEFAULT 'photographer',
  `phone`         VARCHAR(20)     DEFAULT NULL,
  `studio_name`   VARCHAR(200)    DEFAULT NULL,
  `city`          VARCHAR(100)    DEFAULT NULL,
  `status`        ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: products
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
  `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(200)  NOT NULL,
  `category`    VARCHAR(100)  DEFAULT NULL,
  `description` TEXT          DEFAULT NULL,
  `price`       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `price_alt`   DECIMAL(10,2) DEFAULT NULL,
  `sizes`       JSON          DEFAULT NULL,
  `features`    JSON          DEFAULT NULL,
  `tag`         VARCHAR(100)  DEFAULT NULL,
  `image`       VARCHAR(300)  DEFAULT NULL,
  `active`      TINYINT(1)    NOT NULL DEFAULT 1,
  `sort_order`  INT           NOT NULL DEFAULT 0,
  `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: orders
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
  `id`              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `photographer_id` INT UNSIGNED  NOT NULL,
  `status`          ENUM('pending','processing','shipped','delivered','cancelled')
                    NOT NULL DEFAULT 'pending',
  `total`           DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `notes`           TEXT          DEFAULT NULL,
  `admin_notes`     TEXT          DEFAULT NULL,
  `created_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_orders_user`
    FOREIGN KEY (`photographer_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: order_items
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `order_items` (
  `id`           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `order_id`     INT UNSIGNED  NOT NULL,
  `product_id`   INT UNSIGNED  DEFAULT NULL,
  `product_name` VARCHAR(200)  NOT NULL,
  `size`         VARCHAR(100)  DEFAULT NULL,
  `quantity`     INT           NOT NULL DEFAULT 1,
  `unit_price`   DECIMAL(10,2) NOT NULL,
  `notes`        TEXT          DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_items_order`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_items_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SEED DATA
-- ============================================================

-- Default admin user
-- Email: admin@sdcolours.com  |  Password: admin123
INSERT IGNORE INTO `users`
  (`name`, `email`, `password_hash`, `role`, `status`)
VALUES
  ('Admin', 'admin@sdcolours.com',
   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
   'admin', 'approved');

-- Sample products
INSERT IGNORE INTO `products`
  (`name`, `category`, `description`, `price`, `sizes`, `features`, `tag`, `image`, `sort_order`)
VALUES
  ('Leather 2-in-1 Bag Combo', 'combo',
   'Premium leather album with matching bag — classic wedding combo.',
   2500.00,
   '["10x12","12x15","15x18"]',
   '["Premium Leather Cover","Magnetic Closure","Free Carry Bag"]',
   'Bestseller', 'images/combos/leather-2in1-bag.jpg', 1),

  ('Leather 2-in-1 Box Combo', 'combo',
   'Leather album paired with an elegant gift box.',
   2800.00,
   '["10x12","12x15","15x18"]',
   '["Luxury Box Packaging","Premium Pages","Gold Foil Branding"]',
   'Popular', 'images/combos/leather-2in1-box.jpg', 2),

  ('Royal 4-in-1 Combo', 'combo',
   'Four-piece royal wedding set — album, box, bag & frame.',
   4500.00,
   '["12x15","15x18"]',
   '["4 Piece Set","Royal Finish","Customisable Cover"]',
   'Premium', 'images/combos/royal-4in1.jpg', 3),

  ('Acrylic 2-in-1', 'acrylic',
   'Modern acrylic frame + album combo for contemporary studios.',
   3200.00,
   '["10x12","12x15"]',
   '["HD Acrylic Print","UV Protect","Wall Mount Included"]',
   'Trending', 'images/combos/acrylic-2in1.jpg', 4),

  ('ProWood 360° 3-in-1', 'wood',
   'Rotating wooden album stand with two coordinating albums.',
   3800.00,
   '["10x12","12x15"]',
   '["360° Rotating Base","Engraved Branding","Natural Wood Finish"]',
   NULL, 'images/combos/prowood-360-3in1.jpg', 5);
