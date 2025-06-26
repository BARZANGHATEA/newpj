<?php

session_start();
require_once '../includes/database.php';

require_once '../includes/auth_handler.php';

require_role('admin');
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>افزودن مدیر جدید</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
</head>
<body class="bg-light p-4">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="https://raw.githubusercontent.com/twbs/icons/main/icons/person-gear.svg"
                             alt="Admin icon" class="mb-2" style="width: 80px;">
                        <h3 class="card-title">افزودن مدیر جدید</h3>
                    </div>

                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success">مدیر جدید با موفقیت ثبت شد</div>
                    <?php elseif (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">خطا در ثبت مدیر جدید</div>
                    <?php endif; ?>

                    <form action="admin_register_handler.php" method="POST" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">نام کامل</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">شماره تلفن</label>
                            <input type="tel" name="phone" class="form-control" pattern="^09\d{9}$" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">کد ملی</label>
                            <input type="text" name="national_id" class="form-control" pattern="^\d{10}$" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رمز عبور</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success">ثبت مدیر جدید</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
