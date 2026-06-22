-- Celer Market v2 - Global Expansion Schema
-- Adds geolocation, multi-currency, international shipping, vendor order splitting

-- Countries reference data
CREATE TABLE IF NOT EXISTS countries (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code CHAR(2) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    phone_code VARCHAR(10),
    currency_code CHAR(3),
    region ENUM('africa','asia','europe','north_america','south_america','oceania') DEFAULT 'africa',
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_region (region),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- Currencies reference
CREATE TABLE IF NOT EXISTS currencies (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code CHAR(3) NOT NULL UNIQUE,
    name VARCHAR(50) NOT NULL,
    symbol VARCHAR(10) NOT NULL,
    decimal_places TINYINT DEFAULT 2,
    exchange_rate DECIMAL(14,6) DEFAULT 1.000000,
    is_base BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_base (is_base)
) ENGINE=InnoDB;

-- Shipping zones
CREATE TABLE IF NOT EXISTS shipping_zones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    type ENUM('domestic','regional','international') DEFAULT 'domestic',
    countries TEXT COMMENT 'Comma-separated country codes',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_type (type),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- Vendor-specific shipping rates per zone
CREATE TABLE IF NOT EXISTS vendor_shipping_rates (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT UNSIGNED NOT NULL,
    zone_id INT UNSIGNED NOT NULL,
    base_rate DECIMAL(12,2) NOT NULL DEFAULT 0,
    rate_per_kg DECIMAL(12,2) NOT NULL DEFAULT 0,
    free_shipping_min DECIMAL(12,2) DEFAULT NULL COMMENT 'Min order for free shipping',
    estimated_days_min INT DEFAULT 3,
    estimated_days_max INT DEFAULT 7,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (zone_id) REFERENCES shipping_zones(id) ON DELETE CASCADE,
    UNIQUE KEY unique_vendor_zone (vendor_id, zone_id),
    INDEX idx_vendor (vendor_id),
    INDEX idx_zone (zone_id)
) ENGINE=InnoDB;

-- User country/locale preferences
CREATE TABLE IF NOT EXISTS user_preferences (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED,
    session_id VARCHAR(100),
    country_code CHAR(2) DEFAULT 'GH',
    currency_code CHAR(3) DEFAULT 'GHS',
    language VARCHAR(10) DEFAULT 'en',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_session (session_id),
    UNIQUE KEY unique_user (user_id)
) ENGINE=InnoDB;

-- Vendor sub-orders (for multi-vendor checkout)
CREATE TABLE IF NOT EXISTS vendor_orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parent_order_id INT UNSIGNED NOT NULL,
    vendor_id INT UNSIGNED NOT NULL,
    store_id INT UNSIGNED,
    order_number VARCHAR(50) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    shipping_cost DECIMAL(12,2) DEFAULT 0,
    tax DECIMAL(12,2) DEFAULT 0,
    discount DECIMAL(12,2) DEFAULT 0,
    total DECIMAL(12,2) NOT NULL,
    status ENUM('pending','processing','shipped','delivered','cancelled','returned') DEFAULT 'pending',
    tracking_number VARCHAR(100),
    shipping_carrier VARCHAR(100),
    shipping_method VARCHAR(100),
    estimated_delivery_min INT,
    estimated_delivery_max INT,
    notes TEXT,
    shipped_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (vendor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE SET NULL,
    INDEX idx_parent_order (parent_order_id),
    INDEX idx_vendor (vendor_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Add vendor_order_id to order_items
ALTER TABLE order_items ADD COLUMN IF NOT EXISTS vendor_order_id INT UNSIGNED DEFAULT NULL AFTER order_id;
-- Only add FK if column was added
-- (Handled by application logic)

-- Add currency to orders
ALTER TABLE orders ADD COLUMN IF NOT EXISTS currency_code CHAR(3) DEFAULT 'GHS' AFTER total;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS currency_symbol VARCHAR(10) DEFAULT 'GH₵' AFTER currency_code;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS exchange_rate DECIMAL(14,6) DEFAULT 1.000000 AFTER currency_symbol;

-- Add shipping info to orders
ALTER TABLE orders ADD COLUMN IF NOT EXISTS shipping_zone_id INT UNSIGNED DEFAULT NULL AFTER shipping_carrier;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS shipping_country_code CHAR(2) DEFAULT NULL AFTER shipping_zone_id;

-- Add shipping columns to products
ALTER TABLE products ADD COLUMN IF NOT EXISTS weight_kg DECIMAL(10,3) DEFAULT 0 AFTER weight;
ALTER TABLE products ADD COLUMN IF NOT EXISTS ships_from_country CHAR(2) DEFAULT 'GH' AFTER weight_kg;
ALTER TABLE products ADD COLUMN IF NOT EXISTS ships_worldwide BOOLEAN DEFAULT FALSE AFTER ships_from_country;
ALTER TABLE products ADD COLUMN IF NOT EXISTS free_shipping BOOLEAN DEFAULT FALSE AFTER ships_worldwide;

-- Add international columns to stores
ALTER TABLE stores ADD COLUMN IF NOT EXISTS ships_from_country CHAR(2) DEFAULT 'GH' AFTER country;
ALTER TABLE stores ADD COLUMN IF NOT EXISTS ships_worldwide BOOLEAN DEFAULT FALSE AFTER ships_from_country;

-- Seed default data
INSERT IGNORE INTO currencies (code, name, symbol, decimal_places, exchange_rate, is_base, is_active) VALUES
('GHS', 'Ghanaian Cedi', 'GH₵', 2, 1.000000, TRUE, TRUE),
('NGN', 'Nigerian Naira', '₦', 2, 68.500000, FALSE, TRUE),
('USD', 'US Dollar', '$', 2, 0.076000, FALSE, TRUE),
('EUR', 'Euro', '€', 2, 0.070000, FALSE, TRUE),
('GBP', 'British Pound', '£', 2, 0.060000, FALSE, TRUE),
('KES', 'Kenyan Shilling', 'KSh', 2, 10.250000, FALSE, TRUE),
('ZAR', 'South African Rand', 'R', 2, 1.420000, FALSE, TRUE),
('XOF', 'West African CFA', 'CFA', 0, 46.500000, FALSE, TRUE),
('UGX', 'Ugandan Shilling', 'USh', 0, 282.500000, FALSE, TRUE),
('TZS', 'Tanzanian Shilling', 'TSh', 0, 186.500000, FALSE, TRUE),
('RWF', 'Rwandan Franc', 'FRw', 0, 102.500000, FALSE, TRUE),
('CAD', 'Canadian Dollar', 'CA$', 2, 0.104000, FALSE, TRUE),
('AUD', 'Australian Dollar', 'A$', 2, 0.116000, FALSE, TRUE),
('CNY', 'Chinese Yuan', '¥', 2, 0.548000, FALSE, TRUE),
('INR', 'Indian Rupee', '₹', 2, 6.320000, FALSE, TRUE);

-- Seed countries for Africa (primary market)
INSERT IGNORE INTO countries (code, name, phone_code, currency_code, region, sort_order) VALUES
('GH', 'Ghana', '+233', 'GHS', 'africa', 1),
('NG', 'Nigeria', '+234', 'NGN', 'africa', 2),
('KE', 'Kenya', '+254', 'KES', 'africa', 3),
('ZA', 'South Africa', '+27', 'ZAR', 'africa', 4),
('CI', 'Côte d\'Ivoire', '+225', 'XOF', 'africa', 5),
('SN', 'Senegal', '+221', 'XOF', 'africa', 6),
('UG', 'Uganda', '+256', 'UGX', 'africa', 7),
('TZ', 'Tanzania', '+255', 'TZS', 'africa', 8),
('RW', 'Rwanda', '+250', 'RWF', 'africa', 9),
('ET', 'Ethiopia', '+251', 'ETB', 'africa', 10),
('CM', 'Cameroon', '+237', 'XAF', 'africa', 11),
('ZM', 'Zambia', '+260', 'ZMW', 'africa', 12),
('US', 'United States', '+1', 'USD', 'north_america', 13),
('GB', 'United Kingdom', '+44', 'GBP', 'europe', 14),
('CA', 'Canada', '+1', 'CAD', 'north_america', 15),
('DE', 'Germany', '+49', 'EUR', 'europe', 16),
('FR', 'France', '+33', 'EUR', 'europe', 17),
('CN', 'China', '+86', 'CNY', 'asia', 18),
('IN', 'India', '+91', 'INR', 'asia', 19),
('AU', 'Australia', '+61', 'AUD', 'oceania', 20);

-- Seed default shipping zones
INSERT IGNORE INTO shipping_zones (name, description, type, countries) VALUES
('Local - Ghana', 'Domestic shipping within Ghana', 'domestic', 'GH'),
('West Africa', 'Regional shipping to West African countries', 'regional', 'NG,CI,SN,BF,ML,NE,SL,LR,GN,GM,GW'),
('East Africa', 'Regional shipping to East African countries', 'regional', 'KE,UG,TZ,RW,ET,SO,SD,SS,BI,CD'),
('Southern Africa', 'Regional shipping to Southern African countries', 'regional', 'ZA,ZW,ZM,MW,MZ,NA,BW,LS,SZ,AO'),
('International', 'Worldwide shipping to all other countries', 'international', 'US,GB,CA,DE,FR,CN,IN,AU,JP,BR,MX,IT,ES,PT,NL,BE,CH,SE,NO,DK,FI,IE,AT,PL,CZ,HU,RO,BG,GR,TR,AE,SA,QA,KW,OM,BH,MY,SG,TH,ID,PH,KR,TW,PK,BD,LK,NP');

-- Add payment tracking columns used by Payment model & CheckoutController
ALTER TABLE payments ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50) DEFAULT NULL AFTER paystack_access_code;
ALTER TABLE payments ADD COLUMN IF NOT EXISTS payment_reference VARCHAR(100) DEFAULT NULL AFTER payment_method;
ALTER TABLE payments ADD COLUMN IF NOT EXISTS notes TEXT DEFAULT NULL AFTER metadata;

-- Set base currency rate for GHS
UPDATE currencies SET exchange_rate = 1.000000, is_base = TRUE WHERE code = 'GHS';
