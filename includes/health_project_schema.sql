
CREATE DATABASE IF NOT EXISTS health_project;
USE health_project;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(11) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    national_id VARCHAR(10) NOT NULL,
    medical_system_code VARCHAR(20),
    role ENUM('patient', 'doctor', 'admin') NOT NULL,
    status ENUM('pending', 'approved') DEFAULT 'approved',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS symptoms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    temperature FLOAT,
    blood_pressure VARCHAR(50),
    blood_sugar VARCHAR(50),
    energy_level VARCHAR(50),
    recorded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image_url VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS doctor_profiles (
    doctor_id INT PRIMARY KEY,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    english_name VARCHAR(100),
    email VARCHAR(255),
    gender VARCHAR(20),
    specialty VARCHAR(100),
    FOREIGN KEY (doctor_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    patient_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES users(id),
    FOREIGN KEY (patient_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS doctor_patient (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    patient_id INT NOT NULL,
    assigned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES users(id),
    FOREIGN KEY (patient_id) REFERENCES users(id),
    UNIQUE (doctor_id, patient_id)
);

CREATE TABLE IF NOT EXISTS doctor_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    patient_id INT NOT NULL,
    rating INT NOT NULL,
    comment TEXT,
    status ENUM('pending','approved') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES users(id),
    FOREIGN KEY (patient_id) REFERENCES users(id)
);
