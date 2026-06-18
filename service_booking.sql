CREATE DATABASE service_booking;
USE service_booking;



CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user', 'provider') DEFAULT 'user',
    phone VARCHAR(20),
    address VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (username, email, password, role, phone, address)
VALUES 
('admin', 'admin@gmail.com', MD5('admin123'), 'admin', '9876543210', 'Mumbai, Maharashtra'),
('amit_sharma', 'amit@gmail.com', MD5('amit123'), 'provider', '9123456789', '123, MG Road, Bangalore, Karnataka'),
('priya_patel', 'priya@gmail.com', MD5('priya123'), 'provider', '8987654321', '45, Court Road, Pune, Maharashtra'),
('rajesh_plumber', 'rajesh@gmail.com', MD5('rajesh123'), 'provider', '9876543211', 'Dwarka, New Delhi'),
('neha_electrician', 'neha@gmail.com', MD5('neha123'), 'provider', '9765432109', 'Sector 5, Gurgaon, Haryana'),
('vikram_carpenter', 'vikram@gmail.com', MD5('vikram123'), 'provider', '8765432198', 'Koramangala, Bangalore, Karnataka'),
('sneha_cleaning', 'sneha@gmail.com', MD5('sneha123'), 'provider', '9654321087', 'Salt Lake City, Kolkata, West Bengal'),
('rohan_bhatia', 'rohan@bhatia.com', MD5('rohan123'), 'user', '9112345678', 'Sector 7, Mumbai, Maharashtra'),
('aisha_khan', 'aisha.khan@gmail.com', MD5('aisha123'), 'user', '9887654321', 'Anna Nagar, Chennai, Tamil Nadu'),
('rahul_verma', 'rahul.verma@gmail.com', MD5('rahul123'), 'user', '9876543098', 'Jayanagar, Bangalore, Karnataka'),
('prerna_singh', 'prerna.singh@gmail.com', MD5('prerna123'), 'user', '8899776655', 'Sector 12, Noida, Uttar Pradesh'),
('karan_gupta', 'karan.gupta@gmail.com', MD5('karan123'), 'user', '9765432087', 'Andheri East, Mumbai, Maharashtra'),
('divya_mehra', 'divya.mehra@gmail.com', MD5('divya123'), 'user', '9654321876', 'Vile Parle, Mumbai, Maharashtra'),
('arjun_nair', 'arjun.nair@gmail.com', MD5('arjun123'), 'user', '9123456098', 'Fort Kochi, Kerala'),
('sapna_iyer', 'sapna.iyer@gmail.com', MD5('sapna123'), 'user', '9876543212', 'Bandra, Mumbai, Maharashtra');

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL
);

INSERT INTO categories (name, description)
VALUES 
('Plumbing', 'Pipe installation, repairs, and water system maintenance'),
('Electrical', 'Wiring, repairs, and electrical installations'),
('Carpentry', 'Furniture, doors, windows, and woodwork'),
('Cleaning', 'Home and office deep cleaning services'),
('Painting', 'Interior and exterior wall painting'),
('AC/Refrigeration', 'AC installation, repair, and maintenance'),
('Home Appliances', 'Washing machine, microwave, and appliance repairs'),
('Gardening', 'Landscaping, plant care, and garden maintenance'),
('Pest Control', 'Termite and pest elimination services'),
('CCTV/Security', 'Security camera installation and monitoring');

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100) NOT NULL,
    provider_id INT,
    name VARCHAR(100),
    phone VARCHAR(20),
    price DECIMAL(10, 2) DEFAULT 0.00,
    status ENUM('available', 'booked') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(provider_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Existing database migration:
-- ALTER TABLE services MODIFY category VARCHAR(100) NOT NULL;

INSERT INTO services (category, provider_id, name, phone, price, status)
VALUES 
('Plumbing', 4, 'Rajesh Plumbing Solutions', '9876543211', 500.00, 'available'),
('Electrical', 5, 'Neha Electrical Works', '9765432109', 600.00, 'available'),
('Carpentry', 6, 'Vikram Woodcraft', '8765432198', 800.00, 'available'),
('Cleaning', 7, 'Sneha Home Cleaning', '9654321087', 1200.00, 'available'),
('Plumbing', 2, 'Amit Plumbing Experts', '9123456789', 550.00, 'available'),
('AC/Refrigeration', 3, 'Priya AC Services', '8987654321', 1000.00, 'available'),
('Painting', 2, 'Amit Painting Studio', '9123456789', 700.00, 'available'),
('Home Appliances', 3, 'Priya Appliance Repair', '8987654321', 650.00, 'available'),
('Pest Control', 4, 'Rajesh Pest Control', '9876543211', 1500.00, 'available'),
('Gardening', 5, 'Neha Garden Experts', '9765432109', 450.00, 'available'),
('Electrical', 2, 'Amit Electrical Services', '9123456789', 580.00, 'available'),
('CCTV/Security', 6, 'Vikram Security Systems', '8765432198', 2500.00, 'available'),
('Cleaning', 3, 'Priya Office Cleaning', '8987654321', 1500.00, 'available'),
('Plumbing', 7, 'Sneha Bathroom Fittings', '9654321087', 450.00, 'available'),
('Painting', 5, 'Neha Interior Painting', '9765432109', 750.00, 'available');

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    service_id INT,
    provider_id INT,
    customer_name VARCHAR(100),
    customer_phone VARCHAR(20),
    customer_address VARCHAR(255),
    service_price DECIMAL(10, 2) DEFAULT 0.00,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completion_date DATETIME,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(service_id) REFERENCES services(id) ON DELETE SET NULL,
    FOREIGN KEY(provider_id) REFERENCES users(id) ON DELETE SET NULL
);

INSERT INTO bookings (user_id, service_id, provider_id, customer_name, customer_phone, customer_address, service_price, status, booking_date, completion_date)
VALUES 
(8, 1, 4, 'Rohan Bhatia', '9112345678', 'A-101, Shivaji Park, Mumbai, Maharashtra', 500.00, 'completed', '2026-06-10 08:30:00', '2026-06-10 10:30:00'),
(9, 2, 5, 'Aisha Khan', '9887654321', '45, Spenser Road, Teynampet, Chennai, Tamil Nadu', 600.00, 'completed', '2026-06-11 09:00:00', '2026-06-11 11:00:00'),
(10, 3, 6, 'Rahul Verma', '9876543098', '22/A, Ulsoor Road, Bangalore, Karnataka', 800.00, 'confirmed', '2026-06-15 10:00:00', NULL),
(11, 4, 7, 'Prerna Singh', '8899776655', 'D-105, Sector 12, Noida, Uttar Pradesh', 1200.00, 'pending', '2026-06-17 14:30:00', NULL),
(12, 5, 2, 'Karan Gupta', '9765432087', 'B-204, Bhagia Park, Andheri East, Mumbai, Maharashtra', 550.00, 'completed', '2026-06-05 07:30:00', '2026-06-05 09:30:00'),
(13, 6, 3, 'Divya Mehra', '9654321876', 'C-502, Sai Enclave, Vile Parle, Mumbai, Maharashtra', 1000.00, 'confirmed', '2026-06-16 11:00:00', NULL),
(14, 7, 2, 'Arjun Nair', '9123456098', 'House 12, Jew Street, Fort Kochi, Kerala', 700.00, 'pending', '2026-06-17 15:00:00', NULL),
(15, 8, 3, 'Sapna Iyer', '9876543212', 'Flat 402, Hiranandani Gardens, Bandra, Mumbai, Maharashtra', 650.00, 'completed', '2026-06-08 13:00:00', '2026-06-08 15:00:00'),
(8, 9, 4, 'Rohan Bhatia', '9112345678', 'A-101, Shivaji Park, Mumbai, Maharashtra', 1500.00, 'completed', '2026-06-12 09:00:00', '2026-06-12 14:00:00'),
(9, 10, 5, 'Aisha Khan', '9887654321', '45, Spenser Road, Teynampet, Chennai, Tamil Nadu', 450.00, 'confirmed', '2026-06-14 16:00:00', NULL),
(10, 11, 2, 'Rahul Verma', '9876543098', '22/A, Ulsoor Road, Bangalore, Karnataka', 580.00, 'pending', '2026-06-17 13:30:00', NULL),
(11, 12, 6, 'Prerna Singh', '8899776655', 'D-105, Sector 12, Noida, Uttar Pradesh', 2500.00, 'completed', '2026-06-03 10:00:00', '2026-06-04 16:00:00'),
(12, 13, 3, 'Karan Gupta', '9765432087', 'B-204, Bhagia Park, Andheri East, Mumbai, Maharashtra', 1500.00, 'confirmed', '2026-06-15 09:00:00', NULL),
(13, 14, 7, 'Divya Mehra', '9654321876', 'C-502, Sai Enclave, Vile Parle, Mumbai, Maharashtra', 450.00, 'completed', '2026-06-09 11:00:00', '2026-06-09 13:00:00'),
(14, 15, 5, 'Arjun Nair', '9123456098', 'House 12, Jew Street, Fort Kochi, Kerala', 750.00, 'pending', '2026-06-16 10:00:00', NULL);

CREATE TABLE invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL UNIQUE,
    user_id INT,
    provider_id INT,
    amount DECIMAL(10, 2),
    invoice_number VARCHAR(50) UNIQUE,
    issued_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    due_date DATE,
    status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY(booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY(provider_id) REFERENCES users(id) ON DELETE SET NULL
);

INSERT INTO invoices (booking_id, user_id, provider_id, amount, invoice_number, issued_date, due_date, status)
VALUES 
(1, 8, 4, 500.00, 'INV-20260610-00001', '2026-06-10 10:31:00', '2026-06-17', 'paid'),
(2, 9, 5, 600.00, 'INV-20260611-00002', '2026-06-11 11:01:00', '2026-06-18', 'paid'),
(3, 10, 6, 800.00, 'INV-20260615-00003', '2026-06-15 10:01:00', '2026-06-22', 'pending'),
(4, 11, 7, 1200.00, 'INV-20260617-00004', '2026-06-17 14:31:00', '2026-06-24', 'pending'),
(5, 12, 2, 550.00, 'INV-20260605-00005', '2026-06-05 09:31:00', '2026-06-12', 'paid'),
(6, 13, 3, 1000.00, 'INV-20260616-00006', '2026-06-16 11:01:00', '2026-06-23', 'pending'),
(7, 14, 2, 700.00, 'INV-20260617-00007', '2026-06-17 15:01:00', '2026-06-24', 'pending'),
(8, 15, 3, 650.00, 'INV-20260608-00008', '2026-06-08 15:01:00', '2026-06-15', 'paid'),
(9, 8, 4, 1500.00, 'INV-20260612-00009', '2026-06-12 14:01:00', '2026-06-19', 'paid'),
(10, 9, 5, 450.00, 'INV-20260614-00010', '2026-06-14 16:01:00', '2026-06-21', 'pending'),
(11, 10, 2, 580.00, 'INV-20260617-00011', '2026-06-17 13:31:00', '2026-06-24', 'pending'),
(12, 11, 6, 2500.00, 'INV-20260604-00012', '2026-06-04 16:01:00', '2026-06-11', 'paid'),
(13, 12, 3, 1500.00, 'INV-20260615-00013', '2026-06-15 09:01:00', '2026-06-22', 'pending'),
(14, 13, 7, 450.00, 'INV-20260609-00014', '2026-06-09 13:01:00', '2026-06-16', 'paid'),
(15, 14, 5, 750.00, 'INV-20260616-00015', '2026-06-16 10:01:00', '2026-06-23', 'pending');
