CREATE DATABASE fixit_db;
USE fixit_db;

-- 1. Tabel Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('employee', 'technician') NOT NULL,
    fullname VARCHAR(255) NOT NULL,
    address TEXT,
    phone_number VARCHAR(20),
    birth_date DATE,
    birth_place VARCHAR(100),
    department VARCHAR(100)
);

-- 2. Tabel Facilities
CREATE TABLE facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    facilites_name VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL
);

-- 3. Tabel Tickets
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reporter_id INT NOT NULL,
    facilities_id INT NOT NULL,
    technician_id INT,
    issue_description TEXT NOT NULL,
    status ENUM('pending', 'processing', 'solved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reporter_id) REFERENCES users(id),
    FOREIGN KEY (facilities_id) REFERENCES facilities(id),
    FOREIGN KEY (technician_id) REFERENCES users(id)
);