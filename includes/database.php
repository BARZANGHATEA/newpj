<?php
function get_db_connection() {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=health_project;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

function execute_query($query, $params = []) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare($query);
    return $stmt->execute($params);
}

function get_row($query, $params = []) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_rows($query, $params = []) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function ensure_extra_tables() {
    $pdo = get_db_connection();

    $pdo->exec("CREATE TABLE IF NOT EXISTS doctor_patient (
        id INT AUTO_INCREMENT PRIMARY KEY,
        doctor_id INT NOT NULL,
        patient_id INT NOT NULL,
        assigned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (doctor_id) REFERENCES users(id),
        FOREIGN KEY (patient_id) REFERENCES users(id),
        UNIQUE (doctor_id, patient_id)
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS doctor_reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        doctor_id INT NOT NULL,
        patient_id INT NOT NULL,
        rating INT NOT NULL,
        comment TEXT,
        status ENUM('pending','approved') DEFAULT 'pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (doctor_id) REFERENCES users(id),
        FOREIGN KEY (patient_id) REFERENCES users(id)
    )");
}

ensure_extra_tables();
