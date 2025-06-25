<?php
session_start();
require_once 'includes/database.php';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>ورود / ثبت‌نام</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #dbeafe, #eff6ff);
      min-height: 100vh;
      font-family: 'Tahoma', sans-serif;
    }
    .form-wrapper {
      max-width: 600px;
      margin: 60px auto;
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
    }
    .nav-tabs .nav-link.active {
      background-color: #0d6efd;
      color: #fff;
      border: none;
    }
    .btn-primary, .btn-success {
      width: 100%;
    }
    .bg-gradient {
      background: linear-gradient(to top left, #e0f2fe, #c7d2fe);
    }
  </style>
</head>
<body>
  <div class="form-wrapper">
    <ul class="nav nav-tabs mb-4" id="authTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login-tab-pane" type="button" role="tab">ورود</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register-tab-pane" type="button" role="tab">ثبت‌نام</button>
      </li>
    </ul>
    <div class="tab-content" id="authTabsContent">
      <!-- Login Form -->
      <div class="tab-pane fade show active" id="login-tab-pane" role="tabpanel">
        <form action="includes/auth_handler.php" method="POST">
          <input type="hidden" name="action" value="login">
          <div class="mb-3">
            <label class="form-label">شماره تلفن</label>
            <input type="tel" name="phone" class="form-control" pattern="^09\d{9}$" required pattern="^09\d{9}$" maxlength="11" required>
          </div>
          <div class="mb-3">
            <label class="form-label">رمز عبور</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary">ورود</button>
        </form>
      </div>

      <!-- Register Form -->
      <div class="tab-pane fade" id="register-tab-pane" role="tabpanel">
        <form action="includes/auth_handler.php" method="POST" class="row g-3">
          <input type="hidden" name="action" value="register">
          <div class="col-12">
            <label class="form-label">نام کامل</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">شماره تلفن</label>
            <input type="tel" name="phone" class="form-control" pattern="^09\d{9}$" required pattern="^09\d{9}$" maxlength="11" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">کد ملی</label>
            <input type="text" name="national_id" class="form-control" pattern="^\d{10}$" required pattern="^\d{10}$" maxlength="10" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">جنسیت</label>
            <select name="gender" class="form-select" required>
              <option value="">انتخاب کنید</option>
              <option value="مرد">مرد</option>
              <option value="زن">زن</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">سن</label>
            <input type="number" name="age" min="1" max="120" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">رمز عبور</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">نقش</label>
            <select name="role" class="form-select" id="roleSelect" onchange="toggleMedicalCode()" required>
              <option value="">انتخاب کنید</option>
              <option value="patient">بیمار</option>
              <option value="doctor">پزشک</option>
            </select>
          </div>
          <div class="col-12" id="medicalCodeField" style="display: none;">
            <label class="form-label">کد نظام پزشکی</label>
            <input type="text" name="medical_system_code" class="form-control">
          </div>
          <div class="col-12 text-end">
            <button type="submit" class="btn btn-info text-white">ثبت‌نام</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    function toggleMedicalCode() {
      const role = document.getElementById('roleSelect').value;
      const medField = document.getElementById('medicalCodeField');
      medField.style.display = (role === 'doctor') ? 'block' : 'none';
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
