<?php
ob_start();
session_start();
include "includes/config.php";

// Ambil ID dari URL
$event_id = null;
$request_uri = $_SERVER['REQUEST_URI'];
if (preg_match('/event\/(\d+)/', $request_uri, $matches)) {
    $event_id = (int)$matches[1];
} elseif (isset($_GET['id'])) {
    $event_id = (int)$_GET['id'];
}

if (!$event_id) {
    header("Location: main.php");
    exit;
}

// Query untuk mendapatkan data event yang sudah publish di past conferences
$query = mysqli_query($conn, "
    SELECT e.*, pc.status as pc_status, pc.publish_date as pc_publish_date
    FROM events e
    JOIN past_conferences pc ON e.event_id = pc.event_id
    WHERE e.event_id = $event_id AND pc.status = 'Publish'
");

$event = mysqli_fetch_assoc($query);

if (!$event) {
    header("Location: main.php");
    exit;
}

// Ambil schedule untuk event ini
$schedules = [];
$schedule_query = mysqli_query($conn, "SELECT * FROM schedules WHERE event_id = $event_id ORDER BY date_new ASC");
while($schedule_row = mysqli_fetch_assoc($schedule_query)) {
    $schedules[] = $schedule_row;
}

// Ambil downloadable files untuk event ini yang statusnya Publish
$downloadable_files = [];
$files_query = mysqli_query($conn, "SELECT * FROM downloadablefiles WHERE event_id = $event_id AND status = 'Publish' ORDER BY file_id DESC");
while($file_row = mysqli_fetch_assoc($files_query)) {
    $downloadable_files[] = $file_row;
}

$page_title = $event['event_name'];
$base_url = "/senapenmas-2026/frontend/";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - SENAPENMAS 2026</title>

    <link rel="stylesheet" type="text/css" href="<?php echo $base_url; ?>css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url; ?>css/foto.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* --- STYLE HEADER (dari page.php) --- */
        .page-header {
            background: linear-gradient(135deg, #A01626 0%, #7a1a00 100%);
            padding: 40px 0;
            margin-bottom: 60px;
        }
        
        .page-header h1 {
            color: white;
            font-weight: bold;
            margin: 0;
            font-size: 1.75rem;
            display: inline-block;
        }
        
        .page-header .event-year-badge {
            display: inline-block;
            background: #ffc107;
            color: #333;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-left: 10px;
            vertical-align: middle;
        }
        
        /* Breadcrumb (dari page.php) */
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
            
            .page-header h1 {
                font-size: 1.5rem;
            }
            
            .page-header .event-year-badge {
                display: block;
                margin: 10px auto 0;
                width: fit-content;
            }
        }

        /* --- AREA DESKRIPSI UTAMA (SAMA DENGAN MAIN.PHP) --- */
        .event-description {
            line-height: 1.8;
            text-align: justify;
            overflow-wrap: break-word; /* Mencegah teks panjang merusak layout */
        }

        /* 1. Responsif Gambar (Wajib) */
        .event-description img {
            max-width: 100% !important;
            height: auto !important;
        }

        /* 2. Wrapper Widget CKEditor (Wajib jika pakai Image2) */
        .event-description .cke_widget_wrapper {
            max-width: 100% !important;
        }

        /* --- 3. CLASS ALIGNMENT (SINKRONISASI DENGAN ADMIN) --- */
        
        /* Rata Kiri: Gambar di kiri, teks tetap di bawah (tidak mengalir ke samping) */
        .event-description .image-left {
            display: block;
            margin-right: auto;
            margin-bottom: 10px;
            text-align: left;
            clear: both;
        }

        /* Rata Kanan: Gambar di kanan, teks tetap di bawah (tidak mengalir ke samping) */
        .event-description .image-right {
            display: block;
            margin-left: auto;
            margin-bottom: 10px;
            text-align: right;
            clear: both;
        }

        /* Rata Tengah: Gambar sendirian di tengah baris */
        .event-description .image-center {
            display: block;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 10px;
            text-align: center;
            clear: both;
        }

        /* 1. Mengurangi jarak antar paragraf di dalam deskripsi */
        .event-description p {
            margin-bottom: 0.5rem !important; /* Default Bootstrap 1rem, kita kurangi jadi setengahnya */
        }

        /* 2. Mengurangi jarak judul (seperti "Plenary Speaker") agar tidak terlalu jauh dari isinya */
        .event-description h1, 
        .event-description h2, 
        .event-description h3, 
        .event-description h4 {
            margin-top: 1.5rem !important;    /* Jarak dari elemen atasnya */
            margin-bottom: 0.5rem !important; /* Jarak ke elemen bawahnya (isi) */
            line-height: 1.2;
        }

        /* 3. Memastikan list (titik dua) juga rapi */
        .event-description ul, 
        .event-description ol {
            margin-bottom: 0.5rem !important;
        }

        /* 4. Khusus baris kosong yang sering muncul dari CKEditor */
        .event-description p:empty {
            display: none;
        }

        /* --- END CSS DARI MAIN.PHP --- */

        .schedule-item:last-child {
            border-bottom: none !important;
            padding-bottom: 0 !important;
            margin-bottom: 0 !important;
        }
        
        .download-file-item {
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .download-file-item:last-child {
            border-bottom: none;
        }
        
        .download-file-item a {
            color: #495057;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .download-file-item a:hover {
            color: #7a1a00;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <?php include("includes/frontmenu.php"); ?>
    
    <!-- Page Header (dari page.php) -->
    <div class="page-header">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <h1><?php echo htmlspecialchars($event['event_name']); ?></h1>
            </div>
            
            <div class="header-breadcrumb">
                <a href="<?php echo $base_url; ?>main.php">Home</a>
                <span class="separator">/</span>
                <span class="current"><?php echo htmlspecialchars($event['event_name']); ?></span>
            </div>
        </div>
    </div>
    
    <!-- Content Area (BODY DARI MAIN.PHP) -->
    <div class="container mt-5">
        <section class="mb-5">
            <div class="row">
    
                <!-- Left Column: Description (dari main.php) -->
                <div class="col-lg-7 mb-4">
                        <div class="mb-4">
                        <div style="border-top: 4px solid #FFC107; width: 80px; margin-bottom: 10px;"></div>
                        <div class="event-description" style="font-weight: 800; font-size: 2.5rem; line-height: 1.2; margin-bottom: 0.5rem; color: #ac0404;">
                            <?= htmlspecialchars($event['event_name']) ?>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h3 class="fw-bold text-dark">Topic</h3>
                        <p class="text-muted fs-5"><?= htmlspecialchars($event['event_topic']) ?></p>
                    </div>
                    
                    <div class="mb-4">
                        <div class="event-description">
                            <?= $event['description'] ?>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Poster, Schedule, Files (dari main.php) -->
                <div class="col-lg-4 offset-lg-1">
                    
                    <!-- Poster Section -->
                    <div class="mb-4">
                        <div style="border-top: 4px solid #FFC107; width: 80px; margin-bottom: 10px;"></div>
                        <h3 class="fw-bold text-dark mb-3" style="color: #495057;">POSTER</h3>
                        <div class="mb-3">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#posterModal" style="display: block; cursor: pointer;">
                            <img src="<?php echo $base_url; ?>../admin/images/events/<?= $event['poster'] ?>" 
                                class="img-fluid rounded shadow" 
                                alt="<?= htmlspecialchars($event['event_name']) ?>"
                                style="width: 100%; height: auto; object-fit: cover; display: block;"
                                onerror="this.onerror=null; this.src='<?php echo $base_url; ?>../admin/images/no-image.jpg';">
                            </a>
                            <p class="text-muted small mt-2">*Klik gambar untuk memperbesar</p>
                        </div>
                        
                        <div class="text-center">
                            <a href="<?php echo $base_url; ?>../admin/images/events/<?= $event['poster'] ?>" 
                            download="<?= htmlspecialchars($event['event_name']) ?>_Poster.<?= pathinfo($event['poster'], PATHINFO_EXTENSION) ?>"
                            class="btn btn-secondary py-2 px-4"
                            style="background-color: #6c757d; border: none; border-radius: 5px; font-weight: 500;">
                                Download Poster
                            </a>
                        </div>
                    </div>
                    
                    <br>
                    
                    <!-- Schedule Section -->
                    <div class="mb-4">
                        <div style="border-top: 4px solid #FFC107; width: 80px; margin-bottom: 20px;"></div>
                        
                        <h3 class="fw-bold text-dark mb-4">SCHEDULE</h3>
                        
                        <div class="">
                            <?php if(!empty($schedules)): ?>
                                <div class="schedule-list">
                                    <?php foreach($schedules as $schedule): ?>
                                        <div class="schedule-item mb-4">
                                            
                                            <h5 class="fw-bold text-dark mb-1">
                                                <?= htmlspecialchars($schedule['description']) ?>
                                            </h5>
                                            
                                            <div class="text-secondary" style="font-style: italic;">
                                                
                                                <?php if(!empty($schedule['date_old'])): ?>
                                                    <div class="text-decoration-line-through opacity-50 small lh-1 mb-1">
                                                        <?= htmlspecialchars($schedule['date_old']) ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="lh-1">
                                                    <?= date('F jS Y', strtotime($schedule['date_new'])) ?>
                                                </div>

                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted small mb-0">Schedule information not available</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <br>
                    
                    <!-- Downloadable Files Section -->
                    <div class="mb-4">
                        <div style="border-top: 4px solid #FFC107; width: 80px; margin-bottom: 20px;"></div>
                        
                        <h3 class="fw-bold text-dark mb-4">DOWNLOADABLE FILES</h3>
                        
                        <div class="">
                            <?php if(!empty($downloadable_files)): ?>
                                <div class="download-files-list">
                                    <?php foreach($downloadable_files as $file): ?>
                                        <div class="download-file-item">
                                            <a href="<?php echo $base_url; ?>../admin/download.php?id=<?= $file['file_id'] ?>" class="d-flex align-items-center">
                                                <i class="bi bi-file-earmark-arrow-down"></i>
                                                <span><?= htmlspecialchars($file['file_name']) ?></span>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted small mb-0">Files not available</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                </div>
            </div>
        </section>
    </div>
    
    <?php include("includes/frontfooter.php"); ?>

    <!-- Modal untuk Zoom Poster (SAMA DENGAN MAIN.PHP) -->
    <div class="modal fade" id="posterModal" tabindex="-1" aria-labelledby="posterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
            
            <div class="modal-header border-0">
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0 text-center">
                <img src="<?php echo $base_url; ?>../admin/images/events/<?= $event['poster'] ?>" 
                    class="img-fluid rounded shadow" 
                    style="max-height: 300vh; width: auto; max-width: 100%;" 
                    alt="Full Poster">
            </div>

            </div>
        </div>
    </div>

</body>

<script type="text/javascript" src="<?php echo $base_url; ?>js/bootstrap.bundle.min.js"></script>
</html>