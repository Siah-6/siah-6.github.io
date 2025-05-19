CREATE DATABASE IF NOT EXISTS legend_brews;
USE legend_brews;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  price INT NOT NULL,
  category VARCHAR(50) NOT NULL,
  image_url TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS receipts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  total INT NOT NULL DEFAULT 0,
  created_at VARCHAR(255) NOT NULL,
  completed BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE IF NOT EXISTS receipt_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  receipt_id INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  price INT NOT NULL
);

INSERT INTO products (name, description, price, category, image_url) VALUES
('Espresso', 'Strong and bold hot coffee.', 100, 'hot', 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&h=500'),
('Cappuccino', 'Rich foam and espresso blend.', 120, 'hot', 'https://images.unsplash.com/photo-1534778101976-62847782c213?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&h=500'),
('Mocha', 'Chocolate-flavored espresso.', 130, 'hot', 'https://images.unsplash.com/photo-1610889556528-9a770e32642f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&h=500'),
('Americano', 'Espresso diluted with hot water.', 110, 'hot', 'https://images.unsplash.com/photo-1551030173-122aabc4489c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&h=500'),

('Iced Latte', 'Chilled espresso with milk.', 130, 'cold', 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&h=500'),
('Cold Brew', 'Slow-steeped refreshing coffee.', 140, 'cold', 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&h=500'),
('Iced Americano', 'Chilled espresso with water.', 120, 'cold', 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&h=500'),
('Frappuccino', 'Blended creamy iced coffee.', 150, 'cold', 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&h=500'),

('Butter Croissant', 'Flaky, buttery, and baked fresh.', 70, 'pastries', 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&h=500'),
('Blueberry Muffin', 'Moist muffin loaded with berries.', 65, 'pastries', 'https://images.unsplash.com/photo-1607958996333-41aef7caefaa?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&h=500'),
('Cheese Danish', 'Sweet pastry with cream cheese.', 80, 'pastries', 'https://media.istockphoto.com/id/134104042/photo/danish.webp?a=1&b=1&s=612x612&w=0&k=20&c=ue5vG8MESsrKwu2bDGaiYgHMG-WZaG80mwuxtpmUTNc='),
('Chocolate Donut', 'Classic donut dipped in chocolate.', 50, 'pastries', 'https://plus.unsplash.com/premium_photo-1672846027103-a50886f99?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'),

('Legend Tumbler', 'Stainless steel coffee tumbler.', 300, 'merch', 'https://plus.unsplash.com/premium_photo-1681154819809-b660a509e1ee?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8dHVtYmxlcnxlbnwwfHwwfHx8MA%3D%3D'),
('Coffee Tote Bag', 'Reusable and stylish canvas bag.', 200, 'merch', 'https://images.unsplash.com/photo-1548863227-3af567fc3b27?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTF8fHRvdGUlMjBiYWd8ZW58MHx8MHx8fDA%3D'),
('Legend Mug', 'Ceramic mug with our logo.', 150, 'merch', 'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&h=500'),
('Coffee Cap', 'Casual branded coffee cap.', 180, 'merch', 'https://images.unsplash.com/photo-1556306535-0f09a537f0a3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&h=500');