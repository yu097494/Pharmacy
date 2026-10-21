CREATE DATABASE IF NOT EXISTS pharma;
USE pharma;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price INT NOT NULL,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO items (name, price, image) VALUES 
('Paracetamol 500mg', 1500, 'https://t3.ftcdn.net/jpg/01/82/83/46/360_F_182834600_khgA0D4quXD3cRUsrbGCF1GXtrTBffNF.jpg'),
('Decolgen No-Drowse', 2000, 'https://assets.unilab.com.ph/uploads/Unilab/Products/Decolgen/DecolgenNoDrowse.jpg'),
('Vitamin C 1000mg', 3500, 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae'),
('Amoxicillin Caps', 2500, 'https://images.unsplash.com/photo-1471864190281-a93a3070b6de');
