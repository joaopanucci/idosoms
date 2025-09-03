-- db/schema.sql
CREATE DATABASE IF NOT EXISTS idosoms DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE idosoms;

-- Users
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cpf VARCHAR(14) NOT NULL UNIQUE,       -- apenas números no backend; formata na view
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('super_admin','admin_estadual','coord_municipal','profissional') NOT NULL DEFAULT 'profissional',
  municipality_code VARCHAR(10) NULL,
  unit_cnes VARCHAR(15) NULL,
  unit_name VARCHAR(160) NULL,
  last_login_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Patients
CREATE TABLE IF NOT EXISTS patients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cpf VARCHAR(14) NULL,
  name VARCHAR(120) NOT NULL,
  birthdate DATE NULL,
  gender ENUM('M','F','O') NULL,
  municipality_code VARCHAR(10) NULL,
  unit_cnes VARCHAR(15) NULL,
  unit_name VARCHAR(160) NULL,
  created_by INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id),
  INDEX(cpf),
  INDEX(name),
  INDEX(municipality_code),
  INDEX(created_at)
) ENGINE=InnoDB;

-- Evaluations generic table (answers em JSON para flexibilidade)
CREATE TABLE IF NOT EXISTS evaluations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  patient_id INT NOT NULL,
  type ENUM('IVCF20','IVSF10') NOT NULL,
  score INT NOT NULL DEFAULT 0,
  classification VARCHAR(50) NOT NULL,
  answers JSON NOT NULL,
  created_by INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (patient_id) REFERENCES patients(id),
  FOREIGN KEY (created_by) REFERENCES users(id),
  INDEX(type),
  INDEX(created_at)
) ENGINE=InnoDB;

-- Audit log simples
CREATE TABLE IF NOT EXISTS audit_logs (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  action VARCHAR(60) NOT NULL,
  entity VARCHAR(60) NOT NULL,
  entity_id INT NULL,
  ip VARCHAR(64) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- Notifications simples
CREATE TABLE IF NOT EXISTS notifications (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(140) NOT NULL,
  body TEXT NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- Seed: Super Admin (CPF 00000000000 / senha: admin123)
INSERT INTO users (cpf, name, email, password_hash, role)
VALUES ('00000000000', 'Super Admin', 'admin@example.com', 
        '$2y$10$9fTq8qZz8A9g7xgM4s9uHOhm6p9PUM2q5mFv4vPklgYk8qk3rE0N2', 'super_admin')
ON DUPLICATE KEY UPDATE email=VALUES(email);
-- password_hash acima é de "admin123" (bcrypt)
