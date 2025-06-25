<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard/" . $_SESSION['role'] . ".php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود/ثبت‌نام - سیستم مدیریت سلامت</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #0396FF 0%, #ABDCFF 100%);
        }
        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .nav-tabs .nav-link {
            color: #495057;
            border: none;
            padding: 1rem 2rem;
        }
        .nav-tabs .nav-link.active {
            color: #0396FF;
            border-bottom: 2px solid #0396FF;
            background: none;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="auth-card p-4">
                        <!-- Auth Tabs -->
                        <ul class="nav nav-tabs nav-fill mb-4" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#login">ورود</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#register">ثبت‌نام</a>
                            </li>
                        </ul>

                        <!-- Messages -->
                        <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?php 
                            $error = $_GET['error'];
                            switch($error) {
                                case 'invalid':
                                    echo 'ایمیل یا رمز عبور نادرست است.';
                                    break;
                                case 'pending':
                                    echo 'حساب کاربری شما در انتظار تایید است.';
                                    break;
                            case 'phone_exists':
                                    echo 'این شماره تلفن قبلاً ثبت شده است.';
                                    break;
                                case 'password_mismatch':
                                    echo 'رمز عبور و تکرار آن یکسان نیستند.';
                                    break;
                                default:
                                    echo 'خطایی رخ داده است. لطفاً دوباره تلاش کنید.';
                            }
                            ?>
                        </div>
                        <?php endif; ?>

                        <?php if (isset($_GET['success']) && $_GET['success'] === 'registered'): ?>
                        <div class="alert alert-success">
                            ثبت‌نام با موفقیت انجام شد. اکنون می‌توانید وارد شوید.
                            <?php if (isset($_GET['role']) && $_GET['role'] === 'doctor'): ?>
                                <br>حساب کاربری شما پس از تایید مدیر فعال خواهد شد.
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Tab Content -->
                        <div class="tab-content">
                            <!-- Login Form -->
                            <div class="tab-pane fade show active" id="login">
                                <form action="includes/auth_handler.php" method="POST" class="needs-validation" novalidate>
                                    <input type="hidden" name="action" value="login">
                                    
                                    <div class="mb-3">
                                        <label for="login_phone" class="form-label">شماره تلفن</label>
                                        <input type="tel" 
                                               class="form-control" 
                                               id="login_phone" 
                                               name="phone" 
                                               required
                                               pattern="^09\d{9}$"
                                               maxlength="11"
                                               placeholder="۰۹۱۲۳۴۵۶۷۸۹">
                                        <div class="invalid-feedback">
                                            لطفاً شماره تلفن معتبر وارد کنید (مثال: ۰۹۱۲۳۴۵۶۷۸۹)
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="login_password" class="form-label">رمز عبور</label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="login_password" 
                                               name="password" 
                                               required
                                               minlength="6"
                                               placeholder="رمز عبور خود را وارد کنید">
                                        <div class="invalid-feedback">
                                            رمز عبور باید حداقل ۶ کاراکتر باشد
                                        </div>
                                    </div>

                                    <div class="mb-3 form-check">
                                        <input type="checkbox" 
                                               class="form-check-input" 
                                               id="remember" 
                                               name="remember">
                                        <label class="form-check-label" for="remember">
                                            مرا به خاطر بسپار
                                        </label>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">ورود</button>
                                </form>
                            </div>

                            <!-- Registration Form -->
                            <div class="tab-pane fade" id="register">
                                <form action="includes/auth_handler.php" method="POST" class="needs-validation" novalidate>
                                    <input type="hidden" name="action" value="register">
                                    
                                    <div class="mb-3">
                                        <label for="reg_name" class="form-label">نام و نام خانوادگی</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="reg_name" 
                                               name="name" 
                                               required 
                                               minlength="3"
                                               placeholder="نام و نام خانوادگی خود را وارد کنید">
                                        <div class="invalid-feedback">
                                            لطفاً نام و نام خانوادگی خود را وارد کنید
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="reg_phone" class="form-label">شماره تلفن</label>
                                        <input type="tel" 
                                               class="form-control" 
                                               id="reg_phone" 
                                               name="phone" 
                                               required
                                               pattern="^09\d{9}$"
                                               maxlength="11"
                                               placeholder="۰۹۱۲۳۴۵۶۷۸۹">
                                        <div class="invalid-feedback">
                                            لطفاً شماره تلفن معتبر وارد کنید (مثال: ۰۹۱۲۳۴۵۶۷۸۹)
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="reg_national_id" class="form-label">کد ملی</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="reg_national_id" 
                                               name="national_id" 
                                               required
                                               pattern="^\d{10}$"
                                               maxlength="10"
                                               placeholder="کد ملی ۱۰ رقمی">
                                        <div class="invalid-feedback">
                                            لطفاً کد ملی معتبر ۱۰ رقمی وارد کنید
                                        </div>
                                    </div>

                                    <div class="mb-3" id="medical_system_code_container" style="display: none;">
                                        <label for="reg_medical_system_code" class="form-label">کد نظام پزشکی</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="reg_medical_system_code" 
                                               name="medical_system_code"
                                               maxlength="20"
                                               placeholder="کد نظام پزشکی">
                                        <div class="invalid-feedback">
                                            برای پزشکان، وارد کردن کد نظام پزشکی الزامی است
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="reg_password" class="form-label">رمز عبور</label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="reg_password" 
                                               name="password" 
                                               required 
                                               minlength="6"
                                               placeholder="حداقل ۶ کاراکتر">
                                        <div class="invalid-feedback">
                                            رمز عبور باید حداقل ۶ کاراکتر باشد
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="reg_password_confirm" class="form-label">تکرار رمز عبور</label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="reg_password_confirm" 
                                               name="password_confirm" 
                                               required
                                               placeholder="رمز عبور را مجدداً وارد کنید">
                                        <div class="invalid-feedback">
                                            رمز عبور و تکرار آن باید یکسان باشند
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="reg_role" class="form-label">نوع کاربری</label>
                                        <select class="form-select" 
                                                id="reg_role" 
                                                name="role" 
                                                required>
                                            <option value="">انتخاب کنید</option>
                                            <option value="patient">بیمار</option>
                                            <option value="doctor">پزشک</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            لطفاً نوع کاربری خود را انتخاب کنید
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">ثبت‌نام</button>
                                </form>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <a href="index.php" class="text-decoration-none">بازگشت به صفحه اصلی</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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

                    // Additional password match validation for registration
                    if (form.querySelector('#reg_password')) {
                        var password = form.querySelector('#reg_password').value;
                        var confirmPassword = form.querySelector('#reg_password_confirm').value;
                        
                        if (password !== confirmPassword) {
                            event.preventDefault();
                            form.querySelector('#reg_password_confirm').setCustomValidity('رمز عبور و تکرار آن باید یکسان باشند');
                        } else {
                            form.querySelector('#reg_password_confirm').setCustomValidity('');
                        }
                    }

                    form.classList.add('was-validated')
                }, false)
            })
        })()

        // Show registration tab if there's a registration error
        if (window.location.href.includes('error=phone_exists') || 
            window.location.href.includes('error=password_mismatch')) {
            document.querySelector('a[href="#register"]').click();
        }

        // Show/hide medical system code field based on role selection
        document.getElementById('reg_role').addEventListener('change', function() {
            const medicalSystemCodeContainer = document.getElementById('medical_system_code_container');
            const medicalSystemCodeInput = document.getElementById('reg_medical_system_code');
            
            if (this.value === 'doctor') {
                medicalSystemCodeContainer.style.display = 'block';
                medicalSystemCodeInput.required = true;
            } else {
                medicalSystemCodeContainer.style.display = 'none';
                medicalSystemCodeInput.required = false;
                medicalSystemCodeInput.value = '';
            }
        });
    </script>
</body>
</html>
