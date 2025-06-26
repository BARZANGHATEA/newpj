<?php
require_once 'database.php';

// Establish a single database connection for this script
$db = get_db_connection();

try {
    // Create users table with updated schema
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name TEXT NOT NULL,
        phone VARCHAR(11) UNIQUE NOT NULL,
        password TEXT NOT NULL,
        national_id VARCHAR(10) NOT NULL,
        medical_system_code VARCHAR(20) NULL,
        role TEXT NOT NULL CHECK(role IN ('admin', 'doctor', 'patient')),
        status VARCHAR(10) NOT NULL DEFAULT 'pending' CHECK(status IN ('pending', 'approved', 'rejected')),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Create symptoms table
    $db->exec("CREATE TABLE IF NOT EXISTS symptoms (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INTEGER NOT NULL,
        temperature FLOAT,
        blood_pressure VARCHAR(50),
        blood_sugar VARCHAR(50),
        energy_level VARCHAR(50),
        note TEXT,
        recorded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Create messages table
    $db->exec("CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sender_id INTEGER NOT NULL,
        receiver_id INTEGER NOT NULL,
        content TEXT NOT NULL,
        sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        read_at DATETIME,
        FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Create articles table
    $db->exec("CREATE TABLE IF NOT EXISTS articles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title TEXT NOT NULL,
        content TEXT NOT NULL,
        image_url TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Create doctor-patient relationship table
    $db->exec("CREATE TABLE IF NOT EXISTS doctor_patient (
        id INT AUTO_INCREMENT PRIMARY KEY,
        doctor_id INTEGER NOT NULL,
        patient_id INTEGER NOT NULL,
        assigned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE(doctor_id, patient_id)
    )");

    // Create doctor notes table
    $db->exec("CREATE TABLE IF NOT EXISTS doctor_notes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        doctor_id INTEGER NOT NULL,
        patient_id INTEGER NOT NULL,
        note TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Additional tables
    $db->exec("CREATE TABLE IF NOT EXISTS doctor_profiles (
        doctor_id INTEGER PRIMARY KEY,
        first_name TEXT,
        last_name TEXT,
        english_name TEXT,
        email TEXT,
        gender TEXT,
        specialty TEXT,
        FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS prescriptions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        doctor_id INTEGER NOT NULL,
        patient_id INTEGER NOT NULL,
        content TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Table for doctor reviews submitted by patients
    $db->exec("CREATE TABLE IF NOT EXISTS doctor_reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        doctor_id INTEGER NOT NULL,
        patient_id INTEGER NOT NULL,
        rating INTEGER NOT NULL CHECK(rating BETWEEN 1 AND 5),
        comment TEXT,
        status VARCHAR(10) NOT NULL DEFAULT 'pending' CHECK(status IN ('pending','approved')),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Insert admin user if not exists
    $admin = get_row("SELECT * FROM users WHERE role = 'admin'");
    if (!$admin) {
        execute_query(
            "INSERT INTO users (name, phone, password, national_id, role, status) VALUES (?, ?, ?, ?, 'admin', 'approved')",
            ['Admin', '09123456789', password_hash('admin123456', PASSWORD_DEFAULT), '1234567890']
        );
    }

    echo "Database initialized successfully!";
} catch (PDOException $e) {
    die("Database initialization failed: " . $e->getMessage());
}
?>
