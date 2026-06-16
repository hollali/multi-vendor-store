-- Celer Market - Seed Data
USE celer_market;

-- Admin password: admin123 (bcrypt hash)
INSERT INTO users (uuid, first_name, last_name, email, phone, password, role, status, email_verified_at) VALUES
(UUID(), 'Super', 'Admin', 'admin@celermarket.com', '+233500000001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', NOW()),
(UUID(), 'John', 'Doe', 'vendor@celermarket.com', '+233500000002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendor', 'active', NOW()),
(UUID(), 'Jane', 'Smith', 'customer@celermarket.com', '+233500000003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 'active', NOW());

INSERT INTO stores (vendor_id, store_name, slug, description, email, phone, is_verified, is_active) VALUES
(2, 'Tech Haven', 'tech-haven', 'Your premium destination for electronics and gadgets in Accra.', 'vendor@celermarket.com', '+233500000002', TRUE, TRUE);

INSERT INTO categories (name, slug, description, sort_order) VALUES
('Electronics', 'electronics', 'Smartphones, laptops, tablets, and accessories', 1),
('Fashion', 'fashion', 'Clothing, shoes, bags, and accessories', 2),
('Home & Kitchen', 'home-kitchen', 'Home appliances, kitchen tools, and decor', 3),
('Beauty & Health', 'beauty-health', 'Skincare, makeup, health products', 4),
('Sports & Outdoors', 'sports-outdoors', 'Sports equipment, fitness gear, outdoor gear', 5),
('Books & Media', 'books-media', 'Books, e-books, movies, music', 6),
('Toys & Games', 'toys-games', 'Toys, board games, video games', 7),
('Automotive', 'automotive', 'Car parts, accessories, tools', 8);

INSERT INTO categories (name, slug, description, parent_id, sort_order) VALUES
('Smartphones', 'smartphones', 'Mobile phones and smartphones', 1, 1),
('Laptops', 'laptops', 'Notebooks and laptops', 1, 2),
('Tablets', 'tablets', 'Tablets and e-readers', 1, 3),
('Headphones', 'headphones', 'Headphones and earphones', 1, 4);

INSERT INTO brands (name, slug, description) VALUES
('Apple', 'apple', 'Apple Inc.'), ('Samsung', 'samsung', 'Samsung Electronics'),
('Nike', 'nike', 'Nike Inc.'), ('Adidas', 'adidas', 'Adidas AG'),
('Sony', 'sony', 'Sony Corporation'), ('LG', 'lg', 'LG Electronics'),
('Dell', 'dell', 'Dell Technologies'), ('HP', 'hp', 'HP Inc.');

INSERT INTO products (vendor_id, store_id, category_id, brand_id, name, slug, sku, description, base_price, sale_price, quantity, is_active, is_approved, status) VALUES
(2, 1, 9, 1, 'iPhone 15 Pro Max', 'iphone-15-pro-max', 'IP15PM-256', 'A17 Pro chip. 48MP camera. Titanium design. All-day battery.', 1599999.00, 1499999.00, 50, TRUE, TRUE, 'approved'),
(2, 1, 10, 2, 'Samsung Galaxy Book 3', 'samsung-galaxy-book-3', 'SGB3-512', 'Intel i7, 16GB RAM, 512GB SSD. Perfect for work and play.', 1299999.00, 1199999.00, 30, TRUE, TRUE, 'approved'),
(2, 1, 9, 1, 'Apple AirPods Pro 2', 'apple-airpods-pro-2', 'AAP2-USB', 'Active Noise Cancellation. Adaptive Audio. USB-C.', 449999.00, 399999.00, 100, TRUE, TRUE, 'approved'),
(2, 1, 12, 5, 'Sony WH-1000XM5', 'sony-wh-1000xm5', 'SWH5-BLK', 'Industry-leading noise cancellation. 30hr battery.', 599999.00, 549999.00, 75, TRUE, TRUE, 'approved'),
(2, 1, 9, 1, 'iPhone 15 Pro', 'iphone-15-pro', 'IP15P-128', 'A17 Pro chip. 48MP camera. Titanium design.', 1299999.00, 1199999.00, 60, TRUE, TRUE, 'approved'),
(2, 1, 9, 2, 'Samsung Galaxy S24 Ultra', 'samsung-galaxy-s24-ultra', 'SGS24U-256', 'Galaxy AI. Titanium. 200MP camera. S Pen included.', 1450000.00, 1350000.00, 40, TRUE, TRUE, 'approved');

INSERT INTO product_images (product_id, image, is_primary, sort_order) VALUES
(1, 'uploads/products/iphone15pm-1.jpg', TRUE, 1), (1, 'uploads/products/iphone15pm-2.jpg', FALSE, 2),
(2, 'uploads/products/galaxybook3-1.jpg', TRUE, 1), (3, 'uploads/products/airpodspro2-1.jpg', TRUE, 1),
(4, 'uploads/products/wh1000xm5-1.jpg', TRUE, 1), (5, 'uploads/products/iphone15p-1.jpg', TRUE, 1),
(6, 'uploads/products/s24ultra-1.jpg', TRUE, 1);

INSERT INTO product_variants (product_id, name, sku, price_adjustment, quantity) VALUES
(1, 'Color', 'IP15PM-COL', 0, 50), (2, 'Storage', 'SGB3-STOR', 0, 30), (6, 'Color', 'SGS24U-COL', 0, 40);

INSERT INTO product_variant_values (variant_id, value, price_adjustment, quantity) VALUES
(1, 'Natural Titanium', 0, 20), (1, 'Blue Titanium', 0, 15), (1, 'White Titanium', 0, 15),
(2, '256GB', -50000, 10), (2, '512GB', 0, 15), (2, '1TB', 100000, 5),
(3, 'Titanium Gray', 0, 20), (3, 'Titanium Black', 0, 20);

INSERT INTO coupons (code, type, value, min_order_amount, usage_limit, is_active, starts_at, expires_at) VALUES
('WELCOME10', 'percentage', 10, 50000, 100, TRUE, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY)),
('SAVE50K', 'fixed', 50000, 200000, 50, TRUE, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY));

INSERT INTO settings (`key`, `value`, group_name, type) VALUES
('site_name', 'Celer Market', 'general', 'text'),
('site_description', 'Multi-Vendor E-Commerce Platform', 'general', 'text'),
('site_email', 'info@celermarket.com', 'general', 'text'),
('site_phone', '+233500000000', 'general', 'text'),
('currency', 'GHS', 'general', 'text'),
('currency_symbol', 'GH₵', 'general', 'text'),
('tax_rate', '7.5', 'tax', 'text'),
('shipping_fee', '5000', 'shipping', 'text'),
('free_shipping_min', '100000', 'shipping', 'text'),
('commission_rate', '10', 'commission', 'text'),
('min_withdrawal', '5000', 'withdrawal', 'text'),
('max_login_attempts', '5', 'security', 'text'),
('lockout_duration', '15', 'security', 'text'),
('paystack_public_key', 'pk_test_xxxxxxxxxxxxx', 'payment', 'text'),
('paystack_secret_key', 'sk_test_xxxxxxxxxxxxx', 'payment', 'text'),
('homepage_title', 'Welcome to Celer Market', 'general', 'text'),
('homepage_subtitle', 'Discover amazing products from trusted vendors', 'general', 'text');

INSERT INTO banners (title, subtitle, image, link, sort_order) VALUES
('Summer Sale', 'Get up to 50% off on selected items', 'uploads/banners/summer-sale.jpg', '/shop', 1),
('New Arrivals', 'Check out the latest products from top brands', 'uploads/banners/new-arrivals.jpg', '/shop', 2),
('Free Shipping', 'On orders above GH₵1,000', 'uploads/banners/free-shipping.jpg', '/shop', 3);
