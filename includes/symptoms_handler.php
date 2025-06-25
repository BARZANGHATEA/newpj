<?php
require_once 'database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$temperature = $_POST['temperature'] ?? null;
$blood_pressure = $_POST['blood_pressure'] ?? null;
$blood_sugar = $_POST['blood_sugar'] ?? null;
$energy_level = $_POST['energy_level'] ?? null;
$note = $_POST['note'] ?? null;

if (!$temperature || !$blood_pressure || !$blood_sugar || !$energy_level) {
    header("Location: ../dashboard/patient.php?error=missing_fields");
    exit;
}

// Insert into database using secure PDO
execute_query("INSERT INTO symptoms (user_id, temperature, blood_pressure, blood_sugar, energy_level, note) VALUES (?, ?, ?, ?, ?, ?)", [
    $user_id, $temperature, $blood_pressure, $blood_sugar, $energy_level, $note
]);

header("Location: ../dashboard/patient.php?success=1");
exit;
