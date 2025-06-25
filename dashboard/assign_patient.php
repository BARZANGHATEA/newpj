<?php
require_once '../includes/database.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$doctor_id = $_POST['doctor_id'] ?? null;
$patient_id = $_POST['patient_id'] ?? null;

if ($doctor_id && $patient_id) {
    execute_query("INSERT INTO doctor_patient (doctor_id, patient_id) VALUES (?, ?)", [
        $doctor_id, $patient_id
    ]);
    header("Location: admin.php?assign=success");
    exit;
} else {
    header("Location: admin.php?assign=error");
    exit;
}
?>
