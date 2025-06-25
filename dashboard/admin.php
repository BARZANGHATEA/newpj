<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/auth_handler.php';

// Ensure user is logged in and is an admin
require_role('admin');

// Get pending doctor registrations
$pending_doctors = get_rows(
    "SELECT * FROM users WHERE role = 'doctor' AND status = 'pending' ORDER BY created_at DESC"
);

// Get all articles
$articles = get_rows(
    "SELECT * FROM articles ORDER BY created_at DESC"
);

// Get system statistics
$total_patients = get_row("SELECT COUNT(*) as count FROM users WHERE role = 'patient'")['count'];
$total_doctors = get_row("SELECT COUNT(*) as count FROM users WHERE role = 'doctor' AND status = 'approved'")['count'];
$total_articles = get_row("SELECT COUNT(*) as count FROM articles")['count'];
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داشبورد مدیر - سیستم مدیریت سلامت</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">پنل مدیریت</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <span class="nav-link">خوش آمدید، <?php echo htmlspecialchars($_SESSION['name']); ?></span>
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
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-user-injured fa-2x text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="card-title mb-0">تعداد بیماران</h6>
                                <h2 class="mb-0"><?php echo $total_patients; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-user-md fa-2x text-success"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="card-title mb-0">تعداد پزشکان</h6>
                                <h2 class="mb-0"><?php echo $total_doctors; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-newspaper fa-2x text-info"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="card-title mb-0">تعداد مقالات</h6>
                                <h2 class="mb-0"><?php echo $total_articles; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Doctors -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4">درخواست‌های ثبت‌نام پزشکان</h5>
                <?php if (empty($pending_doctors)): ?>
                    <p class="text-muted">در حال حاضر درخواست جدیدی وجود ندارد.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>نام</th>
                                <th>شماره تلفن</th>
                                <th>کد ملی</th>
                                <th>کد نظام پزشکی</th>
                                <th>تاریخ ثبت‌نام</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending_doctors as $doctor): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($doctor['name']); ?></td>
                                <td><?php echo htmlspecialchars($doctor['phone']); ?></td>
                                <td><?php echo htmlspecialchars($doctor['national_id']); ?></td>
                                <td><?php echo htmlspecialchars($doctor['medical_system_code'] ?? '---'); ?></td>
                                <td><?php echo date('Y/m/d H:i', strtotime($doctor['created_at'])); ?></td>
                                <td>
                                    <form action="admin_handler.php" method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="approve_doctor">
                                        <input type="hidden" name="doctor_id" value="<?php echo $doctor['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-success">تایید</button>
                                    </form>
                                    <form action="admin_handler.php" method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="reject_doctor">
                                        <input type="hidden" name="doctor_id" value="<?php echo $doctor['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('آیا از رد این درخواست اطمینان دارید؟')">
                                            رد
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
<form action="assign_patient.php" method="POST" class="row g-3 p-3 bg-light rounded">
  <div class="col-md-5">
    <label>پزشک</label>
    <select name="doctor_id" class="form-select">
      <?php
      $doctors = get_rows("SELECT id, name FROM users WHERE role = 'doctor' AND status = 'approved'");
      foreach ($doctors as $doc) {
          echo "<option value='{$doc['id']}'>{$doc['name']}</option>";
      }
      ?>
    </select>
  </div>
  <div class="col-md-5">
    <label>بیمار</label>
    <select name="patient_id" class="form-select">
      <?php
      $patients = get_rows("SELECT id, name FROM users WHERE role = 'patient'");
      foreach ($patients as $pat) {
          echo "<option value='{$pat['id']}'>{$pat['name']}</option>";
      }
      ?>
    </select>
  </div>
  <div class="col-md-2 d-flex align-items-end">
    <button type="submit" class="btn btn-success w-100">اختصاص</button>
  </div>
</form>

        <!-- Articles Management -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title mb-0">مدیریت مقالات</h5>
                    <a href="../articles/article_create.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> مقاله جدید
                    </a>
                </div>
                
                <?php if (empty($articles)): ?>
                    <p class="text-muted">هنوز مقاله‌ای ثبت نشده است.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>عنوان</th>
                                <th>تاریخ انتشار</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($articles as $article): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($article['title']); ?></td>
                                <td><?php echo date('Y/m/d H:i', strtotime($article['created_at'])); ?></td>
                                <td>
                                    <a href="../articles/article_edit.php?id=<?php echo $article['id']; ?>" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i> ویرایش
                                    </a>
                                    <form action="admin_handler.php" method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="delete_article">
                                        <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('آیا از حذف این مقاله اطمینان دارید؟')">
                                            <i class="fas fa-trash"></i> حذف
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
