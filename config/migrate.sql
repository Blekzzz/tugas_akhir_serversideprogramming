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
    user_detail_id INT NOT NULL,
    FOREIGN KEY (user_detail_id) REFERENCES user_details(id) ON DELETE CASCADE
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

INSERT INTO user_details (name, address, phone_number, birth_date, birth_place, employee_id, department) VALUES 
('John Doe', '123 Main St', '1234567890', '1990-01-01', 'City A', 'EMP001', 'IT'),
('Jane Smith', '456 Elm St', '0987654321', '1985-05-15', 'City B', 'EMP002', 'HR'),
('Mike Johnson', '789 Oak St', '5555555555', '1992-07-20', 'City C', 'EMP003', 'Finance');

INSERT INTO users (email, password, role, user_detail_id) VALUES 
('johndoe@gmail.com', 'password123', 'employee', 1),
('janesmith@gmail.com', 'password123', 'employee', 2),
('mikejohnson@gmail.com', 'password123', 'employee', 3);