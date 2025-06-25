<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/auth_handler.php';

// Ensure user is logged in and is an admin
require_role('admin');

// Function to handle image upload
function handle_image_upload($file) {
    $target_dir = "../uploads/articles/";
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    // Get file extension
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Generate unique filename
    $new_filename = uniqid('article_') . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Check file size (2MB max)
    if ($file['size'] > 2000000) {
        throw new Exception('حجم فایل باید کمتر از ۲ مگابایت باشد');
    }
    
    // Allow certain file formats
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_extension, $allowed_types)) {
        throw new Exception('فقط فایل‌های JPG، PNG و GIF مجاز هستند');
    }
    
    // Upload file
    if (!move_uploaded_file($file['tmp_name'], $target_file)) {
        throw new Exception('خطا در آپلود فایل');
    }
    
    return '/uploads/articles/' . $new_filename;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        switch ($_POST['action']) {
            case 'create':
                // Validate inputs
                $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
                $content = $_POST['content']; // Allow HTML content from TinyMCE
                
                if (empty($title) || mb_strlen($title) < 5) {
                    throw new Exception('عنوان مقاله باید حداقل ۵ کاراکتر باشد');
                }
                
                if (empty($content)) {
                    throw new Exception('متن مقاله نمی‌تواند خالی باشد');
                }
                
                // Handle image upload if provided
                $image_url = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $image_url = handle_image_upload($_FILES['image']);
                }
                
                // Insert article
                $result = execute_query(
                    "INSERT INTO articles (title, content, image_url) VALUES (?, ?, ?)",
                    [$title, $content, $image_url],
                    "sss"
                );
                
                if ($result) {
                    $_SESSION['success'] = 'مقاله با موفقیت ایجاد شد';
                    header('Location: ../dashboard/admin.php');
                    exit;
                } else {
                    throw new Exception('خطا در ایجاد مقاله');
                }
                break;
                
            case 'edit':
                // Validate inputs
                $article_id = filter_var($_POST['article_id'], FILTER_SANITIZE_NUMBER_INT);
                $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
                $content = $_POST['content']; // Allow HTML content from TinyMCE
                
                if (empty($title) || mb_strlen($title) < 5) {
                    throw new Exception('عنوان مقاله باید حداقل ۵ کاراکتر باشد');
                }
                
                if (empty($content)) {
                    throw new Exception('متن مقاله نمی‌تواند خالی باشد');
                }
                
                // Get current article data
                $current_article = get_row(
                    "SELECT image_url FROM articles WHERE id = ?",
                    [$article_id],
                    "i"
                );
                
                if (!$current_article) {
                    throw new Exception('مقاله مورد نظر یافت نشد');
                }
                
                // Handle image upload if provided
                $image_url = $current_article['image_url'];
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    // Delete old image if exists
                    if ($current_article['image_url']) {
                        $old_image_path = $_SERVER['DOCUMENT_ROOT'] . $current_article['image_url'];
                        if (file_exists($old_image_path)) {
                            unlink($old_image_path);
                        }
                    }
                    
                    $image_url = handle_image_upload($_FILES['image']);
                }
                
                // Update article
                $result = execute_query(
                    "UPDATE articles SET title = ?, content = ?, image_url = ? WHERE id = ?",
                    [$title, $content, $image_url, $article_id],
                    "sssi"
                );
                
                if ($result) {
                    $_SESSION['success'] = 'مقاله با موفقیت به‌روزرسانی شد';
                    header('Location: ../dashboard/admin.php');
                    exit;
                } else {
                    throw new Exception('خطا در به‌روزرسانی مقاله');
                }
                break;
                
            case 'delete':
                $article_id = filter_var($_POST['article_id'], FILTER_SANITIZE_NUMBER_INT);
                
                // Get article image URL before deletion
                $article = get_row(
                    "SELECT image_url FROM articles WHERE id = ?",
                    [$article_id],
                    "i"
                );
                
                if ($article) {
                    // Delete article image if exists
                    if ($article['image_url']) {
                        $image_path = $_SERVER['DOCUMENT_ROOT'] . $article['image_url'];
                        if (file_exists($image_path)) {
                            unlink($image_path);
                        }
                    }
                    
                    // Delete article
                    $result = execute_query(
                        "DELETE FROM articles WHERE id = ?",
                        [$article_id],
                        "i"
                    );
                    
                    if ($result) {
                        $_SESSION['success'] = 'مقاله با موفقیت حذف شد';
                    } else {
                        throw new Exception('خطا در حذف مقاله');
                    }
                } else {
                    throw new Exception('مقاله مورد نظر یافت نشد');
                }
                break;
                
            default:
                throw new Exception('عملیات نامعتبر');
        }
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

// If no valid action specified, redirect back
header('Location: ../dashboard/admin.php');
exit;
?>
