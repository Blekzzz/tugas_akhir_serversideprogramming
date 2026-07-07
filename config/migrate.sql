CREATE DATABASE IF NOT EXISTS fixit_db;
USE fixit_db;

-- 1. facilities
CREATE TABLE facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    facilites_name VARCHAR(100) NOT NULL,
    location VARCHAR(100) NOT NULL
);

-- 2. user_details
CREATE TABLE user_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    address TEXT,
    phone_number VARCHAR(15),
    birth_date DATE,
    birth_place VARCHAR(50),
    employee_id VARCHAR(20) UNIQUE NOT NULL,
    department VARCHAR(50)
);

-- 3. users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('employee', 'technician') NOT NULL DEFAULT 'employee',
    employee_id INT NOT NULL,
    FOREIGN KEY (employee_id) REFERENCES user_details(id) ON DELETE CASCADE
);


-- 4. tickets
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reporter_id INT NOT NULL,
    facilities_id INT NOT NULL,
    technician_id INT NULL,
    issue_description TEXT NOT NULL,
    status ENUM('pending', 'processing', 'solved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (facilities_id) REFERENCES facilities(id) ON DELETE CASCADE,
    FOREIGN KEY (technician_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 5. insert dummy data
INSERT INTO facilities (facilites_name, location) VALUES 
('AC Kantor 1', 'Gedung Utama'),
('Proyektor Lab Komputer', 'Gedung A Lantai 2'),
('Printer Epson L6460', 'Gedung Utama'),
('WiFi Router', 'Gedung Utama'),
('AC Kantor 2', 'Gedung Utama');