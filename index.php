<?php
require_once 'includes/database.php';
$latest_articles = get_rows(
    "SELECT id, title, content, image_url, created_at FROM articles ORDER BY created_at DESC LIMIT 3"
);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سیستم مدیریت سلامت</title>
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <!-- Vazir Font -->
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
    <!-- Header Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">سیستم مدیریت سلامت</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#about">درباره ما</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">خدمات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="articles/articles.php">مقالات سلامت</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">تماس با ما</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">ورود</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section text-center py-5">
        <div class="container">
            <h1 class="display-4 mb-4">به سیستم مدیریت سلامت خوش آمدید</h1>
            <p class="lead mb-4">مراقبت سلامت هوشمند برای آینده‌ای سالم‌تر</p>
            <a href="login.php" class="btn btn-primary btn-lg">شروع کنید</a>
        </div>
    </div>

    <!-- About Section -->
    <section id="about" class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">درباره ما</h2>
            <div class="row">
                <div class="col-md-6">
                    <p>سیستم مدیریت سلامت ما با هدف ارائه خدمات بهداشتی و درمانی با کیفیت و قابل دسترس برای همه طراحی شده است.</p>
                </div>
                <div class="col-md-6">
                    <p>با استفاده از فناوری‌های پیشرفته، ما ارتباط بین بیماران و پزشکان را تسهیل می‌کنیم.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Articles Section -->
    <section id="latest-articles" class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">آخرین مقالات</h2>
            <div class="row g-4">
                <?php foreach ($latest_articles as $article): ?>
                <div class="col-md-4">
                    <div class="card h-100">
                        <?php if ($article['image_url']): ?>
                        <img src="<?php echo htmlspecialchars($article['image_url']); ?>"
                             class="card-img-top"
                             alt="<?php echo htmlspecialchars($article['title']); ?>"
                             style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($article['title']); ?></h5>
                            <p class="card-text text-muted">
                                <?php
                                $excerpt = mb_substr(strip_tags($article['content']), 0, 100, 'UTF-8');
                                echo htmlspecialchars($excerpt) . '...';
                                ?>
                            </p>
                            <div class="mt-auto">
                                <a href="articles/article_details.php?id=<?php echo $article['id']; ?>" class="btn btn-primary">
                                    ادامه مطلب
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="articles/articles.php" class="btn btn-outline-primary">مشاهده همه مقالات</a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-4">خدمات ما</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">پایش سلامت</h5>
                            <p class="card-text">ثبت و پیگیری اطلاعات سلامت روزانه</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">مشاوره آنلاین</h5>
                            <p class="card-text">ارتباط مستقیم با پزشکان متخصص</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">مقالات سلامت</h5>
                            <p class="card-text">دسترسی به مطالب آموزشی و بهداشتی</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>تماس با ما</h5>
                    <p>ایمیل: info@health.com</p>
                    <p>تلفن: ۰۲۱-۱۲۳۴۵۶۷۸</p>
                </div>
                <div class="col-md-4">
                    <h5>لینک‌های مفید</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light">حریم خصوصی</a></li>
                        <li><a href="#" class="text-light">شرایط استفاده</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>شبکه‌های اجتماعی</h5>
                    <div class="social-links">
                        <a href="#" class="text-light me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light me-2"><i class="fab fa-telegram"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-code.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
