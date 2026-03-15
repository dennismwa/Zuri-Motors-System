-- ============================================================
-- ZURI MOTORS - Complete Database Schema
-- Automotive Marketplace Platform
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `zuri_motors` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `zuri_motors`;

-- ============================================================
-- 1. SETTINGS TABLE - Site Configuration
-- ============================================================
CREATE TABLE `settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT,
    `setting_group` VARCHAR(50) DEFAULT 'general',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`) VALUES
('company_name', 'Zuri Motors', 'general'),
('tagline', 'Find the Perfect Car for Your Lifestyle', 'general'),
('logo', 'assets/images/logo.png', 'general'),
('logo_dark', 'assets/images/logo-dark.png', 'general'),
('favicon', 'assets/images/favicon.png', 'general'),
('email', 'info@zurimotors.com', 'contact'),
('phone', '+254 700 000 000', 'contact'),
('whatsapp', '254700000000', 'contact'),
('address', 'Westlands, Nairobi, Kenya', 'contact'),
('google_maps_url', '', 'contact'),
('google_maps_embed', '', 'contact'),
('facebook', 'https://facebook.com/zurimotors', 'social'),
('twitter', 'https://twitter.com/zurimotors', 'social'),
('instagram', 'https://instagram.com/zurimotors', 'social'),
('youtube', '', 'social'),
('linkedin', '', 'social'),
('tiktok', '', 'social'),
('meta_title', 'Zuri Motors - Buy & Sell Quality Cars in Kenya', 'seo'),
('meta_description', 'Find the best deals on used and new cars in Kenya. Zuri Motors is your trusted automotive marketplace for quality vehicles.', 'seo'),
('meta_keywords', 'buy used cars, used cars for sale, affordable cars, second hand cars, best car deals, cars Kenya', 'seo'),
('currency_symbol', 'KSh', 'finance'),
('currency_code', 'KES', 'finance'),
('tax_rate', '16', 'finance'),
('enable_tax', '0', 'finance'),
('enable_financing', '1', 'finance'),
('default_deposit_percent', '20', 'finance'),
('max_installment_months', '60', 'finance'),
('interest_rate', '14', 'finance'),
('enable_chat', '1', 'features'),
('enable_compare', '1', 'features'),
('enable_favorites', '1', 'features'),
('enable_vendor_registration', '1', 'features'),
('cars_per_page', '12', 'features'),
('max_compare_cars', '4', 'features'),
('max_images_per_car', '20', 'features'),
('hero_title', 'Find the Perfect Car for Your Lifestyle', 'homepage'),
('hero_subtitle', 'Browse thousands of quality vehicles from trusted dealers across Kenya', 'homepage'),
('hero_image', 'assets/images/hero-bg.jpg', 'homepage'),
('maintenance_mode', '0', 'system'),
('smtp_host', '', 'email'),
('smtp_port', '587', 'email'),
('smtp_user', '', 'email'),
('smtp_pass', '', 'email'),
('smtp_from_name', 'Zuri Motors', 'email'),
('smtp_from_email', 'noreply@zurimotors.com', 'email');

-- ============================================================
-- 2. USERS TABLE
-- ============================================================
CREATE TABLE `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `role` ENUM('admin','vendor','customer','staff') NOT NULL DEFAULT 'customer',
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `phone` VARCHAR(30),
    `password` VARCHAR(255) NOT NULL,
    `avatar` VARCHAR(500),
    `company_name` VARCHAR(255),
    `company_logo` VARCHAR(500),
    `company_description` TEXT,
    `address` VARCHAR(500),
    `city` VARCHAR(100),
    `country` VARCHAR(100) DEFAULT 'Kenya',
    `whatsapp` VARCHAR(30),
    `website` VARCHAR(500),
    `is_verified` TINYINT(1) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `email_verified_at` TIMESTAMP NULL,
    `remember_token` VARCHAR(255),
    `reset_token` VARCHAR(255),
    `reset_token_expires` TIMESTAMP NULL,
    `last_login` TIMESTAMP NULL,
    `login_count` INT DEFAULT 0,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default admin user (password: admin123 - bcrypt hashed)
INSERT INTO `users` (`role`, `first_name`, `last_name`, `email`, `phone`, `password`, `is_verified`, `is_active`) VALUES
('admin', 'Admin', 'User', 'admin@zurimotors.com', '+254700000000', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1);

-- ============================================================
-- 3. BRANDS TABLE
-- ============================================================
CREATE TABLE `brands` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(120) NOT NULL UNIQUE,
    `logo` VARCHAR(500),
    `description` TEXT,
    `is_popular` TINYINT(1) DEFAULT 0,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO `brands` (`name`, `slug`, `logo`, `is_popular`, `sort_order`) VALUES
('Toyota', 'toyota', NULL, 1, 1),
('Mercedes-Benz', 'mercedes-benz', NULL, 1, 2),
('BMW', 'bmw', NULL, 1, 3),
('Audi', 'audi', NULL, 1, 4),
('Land Rover', 'land-rover', NULL, 1, 5),
('Nissan', 'nissan', NULL, 1, 6),
('Honda', 'honda', NULL, 1, 7),
('Volkswagen', 'volkswagen', NULL, 1, 8),
('Subaru', 'subaru', NULL, 1, 9),
('Mazda', 'mazda', NULL, 1, 10),
('Mitsubishi', 'mitsubishi', NULL, 0, 11),
('Hyundai', 'hyundai', NULL, 0, 12),
('Kia', 'kia', NULL, 0, 13),
('Ford', 'ford', NULL, 0, 14),
('Lexus', 'lexus', NULL, 1, 15),
('Porsche', 'porsche', NULL, 0, 16),
('Jaguar', 'jaguar', NULL, 0, 17),
('Volvo', 'volvo', NULL, 0, 18),
('Jeep', 'jeep', NULL, 0, 19),
('Peugeot', 'peugeot', NULL, 0, 20),
('Suzuki', 'suzuki', NULL, 0, 21),
('Isuzu', 'isuzu', NULL, 0, 22),
('Tesla', 'tesla', NULL, 0, 23),
('Chevrolet', 'chevrolet', NULL, 0, 24);

-- ============================================================
-- 4. CATEGORIES TABLE
-- ============================================================
CREATE TABLE `categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(120) NOT NULL UNIQUE,
    `icon` VARCHAR(100),
    `image` VARCHAR(500),
    `description` TEXT,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO `categories` (`name`, `slug`, `icon`, `sort_order`) VALUES
('SUV', 'suv', 'truck', 1),
('Sedan', 'sedan', 'car', 2),
('Pickup', 'pickup', 'truck', 3),
('Hatchback', 'hatchback', 'car', 4),
('Coupe', 'coupe', 'car', 5),
('Convertible', 'convertible', 'car', 6),
('Van', 'van', 'truck', 7),
('Wagon', 'wagon', 'car', 8),
('Electric', 'electric', 'zap', 9),
('Luxury', 'luxury', 'gem', 10),
('Sports', 'sports', 'gauge', 11),
('Crossover', 'crossover', 'car', 12);

-- ============================================================
-- 5. FEATURES TABLE (Master List)
-- ============================================================
CREATE TABLE `features` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `icon` VARCHAR(100) DEFAULT 'check',
    `category` VARCHAR(50) DEFAULT 'comfort',
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO `features` (`name`, `icon`, `category`, `sort_order`) VALUES
('Air Conditioning', 'wind', 'comfort', 1),
('Climate Control', 'thermometer', 'comfort', 2),
('Heated Seats', 'flame', 'comfort', 3),
('Leather Seats', 'armchair', 'comfort', 4),
('Power Seats', 'move', 'comfort', 5),
('Sunroof', 'sun', 'comfort', 6),
('Panoramic Roof', 'maximize', 'comfort', 7),
('Keyless Entry', 'key', 'comfort', 8),
('Push Start', 'power', 'comfort', 9),
('Cruise Control', 'gauge', 'comfort', 10),
('Bluetooth', 'bluetooth', 'technology', 11),
('Apple CarPlay', 'smartphone', 'technology', 12),
('Android Auto', 'smartphone', 'technology', 13),
('Navigation System', 'navigation', 'technology', 14),
('Touchscreen Display', 'monitor', 'technology', 15),
('USB Ports', 'usb', 'technology', 16),
('Wireless Charging', 'battery-charging', 'technology', 17),
('Premium Sound System', 'volume-2', 'technology', 18),
('Rear Camera', 'camera', 'safety', 19),
('360 Camera', 'video', 'safety', 20),
('Parking Sensors', 'radar', 'safety', 21),
('Blind Spot Monitor', 'eye', 'safety', 22),
('Lane Departure Warning', 'alert-triangle', 'safety', 23),
('ABS', 'shield', 'safety', 24),
('Airbags', 'shield-check', 'safety', 25),
('Traction Control', 'settings', 'safety', 26),
('Hill Assist', 'mountain', 'safety', 27),
('Fog Lights', 'cloud-fog', 'exterior', 28),
('LED Headlights', 'lightbulb', 'exterior', 29),
('Alloy Wheels', 'circle', 'exterior', 30),
('Roof Rails', 'minus', 'exterior', 31),
('Tow Bar', 'link', 'exterior', 32),
('Roof Rack', 'grid', 'exterior', 33),
('4WD', 'truck', 'performance', 34),
('AWD', 'truck', 'performance', 35),
('Turbo', 'zap', 'performance', 36),
('Sport Mode', 'activity', 'performance', 37),
('Paddle Shifters', 'chevrons-up', 'performance', 38);

-- ============================================================
-- 6. CARS TABLE
-- ============================================================
CREATE TABLE `cars` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(300) NOT NULL,
    `slug` VARCHAR(350) NOT NULL UNIQUE,
    `brand_id` INT UNSIGNED NOT NULL,
    `category_id` INT UNSIGNED NOT NULL,
    `model` VARCHAR(150) NOT NULL,
    `year` YEAR NOT NULL,
    `mileage` INT UNSIGNED DEFAULT 0,
    `mileage_unit` ENUM('km','miles') DEFAULT 'km',
    `fuel_type` ENUM('petrol','diesel','electric','hybrid','plugin_hybrid','lpg') NOT NULL DEFAULT 'petrol',
    `transmission` ENUM('automatic','manual','cvt','semi_automatic') NOT NULL DEFAULT 'automatic',
    `engine_size` VARCHAR(30),
    `horsepower` INT UNSIGNED,
    `body_type` VARCHAR(50),
    `color` VARCHAR(50),
    `interior_color` VARCHAR(50),
    `doors` TINYINT UNSIGNED DEFAULT 4,
    `seats` TINYINT UNSIGNED DEFAULT 5,
    `drivetrain` ENUM('fwd','rwd','awd','4wd') DEFAULT 'fwd',
    `price` DECIMAL(15,2) NOT NULL,
    `old_price` DECIMAL(15,2),
    `price_negotiable` TINYINT(1) DEFAULT 0,
    `condition_type` ENUM('used','new','certified') DEFAULT 'used',
    `location` VARCHAR(255),
    `latitude` DECIMAL(10,8),
    `longitude` DECIMAL(11,8),
    `description` TEXT,
    `excerpt` VARCHAR(500),
    `registration_number` VARCHAR(50),
    `vin_number` VARCHAR(50),
    `registration_year` YEAR,
    `is_featured` TINYINT(1) DEFAULT 0,
    `is_offer` TINYINT(1) DEFAULT 0,
    `is_hot_deal` TINYINT(1) DEFAULT 0,
    `offer_label` VARCHAR(100),
    `status` ENUM('active','pending','sold','reserved','draft','archived') DEFAULT 'pending',
    `views_count` INT UNSIGNED DEFAULT 0,
    `inquiries_count` INT UNSIGNED DEFAULT 0,
    `featured_until` DATE,
    `meta_title` VARCHAR(300),
    `meta_description` VARCHAR(500),
    `deposit_amount` DECIMAL(15,2),
    `monthly_installment` DECIMAL(15,2),
    `installment_months` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE,
    INDEX `idx_status` (`status`),
    INDEX `idx_featured` (`is_featured`),
    INDEX `idx_brand` (`brand_id`),
    INDEX `idx_category` (`category_id`),
    INDEX `idx_price` (`price`),
    INDEX `idx_year` (`year`),
    INDEX `idx_condition` (`condition_type`),
    INDEX `idx_location` (`location`),
    FULLTEXT INDEX `ft_search` (`title`, `description`, `model`, `location`)
) ENGINE=InnoDB;

-- ============================================================
-- 7. CAR IMAGES TABLE
-- ============================================================
CREATE TABLE `car_images` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `car_id` INT UNSIGNED NOT NULL,
    `image_url` VARCHAR(500) NOT NULL,
    `thumbnail_url` VARCHAR(500),
    `alt_text` VARCHAR(300),
    `is_primary` TINYINT(1) DEFAULT 0,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`car_id`) REFERENCES `cars`(`id`) ON DELETE CASCADE,
    INDEX `idx_car_primary` (`car_id`, `is_primary`)
) ENGINE=InnoDB;

-- ============================================================
-- 8. CAR FEATURES (Linking Table)
-- ============================================================
CREATE TABLE `car_features` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `car_id` INT UNSIGNED NOT NULL,
    `feature_id` INT UNSIGNED NOT NULL,
    `custom_value` VARCHAR(255),
    FOREIGN KEY (`car_id`) REFERENCES `cars`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`feature_id`) REFERENCES `features`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `uk_car_feature` (`car_id`, `feature_id`)
) ENGINE=InnoDB;

-- ============================================================
-- 9. INQUIRIES / LEADS TABLE
-- ============================================================
CREATE TABLE `inquiries` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `car_id` INT UNSIGNED,
    `type` ENUM('general','test_drive','price_quote','sell_car','financing','callback') DEFAULT 'general',
    `name` VARCHAR(200) NOT NULL,
    `email` VARCHAR(255),
    `phone` VARCHAR(30) NOT NULL,
    `message` TEXT,
    `car_model_sell` VARCHAR(255),
    `price_expectation` DECIMAL(15,2),
    `year_sell` YEAR,
    `status` ENUM('new','contacted','in_progress','qualified','converted','lost','closed') DEFAULT 'new',
    `priority` ENUM('low','medium','high','urgent') DEFAULT 'medium',
    `assigned_to` INT UNSIGNED,
    `source` VARCHAR(50) DEFAULT 'website',
    `ip_address` VARCHAR(45),
    `user_agent` VARCHAR(500),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`car_id`) REFERENCES `cars`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_status` (`status`),
    INDEX `idx_assigned` (`assigned_to`),
    INDEX `idx_type` (`type`)
) ENGINE=InnoDB;

-- ============================================================
-- 10. INQUIRY NOTES TABLE
-- ============================================================
CREATE TABLE `inquiry_notes` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `inquiry_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `note` TEXT NOT NULL,
    `is_internal` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 11. PAYMENT METHODS TABLE
-- ============================================================
CREATE TABLE `payment_methods` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `description` TEXT,
    `instructions` TEXT,
    `icon` VARCHAR(100),
    `config_data` JSON,
    `is_online` TINYINT(1) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO `payment_methods` (`name`, `code`, `description`, `icon`, `is_online`, `sort_order`) VALUES
('M-Pesa', 'mpesa', 'Pay via M-Pesa mobile money', 'smartphone', 1, 1),
('Bank Transfer', 'bank_transfer', 'Direct bank transfer', 'landmark', 0, 2),
('Cash', 'cash', 'Cash payment at office', 'banknote', 0, 3),
('Cheque', 'cheque', 'Payment by cheque', 'file-text', 0, 4),
('Credit Card', 'credit_card', 'Visa / Mastercard payment', 'credit-card', 1, 5),
('PayPal', 'paypal', 'Pay via PayPal', 'globe', 1, 6);

-- ============================================================
-- 12. INVOICES TABLE
-- ============================================================
CREATE TABLE `invoices` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `invoice_number` VARCHAR(50) NOT NULL UNIQUE,
    `car_id` INT UNSIGNED,
    `customer_id` INT UNSIGNED,
    `vendor_id` INT UNSIGNED,
    `inquiry_id` INT UNSIGNED,
    `customer_name` VARCHAR(200),
    `customer_email` VARCHAR(255),
    `customer_phone` VARCHAR(30),
    `customer_address` TEXT,
    `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `tax_amount` DECIMAL(15,2) DEFAULT 0,
    `discount_amount` DECIMAL(15,2) DEFAULT 0,
    `total_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `paid_amount` DECIMAL(15,2) DEFAULT 0,
    `balance_due` DECIMAL(15,2) DEFAULT 0,
    `status` ENUM('draft','sent','partially_paid','paid','overdue','cancelled','refunded') DEFAULT 'draft',
    `due_date` DATE,
    `notes` TEXT,
    `terms` TEXT,
    `created_by` INT UNSIGNED,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`car_id`) REFERENCES `cars`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`customer_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`vendor_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_status` (`status`),
    INDEX `idx_invoice_number` (`invoice_number`)
) ENGINE=InnoDB;

-- ============================================================
-- 13. INVOICE ITEMS TABLE
-- ============================================================
CREATE TABLE `invoice_items` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `invoice_id` INT UNSIGNED NOT NULL,
    `description` VARCHAR(500) NOT NULL,
    `quantity` DECIMAL(10,2) DEFAULT 1,
    `unit_price` DECIMAL(15,2) NOT NULL,
    `total_price` DECIMAL(15,2) NOT NULL,
    `sort_order` INT DEFAULT 0,
    FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 14. PAYMENTS TABLE
-- ============================================================
CREATE TABLE `payments` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `invoice_id` INT UNSIGNED,
    `car_id` INT UNSIGNED,
    `customer_id` INT UNSIGNED,
    `payment_method_id` INT UNSIGNED,
    `transaction_id` VARCHAR(255),
    `amount` DECIMAL(15,2) NOT NULL,
    `currency` VARCHAR(10) DEFAULT 'KES',
    `status` ENUM('pending','completed','failed','refunded','cancelled') DEFAULT 'pending',
    `payment_date` DATE,
    `reference_number` VARCHAR(255),
    `notes` TEXT,
    `receipt_url` VARCHAR(500),
    `recorded_by` INT UNSIGNED,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`car_id`) REFERENCES `cars`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`customer_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`recorded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_status` (`status`),
    INDEX `idx_invoice` (`invoice_id`)
) ENGINE=InnoDB;

-- ============================================================
-- 15. TESTIMONIALS TABLE
-- ============================================================
CREATE TABLE `testimonials` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL,
    `role` VARCHAR(100),
    `avatar` VARCHAR(500),
    `content` TEXT NOT NULL,
    `rating` TINYINT UNSIGNED DEFAULT 5,
    `is_active` TINYINT(1) DEFAULT 1,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 16. PAGES TABLE (Static Content)
-- ============================================================
CREATE TABLE `pages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(300) NOT NULL,
    `slug` VARCHAR(350) NOT NULL UNIQUE,
    `content` LONGTEXT,
    `meta_title` VARCHAR(300),
    `meta_description` VARCHAR(500),
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO `pages` (`title`, `slug`, `content`, `meta_title`) VALUES
('About Us', 'about', '', 'About Zuri Motors - Your Trusted Car Marketplace'),
('Terms & Conditions', 'terms', '', 'Terms & Conditions - Zuri Motors'),
('Privacy Policy', 'privacy', '', 'Privacy Policy - Zuri Motors'),
('FAQ', 'faq', '', 'Frequently Asked Questions - Zuri Motors');

-- ============================================================
-- 17. CHAT MESSAGES TABLE
-- ============================================================
CREATE TABLE `chat_messages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `session_id` VARCHAR(100) NOT NULL,
    `sender_type` ENUM('visitor','agent','system') DEFAULT 'visitor',
    `sender_id` INT UNSIGNED,
    `sender_name` VARCHAR(200),
    `sender_email` VARCHAR(255),
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `car_id` INT UNSIGNED,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`car_id`) REFERENCES `cars`(`id`) ON DELETE SET NULL,
    INDEX `idx_session` (`session_id`),
    INDEX `idx_read` (`is_read`)
) ENGINE=InnoDB;

-- ============================================================
-- 18. ACTIVITY LOG TABLE
-- ============================================================
CREATE TABLE `activity_log` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED,
    `action` VARCHAR(100) NOT NULL,
    `entity_type` VARCHAR(50),
    `entity_id` INT UNSIGNED,
    `description` TEXT,
    `old_values` JSON,
    `new_values` JSON,
    `ip_address` VARCHAR(45),
    `user_agent` VARCHAR(500),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_user` (`user_id`),
    INDEX `idx_entity` (`entity_type`, `entity_id`),
    INDEX `idx_action` (`action`)
) ENGINE=InnoDB;

-- ============================================================
-- 19. FAVORITES / WISHLIST TABLE
-- ============================================================
CREATE TABLE `favorites` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED,
    `car_id` INT UNSIGNED NOT NULL,
    `session_id` VARCHAR(100),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`car_id`) REFERENCES `cars`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `uk_user_car` (`user_id`, `car_id`)
) ENGINE=InnoDB;

-- ============================================================
-- 20. COMPARE SESSIONS TABLE
-- ============================================================
CREATE TABLE `compare_sessions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `session_id` VARCHAR(100) NOT NULL,
    `car_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`car_id`) REFERENCES `cars`(`id`) ON DELETE CASCADE,
    INDEX `idx_session` (`session_id`)
) ENGINE=InnoDB;

-- ============================================================
-- 21. LOCATIONS TABLE (for dropdown filters)
-- ============================================================
CREATE TABLE `locations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `slug` VARCHAR(170) NOT NULL UNIQUE,
    `parent_id` INT UNSIGNED,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    FOREIGN KEY (`parent_id`) REFERENCES `locations`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO `locations` (`name`, `slug`, `sort_order`) VALUES
('Nairobi', 'nairobi', 1),
('Mombasa', 'mombasa', 2),
('Kisumu', 'kisumu', 3),
('Nakuru', 'nakuru', 4),
('Eldoret', 'eldoret', 5),
('Thika', 'thika', 6),
('Nyeri', 'nyeri', 7),
('Malindi', 'malindi', 8),
('Nanyuki', 'nanyuki', 9),
('Naivasha', 'naivasha', 10);

-- ============================================================
-- 22. EMAIL TEMPLATES TABLE
-- ============================================================
CREATE TABLE `email_templates` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(120) NOT NULL UNIQUE,
    `subject` VARCHAR(300),
    `body` LONGTEXT,
    `variables` JSON,
    `is_active` TINYINT(1) DEFAULT 1,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO `email_templates` (`name`, `slug`, `subject`, `body`, `variables`) VALUES
('New Inquiry Notification', 'new-inquiry', 'New Inquiry - {{car_title}}', '<p>A new inquiry has been received for <strong>{{car_title}}</strong></p><p>Name: {{name}}</p><p>Phone: {{phone}}</p><p>Message: {{message}}</p>', '["car_title","name","phone","email","message"]'),
('Inquiry Status Update', 'inquiry-status', 'Your Inquiry Update - {{company_name}}', '<p>Dear {{name}},</p><p>Your inquiry status has been updated to: <strong>{{status}}</strong></p>', '["name","status","company_name"]'),
('Invoice Created', 'invoice-created', 'Invoice #{{invoice_number}} - {{company_name}}', '<p>Dear {{customer_name}},</p><p>Invoice #{{invoice_number}} has been created.</p><p>Amount: {{total_amount}}</p>', '["customer_name","invoice_number","total_amount","company_name"]'),
('Payment Confirmation', 'payment-confirmation', 'Payment Received - {{company_name}}', '<p>Dear {{customer_name}},</p><p>We have received your payment of {{amount}}.</p>', '["customer_name","amount","company_name"]');

COMMIT;
