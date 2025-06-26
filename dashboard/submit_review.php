<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/auth_handler.php';

require_role('patient');

$doctor_id = filter_var($_POST['doctor_id'] ?? null, FILTER_SANITIZE_NUMBER_INT);
$rating = filter_var($_POST['rating'] ?? null, FILTER_SANITIZE_NUMBER_INT);
$comment = strip_tags(filter_var($_POST['comment'] ?? '', FILTER_UNSAFE_RAW));

if (!$doctor_id || !$rating || $rating < 1 || $rating > 5) {
    header('Location: patient.php');
    exit;
}

$patient_id = $_SESSION['user_id'];

execute_query(
    "INSERT INTO doctor_reviews (doctor_id, patient_id, rating, comment) VALUES (?, ?, ?, ?)",
    [$doctor_id, $patient_id, $rating, $comment]
);

$_SESSION['success'] = 'نظر شما پس از تایید ادمین نمایش داده خواهد شد';
header('Location: patient.php');
exit;
?>
