<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/database.php';
require_once '../includes/auth_handler.php';

require_role('doctor');

$doctor_id = $_SESSION['user_id'];

$patients = get_rows("
    SELECT u.id, u.name, u.phone, u.national_id, u.avatar
    FROM doctor_patient dp
    JOIN users u ON dp.patient_id = u.id
    WHERE dp.doctor_id = ?
", [$doctor_id]);

$symptoms_map = [];
foreach ($patients as $p) {
    $symptoms_map[$p['id']] = get_rows(
        "SELECT * FROM symptoms WHERE user_id = ? ORDER BY recorded_at DESC",
        [$p['id']]
    );
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>بیماران پزشک</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        .card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease-in-out;
        }
        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }
    </style>
</head>
<body class="bg-light p-4">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">پنل پزشک</a>
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
                    <li class="nav-item"><a href="doctor_profile.php" class="nav-link"><i class="fas fa-user"></i> پروفایل</a></li>
                    <li class="nav-item"><a href="doctor.php" class="nav-link"><i class="fas fa-users"></i> لیست بیماران</a></li>
                    <li class="nav-item"><a href="../includes/auth_handler.php?action=logout" class="nav-link"><i class="fas fa-sign-out-alt"></i> خروج</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <h2 class="mb-4 text-center">بیماران شما</h2>
        <div class="row g-4">
            <?php foreach ($patients as $p): 
                $avatar = $p['avatar'] ?: '../assets/images/default_avatar.png'; ?>
                <div class="col-md-4">
                    <div class="card text-center shadow-sm p-3">
                        <img src="<?= htmlspecialchars($avatar) ?>" class="avatar mx-auto" alt="آواتار">
                        <h5 class="card-title"><?= htmlspecialchars($p['name']) ?></h5>
                        <p class="text-muted small"><?= htmlspecialchars($p['national_id']) ?></p>
                        <button class="btn btn-outline-primary btn-sm btn-glass mt-2" data-bs-toggle="modal" data-bs-target="#modal<?= $p['id'] ?>">
                            <i class="fas fa-eye"></i> مشاهده علائم
                        </button>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="modal<?= $p['id'] ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">علائم <?= htmlspecialchars($p['name']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
                      </div>
                      <div class="modal-body">
                        <?php if (count($symptoms_map[$p['id']]) > 0): ?>
                          <table class="table table-bordered">
                            <thead>
                              <tr>
                                <th>تاریخ</th>
                                <th>دما</th>
                                <th>فشار خون</th>
                                <th>قند خون</th>
                                <th>سطح انرژی</th>
                                <th>یادداشت</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php foreach ($symptoms_map[$p['id']] as $s): ?>
                                <tr>
                                  <td><?= htmlspecialchars($s['recorded_at']) ?></td>
                                  <td><?= htmlspecialchars($s['temperature']) ?></td>
                                  <td><?= htmlspecialchars($s['blood_pressure']) ?></td>
                                  <td><?= htmlspecialchars($s['blood_sugar']) ?></td>
                                  <td><?= htmlspecialchars($s['energy_level']) ?></td>
                                  <td><?= htmlspecialchars($s['note']) ?></td>
                                </tr>
                              <?php endforeach; ?>
                            </tbody>
                          </table>
                        <?php else: ?>
                          <p class="text-muted">علائمی ثبت نشده است.</p>
                        <?php endif; ?>

                        <hr>
                        <form action="doctor_handler.php" method="POST">
                            <input type="hidden" name="action" value="add_prescription">
                            <input type="hidden" name="patient_id" value="<?= $p['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label">نسخه</label>
                                <textarea name="prescription" class="form-control" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-success btn-glass">
                                <i class="fas fa-prescription"></i> ثبت نسخه
                            </button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
