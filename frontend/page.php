<?php
ob_start();
session_start();
include "includes/config.php";

// Ambil ID dari URL
// Format URL: page/8/mainpage atau page.php?id=8
$page_id = null;

// Cek apakah menggunakan SEO-friendly URL
$request_uri = $_SERVER['REQUEST_URI'];
if (preg_match('/page\/(\d+)/', $request_uri, $matches)) {
    $page_id = (int)$matches[1];
} elseif (isset($_GET['id'])) {
    // Fallback untuk format lama
    $page_id = (int)$_GET['id'];
}

// Jika tidak ada ID, redirect ke home
if (!$page_id) {
    header("Location: main.php");
    exit;
}

// Ambil data page dari database
$query = mysqli_query($conn, "SELECT * FROM pages WHERE page_id = $page_id AND status = 'Publish'");
$page = mysqli_fetch_assoc($query);

// Jika page tidak ditemukan atau tidak publish
if (!$page) {
    header("Location: main.php");
    exit;
}

// Set page title
$page_title = $page['page_title'];

// Tentukan base URL untuk assets
$base_url = "/senapenmas-2026/frontend/";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - TICASH</title>

    <!-- Bootstrap CSS dengan absolute path -->
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url; ?>css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url; ?>css/foto.css">
    
    <style>
        .page-header {
            background: linear-gradient(135deg, #8B0000 0%, #DC143C 100%);
            color: white;
            padding: 60px 0 40px;
            margin-bottom: 40px;
        }
        
        .page-header h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .page-meta {
            color: rgba(255,255,255,0.9);
            font-size: 0.95rem;
        }
        
        .page-cover {
            width: 100%;
            height: auto;
            max-height: 500px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .page-content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
            text-align: justify;
        }
        
        .page-content h1, 
        .page-content h2, 
        .page-content h3, 
        .page-content h4, 
        .page-content h5, 
        .page-content h6 {
            color: #8B0000;
            margin-top: 30px;
            margin-bottom: 15px;
            font-weight: bold;
        }
        
        .page-content p {
            margin-bottom: 20px;
        }
        
        .page-content ul, 
        .page-content ol {
            margin-bottom: 20px;
            padding-left: 30px;
        }
        
        .page-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .page-content table {
            width: 100%;
            margin: 20px 0;
        }
        
        .back-button {
            background: #8B0000;
            color: white;
            padding: 10px 25px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 30px;
            transition: background 0.3s;
        }
        
        .back-button:hover {
            background: #DC143C;
            color: white;
        }
    </style>
</head>
<body>

    <!-- Menu Navigation -->
    <?php include("includes/frontmenu.php"); ?>
    
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1><?php echo htmlspecialchars($page['page_title']); ?></h1>
            <div class="page-meta">
                <i class="bi bi-calendar"></i> Published: <?php echo date('F d, Y', strtotime($page['publish_date'])); ?>
            </div>
        </div>
    </div>
    
    <!-- Page Content -->
    <div class="container mb-5">
        <div class="row">
            <div class="col-lg-10 offset-lg-1">
                <a href="<?php echo $base_url; ?>main.php" class="back-button">
                    <i class="bi bi-arrow-left"></i> Back to Home
                </a>
                
                <?php if (!empty($page['page_cover']) && file_exists('images/pages/' . $page['page_cover'])): ?>
                    <img src="<?php echo $base_url; ?>images/pages/<?php echo $page['page_cover']; ?>" 
                         alt="<?php echo htmlspecialchars($page['page_title']); ?>" 
                         class="page-cover">
                <?php endif; ?>
                
                <div class="page-content">
                    <?php echo $page['page_content']; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <?php include("includes/frontfooter.php"); ?>

<!-- Bootstrap JS dengan absolute path -->
<script type="text/javascript" src="<?php echo $base_url; ?>js/bootstrap.bundle.min.js"></script>
</body>
</html>