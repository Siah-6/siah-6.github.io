-- Fix the total column to use DECIMAL instead of INT
ALTER TABLE receipts 
MODIFY COLUMN total DECIMAL(10, 2) NOT NULL DEFAULT 0.00;

-- Also update the price column in receipt_items to be consistent
ALTER TABLE receipt_items 
MODIFY COLUMN price DECIMAL(10, 2) NOT NULL DEFAULT 0.00;

-- Update the price column in products to be consistent
ALTER TABLE products 
MODIFY COLUMN price DECIMAL(10, 2) NOT NULL DEFAULT 0.00;
