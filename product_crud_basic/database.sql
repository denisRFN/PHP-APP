CREATE DATABASE IF NOT EXISTS product_crud_basic;
USE product_crud_basic;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    availability_date DATE,
    in_stock TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO products (name, description, price, image, availability_date, in_stock) VALUES
('Notebook', 'Simple lined notebook.', 12.50, NULL, '2026-11-10', 1),
('Pen', 'Blue ink pen.', 3.25, NULL, '2026-11-12', 1),
('Backpack', 'Basic school backpack.', 55.00, NULL, '2026-11-20', 0);
