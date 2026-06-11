-- ============================================================
--  SHASEA Fashion - Database Schema
--  Muslim Women's Fashion Brand
--  Version 1.0
-- ============================================================

CREATE DATABASE IF NOT EXISTS shasea_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shasea_db;

-- ────────────────────────────────────────────────────────────
--  ADMIN USERS
-- ────────────────────────────────────────────────────────────
CREATE TABLE admin_users (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    username    VARCHAR(50)  UNIQUE NOT NULL,
    email       VARCHAR(100) UNIQUE NOT NULL,
    password    VARCHAR(255) NOT NULL,
    name        VARCHAR(100) NOT NULL,
    role        ENUM('superadmin','admin','editor') DEFAULT 'admin',
    avatar      VARCHAR(255),
    is_active   TINYINT(1)   DEFAULT 1,
    last_login  TIMESTAMP    NULL,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

-- ────────────────────────────────────────────────────────────
--  CATEGORIES
-- ────────────────────────────────────────────────────────────
CREATE TABLE categories (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(100) NOT NULL,
    slug        VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    image       VARCHAR(255),
    parent_id   INT          DEFAULT NULL,
    sort_order  INT          DEFAULT 0,
    is_active   TINYINT(1)   DEFAULT 1,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- ────────────────────────────────────────────────────────────
--  PRODUCTS
-- ────────────────────────────────────────────────────────────
CREATE TABLE products (
    id             INT PRIMARY KEY AUTO_INCREMENT,
    category_id    INT,
    name           VARCHAR(255)  NOT NULL,
    slug           VARCHAR(255)  UNIQUE NOT NULL,
    description    TEXT,
    price          DECIMAL(12,2) NOT NULL,
    original_price DECIMAL(12,2),
    images         JSON,         -- ["path/img1.jpg","path/img2.jpg"]
    sizes          JSON,         -- ["S","M","L","XL","XXL"]
    colors         JSON,         -- [{"name":"Hitam","hex":"#000000"},...]
    material       VARCHAR(255),
    weight         DECIMAL(6,2)  DEFAULT 0,
    stock          INT           DEFAULT 0,
    is_featured    TINYINT(1)    DEFAULT 0,
    is_new         TINYINT(1)    DEFAULT 0,
    status         ENUM('active','inactive','out_of_stock') DEFAULT 'active',
    tags           VARCHAR(500),
    total_clicks   INT           DEFAULT 0,
    total_sold     INT           DEFAULT 0,
    created_at     TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- ────────────────────────────────────────────────────────────
--  PRODUCT CLICK TRACKING
-- ────────────────────────────────────────────────────────────
CREATE TABLE product_clicks (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    product_id  INT          NOT NULL,
    session_id  VARCHAR(100),
    ip_address  VARCHAR(45),
    user_agent  TEXT,
    source      ENUM('catalog','homepage','search','related','direct') DEFAULT 'catalog',
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product_id   (product_id),
    INDEX idx_created_at   (created_at),
    INDEX idx_source       (source)
);

-- ────────────────────────────────────────────────────────────
--  ORDERS
-- ────────────────────────────────────────────────────────────
CREATE TABLE orders (
    id                  INT PRIMARY KEY AUTO_INCREMENT,
    order_number        VARCHAR(50)   UNIQUE NOT NULL,
    customer_name       VARCHAR(100)  NOT NULL,
    customer_email      VARCHAR(100),
    customer_phone      VARCHAR(20)   NOT NULL,
    customer_address    TEXT          NOT NULL,
    customer_city       VARCHAR(100),
    customer_province   VARCHAR(100),
    customer_postal_code VARCHAR(10),
    subtotal            DECIMAL(12,2) NOT NULL,
    shipping_cost       DECIMAL(12,2) DEFAULT 0,
    discount            DECIMAL(12,2) DEFAULT 0,
    total_amount        DECIMAL(12,2) NOT NULL,
    status              ENUM('pending','confirmed','processing','shipped','delivered','cancelled','refunded') DEFAULT 'pending',
    payment_method      ENUM('transfer_bank','cod','gopay','ovo','dana','qris') DEFAULT 'transfer_bank',
    payment_status      ENUM('unpaid','paid','failed','refunded') DEFAULT 'unpaid',
    payment_proof       VARCHAR(255),
    shipping_courier    VARCHAR(50),
    tracking_number     VARCHAR(100),
    estimated_delivery  DATE,
    notes               TEXT,
    admin_notes         TEXT,
    created_at          TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status        (status),
    INDEX idx_payment_status(payment_status),
    INDEX idx_created_at    (created_at)
);

-- ────────────────────────────────────────────────────────────
--  ORDER ITEMS
-- ────────────────────────────────────────────────────────────
CREATE TABLE order_items (
    id            INT PRIMARY KEY AUTO_INCREMENT,
    order_id      INT           NOT NULL,
    product_id    INT,
    product_name  VARCHAR(255)  NOT NULL,
    product_image VARCHAR(255),
    price         DECIMAL(12,2) NOT NULL,
    quantity      INT           NOT NULL,
    size          VARCHAR(20),
    color         VARCHAR(50),
    subtotal      DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- ────────────────────────────────────────────────────────────
--  BANNERS / HERO SLIDER
-- ────────────────────────────────────────────────────────────
CREATE TABLE banners (
    id           INT PRIMARY KEY AUTO_INCREMENT,
    title        VARCHAR(255),
    subtitle     TEXT,
    image        VARCHAR(255)  NOT NULL,
    button_text  VARCHAR(100),
    button_link  VARCHAR(255),
    badge_text   VARCHAR(50),
    text_position ENUM('left','center','right') DEFAULT 'center',
    is_active    TINYINT(1)    DEFAULT 1,
    sort_order   INT           DEFAULT 0,
    created_at   TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);

-- ────────────────────────────────────────────────────────────
--  SITE CONTENT  (flexible key-value CMS)
-- ────────────────────────────────────────────────────────────
CREATE TABLE site_content (
    id            INT PRIMARY KEY AUTO_INCREMENT,
    content_key   VARCHAR(100)  UNIQUE NOT NULL,
    content_value LONGTEXT,
    content_type  ENUM('text','html','image','json') DEFAULT 'text',
    label         VARCHAR(255),
    updated_at    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ────────────────────────────────────────────────────────────
--  CONTACTS / MESSAGES
-- ────────────────────────────────────────────────────────────
CREATE TABLE contacts (
    id         INT PRIMARY KEY AUTO_INCREMENT,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(100),
    phone      VARCHAR(20),
    subject    VARCHAR(255),
    message    TEXT NOT NULL,
    status     ENUM('unread','read','replied') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status)
);

-- ────────────────────────────────────────────────────────────
--  NEWSLETTER
-- ────────────────────────────────────────────────────────────
CREATE TABLE newsletter_subscribers (
    id         INT PRIMARY KEY AUTO_INCREMENT,
    email      VARCHAR(100) UNIQUE NOT NULL,
    is_active  TINYINT(1)   DEFAULT 1,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

-- ────────────────────────────────────────────────────────────
--  SEED DATA
-- ────────────────────────────────────────────────────────────

-- Default admin (password: shasea2025)
INSERT INTO admin_users (username, email, password, name, role) VALUES
('superadmin', 'admin@shasea.id', '$2y$12$tHkVK.VHYQ0OmjL5sRkpuuMHJQj8zAGBfv/OsIkpY9jVTZxKVgM.i', 'Super Admin Shasea', 'superadmin');

-- Default categories
INSERT INTO categories (name, slug, description, sort_order) VALUES
('Gamis',        'gamis',        'Koleksi gamis elegan dan modern',        1),
('Outer',        'outer',        'Outer stylish untuk tampilan layering',   2),
('Hijab',        'hijab',        'Koleksi hijab premium berkualitas',       3),
('Set Busana',   'set-busana',   'Set busana muslimah lengkap',             4),
('Rok',          'rok',          'Koleksi rok muslimah berbagai model',     5),
('Kemeja',       'kemeja',       'Kemeja wanita muslimah',                  6);

-- Default site content
INSERT INTO site_content (content_key, content_value, content_type, label) VALUES
('hero_tagline',        'Elegance in Every Thread',                         'text', 'Hero Tagline'),
('hero_subtitle',       'Koleksi busana muslimah premium yang menggabungkan keanggunan dan kenyamanan dalam setiap detail.', 'text', 'Hero Subtitle'),
('about_short',         'Shasea hadir untuk perempuan muslimah modern yang menginginkan busana berkualitas tinggi dengan desain yang timeless dan elegan.', 'text', 'About Short Desc'),
('about_full',          'Shasea berdiri atas dasar kecintaan terhadap keindahan busana muslimah. Kami percaya bahwa setiap perempuan berhak tampil anggun dan percaya diri...', 'html', 'About Full'),
('whatsapp_number',     '6281234567890',                                     'text', 'WhatsApp Number'),
('instagram_handle',    '@shasea.official',                                  'text', 'Instagram Handle'),
('address',             'Bandung, Jawa Barat, Indonesia',                    'text', 'Alamat Toko'),
('shipping_info',       'Pengiriman ke seluruh Indonesia via JNE, J&T, SiCepat', 'text', 'Info Pengiriman'),
('return_policy',       '7 hari pengembalian barang jika terdapat cacat produksi', 'text', 'Return Policy'),
('free_shipping_min',   '500000',                                            'text', 'Min. Free Shipping (IDR)');

-- Sample banner
INSERT INTO banners (title, subtitle, image, button_text, button_link, badge_text, is_active, sort_order) VALUES
('New Collection 2025', 'Temukan keanggunan dalam setiap helai kain premium pilihan kami', 'assets/images/banner-1.jpg', 'Explore Now', 'catalog.php', 'New Arrival', 1, 1),
('Ramadan Collection', 'Tampil sempurna di setiap momen spesial bersama Shasea', 'assets/images/banner-2.jpg', 'Shop Now', 'catalog.php?category=gamis', 'Special Edition', 1, 2);

-- Sample products
INSERT INTO products (category_id, name, slug, description, price, original_price, images, sizes, colors, material, stock, is_featured, is_new, status) VALUES
(1, 'Gamis Naura Dusty', 'gamis-naura-dusty', 'Gamis dengan potongan A-line yang anggun, menggunakan bahan premium crepe korea yang jatuh dan nyaman dipakai seharian. Detail tangan kerut menambah kesan feminin dan elegan.', 285000, 350000, '["assets/images/products/gamis-naura-1.jpg","assets/images/products/gamis-naura-2.jpg"]', '["S","M","L","XL","XXL"]', '[{"name":"Dusty Rose","hex":"#C9A0A0"},{"name":"Sage Green","hex":"#8FAF8F"},{"name":"Navy","hex":"#1B2A4A"}]', 'Crepe Korea Premium', 45, 1, 1, 'active'),
(1, 'Gamis Alula Hitam', 'gamis-alula-hitam', 'Gamis simpel berkesan mewah dengan detail gold button di bagian depan. Bahan ceruti matte yang tidak mudah kusut, cocok untuk berbagai acara formal maupun kasual.', 320000, NULL, '["assets/images/products/gamis-alula-1.jpg"]', '["S","M","L","XL"]', '[{"name":"Hitam","hex":"#1A1A1A"},{"name":"Cokelat Tua","hex":"#4A3728"}]', 'Ceruti Matte', 30, 1, 0, 'active'),
(2, 'Outer Kiara Caramel', 'outer-kiara-caramel', 'Outer panjang dengan silhouette yang elegan, menggunakan bahan woven premium. Bisa dipadukan dengan berbagai outfit untuk tampilan yang stylish dan modest.', 195000, 240000, '["assets/images/products/outer-kiara-1.jpg"]', '["S","M","L","XL","XXL"]', '[{"name":"Caramel","hex":"#C4A882"},{"name":"Cream","hex":"#F0E8DC"},{"name":"Mocca","hex":"#7A5C3C"}]', 'Woven Premium', 60, 1, 1, 'active'),
(3, 'Hijab Segi Empat Premium', 'hijab-segi-empat-premium', 'Hijab segi empat dengan bahan voal laser yang lembut dan tidak mudah kusut. Tersedia dalam berbagai pilihan warna netral yang mudah dipadukan.', 85000, NULL, '["assets/images/products/hijab-voal-1.jpg"]', '["All Size"]', '[{"name":"Krem","hex":"#F5EDD6"},{"name":"Hitam","hex":"#1A1A1A"},{"name":"Abu","hex":"#9B9B9B"},{"name":"Dusty Pink","hex":"#D4A0A0"}]', 'Voal Laser Premium', 120, 0, 1, 'active'),
(4, 'Set Syana Olive', 'set-syana-olive', 'Set lengkap terdiri dari inner dress dan outer yang bisa digunakan terpisah. Menggunakan bahan linen premium yang adem dan nyaman.', 425000, 500000, '["assets/images/products/set-syana-1.jpg"]', '["S","M","L","XL"]', '[{"name":"Olive Green","hex":"#6B7A50"},{"name":"Sand","hex":"#C4A882"}]', 'Linen Premium', 25, 1, 0, 'active'),
(1, 'Gamis Rania Broken White', 'gamis-rania-broken-white', 'Gamis dengan tampilan yang clean dan minimalis. Potongan loose fit yang nyaman dengan detail rempel di bagian dada. Cocok untuk aktivitas sehari-hari maupun acara semi-formal.', 265000, NULL, '["assets/images/products/gamis-rania-1.jpg"]', '["S","M","L","XL","XXL"]', '[{"name":"Broken White","hex":"#F0EDE6"},{"name":"Dusty Blue","hex":"#8FA3B1"}]', 'Rayon Premium', 50, 0, 1, 'active');
