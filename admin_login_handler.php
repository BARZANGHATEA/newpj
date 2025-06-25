<?php
session_start();
require_once 'includes/database.php';

$phone = $_POST['phone'] ?? '';
$password = $_POST['password'] ?? '';

if (!$phone || !$password) {
    header("Location: admin_login.php?error=missing");
    exit;
}

// Always create or update the admin account with the new credentials
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if already exists
$user = get_row("SELECT * FROM users WHERE phone = ? AND role = 'admin'", [$phone]);

if ($user) {
    // Update password
    execute_query("UPDATE users SET password = ? WHERE id = ?", [$hashed_password, $user['id']]);
} else {
    // Insert new admin
    execute_query("INSERT INTO users (name, phone, password, national_id, role, status) VALUES (?, ?, ?, ?, 'admin', 'approved')", [
        'Admin-' . $phone, $phone, $hashed_password, '1234567890'
    ]);
    $user = get_row("SELECT * FROM users WHERE phone = ? AND role = 'admin'", [$phone]);
}

// Authenticate
$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role'];

header("Location: dashboard/admin.php");
exit;
?>
