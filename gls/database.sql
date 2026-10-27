CREATE DATABASE IF NOT EXISTS graveyard_locator CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE graveyard_locator;
CREATE TABLE users(id INT AUTO_INCREMENT PRIMARY KEY,username VARCHAR(50) NOT NULL UNIQUE,full_name VARCHAR(100) NOT NULL,email VARCHAR(120) NOT NULL UNIQUE,password VARCHAR(255) NOT NULL,role ENUM('admin','user') NOT NULL DEFAULT 'user',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;
CREATE TABLE plots(id INT AUTO_INCREMENT PRIMARY KEY,section VARCHAR(20) NOT NULL,row_no INT NOT NULL,plot_no VARCHAR(20) NOT NULL,status ENUM('available','reserved','occupied') NOT NULL DEFAULT 'available',tier ENUM('Regular','Prime','Super Prime') NOT NULL DEFAULT 'Regular',map_x DECIMAL(5,2) NULL,map_y DECIMAL(5,2) NULL,map_polygon TEXT NULL,UNIQUE KEY uq_plot(section,row_no,plot_no)) ENGINE=InnoDB;
CREATE TABLE graves(id INT AUTO_INCREMENT PRIMARY KEY,first_name VARCHAR(80) NOT NULL,last_name VARCHAR(80) NOT NULL,birth_date DATE NULL,death_date DATE NULL,interment_date DATE NULL,plot_id INT NULL,notes TEXT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(plot_id) REFERENCES plots(id) ON DELETE SET NULL) ENGINE=InnoDB;
CREATE TABLE reservations(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NULL,plot_id INT NOT NULL,name VARCHAR(100) NOT NULL,email VARCHAR(120) NOT NULL,phone VARCHAR(40) NOT NULL,message TEXT NULL,status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,processed_at DATETIME NULL,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL,FOREIGN KEY(plot_id) REFERENCES plots(id) ON DELETE CASCADE) ENGINE=InnoDB;
CREATE TABLE search_history(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NOT NULL,query VARCHAR(255) NOT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE) ENGINE=InnoDB;
CREATE TABLE viewed_graves(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NOT NULL,grave_id INT NOT NULL,viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,FOREIGN KEY(grave_id) REFERENCES graves(id) ON DELETE CASCADE) ENGINE=InnoDB;
CREATE TABLE settings(k VARCHAR(50) PRIMARY KEY,v TEXT) ENGINE=InnoDB;
CREATE TABLE activities(id INT AUTO_INCREMENT PRIMARY KEY,message VARCHAR(255) NOT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;

INSERT INTO settings(k,v) VALUES('name','Holy Cross Gardens'),('email','Holycross@gmail.com'),('phone','09971949972/09060626703'),('address','Brgy. Lagundi, Catbalogan City, Samar'),('min_password','8');
INSERT INTO plots(section,row_no,plot_no,status,map_x,map_y,map_polygon) VALUES
('A',1,'1-B','occupied',66.40,76.70,'[[66.0,76.3],[66.8,76.3],[66.8,77.1],[66.0,77.1]]'),
('A',1,'1-C','reserved',67.00,76.60,'[[66.9,76.2],[67.7,76.2],[67.7,77.0],[66.9,77.0]]'),
('A',1,'1-D','available',67.40,76.60,'[[67.8,76.2],[68.6,76.2],[68.6,77.0],[67.8,77.0]]'),
('A',1,'1-E','available',67.80,76.60,'[[68.7,76.2],[69.5,76.2],[69.5,77.0],[68.7,77.0]]'),
('A',1,'101','available',NULL,NULL,NULL),('A',1,'102','occupied',NULL,NULL,NULL),('A',1,'103','available',NULL,NULL,NULL),('A',1,'104','available',NULL,NULL,NULL),('A',1,'105','available',NULL,NULL,NULL),
('B',2,'201','available',NULL,NULL,NULL),('B',2,'202','occupied',NULL,NULL,NULL),('B',2,'203','available',NULL,NULL,NULL),('B',2,'204','available',NULL,NULL,NULL),
('C',3,'301','available',NULL,NULL,NULL),('C',3,'302','occupied',NULL,NULL,NULL),('C',3,'303','available',NULL,NULL,NULL),('C',3,'304','available',NULL,NULL,NULL),('C',3,'305','occupied',NULL,NULL,NULL),('C',3,'306','available',NULL,NULL,NULL);
INSERT INTO graves(first_name,last_name,birth_date,death_date,interment_date,plot_id) VALUES
('Juan','Dela Cruz','1922-11-11','1929-11-12','1929-11-14',(SELECT id FROM plots WHERE plot_no='102')),
('Maria','Santos','1976-11-11','2020-02-12','2020-02-15',(SELECT id FROM plots WHERE plot_no='202')),
('Pedro','Reyes','1983-02-11','2000-03-12','2000-03-15',(SELECT id FROM plots WHERE plot_no='305')),
('Ana','Villanueva','1997-02-12','2001-03-12','2001-03-14',(SELECT id FROM plots WHERE plot_no='302'));
INSERT INTO activities(message) VALUES('System installed and sample data loaded');
-- Users are created by setup.php (run it once in the browser, then delete it).
