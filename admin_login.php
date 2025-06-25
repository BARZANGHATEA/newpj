<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    header("Location: dashboard/admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ورود ادمین</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
</head>
<body class="bg-light d-flex align-items-center" style="height:100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 bg-white shadow-sm rounded p-4">
                <h3 class="mb-3 text-center">ورود مدیر سایت</h3>
                <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid'): ?>
                    <div class="alert alert-danger">شماره تلفن یا رمز عبور نادرست است</div>
                <?php endif; ?>
                <form action="admin_login_handler.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">شماره تلفن</label>
                        <input type="tel" name="phone" class="form-control" required pattern="^09\d{9}$">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">رمز عبور</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">ورود</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
