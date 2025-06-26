<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/auth_handler.php';

require_role('patient');

$doctor_id = filter_var($_POST['doctor_id'] ?? null, FILTER_SANITIZE_NUMBER_INT);
if (!$doctor_id) {
    header('Location: patient.php');
    exit;
}

$patient_id = $_SESSION['user_id'];

// Check if relationship already exists
$existing = get_row(
    "SELECT id FROM doctor_patient WHERE patient_id = ?",
    [$patient_id]
);

if ($existing) {
    // Update to new doctor
    execute_query(
        "UPDATE doctor_patient SET doctor_id = ?, assigned_at = NOW() WHERE id = ?",
        [$doctor_id, $existing['id']]
    );
} else {
    execute_query(
        "INSERT INTO doctor_patient (doctor_id, patient_id) VALUES (?, ?)",
        [$doctor_id, $patient_id]
    );
}

$_SESSION['success'] = 'پزشک با موفقیت انتخاب شد';
header('Location: patient.php');
exit;
?>
