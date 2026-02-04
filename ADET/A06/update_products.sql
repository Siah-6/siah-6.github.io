-- Add stock column to products table
ALTER TABLE products 
ADD COLUMN stock INT DEFAULT 50 AFTER image_url,
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER stock,
ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

-- Update existing products with stock values
UPDATE products SET stock = 50 WHERE stock IS NULL;

-- Add user_id to receipts table for order tracking
ALTER TABLE receipts 
ADD COLUMN user_id INT AFTER id,
ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;
