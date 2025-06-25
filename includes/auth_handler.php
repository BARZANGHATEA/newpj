<?php
// Authentication and authorization helper
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/database.php';

/**
 * Redirect to login page if the current user does not have the required role.
 */
function require_role(string $role): void
{
    if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== $role) {
        header('Location: ../login.php');
        exit;
    }
}

/**
 * Handle user login. Assumes POST parameters 'phone' and 'password'.
 */
function handle_login(): void
{
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$phone || !$password) {
        header('Location: ../login.php?error=missing');
        exit;
    }

    $user = get_row('SELECT * FROM users WHERE phone = ?', [$phone]);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        header('Location: ../dashboard/' . $user['role'] . '.php');
        exit;
    }

    header('Location: ../login.php?error=invalid');
    exit;
}

/**
 * Handle user registration.
 */
function handle_register(): void
{
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $national_id = $_POST['national_id'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';
    $medical_code = $_POST['medical_system_code'] ?? null;

    if (!$name || !$phone || !$national_id || !$password || !$role) {
        header('Location: ../login.php?error=missing');
        exit;
    }

    if (get_row('SELECT id FROM users WHERE phone = ?', [$phone])) {
        header('Location: ../login.php?error=phone_exists');
        exit;
    }

    $status = $role === 'doctor' ? 'pending' : 'approved';
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    execute_query(
        'INSERT INTO users (name, phone, password, national_id, medical_system_code, role, status) VALUES (?, ?, ?, ?, ?, ?, ?)',
        [$name, $phone, $hashed, $national_id, $medical_code, $role, $status]
    );

    $user = get_row('SELECT * FROM users WHERE phone = ?', [$phone]);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['name'] = $user['name'];

    header('Location: ../dashboard/' . $user['role'] . '.php');
    exit;
}

/**
 * Log the current user out.
 */
function handle_logout(): void
{
    session_unset();
    session_destroy();
    header('Location: ../login.php');
    exit;
}

// If the file is accessed directly via a POST/GET request, handle the action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'login') {
        handle_login();
    } elseif ($_POST['action'] === 'register') {
        handle_register();
    }
} elseif (isset($_GET['action']) && $_GET['action'] === 'logout') {
    handle_logout();
}
