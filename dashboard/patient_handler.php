<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/auth_handler.php';

// Ensure user is logged in and is a patient
require_role('patient');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $user_id = $_SESSION['user_id'];
    $temperature = filter_var($_POST['temperature'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $blood_pressure = filter_var($_POST['blood_pressure'], FILTER_SANITIZE_STRING);
    $blood_sugar = filter_var($_POST['blood_sugar'], FILTER_SANITIZE_STRING);
    $energy_level = filter_var($_POST['energy_level'], FILTER_SANITIZE_STRING);
    $note = filter_var($_POST['note'], FILTER_SANITIZE_STRING);

    // Validate temperature
    if ($temperature < 35 || $temperature > 42) {
        $_SESSION['error'] = 'لطفاً دمای بدن معتبر وارد کنید (بین ۳۵ تا ۴۲ درجه)';
        header('Location: patient.php');
        exit();
    }

    // Validate blood pressure format (e.g., 120/80)
    if (!preg_match('/^\d{2,3}\/\d{2,3}$/', $blood_pressure)) {
        $_SESSION['error'] = 'لطفاً فشار خون را به فرمت صحیح وارد کنید (مثال: ۱۲۰/۸۰)';
        header('Location: patient.php');
        exit();
    }

    // Validate energy level
    $valid_energy_levels = ['عالی', 'خوب', 'متوسط', 'ضعیف'];
    if (!in_array($energy_level, $valid_energy_levels)) {
        $_SESSION['error'] = 'لطفاً سطح انرژی معتبر را انتخاب کنید';
        header('Location: patient.php');
        exit();
    }

    try {
        // Insert the new symptoms record
        $sql = "INSERT INTO symptoms (user_id, temperature, blood_pressure, blood_sugar, energy_level, note) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = execute_query(
            $sql, 
            [$user_id, $temperature, $blood_pressure, $blood_sugar, $energy_level, $note]
        );

        if ($stmt) {
            // Check if any values are concerning and need doctor notification
            $is_concerning = false;
            $concerns = [];

            if ($temperature >= 38) {
                $is_concerning = true;
                $concerns[] = 'دمای بدن بالا';
            }

            list($systolic, $diastolic) = explode('/', $blood_pressure);
            if ($systolic >= 140 || $diastolic >= 90) {
                $is_concerning = true;
                $concerns[] = 'فشار خون بالا';
            }

            // If there are concerning values, notify the assigned doctor
            if ($is_concerning) {
                // Get assigned doctor (assuming there's a doctor_patient relationship table)
                $doctor_id = get_row(
                    "SELECT doctor_id FROM doctor_patient WHERE patient_id = ?",
                    [$user_id]
                );

                if ($doctor_id) {
                    $message = "هشدار: بیمار " . $_SESSION['name'] . " علائم نگران‌کننده دارد: " . 
                             implode('، ', $concerns);

                    execute_query(
                        "INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)",
                        [$user_id, $doctor_id['doctor_id'], $message]
                    );
                }
            }

            $_SESSION['success'] = 'اطلاعات سلامت شما با موفقیت ثبت شد';
        } else {
            throw new Exception("خطا در ثبت اطلاعات");
        }

    } catch (Exception $e) {
        error_log($e->getMessage());
        $_SESSION['error'] = 'خطا در ثبت اطلاعات. لطفاً دوباره تلاش کنید.';
    }

    header('Location: patient.php');
    exit();
}

// If not POST request, redirect back to dashboard
header('Location: patient.php');
exit();
?>
