-- SQL om customizations kolom toe te voegen aan order_items tabel
-- Voer dit uit in phpMyAdmin als de kolom nog niet bestaat

ALTER TABLE order_items 
ADD COLUMN IF NOT EXISTS special_requests TEXT COMMENT 'Customizations zoals broth, noodles, spice, toppings';

-- Als je database IF NOT EXISTS niet ondersteunt, gebruik dan:
-- ALTER TABLE order_items ADD COLUMN special_requests TEXT COMMENT 'Customizations zoals broth, noodles, spice, toppings';
