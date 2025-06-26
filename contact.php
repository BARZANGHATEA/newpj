<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تماس با ما</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body class="bg-light">
<header class="contact-header text-center text-white mb-5">
    <h1 class="display-5 fw-bold py-5">تماس با ما</h1>
</header>
<div class="container py-5">
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success text-center">پیام شما با موفقیت ارسال شد.</div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger text-center">
            <?php
            switch ($_GET['error']) {
                case 'missing':
                    echo 'لطفا تمام فیلدها را پر کنید.';
                    break;
                case 'invalid_email':
                    echo 'ایمیل نامعتبر است.';
                    break;
                default:
                    echo 'خطایی رخ داده است. لطفا دوباره تلاش کنید.';
            }
            ?>
        </div>
    <?php endif; ?>
    <div class="glass-card mx-auto p-4" style="max-width:600px;">
    <form action="includes/contact_handler.php" method="post">
        <div class="mb-3">
            <label class="form-label">نام</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">ایمیل</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">پیام</label>
            <textarea name="message" class="form-control" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100">ارسال</button>
    </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
