<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/auth_handler.php';

// Ensure user is logged in and is an admin
require_role('admin');

// Get article ID from URL
$article_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

if (!$article_id) {
    header('Location: ../dashboard/admin.php');
    exit();
}

// Get article details
$article = get_row(
    "SELECT * FROM articles WHERE id = ?",
    [$article_id],
    "i"
);

if (!$article) {
    header('Location: ../dashboard/admin.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ویرایش مقاله - سیستم مدیریت سلامت</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css">
    <!-- Include TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js"></script>
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        .preview-image {
            max-width: 300px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="../dashboard/admin.php">پنل مدیریت</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../dashboard/admin.php">داشبورد</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="articles.php">مقالات</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="../includes/auth_handler.php?action=logout" class="nav-link">خروج</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title mb-4">ویرایش مقاله</h2>

                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <?php 
                                echo $_SESSION['error'];
                                unset($_SESSION['error']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <form action="article_handler.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="article_id" value="<?php echo $article_id; ?>">
                            
                            <div class="mb-4">
                                <label for="title" class="form-label">عنوان مقاله</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="title" 
                                       name="title" 
                                       required 
                                       minlength="5"
                                       maxlength="255"
                                       value="<?php echo htmlspecialchars($article['title']); ?>">
                                <div class="invalid-feedback">
                                    لطفاً عنوان مقاله را وارد کنید (حداقل ۵ کاراکتر)
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="image" class="form-label">تصویر شاخص</label>
                                <?php if ($article['image_url']): ?>
                                    <div class="mb-2">
                                        <img src="<?php echo htmlspecialchars($article['image_url']); ?>" 
                                             alt="تصویر فعلی" 
                                             class="preview-image">
                                    </div>
                                <?php endif; ?>
                                <input type="file" 
                                       class="form-control" 
                                       id="image" 
                                       name="image" 
                                       accept="image/*"
                                       onchange="previewImage(this)">
                                <div class="form-text">فرمت‌های مجاز: JPG، PNG، GIF - حداکثر حجم: ۲ مگابایت</div>
                                <img id="imagePreview" src="#" alt="پیش‌نمایش تصویر جدید" class="mt-3 preview-image" style="display: none;">
                            </div>

                            <div class="mb-4">
                                <label for="content" class="form-label">متن مقاله</label>
                                <textarea class="form-control" 
                                          id="content" 
                                          name="content" 
                                          rows="15" 
                                          required><?php echo htmlspecialchars($article['content']); ?></textarea>
                                <div class="invalid-feedback">
                                    لطفاً متن مقاله را وارد کنید
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> ذخیره تغییرات
                                    </button>
                                    <a href="../dashboard/admin.php" class="btn btn-outline-secondary me-2">
                                        <i class="fas fa-times"></i> انصراف
                                    </a>
                                </div>
                                <button type="button" 
                                        class="btn btn-danger" 
                                        onclick="confirmDelete(<?php echo $article_id; ?>)">
                                    <i class="fas fa-trash"></i> حذف مقاله
                                </button>
                            </div>
                        </form>

                        <!-- Hidden delete form -->
                        <form id="deleteForm" action="article_handler.php" method="POST" style="display: none;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="article_id" value="<?php echo $article_id; ?>">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize TinyMCE
        tinymce.init({
            selector: '#content',
            directionality: 'rtl',
            language: 'fa',
            plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignright aligncenter alignleft alignjustify | bullist numlist outdent indent | removeformat | help',
            height: 400,
            menubar: false
        });

        // Image preview
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = '#';
                preview.style.display = 'none';
            }
        }

        // Delete confirmation
        function confirmDelete(articleId) {
            if (confirm('آیا از حذف این مقاله اطمینان دارید؟ این عمل قابل بازگشت نیست.')) {
                document.getElementById('deleteForm').submit();
            }
        }

        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>
</html>
