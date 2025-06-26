<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/auth_handler.php';

// Ensure user is logged in and is an admin
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['action']) {
        case 'approve_doctor':
            $doctor_id = filter_var($_POST['doctor_id'], FILTER_SANITIZE_NUMBER_INT);
            
            // Update doctor status to approved
            $result = execute_query(
                "UPDATE users SET status = 'approved' WHERE id = ? AND role = 'doctor'",
                [$doctor_id]
            );
            
            if ($result) {
                // Send SMS notification to doctor (in a real application)
                // sendSMS($doctor['phone'], 'حساب کاربری شما در سیستم مدیریت سلامت تایید شد.');
                
                $_SESSION['success'] = 'پزشک با موفقیت تایید شد';
            } else {
                $_SESSION['error'] = 'خطا در تایید پزشک';
            }
            break;
            
        case 'reject_doctor':
            $doctor_id = filter_var($_POST['doctor_id'], FILTER_SANITIZE_NUMBER_INT);
            
            // Get doctor's phone before deletion for notification
            $doctor = get_row(
                "SELECT phone FROM users WHERE id = ? AND role = 'doctor'",
                [$doctor_id]
            );
            
            if ($doctor) {
                // Delete the doctor account
                $result = execute_query(
                    "DELETE FROM users WHERE id = ? AND role = 'doctor'",
                [$doctor_id]
                );
                
                if ($result) {
                    // Send SMS notification to doctor (in a real application)
                    // sendSMS($doctor['phone'], 'متاسفانه درخواست ثبت‌نام شما در سیستم مدیریت سلامت تایید نشد.');
                    
                    $_SESSION['success'] = 'درخواست پزشک رد شد';
                } else {
                    $_SESSION['error'] = 'خطا در رد درخواست پزشک';
                }
            } else {
                $_SESSION['error'] = 'پزشک مورد نظر یافت نشد';
            }
            break;
            
        case 'delete_article':
            $article_id = filter_var($_POST['article_id'], FILTER_SANITIZE_NUMBER_INT);
            
            // Get article details to delete associated image if exists
            $article = get_row(
                "SELECT image_url FROM articles WHERE id = ?",
                [$article_id]
            );
            
            if ($article && $article['image_url']) {
                // Delete the image file if it exists
                $image_path = $_SERVER['DOCUMENT_ROOT'] . $article['image_url'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            
            // Delete the article
            $result = execute_query(
                "DELETE FROM articles WHERE id = ?",
                [$article_id]
            );
            
            if ($result) {
                $_SESSION['success'] = 'مقاله با موفقیت حذف شد';
            } else {
                $_SESSION['error'] = 'خطا در حذف مقاله';
            }
            break;
            
        case 'update_settings':
            // Example of updating system settings
            $site_name = filter_var($_POST['site_name'], FILTER_SANITIZE_STRING);
            $contact_phone = filter_var($_POST['contact_phone'], FILTER_SANITIZE_STRING);
            
            $updates = [
                "UPDATE settings SET value = ? WHERE name = 'site_name'",
                "UPDATE settings SET value = ? WHERE name = 'contact_phone'"
            ];
            
            $success = true;
            foreach ($updates as $sql) {
                $result = execute_query($sql, [$_POST[$name]]);
                if (!$result) {
                    $success = false;
                    break;
                }
            }
            
            if ($success) {
                $_SESSION['success'] = 'تنظیمات با موفقیت به‌روزرسانی شد';
            } else {
                $_SESSION['error'] = 'خطا در به‌روزرسانی تنظیمات';
            }
            break;
            
        case 'backup_database':
            // Example of creating a database backup
            $backup_dir = '../backups/';
            if (!is_dir($backup_dir)) {
                mkdir($backup_dir, 0755, true);
            }
            
            $backup_file = $backup_dir . 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $command = sprintf(
                'mysqldump -h %s -u %s -p%s %s > %s',
                DB_HOST,
                DB_USER,
                DB_PASS,
                DB_NAME,
                $backup_file
            );
            
            exec($command, $output, $return_var);
            
            if ($return_var === 0) {
                $_SESSION['success'] = 'پشتیبان‌گیری با موفقیت انجام شد';
            } else {
                $_SESSION['error'] = 'خطا در پشتیبان‌گیری از پایگاه داده';
            }
            break;

        case 'approve_review':
            $review_id = filter_var($_POST['review_id'], FILTER_SANITIZE_NUMBER_INT);
            execute_query("UPDATE doctor_reviews SET status='approved' WHERE id=?", [$review_id]);
            $_SESSION['success'] = 'نظر تایید شد';
            break;

        case 'reject_review':
            $review_id = filter_var($_POST['review_id'], FILTER_SANITIZE_NUMBER_INT);
            execute_query("DELETE FROM doctor_reviews WHERE id=?", [$review_id]);
            $_SESSION['success'] = 'نظر حذف شد';
            break;
    }
    
    header('Location: admin.php');
    exit;
}

// If no valid action specified, redirect back to dashboard
header('Location: admin.php');
exit;
?>
