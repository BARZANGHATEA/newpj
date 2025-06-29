<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/auth_handler.php';

// Ensure user is logged in and is a patient
require_role('patient');

// Get patient's data
$user_id = $_SESSION['user_id'];
$patient_data = get_row("SELECT * FROM users WHERE id = ?", [$user_id]);

// Get latest symptoms record
$latest_symptoms = get_row(
    "SELECT * FROM symptoms WHERE user_id = ? ORDER BY recorded_at DESC LIMIT 1",
    [$user_id]
);

// Get last 7 days of symptoms for the chart
$weekly_symptoms = get_rows(
    "SELECT * FROM symptoms WHERE user_id = ? AND recorded_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY recorded_at",
    [$user_id]
);

$prescriptions = get_rows(
    "SELECT p.*, u.name AS doctor_name FROM prescriptions p JOIN users u ON p.doctor_id = u.id WHERE patient_id = ? ORDER BY created_at DESC",
    [$user_id]
);

// List of approved doctors for selection
$doctors = get_rows(
    "SELECT u.id, u.name, u.avatar, dp.specialty FROM users u
     LEFT JOIN doctor_profiles dp ON u.id = dp.doctor_id
     WHERE u.role = 'doctor' AND u.status = 'approved'"
);
$assigned_doctor = get_row(
    "SELECT doctor_id FROM doctor_patient WHERE patient_id = ?",
    [$user_id]
);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داشبورد بیمار - سیستم مدیریت سلامت</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">سیستم مدیریت سلامت</a>
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
                        <!-- دکمه باز کردن مودال -->
                        <button class="btn btn-outline-primary btn-glass mb-3" data-bs-toggle="modal" data-bs-target="#avatarModal">
                            <i class="fas fa-camera"></i> تغییر تصویر پروفایل
                        </button>
                    </li>
                </ul>

                <!-- مودال آپلود -->
                <div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form action="upload_avatar.php" method="POST" enctype="multipart/form-data">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="avatarModalLabel">آپلود تصویر جدید</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <img id="avatarPreview" src="<?= htmlspecialchars($patient_data['avatar'] ?? '../assets/images/default_avatar.png') ?>"
                                         class="img-thumbnail rounded-circle mb-3" style="width: 120px; height: 120px;" alt="آواتار فعلی">
                                    <input type="file" name="avatar" id="avatarInput" accept="image/*" class="form-control" required>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success btn-glass">
                                        <i class="fas fa-save"></i> ذخیره
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <ul class="navbar-nav me-2">
                    <li class="nav-item">
                        <button class="btn btn-outline-secondary btn-glass mb-3" data-bs-toggle="modal" data-bs-target="#doctorModal">
                            <i class="fas fa-user-md"></i> انتخاب پزشک
                        </button>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="../includes/auth_handler.php?action=logout" class="nav-link"><i class="fas fa-sign-out-alt"></i> خروج</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

<!-- Modal for choosing doctor -->
<div class="modal fade" id="doctorModal" tabindex="-1" aria-labelledby="doctorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="doctorModalLabel">انتخاب پزشک</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <?php foreach ($doctors as $doc): ?>
            <div class="col-md-4 text-center">
              <form action="choose_doctor.php" method="POST">
                <input type="hidden" name="doctor_id" value="<?= $doc['id'] ?>">
                <img src="<?= htmlspecialchars($doc['avatar'] ?: '../assets/images/default_avatar.png') ?>" class="rounded-circle mb-2" style="width:80px;height:80px;object-fit:cover" alt="avatar">
                <h6 class="mb-1"><?= htmlspecialchars($doc['name']) ?></h6>
                <small class="text-muted d-block mb-2"><?= htmlspecialchars($doc['specialty'] ?? '') ?></small>
                <button type="submit" class="btn btn-sm btn-primary">انتخاب</button>
              </form>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// نمایش زنده تصویر انتخاب‌شده
document.addEventListener("DOMContentLoaded", function () {
  const input = document.getElementById("avatarInput");
  const preview = document.getElementById("avatarPreview");
  input.addEventListener("change", function () {
    const file = this.files[0];
    if (file) {
      preview.src = URL.createObjectURL(file);
    }
  });
});
</script>

    <!-- Main Content -->
    <div class="container py-4">
        <div class="row">
            <!-- Health Data Form -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">ثبت اطلاعات سلامت روزانه</h5>
                        <form action="../includes/symptoms_handler.php" method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="temperature" class="form-label">دمای بدن (درجه سانتی‌گراد)</label>
                                <input type="number" step="0.1" class="form-control" id="temperature" name="temperature" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="blood_pressure" class="form-label">فشار خون</label>
                                <input type="text" class="form-control" id="blood_pressure" name="blood_pressure" 
                                       placeholder="مثال: 120/80" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="blood_sugar" class="form-label">قند خون</label>
                                <input type="text" class="form-control" id="blood_sugar" name="blood_sugar" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="energy_level" class="form-label">سطح انرژی</label>
                                <select class="form-select" id="energy_level" name="energy_level" required>
                                    <option value="">انتخاب کنید</option>
                                    <option value="عالی">عالی</option>
                                    <option value="خوب">خوب</option>
                                    <option value="متوسط">متوسط</option>
                                    <option value="ضعیف">ضعیف</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="note" class="form-label">یادداشت</label>
                                <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-glass">
                                <i class="fas fa-check-circle"></i> ثبت اطلاعات
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Health Data Overview -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">آخرین وضعیت علائم</h5>
                        <div class="d-flex justify-content-around text-center">
                            <div class="symptom-circle bg-pink">
                                <div class="fw-bold"><?php echo htmlspecialchars($latest_symptoms['temperature'] ?? '-'); ?></div>
                                <small>دمای بدن</small>
                            </div>
                            <div class="symptom-circle bg-blue">
                                <div class="fw-bold"><?php echo htmlspecialchars($latest_symptoms['blood_pressure'] ?? '-'); ?></div>
                                <small>فشار خون</small>
                            </div>
                            <div class="symptom-circle bg-green">
                                <div class="fw-bold"><?php echo htmlspecialchars($latest_symptoms['blood_sugar'] ?? '-'); ?></div>
                                <small>قند خون</small>
                            </div>
                            <div class="symptom-circle bg-yellow">
                                <div class="fw-bold"><?php echo htmlspecialchars($latest_symptoms['energy_level'] ?? '-'); ?></div>
                                <small>سطح انرژی</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Latest Records -->
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">آخرین ثبت‌های شما</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>تاریخ</th>
                                        <th>دمای بدن</th>
                                        <th>فشار خون</th>
                                        <th>قند خون</th>
                                        <th>سطح انرژی</th>
                                        <th>یادداشت</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $recent_records = get_rows(
                                        "SELECT * FROM symptoms WHERE user_id = ? ORDER BY recorded_at DESC LIMIT 5",
                                        [$user_id]
                                    );
                                    
                                    foreach ($recent_records as $record): ?>
                                    <tr>
                                        <td><?php echo date('Y/m/d H:i', strtotime($record['recorded_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($record['temperature']); ?></td>
                                        <td><?php echo htmlspecialchars($record['blood_pressure']); ?></td>
                                        <td><?php echo htmlspecialchars($record['blood_sugar']); ?></td>
                                        <td><?php echo htmlspecialchars($record['energy_level']); ?></td>
                                        <td><?php echo htmlspecialchars($record['note']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prescriptions -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">نسخه‌های پزشک</h5>
                    <?php if (empty($prescriptions)): ?>
                        <p class="text-muted">نسخه‌ای ثبت نشده است.</p>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($prescriptions as $pr): ?>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span><?php echo htmlspecialchars($pr['content']); ?></span>
                                    <small class="text-muted">
                                        <?php echo date('Y/m/d', strtotime($pr['created_at'])); ?> -
                                        <?php echo htmlspecialchars($pr['doctor_name']); ?>
                                    </small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Doctor Review -->
        <?php if ($assigned_doctor): ?>
        <div class="col-12 mt-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">ثبت نظر برای پزشک</h5>
                    <form action="submit_review.php" method="POST">
                        <input type="hidden" name="doctor_id" value="<?= $assigned_doctor['doctor_id'] ?>">
                        <div class="mb-3">
                            <label class="form-label">امتیاز</label>
                            <select name="rating" class="form-select" required>
                                <option value="">انتخاب کنید</option>
                                <?php for ($i=1;$i<=5;$i++): ?>
                                    <option value="<?= $i ?>"><?= str_repeat('★',$i) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">نظر شما</label>
                            <textarea name="comment" class="form-control" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">ارسال</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
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
                    form.classList.add('was-validated')
                }, false)
            })
        })()

    </script>
</body>
</html>
