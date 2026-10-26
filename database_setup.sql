-- ============================================
-- YUME RAMEN DATABASE SETUP
-- ============================================
-- Database voor het beheren van een ramen restaurant
-- Inclusief: menu items, bestellingen, klanten, ingrediënten, en voorraad

-- Database aanmaken
CREATE DATABASE IF NOT EXISTS yume;
USE yume;

-- ============================================
-- TABELLEN VOOR MENU BEHEER
-- ============================================

-- Categorieën voor menu items (ramen, bijgerechten, dranken, etc.)
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Menu items (alle gerechten en dranken)
CREATE TABLE menu_items (
    item_id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    spice_level ENUM('mild', 'medium', 'spicy', 'extra_spicy') DEFAULT 'mild',
    is_vegetarian BOOLEAN DEFAULT FALSE,
    is_vegan BOOLEAN DEFAULT FALSE,
    is_gluten_free BOOLEAN DEFAULT FALSE,
    calories INT,
    preparation_time INT COMMENT 'Tijd in minuten',
    is_available BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- Ingrediënten
CREATE TABLE ingredients (
    ingredient_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    unit VARCHAR(50) NOT NULL COMMENT 'gram, ml, stuks, etc.',
    stock_quantity DECIMAL(10, 2) DEFAULT 0,
    min_stock_level DECIMAL(10, 2) DEFAULT 0 COMMENT 'Minimum voorraad niveau',
    cost_per_unit DECIMAL(10, 2),
    supplier VARCHAR(150),
    is_allergen BOOLEAN DEFAULT FALSE,
    allergen_type VARCHAR(100) COMMENT 'gluten, noten, schaaldieren, etc.',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Koppeltabel: welke ingrediënten zitten in welk menu item
CREATE TABLE menu_item_ingredients (
    item_id INT NOT NULL,
    ingredient_id INT NOT NULL,
    quantity DECIMAL(10, 2) NOT NULL COMMENT 'Hoeveelheid per portie',
    PRIMARY KEY (item_id, ingredient_id),
    FOREIGN KEY (item_id) REFERENCES menu_items(item_id) ON DELETE CASCADE,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(ingredient_id)
);

-- ============================================
-- TABELLEN VOOR KLANTEN
-- ============================================

-- Klanten
CREATE TABLE customers (
    customer_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    date_of_birth DATE,
    loyalty_points INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Adressen van klanten (voor bezorging)
CREATE TABLE customer_addresses (
    address_id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    address_type ENUM('home', 'work', 'other') DEFAULT 'home',
    street VARCHAR(200) NOT NULL,
    house_number VARCHAR(20) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    city VARCHAR(100) NOT NULL,
    country VARCHAR(100) DEFAULT 'Nederland',
    is_default BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE
);

-- ============================================
-- TABELLEN VOOR BESTELLINGEN
-- ============================================

-- Bestellingen
CREATE TABLE orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    order_type ENUM('dine_in', 'takeaway', 'delivery') NOT NULL,
    order_status ENUM('pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled') DEFAULT 'pending',
    table_number INT COMMENT 'Voor dine-in bestellingen',
    delivery_address_id INT COMMENT 'Voor bezorgbestellingen',
    subtotal DECIMAL(10, 2) NOT NULL,
    tax_amount DECIMAL(10, 2) DEFAULT 0,
    delivery_fee DECIMAL(10, 2) DEFAULT 0,
    discount_amount DECIMAL(10, 2) DEFAULT 0,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('cash', 'card', 'online', 'ideal', 'paypal') NOT NULL,
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    special_instructions TEXT,
    estimated_delivery_time TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (delivery_address_id) REFERENCES customer_addresses(address_id)
);

-- Bestelling items (welke items zitten in een bestelling)
CREATE TABLE order_items (
    order_item_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    special_requests TEXT COMMENT 'Extra toppings, geen ui, etc.',
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES menu_items(item_id)
);

-- ============================================
-- TABELLEN VOOR PERSONEEL & OPERATIES
-- ============================================

-- Personeel
CREATE TABLE staff (
    staff_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    phone VARCHAR(20),
    role ENUM('chef', 'waiter', 'cashier', 'manager', 'delivery') NOT NULL,
    hourly_rate DECIMAL(10, 2),
    hire_date DATE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reserveringen (voor tafels)
CREATE TABLE reservations (
    reservation_id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT,
    customer_name VARCHAR(200) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    customer_email VARCHAR(150),
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    party_size INT NOT NULL,
    table_number INT,
    status ENUM('pending', 'confirmed', 'seated', 'completed', 'cancelled') DEFAULT 'pending',
    special_requests TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);

-- Reviews en ratings
CREATE TABLE reviews (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    order_id INT,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    title VARCHAR(200),
    comment TEXT,
    response TEXT COMMENT 'Reactie van restaurant',
    is_approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

-- ============================================
-- VOORBEELDDATA INVOEGEN
-- ============================================

-- Categorieën
INSERT INTO categories (name, description, display_order) VALUES
('Ramen', 'Authentieke Japanse ramen soepen', 1),
('Bijgerechten', 'Heerlijke Japanse bijgerechten', 2),
('Dranken', 'Verfrissende dranken en thee', 3),
('Desserts', 'Zoete afsluiting', 4);

-- Menu items
INSERT INTO menu_items (category_id, name, description, price, spice_level, is_vegetarian, preparation_time, is_featured) VALUES
(1, 'Tonkotsu Ramen', 'Rijke varkensbouillon met chashu, ajitsuke tamago, en nori', 14.50, 'mild', FALSE, 15, TRUE),
(1, 'Shoyu Ramen', 'Klassieke sojasaus bouillon met kip, bamboescheuten en lente-ui', 13.50, 'mild', FALSE, 15, TRUE),
(1, 'Miso Ramen', 'Hartige miso bouillon met gegrilde groenten en tofu', 13.00, 'medium', TRUE, 15, FALSE),
(1, 'Spicy Tan Tan Ramen', 'Pittige sesam bouillon met gehakt en chili olie', 15.00, 'extra_spicy', FALSE, 15, TRUE),
(2, 'Gyoza (6 stuks)', 'Japanse dumplings gevuld met varkensvlees en groenten', 7.50, 'mild', FALSE, 10, FALSE),
(2, 'Edamame', 'Gestoomde sojabonen met zeezout', 5.00, 'mild', TRUE, 5, FALSE),
(2, 'Takoyaki (6 stuks)', 'Octopus balletjes met takoyaki saus en bonito flakes', 8.50, 'mild', FALSE, 12, FALSE),
(3, 'Groene Thee', 'Traditionele Japanse groene thee', 3.00, 'mild', TRUE, 3, FALSE),
(3, 'Ramune', 'Japanse limonade met verschillende smaken', 3.50, 'mild', TRUE, 1, FALSE),
(4, 'Mochi IJsjes (3 stuks)', 'Zachte rijstcake met ijs in verschillende smaken', 6.50, 'mild', TRUE, 2, FALSE);

-- Ingrediënten
INSERT INTO ingredients (name, unit, stock_quantity, min_stock_level, cost_per_unit, is_allergen, allergen_type) VALUES
('Ramen noedels', 'gram', 5000, 1000, 0.50, TRUE, 'gluten'),
('Varkensbouillon', 'ml', 10000, 2000, 0.30, FALSE, NULL),
('Chashu (varkensbuik)', 'gram', 2000, 500, 1.20, FALSE, NULL),
('Ajitsuke tamago (gekookt ei)', 'stuks', 100, 20, 0.40, TRUE, 'ei'),
('Nori (zeewier)', 'vellen', 200, 50, 0.15, FALSE, NULL),
('Lente-ui', 'gram', 500, 100, 0.05, FALSE, NULL),
('Bamboescheuten', 'gram', 1000, 200, 0.30, FALSE, NULL),
('Miso pasta', 'gram', 2000, 500, 0.80, TRUE, 'soja'),
('Tofu', 'gram', 1500, 300, 0.60, FALSE, NULL),
('Gyoza vellen', 'stuks', 500, 100, 0.10, TRUE, 'gluten');

-- Klanten (voorbeelddata)
INSERT INTO customers (first_name, last_name, email, phone, password_hash, loyalty_points) VALUES
('Jan', 'de Vries', 'jan.devries@email.com', '0612345678', '$2y$10$example_hash_1', 150),
('Emma', 'Jansen', 'emma.jansen@email.com', '0687654321', '$2y$10$example_hash_2', 250),
('Mohammed', 'Ali', 'mohammed.ali@email.com', '0698765432', '$2y$10$example_hash_3', 50);

-- Adressen
INSERT INTO customer_addresses (customer_id, street, house_number, postal_code, city, is_default) VALUES
(1, 'Hoofdstraat', '123', '3011 AB', 'Rotterdam', TRUE),
(2, 'Westersingel', '45', '3014 GP', 'Rotterdam', TRUE),
(3, 'Coolsingel', '78', '3012 AG', 'Rotterdam', TRUE);

-- Personeel
INSERT INTO staff (first_name, last_name, email, phone, role, hourly_rate, hire_date) VALUES
('Kenji', 'Tanaka', 'kenji@yumeramen.nl', '0611111111', 'chef', 18.50, '2024-01-15'),
('Lisa', 'van Dam', 'lisa@yumeramen.nl', '0622222222', 'waiter', 12.00, '2024-03-20'),
('Ahmed', 'Hassan', 'ahmed@yumeramen.nl', '0633333333', 'manager', 20.00, '2023-11-01');

-- ============================================
-- NUTTIGE VIEWS
-- ============================================

-- View voor populaire menu items
CREATE VIEW popular_menu_items AS
SELECT 
    mi.item_id,
    mi.name,
    c.name AS category,
    mi.price,
    COUNT(oi.order_item_id) AS times_ordered,
    SUM(oi.quantity) AS total_quantity_sold,
    SUM(oi.subtotal) AS total_revenue
FROM menu_items mi
LEFT JOIN order_items oi ON mi.item_id = oi.item_id
LEFT JOIN categories c ON mi.category_id = c.category_id
GROUP BY mi.item_id
ORDER BY times_ordered DESC;

-- View voor klant besteloverzicht
CREATE VIEW customer_order_summary AS
SELECT 
    c.customer_id,
    CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
    c.email,
    COUNT(o.order_id) AS total_orders,
    SUM(o.total_amount) AS total_spent,
    c.loyalty_points
FROM customers c
LEFT JOIN orders o ON c.customer_id = o.customer_id
GROUP BY c.customer_id;

-- View voor lage voorraad
CREATE VIEW low_stock_ingredients AS
SELECT 
    ingredient_id,
    name,
    stock_quantity,
    min_stock_level,
    unit,
    supplier
FROM ingredients
WHERE stock_quantity <= min_stock_level
ORDER BY stock_quantity ASC;

-- ============================================
-- INDEXES VOOR BETERE PERFORMANCE
-- ============================================

CREATE INDEX idx_menu_items_category ON menu_items(category_id);
CREATE INDEX idx_orders_customer ON orders(customer_id);
CREATE INDEX idx_orders_status ON orders(order_status);
CREATE INDEX idx_orders_date ON orders(created_at);
CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_customers_email ON customers(email);
CREATE INDEX idx_reservations_date ON reservations(reservation_date);

-- ============================================
-- EINDE VAN DATABASE SETUP
-- ============================================
