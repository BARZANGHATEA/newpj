<?php
session_start();
require_once '../includes/database.php';

// Get article ID from URL
$article_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

if (!$article_id) {
    header('Location: articles.php');
    exit();
}

// Get article details
$article = get_row(
    "SELECT * FROM articles WHERE id = ?",
    [$article_id],
    "i"
);

if (!$article) {
    header('Location: articles.php');
    exit();
}

// Get related articles (same day or similar titles)
$related_articles = get_rows(
    "SELECT * FROM articles 
     WHERE id != ? 
     AND DATE(created_at) = DATE(?) 
     ORDER BY created_at DESC 
     LIMIT 3",
    [$article_id, $article['created_at']],
    "is"
);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> - سیستم مدیریت سلامت</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        .article-content {
            line-height: 1.8;
            text-align: justify;
        }
        .article-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 20px 0;
        }
        .article-header {
            background: linear-gradient(135deg, #0396FF 0%, #ABDCFF 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
        }
        .article-meta {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.9);
        }
        .related-article {
            transition: transform 0.3s ease;
        }
        .related-article:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="../index.php">سیستم مدیریت سلامت</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">صفحه اصلی</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="articles.php">مقالات</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../dashboard/<?php echo $_SESSION['role']; ?>.php">داشبورد</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../login.php">ورود</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Article Header -->
    <header class="article-header">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../index.php" class="text-white">صفحه اصلی</a></li>
                    <li class="breadcrumb-item"><a href="articles.php" class="text-white">مقالات</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?php echo htmlspecialchars($article['title']); ?>
                    </li>
                </ol>
            </nav>
            <h1 class="display-4 mb-3"><?php echo htmlspecialchars($article['title']); ?></h1>
            <div class="article-meta">
                <i class="far fa-calendar-alt"></i>
                <?php echo date('Y/m/d', strtotime($article['created_at'])); ?>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row">
            <!-- Article Content -->
            <div class="col-lg-8">
                <?php if ($article['image_url']): ?>
                <img src="<?php echo htmlspecialchars($article['image_url']); ?>" 
                     class="img-fluid rounded mb-4" 
                     alt="<?php echo htmlspecialchars($article['title']); ?>">
                <?php endif; ?>
                
                <article class="article-content">
                    <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                </article>

                <!-- Social Share Buttons -->
                <div class="mt-5 pt-4 border-top">
                    <h5 class="mb-3">اشتراک‌گذاری مقاله:</h5>
                    <a href="https://telegram.me/share/url?url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($article['title']); ?>" 
                       class="btn btn-outline-primary me-2" 
                       target="_blank">
                        <i class="fab fa-telegram"></i> تلگرام
                    </a>
                    <a href="https://wa.me/?text=<?php echo urlencode($article['title'] . ' - ' . $_SERVER['REQUEST_URI']); ?>" 
                       class="btn btn-outline-success me-2" 
                       target="_blank">
                        <i class="fab fa-whatsapp"></i> واتساپ
                    </a>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <?php if (!empty($related_articles)): ?>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">مقالات مرتبط</h5>
                        <?php foreach ($related_articles as $related): ?>
                        <a href="article_details.php?id=<?php echo $related['id']; ?>" 
                           class="text-decoration-none">
                            <div class="card mb-3 related-article">
                                <?php if ($related['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($related['image_url']); ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($related['title']); ?>"
                                     style="height: 150px; object-fit: cover;">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h6 class="card-title text-dark">
                                        <?php echo htmlspecialchars($related['title']); ?>
                                    </h6>
                                    <small class="text-muted">
                                        <?php echo date('Y/m/d', strtotime($related['created_at'])); ?>
                                    </small>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>درباره ما</h5>
                    <p>سیستم مدیریت سلامت، ارائه‌دهنده خدمات پزشکی و سلامت آنلاین</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h5>تماس با ما</h5>
                    <p>ایمیل: info@health.com</p>
                    <p>تلفن: ۰۲۱-۱۲۳۴۵۶۷۸</p>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p class="mb-0">تمامی حقوق محفوظ است &copy; <?php echo date('Y'); ?></p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
