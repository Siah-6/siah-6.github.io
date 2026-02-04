-- Add missing columns to users table
ALTER TABLE users 
ADD COLUMN email VARCHAR(255) UNIQUE AFTER username,
ADD COLUMN first_name VARCHAR(255) AFTER email,
ADD COLUMN last_name VARCHAR(255) AFTER first_name,
ADD COLUMN phone VARCHAR(20) AFTER last_name,
ADD COLUMN role ENUM('admin', 'customer') DEFAULT 'customer' AFTER phone,
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER role;

-- Insert admin user (password: admin123)
INSERT INTO users (username, email, password, first_name, last_name, phone, role) 
VALUES ('admin', 'admin@legendbrews.com', 'admin123', 'Admin', 'User', '1234567890', 'admin')
ON DUPLICATE KEY UPDATE role = 'admin';

-- Insert sample customer users
INSERT INTO users (username, email, password, first_name, last_name, phone, role) VALUES
('john_doe', 'john@example.com', 'password123', 'John', 'Doe', '09123456789', 'customer'),
('jane_smith', 'jane@example.com', 'password123', 'Jane', 'Smith', '09987654321', 'customer')
ON DUPLICATE KEY UPDATE role = 'customer';
