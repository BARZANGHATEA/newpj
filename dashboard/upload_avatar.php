<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/auth_handler.php';

require_role('patient');

$upload_dir = '../assets/images/';
$allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        if (in_array($file['type'], $allowed_types)) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'avatar_' . $_SESSION['user_id'] . '.' . $ext;
            $destination = $upload_dir . $filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $avatar_path = $destination;
                // Save relative path to DB
                execute_query("UPDATE users SET avatar = ? WHERE id = ?", [$avatar_path, $_SESSION['user_id']]);
                header("Location: patient.php?upload=success");
                exit;
            } else {
                header("Location: patient.php?upload=fail");
                exit;
            }
        } else {
            header("Location: patient.php?upload=invalid");
            exit;
        }
    } else {
        header("Location: patient.php?upload=error");
        exit;
    }
}
?>
