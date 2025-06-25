<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/auth_handler.php';

// Ensure user is logged in and is an admin
require_role('admin');
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ایجاد مقاله جدید - سیستم مدیریت سلامت</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css">
    <!-- Include TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/3fhpj4fbwaga5z3i2uk4yyi9bbfzl62i3nnykuzxyesrio3v/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        .preview-image {
            max-width: 300px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
            display: none;
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
                        <h2 class="card-title mb-4">ایجاد مقاله جدید</h2>

                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <?php 
                                echo $_SESSION['error'];
                                unset($_SESSION['error']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <form action="article_handler.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <input type="hidden" name="action" value="create">
                            
                            <div class="mb-4">
                                <label for="title" class="form-label">عنوان مقاله</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="title" 
                                       name="title" 
                                       required 
                                       minlength="5"
                                       maxlength="255">
                                <div class="invalid-feedback">
                                    لطفاً عنوان مقاله را وارد کنید (حداقل ۵ کاراکتر)
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="image" class="form-label">تصویر شاخص</label>
                                <input type="file" 
                                       class="form-control" 
                                       id="image" 
                                       name="image" 
                                       accept="image/*"
                                       onchange="previewImage(this)">
                                <div class="form-text">فرمت‌های مجاز: JPG، PNG، GIF - حداکثر حجم: ۲ مگابایت</div>
                                <img id="imagePreview" src="#" alt="پیش‌نمایش تصویر" class="mt-3 preview-image">
                            </div>

                            <div class="mb-4">
                                <label for="content" class="form-label">متن مقاله</label>
                                <textarea class="form-control" 
                                          id="content" 
                                          name="content" 
                                          rows="15" 
                                          required></textarea>
                                <div class="invalid-feedback">
                                    لطفاً متن مقاله را وارد کنید
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> ذخیره مقاله
                                </button>
                                <a href="../dashboard/admin.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> انصراف
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize TinyMCE with enhanced configuration
        tinymce.init({
            selector: '#content',
            directionality: 'rtl',
            language: 'fa',
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic forecolor | alignright aligncenter ' +
                'alignleft alignjustify | bullist numlist outdent indent | ' +
                'link image media emoticons | removeformat | help',
            height: 500,
            menubar: true,
            content_style: 'body { font-family: Vazir, Tahoma, Arial; font-size: 14px }',
            images_upload_url: 'article_handler.php?action=upload_image',
            automatic_uploads: true,
            file_picker_types: 'image',
            relative_urls: false,
            remove_script_host: false,
            convert_urls: true,
            branding: false,
            promotion: false
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
