<?php
ob_start();
session_start();
include "includes/config.php";

// Ambil ID dari URL
$page_id = null;
$request_uri = $_SERVER['REQUEST_URI'];
if (preg_match('/page\/(\d+)/', $request_uri, $matches)) {
    $page_id = (int)$matches[1];
} elseif (isset($_GET['id'])) {
    $page_id = (int)$_GET['id'];
}

if (!$page_id) {
    header("Location: main.php");
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM pages WHERE page_id = $page_id AND status = 'Publish'");
$page = mysqli_fetch_assoc($query);

if (!$page) {
    header("Location: main.php");
    exit;
}

$page_title = $page['page_title'];
$base_url = "/senapenmas-2026/frontend/";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - SENAPENMAS 2026</title>

    <link rel="stylesheet" type="text/css" href="<?php echo $base_url; ?>css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url; ?>css/foto.css">
    
    <style>
        /* --- STYLE HEADER --- */
        .page-header {
            background: linear-gradient(135deg, #A01626 0%, #7a1a00 100%);
            padding: 40px 0;
            margin-bottom: 40px;
        }
        
        .page-header h1 {
            color: white;
            font-weight: bold;
            margin: 0;
            font-size: 1.75rem;
        }
        
        /* Breadcrumb */
        .header-breadcrumb {
            font-size: 1rem;
            color: rgb(255, 255, 255);
        }
        
        .header-breadcrumb a {
            color: #ffc107;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .header-breadcrumb a:hover {
            color: #fff;
        }
        
        .header-breadcrumb .separator {
            margin: 0 8px;
            color: rgba(255, 255, 255, 0.5);
        }

        @media (max-width: 576px) {
            .page-header .container {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }
        }

        /* --- STYLE KONTEN --- */
        .page-cover {
            width: 100%;
            height: auto;
            max-height: 500px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        /* UPDATED: Page Meta (Tanggal) di Kanan */
        .page-meta {
            color: #666;
            font-size: 0.9rem;
            margin-top: 30px;      /* Jarak dari konten atas */
            margin-bottom: 20px;
            padding-top: 15px;     /* Jarak tulisan ke garis */
            font-style: italic;
            display: block;
            text-align: right;     /* <--- INI KUNCINYA (Rata Kanan) */
            border-top: 1px solid #eee; /* Opsional: Garis pemisah tipis */
        }

        .page-content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
            text-align: justify;
        }
        
        .page-content h1, .page-content h2, .page-content h3, 
        .page-content h4, .page-content h5, .page-content h6 {
            color: #ac0404;
            margin-top: 30px;
            margin-bottom: 15px;
            font-weight: bold;
        }
        
        .page-content p { margin-bottom: 20px; }
        .page-content ul, .page-content ol { margin-bottom: 20px; padding-left: 30px; }
        .page-content img { max-width: 100%; height: auto; border-radius: 8px; margin: 20px 0; }
        .page-content table { width: 100%; margin: 20px 0; }
    </style>
</head>
<body>

    <?php include("includes/frontmenu.php"); ?>
    
    <div class="page-header">
        <div class="container d-flex justify-content-between align-items-center">
            <h1><?php echo htmlspecialchars($page['page_title']); ?></h1>
            
            <div class="header-breadcrumb">
                <a href="<?php echo $base_url; ?>main.php">Home</a>
                <span class="separator">/</span>
                <span class="current"><?php echo htmlspecialchars($page['page_title']); ?></span>
            </div>
        </div>
    </div>
    
    <div class="container mb-5">
        <div class="row">
            <div class="col-lg-10 offset-lg-1">
                
                <?php if (!empty($page['page_cover']) && file_exists('images/pages/' . $page['page_cover'])): ?>
                    <img src="<?php echo $base_url; ?>images/pages/<?php echo $page['page_cover']; ?>" 
                         alt="<?php echo htmlspecialchars($page['page_title']); ?>" 
                         class="page-cover">
                <?php endif; ?>
                
                <div class="page-content">
                    <?php echo $page['page_content']; ?>
                </div>

                <div class="page-meta">
                    <i class="bi bi-calendar3"></i> Published: <?php echo date('F d, Y', strtotime($page['publish_date'])); ?>
                </div>

            </div>
        </div>
    </div>
    
    <?php include("includes/frontfooter.php"); ?>

<script type="text/javascript" src="<?php echo $base_url; ?>js/bootstrap.bundle.min.js"></script>
</body>
</html>