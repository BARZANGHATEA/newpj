<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/auth_handler.php';

// Ensure user is logged in and is a doctor
require_role('doctor');

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'get_records':
            $patient_id = filter_var($_GET['patient_id'], FILTER_SANITIZE_NUMBER_INT);
            
            // Verify this patient is assigned to the current doctor
            $is_assigned = get_row(
                "SELECT 1 FROM doctor_patient WHERE doctor_id = ? AND patient_id = ?",
                [$_SESSION['user_id'], $patient_id]
            );
            
            if (!$is_assigned) {
                echo json_encode(['error' => 'دسترسی غیرمجاز']);
                exit;
            }
            
            // Get last 30 days of records
            $records = get_rows(
                "SELECT * FROM symptoms 
                 WHERE user_id = ? AND recorded_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                 ORDER BY recorded_at DESC",
                [$patient_id]
            );
            
            // Format data for chart and table
            $dates = [];
            $temperatures = [];
            $formatted_records = [];
            
            foreach ($records as $record) {
                // Data for chart
                $dates[] = date('m/d', strtotime($record['recorded_at']));
                $temperatures[] = $record['temperature'];
                
                // Data for table
                $formatted_records[] = [
                    'date' => date('Y/m/d H:i', strtotime($record['recorded_at'])),
                    'temperature' => $record['temperature'],
                    'blood_pressure' => $record['blood_pressure'],
                    'blood_sugar' => $record['blood_sugar'],
                    'energy_level' => $record['energy_level'],
                    'note' => $record['note']
                ];
            }
            
            echo json_encode([
                'dates' => array_reverse($dates),
                'temperatures' => array_reverse($temperatures),
                'records' => $formatted_records
            ]);
            break;
            
        default:
            echo json_encode(['error' => 'عملیات نامعتبر']);
    }
    exit;
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['action']) {
        case 'send_message':
            $patient_id = filter_var($_POST['patient_id'], FILTER_SANITIZE_NUMBER_INT);
            $content = strip_tags(filter_var($_POST['content'], FILTER_UNSAFE_RAW));
            
            // Verify this patient is assigned to the current doctor
            $is_assigned = get_row(
                "SELECT 1 FROM doctor_patient WHERE doctor_id = ? AND patient_id = ?",
                [$_SESSION['user_id'], $patient_id]
            );
            
            if (!$is_assigned) {
                $_SESSION['error'] = 'دسترسی غیرمجاز';
                header('Location: doctor.php');
                exit;
            }
            
            if (empty($content)) {
                $_SESSION['error'] = 'متن پیام نمی‌تواند خالی باشد';
                header('Location: doctor.php');
                exit;
            }
            
            // Send message
            $result = execute_query(
                "INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)",
                [$_SESSION['user_id'], $patient_id, $content]
            );
            
            if ($result) {
                $_SESSION['success'] = 'پیام با موفقیت ارسال شد';
            } else {
                $_SESSION['error'] = 'خطا در ارسال پیام';
            }
            break;
            
        case 'update_patient_status':
            $patient_id = filter_var($_POST['patient_id'], FILTER_SANITIZE_NUMBER_INT);
            $status = strip_tags(filter_var($_POST['status'], FILTER_UNSAFE_RAW));
            
            // Verify this patient is assigned to the current doctor
            $is_assigned = get_row(
                "SELECT 1 FROM doctor_patient WHERE doctor_id = ? AND patient_id = ?",
                [$_SESSION['user_id'], $patient_id]
            );
            
            if (!$is_assigned) {
                $_SESSION['error'] = 'دسترسی غیرمجاز';
                header('Location: doctor.php');
                exit;
            }
            
            // Update patient status
            $result = execute_query(
                "UPDATE doctor_patient SET status = ? WHERE doctor_id = ? AND patient_id = ?",
                [$status, $_SESSION['user_id'], $patient_id]
            );
            
            if ($result) {
                $_SESSION['success'] = 'وضعیت بیمار با موفقیت به‌روزرسانی شد';
            } else {
                $_SESSION['error'] = 'خطا در به‌روزرسانی وضعیت بیمار';
            }
            break;
            
        case 'add_note':
            $patient_id = filter_var($_POST['patient_id'], FILTER_SANITIZE_NUMBER_INT);
            $note = strip_tags(filter_var($_POST['note'], FILTER_UNSAFE_RAW));
            
            // Verify this patient is assigned to the current doctor
            $is_assigned = get_row(
                "SELECT 1 FROM doctor_patient WHERE doctor_id = ? AND patient_id = ?",
                [$_SESSION['user_id'], $patient_id]
            );
            
            if (!$is_assigned) {
                $_SESSION['error'] = 'دسترسی غیرمجاز';
                header('Location: doctor.php');
                exit;
            }
            
            // Add doctor's note
            $result = execute_query(
                "INSERT INTO doctor_notes (doctor_id, patient_id, note) VALUES (?, ?, ?)",
                [$_SESSION['user_id'], $patient_id, $note]
            );
            
            if ($result) {
                $_SESSION['success'] = 'یادداشت با موفقیت ثبت شد';
            } else {
                $_SESSION['error'] = 'خطا در ثبت یادداشت';
            }
            break;

        case 'add_prescription':
            $patient_id = filter_var($_POST['patient_id'], FILTER_SANITIZE_NUMBER_INT);
            $content = strip_tags(filter_var($_POST['prescription'], FILTER_UNSAFE_RAW));

            $is_assigned = get_row(
                "SELECT 1 FROM doctor_patient WHERE doctor_id = ? AND patient_id = ?",
                [$_SESSION['user_id'], $patient_id]
            );

            if (!$is_assigned) {
                $_SESSION['error'] = 'دسترسی غیرمجاز';
                header('Location: doctor.php');
                exit;
            }

            $result = execute_query(
                "INSERT INTO prescriptions (doctor_id, patient_id, content) VALUES (?, ?, ?)",
                [$_SESSION['user_id'], $patient_id, $content]
            );

            if ($result) {
                $_SESSION['success'] = 'نسخه با موفقیت ثبت شد';
            } else {
                $_SESSION['error'] = 'خطا در ثبت نسخه';
            }
            break;
    }
    
    header('Location: doctor.php');
    exit;
}

// If no valid action specified, redirect back to dashboard
header('Location: doctor.php');
exit;
?>
