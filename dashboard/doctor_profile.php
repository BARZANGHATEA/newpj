<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/auth_handler.php';
require_role('doctor');

$doctor_id = $_SESSION['user_id'];

$profile = get_row("SELECT * FROM doctor_profiles WHERE doctor_id = ?", [$doctor_id]);
$reviews = get_rows("SELECT r.rating, r.comment, u.name AS patient_name, r.created_at
    FROM doctor_reviews r JOIN users u ON r.patient_id = u.id
    WHERE r.doctor_id = ? AND r.status = 'approved'
    ORDER BY r.created_at DESC", [$doctor_id]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = $_POST['first_name'] ?? '';
    $last = $_POST['last_name'] ?? '';
    $english = $_POST['english_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $specialty = $_POST['specialty'] ?? '';

    if ($profile) {
        execute_query(
            "UPDATE doctor_profiles SET first_name=?, last_name=?, english_name=?, email=?, gender=?, specialty=? WHERE doctor_id=?",
            [$first, $last, $english, $email, $gender, $specialty, $doctor_id]
        );
    } else {
        execute_query(
            "INSERT INTO doctor_profiles (first_name, last_name, english_name, email, gender, specialty, doctor_id) VALUES (?, ?, ?, ?, ?, ?, ?)",
            [$first, $last, $english, $email, $gender, $specialty, $doctor_id]
        );
    }
    $profile = get_row("SELECT * FROM doctor_profiles WHERE doctor_id = ?", [$doctor_id]);
    $_SESSION['success'] = 'اطلاعات با موفقیت ذخیره شد';
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>پروفایل پزشک</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
</head>
<body class="bg-light">
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
                <li class="nav-item"><a href="doctor_profile.php" class="nav-link">پروفایل</a></li>
                <li class="nav-item"><a href="doctor.php" class="nav-link">لیست بیماران</a></li>
                <li class="nav-item"><a href="../includes/auth_handler.php?action=logout" class="nav-link">خروج</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container">
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="" method="POST" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">کد نظام پزشکی</label>
                    <input type="text" class="form-control" name="medical_system_code" value="<?php echo htmlspecialchars($profile['medical_system_code'] ?? ($_SESSION['medical_system_code'] ?? '')); ?>" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">نام</label>
                    <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($profile['first_name'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">نام خانوادگی</label>
                    <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($profile['last_name'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">نام کامل انگلیسی</label>
                    <input type="text" class="form-control" name="english_name" value="<?php echo htmlspecialchars($profile['english_name'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">کد ملی</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['national_id'] ?? ''); ?>" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">ایمیل</label>
                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">جنسیت</label>
                    <select name="gender" class="form-select" required>
                        <option value="">انتخاب کنید</option>
                        <option value="مرد" <?php echo (($profile['gender'] ?? '')==='مرد')? 'selected':''; ?>>مرد</option>
                        <option value="زن" <?php echo (($profile['gender'] ?? '')==='زن')? 'selected':''; ?>>زن</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">تخصص</label>
                    <select name="specialty" class="form-select" required>
                        <option value="">انتخاب کنید</option>
                        <option value="ارتوپدی">ارتوپدی</option>
                        <option value="ارتوپدی کودکان">ارتوپدی کودکان</option>
                        <option value="جراحی دست و اعصاب محیطی (میکروسرجری)">جراحی دست و اعصاب محیطی (میکروسرجری)</option>
                        <option value="جراحی زانو">جراحی زانو</option>
                        <option value="جراحی دیسک و ستون فقرات">جراحی دیسک و ستون فقرات</option>
                        <option value="جراحی لگن و ران">جراحی لگن و ران</option>
                        <option value="جراحی شانه و آرنج">جراحی شانه و آرنج</option>
                        <option value="جراحی پا و مچ پا">جراحی پا و مچ پا</option>
                        <option value="چشم پزشکی">چشم پزشکی</option>
                        <option value="شبکیه (ویتره و رتین)">شبکیه (ویتره و رتین)</option>
                        <option value="سرطان شناسی چشم (افتالموانکولوژی)">سرطان شناسی چشم (افتالموانکولوژی)</option>
                        <option value="قرنیه و خارج چشمی">قرنیه و خارج چشمی</option>
                        <option value="چشم پزشکی کودکان">چشم پزشکی کودکان</option>
                        <option value="جراحی پلاستیک و انحراف چشم (اکولوپلاستی و استرابیسم)">جراحی پلاستیک و انحراف چشم (اکولوپلاستی و استرابیسم)</option>
                        <option value="گلوکوم (آب سیاه)">گلوکوم (آب سیاه)</option>
                        <option value="بیماری های عفونی و گرمسیری">بیماری های عفونی و گرمسیری</option>
                        <option value="اعصاب و روان (روانپزشکی)">اعصاب و روان (روانپزشکی)</option>
                        <option value="روانپزشکی کودک و نوجوان">روانپزشکی کودک و نوجوان</option>
                        <option value="درمان اختلالات جنسی و زوج درمانی">درمان اختلالات جنسی و زوج درمانی</option>
                        <option value="پرتودرمانی (رادیوتراپی)">پرتودرمانی (رادیوتراپی)</option>
                        <option value="فلوشیپ آنکولوژی">فلوشیپ آنکولوژی</option>
                        <option value="بیهوشی">بیهوشی</option>
                        <option value="درد">درد</option>
                        <option value="پزشکی ورزشی">پزشکی ورزشی</option>
                        <option value="اینترونشن غیر جراحی ستون فقرات">اینترونشن غیر جراحی ستون فقرات</option>
                        <option value="پوست، مو و زیبایی">پوست، مو و زیبایی</option>
                        <option value="جراحی عمومی">جراحی عمومی</option>
                        <option value="جراحی پلاستیک">جراحی پلاستیک</option>
                        <option value="جراحی کودکان">جراحی کودکان</option>
                        <option value="جراحی روده بزرگ (جراحی کولورکتال)">جراحی روده بزرگ (جراحی کولورکتال)</option>
                        <option value="جراحی قلب و عروق">جراحی قلب و عروق</option>
                        <option value="پیوند کبد">پیوند کبد</option>
                        <option value="جراحی عروق و تروما">جراحی عروق و تروما</option>
                        <option value="جراحی لاپاراسکوپی">جراحی لاپاراسکوپی</option>
                        <option value="جراحی سرطان">جراحی سرطان</option>
                        <option value="جراحی پستان">جراحی پستان</option>
                        <option value="جراحی قفسه سینه (جراحی توراکس)">جراحی قفسه سینه (جراحی توراکس)</option>
                        <option value="اورولوژی (جراحی مجاری ادراری و تناسلی)">اورولوژی (جراحی مجاری ادراری و تناسلی)</option>
                        <option value="جراحی لاپاروسکوپی کلیه، مجاری ادراری و تناسلی (اندویورولوژی)">جراحی لاپاروسکوپی کلیه، مجاری ادراری و تناسلی (اندویورولوژی)</option>
                        <option value="آنکولوژی">آنکولوژی</option>
                        <option value="جراحی ترمیمی اورولوژی">جراحی ترمیمی اورولوژی</option>
                        <option value="پیوند کلیه">پیوند کلیه</option>
                        <option value="سرطان شناسی دستگاه ادراری وتناسلی (اوروانکولوژی)">سرطان شناسی دستگاه ادراری وتناسلی (اوروانکولوژی)</option>
                        <option value="اورولوژی (جراحی مجاری ادراری و تناسلی) کودکان">اورولوژی (جراحی مجاری ادراری و تناسلی) کودکان</option>
                        <option value="جراحی کلیه، مجاری ادراری و تناسلی زنان (اورولوژی زنان)">جراحی کلیه، مجاری ادراری و تناسلی زنان (اورولوژی زنان)</option>
                        <option value="جراحی مغز و اعصاب">جراحی مغز و اعصاب</option>
                        <option value="جراحی دیسک و ستون فقرات">جراحی دیسک و ستون فقرات</option>
                        <option value="جراحی های فانکشنال و استریوتاکسی مغز و اعصاب">جراحی های فانکشنال و استریوتاکسی مغز و اعصاب</option>
                        <option value="مغز و اعصاب (نورولوژی)">مغز و اعصاب (نورولوژی)</option>
                        <option value="مغز و اعصاب (نورولوژی) کودکان">مغز و اعصاب (نورولوژی) کودکان</option>
                        <option value="سکته مغزی">سکته مغزی</option>
                        <option value="صرع">صرع</option>
                        <option value="طب خواب">طب خواب</option>
                        <option value="داخلی">داخلی</option>
                        <option value="گوارش و کبد">گوارش و کبد</option>
                        <option value="کلیه (نفرولوژی)">کلیه (نفرولوژی)</option>
                        <option value="روماتولوژی">روماتولوژی</option>
                        <option value="ریه">ریه</option>
                        <option value="خون و سرطان (هماتولوژی آنکولوژی)">خون و سرطان (هماتولوژی آنکولوژی)</option>
                        <option value="غدد و متابولیسم">غدد و متابولیسم</option>
                        <option value="رادیولوژی">رادیولوژی</option>
                        <option value="زنان و زایمان">زنان و زایمان</option>
                        <option value="نازایی و آی وی اف">نازایی و آی وی اف</option>
                        <option value="جراحی لاپاراسکوپی">جراحی لاپاراسکوپی</option>
                        <option value="طب مادر و جنین (پریناتولوژی)">طب مادر و جنین (پریناتولوژی)</option>
                        <option value="اورولوژی زنان و جراحی ترمیمی لگن">اورولوژی زنان و جراحی ترمیمی لگن</option>
                        <option value="آنکولوژی و سرطان زنان">آنکولوژی و سرطان زنان</option>
                        <option value="طب فیزیکی و توانبخشی">طب فیزیکی و توانبخشی</option>
                        <option value="قلب و عروق">قلب و عروق</option>
                        <option value="اینترونشنال کاردیولوژی">اینترونشنال کاردیولوژی</option>
                        <option value="بیماری های مادرزادی قلب و عروق در بزرگسالان">بیماری های مادرزادی قلب و عروق در بزرگسالان</option>
                        <option value="الکتروفیزیولوژی بالینی قلب">الکتروفیزیولوژی بالینی قلب</option>
                        <option value="کودکان و اطفال">کودکان و اطفال</option>
                        <option value="قلب کودکان">قلب کودکان</option>
                        <option value="کلیه (نفرولوژی) کودکان">کلیه (نفرولوژی) کودکان</option>
                        <option value="آسم و آلرژی">آسم و آلرژی</option>
                        <option value="عفونی کودکان">عفونی کودکان</option>
                        <option value="طب نوزادی">طب نوزادی</option>
                        <option value="گوارش و کبد کودکان">گوارش و کبد کودکان</option>
                        <option value="بیماری های مغز و اعصاب کودکان (نورولوژی کودکان)">بیماری های مغز و اعصاب کودکان (نورولوژی کودکان)</option>
                        <option value="خون و سرطان (هماتولوژی آنکولوژی) کودکان">خون و سرطان (هماتولوژی آنکولوژی) کودکان</option>
                        <option value="روماتولوژی (روماتیسم) کودکان">روماتولوژی (روماتیسم) کودکان</option>
                        <option value="ریه کودکان">ریه کودکان</option>
                        <option value="غدد و رشد کودکان">غدد و رشد کودکان</option>
                        <option value="گوش و حلق و بینی">گوش و حلق و بینی</option>
                        <option value="جراحی پلاستیک صورت">جراحی پلاستیک صورت</option>
                        <option value="جراحی بینی و سینوس (رینولوژی)">جراحی بینی و سینوس (رینولوژی)</option>
                        <option value="جراحی قاعده جمجمه">جراحی قاعده جمجمه</option>
                        <option value="اتولوژی و نورواتولوژی">اتولوژی و نورواتولوژی</option>
                        <option value="ژنتیک">ژنتیک</option>
                        <option value="پزشکی هسته‌ای">پزشکی هسته‌ای</option>
                        <option value="عمومی">عمومی</option>
                        <option value="پاتولوژی (آسیب شناسی)">پاتولوژی (آسیب شناسی)</option>
                        <option value="طب اورژانس">طب اورژانس</option>
                        <option value="طب کار">طب کار</option>
                        <option value="رادیوانکولوژی">رادیوانکولوژی</option>
                        <option value="مشاوره دارویی">مشاوره دارویی</option>
                        <option value="طب سنتی">طب سنتی</option>
                        <option value="طب سوزنی">طب سوزنی</option>
                        <option value="دامپزشکی">دامپزشکی</option>
                        <option value="گفتاردرمانی">گفتاردرمانی</option>
                        <option value="فیزیوتراپی">فیزیوتراپی</option>
                        <option value="بینایی سنجی">بینایی سنجی</option>
                        <option value="تغذیه">تغذیه</option>
                        <option value="ایمنی شناسی بالینی (ایمونولوژی)">ایمنی شناسی بالینی (ایمونولوژی)</option>
                        <option value="روانشناس">روانشناس</option>
                        <option value="مامایی">مامایی</option>
                        <option value="شنوایی سنجی">شنوایی سنجی</option>
                        <option value="کایروپراکتیک">کایروپراکتیک</option>
                        <option value="فیزیولوژی">فیزیولوژی</option>
                        <option value="ویروس شناسی">ویروس شناسی</option>
                        <option value="کار درمانی">کار درمانی</option>
                        <option value="ارتوپدی فنی">ارتوپدی فنی</option>
                        <option value="روانشناس کودک">روانشناس کودک</option>
                        <option value="دندانپزشک">دندانپزشک</option>
                        <option value="جراحی فک و صورت">جراحی فک و صورت</option>
                        <option value="پروتزهای دندانی (پروستودانتیکس)">پروتزهای دندانی (پروستودانتیکس)</option>
                        <option value="رادیولوژی دهان، فک و صورت">رادیولوژی دهان، فک و صورت</option>
                        <option value="دندانپزشکی کودکان">دندانپزشکی کودکان</option>
                        <option value="ارتودنسی">ارتودنسی</option>
                        <option value="درمان ریشه (اندودانتیکس)">درمان ریشه (اندودانتیکس)</option>
                        <option value="دندانپزشکی ترمیمی">دندانپزشکی ترمیمی</option>
                        <option value="جراحی لثه">جراحی لثه</option>
                    </select>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-success">ذخیره</button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($reviews): ?>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h5 class="card-title mb-3">نظرات بیماران</h5>
            <ul class="list-group">
                <?php foreach ($reviews as $rv): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($rv['patient_name']) ?></strong>
                    - <?= str_repeat('★', $rv['rating']) ?>
                    <p class="mb-0 small mt-1"><?= htmlspecialchars($rv['comment']) ?></p>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
