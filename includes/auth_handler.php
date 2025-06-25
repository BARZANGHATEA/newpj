<?php
session_start();
require_once(__DIR__ . '/database.php');
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>ثبت‌نام کاربر</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <style>
    .register-box {
      max-width: 700px;
      margin: auto;
      margin-top: 40px;
      background-color: #ffffff;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }
    .title-border {
      border-bottom: 2px solid #0d6efd;
      padding-bottom: 8px;
      margin-bottom: 25px;
    }
  </style>
</head>
<body class="bg-light">
  <div class="container">
    <div class="register-box">
      <h4 class="title-border">فرم ثبت‌نام کاربر جدید</h4>
      <form action="includes/auth_handler.php" method="POST" class="row g-3">
        <input type="hidden" name="action" value="register">
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
        <div class="col-md-3">
          <label class="form-label">جنسیت</label>
          <select name="gender" class="form-select" required>
            <option value="">انتخاب کنید</option>
            <option value="مرد">مرد</option>
            <option value="زن">زن</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">سن</label>
          <input type="number" name="age" min="1" max="120" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">رمز عبور</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">نقش</label>
          <select name="role" class="form-select" required>
            <option value="">انتخاب کنید</option>
            <option value="patient">بیمار</option>
            <option value="doctor">پزشک</option>
          </select>
        </div>
        <div class="col-12 text-end">
          <button type="submit" class="btn btn-primary px-4">ثبت‌نام</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
