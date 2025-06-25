<?php
require_once __DIR__ . '/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $message === '') {
        header('Location: ../contact.php?error=missing');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ../contact.php?error=invalid_email');
        exit;
    }

    try {
        $pdo = get_db_connection();
        $pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $message]);
        // You could also send an email here using the mail() function
        header('Location: ../contact.php?success=1');
        exit;
    } catch (Exception $e) {
        header('Location: ../contact.php?error=server');
        exit;
    }
}

header('Location: ../contact.php');
