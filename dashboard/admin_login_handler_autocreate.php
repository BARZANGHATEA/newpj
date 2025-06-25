<?php
session_start();
require_once 'includes/database.php';

$phone = $_POST['phone'] ?? '';
$password = $_POST['password'] ?? '';

if (!$phone || !$password) {
    header("Location: admin_login.php?error=missing");
    exit;
}

// Check if admin already exists
$user = get_row("SELECT * FROM users WHERE phone = ? AND role = 'admin'", [$phone]);

if (!$user) {
    // Auto-register admin
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    execute_query(
        "INSERT INTO users (name, phone, password, national_id, role, status)
         VALUES (?, ?, ?, ?, 'admin', 'approved')",
        ['Admin-' . $phone, $phone, $hashed_password, '1234567890']
    );
    // Fetch new user
    $user = get_row("SELECT * FROM users WHERE phone = ? AND role = 'admin'", [$phone]);
}

// Try login
if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    header("Location: dashboard/admin.php");
    exit;
} else {
    header("Location: admin_login.php?error=invalid");
    exit;
}
?>
