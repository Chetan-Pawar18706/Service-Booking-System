CREATE DATABASE service_booking;
USE service_booking;



CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) not null unique,
	email VARCHAR(100) not null unique,
    password VARCHAR(255) not null 
);

INSERT INTO users (username,email, password)
VALUES ('admin','admin@gmail.com', MD5('admin123'));

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100) NOT NULL,
    name VARCHAR(100),
    phone VARCHAR(20),
    status ENUM('available', 'booked') DEFAULT 'available'
);

-- Existing database migration:
-- ALTER TABLE services MODIFY category VARCHAR(100) NOT NULL;

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    service_id INT,
    customer_name VARCHAR(100),
    customer_phone VARCHAR(20),
    customer_address VARCHAR(255),
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id),
    FOREIGN KEY(service_id) REFERENCES services(id)
);
