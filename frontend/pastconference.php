<?php
ob_start();
session_start();
include "includes/config.php";

// 1. TANGKAP ID DARI URL
// Jika user klik menu, linknya adalah pastconference.php?id=5
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 2. QUERY DATA BERDASARKAN ID TERSEBUT
$query_event = mysqli_query($conn, "SELECT * FROM events WHERE event_id = '$event_id' AND status = 1");
$current_event = mysqli_fetch_assoc($query_event);

// Jika ID tidak ditemukan atau salah, redirect ke home
if (!$current_event) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($current_event['event_name']) ?> - Archive</title>

    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/foto.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* Style sama persis dengan index.php */
        .event-description { line-height: 1.8; text-align: justify; }
        .schedule-item:last-child { border-bottom: none !important; padding-bottom: 0 !important; margin-bottom: 0 !important; }
        .download-file-item { padding: 8px 0; border-bottom: 1px solid #e9ecef; }
        .download-file-item:last-child { border-bottom: none; }
        .download-file-item a { color: #495057; text-decoration: none; display: flex; align-items: center; gap: 8px; }
        .download-file-item a:hover { color: #7a1a00; font-weight: 500; }
    </style>
</head>
<body>

    <?php include("includes/frontmenu.php"); ?>

    <div class="container-fluid p-0 mb-5">
         <div style="background: #f8f9fa; padding: 60px 0; text-align: center; border-bottom: 5px solid #FFC107;">
            <h1 class="display-4 fw-bold">Conference Archive</h1>
            <p class="lead">Arsip kegiatan dan konferensi terdahulu.</p>
        </div>
    </div>

    <div class="container mt-5">
        
        <?php
        // Ambil schedule untuk event yang dipilih
        $schedules = [];
        $schedule_query = mysqli_query($conn, "SELECT * FROM schedules WHERE event_id = " . $current_event['event_id'] . " ORDER BY date_new ASC");
        while($schedule_row = mysqli_fetch_assoc($schedule_query)) {
            $schedules[] = $schedule_row;
        }
        
        // Ambil downloadable files untuk event yang dipilih
        $downloadable_files = [];
        $files_query = mysqli_query($conn, "SELECT * FROM downloadablefiles WHERE event_id = " . $current_event['event_id'] . " AND status = 'Publish' ORDER BY file_id DESC");
        while($file_row = mysqli_fetch_assoc($files_query)) {
            $downloadable_files[] = $file_row;
        }
        ?>
        
        <section class="mb-5">
            <div class="row">
    
                <div class="col-lg-7 mb-4">
                    <div class="mb-4">
                        <div style="border-top: 4px solid #FFC107; width: 80px; margin-bottom: 10px;"></div>
                        <h2 class="fw-bold text-danger"><?= htmlspecialchars($current_event['event_name']) ?></h2>
                    </div>
                    
                    <div class="mb-4">
                        <h3 class="fw-bold text-dark">Topic</h3>
                        <p class="text-muted fs-5"><?= htmlspecialchars($current_event['event_topic']) ?></p>
                    </div>
                    
                    <div class="mb-4">
                        <div class="event-description">
                            <?= $current_event['description'] ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 offset-lg-1">
                    <div class="mb-4">
                        <div style="border-top: 4px solid #FFC107; width: 80px; margin-bottom: 10px;"></div>
                        <h3 class="fw-bold text-dark mb-3" style="color: #495057;">POSTER</h3>
                        <div class="mb-3">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#posterModal" style="display: block; cursor: pointer;">
                            <img src="../admin/images/events/<?= $current_event['poster'] ?>" 
                                class="img-fluid rounded shadow" 
                                alt="<?= htmlspecialchars($current_event['event_name']) ?>"
                                style="width: 100%; height: auto; object-fit: cover; display: block;"
                                onerror="this.onerror=null; this.src='../admin/images/no-image.jpg';">
                            </a>
                        </div>
                        <div class="text-center">
                            <a href="../admin/images/events/<?= $current_event['poster'] ?>" 
                            download="<?= htmlspecialchars($current_event['event_name']) ?>_Poster.<?= pathinfo($current_event['poster'], PATHINFO_EXTENSION) ?>"
                            class="btn btn-secondary py-2 px-4"
                            style="background-color: #6c757d; border: none; border-radius: 5px; font-weight: 500;">
                                Download Poster
                            </a>
                        </div>
                    </div>
                    
                    <br>
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
                                                <div class="lh-1">
                                                    <?= date('F jS Y', strtotime($schedule['date_new'])) ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted small mb-0">No schedule data available.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <br>
                    <div class="mb-4">
                        <div style="border-top: 4px solid #FFC107; width: 80px; margin-bottom: 20px;"></div>
                        <h3 class="fw-bold text-dark mb-4">DOCUMENTS</h3>
                        <div class="">
                            <?php if(!empty($downloadable_files)): ?>
                                <div class="download-files-list">
                                    <?php foreach($downloadable_files as $file): ?>
                                        <div class="download-file-item">
                                            <a href="../admin/download.php?id=<?= $file['file_id'] ?>" class="d-flex align-items-center">
                                                <i class="bi bi-file-earmark-arrow-down"></i>
                                                <span><?= htmlspecialchars($file['file_name']) ?></span>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted small mb-0">No files available.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
    <?php include("includes/frontfooter.php"); ?>

    <div class="modal fade" id="posterModal" tabindex="-1" aria-labelledby="posterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
            <div class="modal-header border-0">
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center">
                <img src="../admin/images/events/<?= $current_event['poster'] ?>" 
                    class="img-fluid rounded shadow" 
                    style="max-height: 300vh; width: auto; max-width: 100%;" 
                    alt="Full Poster">
            </div>
            </div>
        </div>
    </div>

</body>
<script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
</html>