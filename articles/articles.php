<?php
session_start();
require_once '../includes/database.php';

// Get all published articles with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 6;
$offset = ($page - 1) * $per_page;

// Get total articles count
$total_articles = get_row("SELECT COUNT(*) as count FROM articles")['count'];
$total_pages = ceil($total_articles / $per_page);

// Get articles for current page
$articles = get_rows(
    "SELECT * FROM articles ORDER BY created_at DESC LIMIT ? OFFSET ?",
    [$per_page, $offset],
    "ii"
);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مقالات سلامت - سیستم مدیریت سلامت</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="bg-light">
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

    <!-- Main Content -->
    <div class="container py-5">
        <h1 class="text-center mb-5">مقالات سلامت</h1>

        <!-- Articles Grid -->
        <div class="row g-4">
            <?php foreach ($articles as $article): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <?php if ($article['image_url']): ?>
                    <img src="<?php echo htmlspecialchars($article['image_url']); ?>" 
                         class="card-img-top" 
                         alt="<?php echo htmlspecialchars($article['title']); ?>"
                         style="height: 200px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($article['title']); ?></h5>
                        <p class="card-text text-muted">
                            <?php 
                            // Show excerpt of content
                            $excerpt = mb_substr(strip_tags($article['content']), 0, 150, 'UTF-8');
                            echo htmlspecialchars($excerpt) . '...';
                            ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="article_details.php?id=<?php echo $article['id']; ?>" 
                               class="btn btn-primary">
                                ادامه مطلب
                            </a>
                            <small class="text-muted">
                                <?php echo date('Y/m/d', strtotime($article['created_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <nav aria-label="صفحه‌بندی مقالات" class="mt-5">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
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
