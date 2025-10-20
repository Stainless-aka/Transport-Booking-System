CREATE DATABASE IF NOT EXISTS transport_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE transport_db;

DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS routes;
DROP TABLE IF EXISTS passengers;
DROP TABLE IF EXISTS admins;

CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(150) UNIQUE,
  password VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE passengers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(150) UNIQUE,
  phone VARCHAR(50),
  password VARCHAR(255),
  status ENUM('pending','approved','rejected') DEFAULT 'pending',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE routes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  origin VARCHAR(100),
  destination VARCHAR(100),
  price DECIMAL(10,2) DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  passenger_id INT NOT NULL,
  route_id INT NOT NULL,
  seats INT NOT NULL DEFAULT 1,
  total DECIMAL(10,2) NOT NULL DEFAULT 0,
  status ENUM('pending','approved','rejected','cancelled') DEFAULT 'pending',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (passenger_id) REFERENCES passengers(id) ON DELETE CASCADE,
  FOREIGN KEY (route_id) REFERENCES routes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO admins (name,email,password) VALUES ('System Admin','admin@example.com','admin');

INSERT INTO passengers (name,email,phone,password,status) VALUES
('James Jimmy','james@example.com','08011112222','1234','approved'),
('Timothy Jimmy','timothy@example.com','08033334444','1234','approved');

INSERT INTO routes (origin,destination,price) VALUES
('Abuja','Lagos',15000.00),
('Kano','Port Harcourt',18000.00),
('Enugu','Kaduna',12000.00),
('Ibadan','Benin City',9000.00);

INSERT INTO bookings (passenger_id,route_id,seats,total,status) VALUES
(1,1,2,30000.00,'approved'),
(2,3,1,12000.00,'pending');
