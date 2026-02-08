<?php
ob_start();
session_start();
include "includes/config.php";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENAPENMAS 2026</title>

    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/foto.css">
    
    <style>
        .event-card {
            transition: transform 0.3s;
        }
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .carousel-item img {
            height: 600px;
            object-fit: cover;
        }
        .event-description {
            line-height: 1.8;
            text-align: justify;
        }
    </style>
</head>
<body>

    <!-- Menu Navigation -->
    <?php include("includes/frontmenu.php"); ?>

    <!-- Slider Section -->
    <?php
    // Ambil slider yang aktif dari database, urutkan berdasarkan order_number
    $slider_query = mysqli_query($conn, "SELECT * FROM sliders WHERE status = 'Active' ORDER BY order_number ASC");
    $sliders = [];
    while($slider = mysqli_fetch_assoc($slider_query)) {
        $sliders[] = $slider;
    }
    $slider_count = count($sliders);
    ?>
    
    <?php if($slider_count > 0): ?>
    <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <?php for($i = 0; $i < $slider_count; $i++): ?>
                <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="<?= $i ?>" 
                        class="<?= $i == 0 ? 'active' : '' ?>" aria-current="<?= $i == 0 ? 'true' : 'false' ?>" 
                        aria-label="Slide <?= $i + 1 ?>"></button>
            <?php endfor; ?>
        </div>
        <div class="carousel-inner">
            <?php foreach($sliders as $index => $slider): ?>
                <div class="carousel-item <?= $index == 0 ? 'active' : '' ?>">
                    <?php if(!empty($slider['slider_link'])): ?>
                        <a href="<?= htmlspecialchars($slider['slider_link']) ?>" target="_blank">
                            <img src="images/sliders/<?= $slider['slider_image'] ?>" class="d-block w-100" alt="<?= htmlspecialchars($slider['slider_title']) ?>">
                        </a>
                    <?php else: ?>
                        <img src="images/sliders/<?= $slider['slider_image'] ?>" class="d-block w-100" alt="<?= htmlspecialchars($slider['slider_title']) ?>">
                    <?php endif; ?>
                    <div class="carousel-caption d-none d-md-block">
                        <h3><?= htmlspecialchars($slider['slider_title']) ?></h3>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    <?php else: ?>
        <!-- Default slider jika belum ada data di database -->
        <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="images/banner1.jpg" class="d-block w-100" alt="Banner 1">
                </div>
            </div>
        </div>
    <?php endif; ?>
    <!-- End Slider Section -->

    <!-- Main Content -->
    <div class="container mt-5">
        
        <?php
        // Cek apakah ada event yang di-set sebagai homepage
        $homepage_event_query = mysqli_query($conn, "SELECT * FROM events WHERE homepage = 'Yes' AND status = 1 ORDER BY event_id DESC LIMIT 1");
        $homepage_event = mysqli_fetch_assoc($homepage_event_query);
        ?>
        
        <?php if($homepage_event): ?>
        <section class="mb-5">
            <div class="row gx-5">
                
                <div class="col-lg-8 mb-4">
                    <div class="mb-4">
                        <div style="border-top: 4px solid #FFC107; width: 80px; margin-bottom: 10px;"></div>
                        <h2 class="fw-bold text-danger"><?= htmlspecialchars($homepage_event['event_name']) ?></h2>
                    </div>
                    
                    <div class="mb-4">
                        <h3 class="fw-bold text-dark">Topic</h3>
                        <p class="text-muted fs-5"><?= htmlspecialchars($homepage_event['event_topic']) ?></p>
                    </div>
                    
                    <div class="mb-4">
                        <div class="event-description">
                            <?= $homepage_event['description'] ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="mb-4">
                        <div style="border-top: 4px solid #FFC107; width: 80px; margin-bottom: 10px;"></div>
                        <h3 class="fw-bold text-dark mb-3" style="color: #495057;">POSTER</h3>
                        
                        <div class="mb-3 text-start">
                            <img src="../admin/images/events/<?= $homepage_event['poster'] ?>" 
                                 class="img-fluid rounded shadow" 
                                 alt="<?= htmlspecialchars($homepage_event['event_name']) ?>"
                                 style="width: 100%; height: auto; object-fit: contain; max-height: 500px;"
                                 onerror="this.onerror=null; this.src='../admin/images/no-image.jpg';">
                        </div>
                        
                        <div class="text-start">
                            <a href="../admin/images/events/<?= $homepage_event['poster'] ?>" 
                               download="<?= htmlspecialchars($homepage_event['event_name']) ?>_Poster.<?= pathinfo($homepage_event['poster'], PATHINFO_EXTENSION) ?>"
                               class="btn btn-secondary w-100 py-2"
                               style="background-color: #6c757d; border: none; border-radius: 5px; font-weight: 500;">
                                Download Poster
                            </a>
                        </div>
                    </div>
                    
                    <div style="border-top: 4px solid #FFC107; width: 80px; margin-bottom: 10px;"></div>
                        <h3 class="fw-bold text-dark mb-3" style="color: #495057;">SCHEDULE</h3>

                    <?php if(!empty($homepage_event['link_registration'])): ?>
                    <div class="mb-4">
                        <a href="<?= htmlspecialchars($homepage_event['link_registration']) ?>" 
                           class="btn btn-danger w-100 py-2" 
                           target="_blank">
                            <i class="bi bi-box-arrow-up-right"></i> Register Now
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($homepage_event['linkpage_event'])): ?>
                    <div class="mb-4">
                        <a href="<?= htmlspecialchars($homepage_event['linkpage_event']) ?>" 
                           class="btn btn-outline-danger w-100">
                            <i class="bi bi-info-circle"></i> More Information
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-4 p-3 border rounded bg-light" style="min-height: 100px;">
                        <h6 class="fw-bold mb-2 text-muted"><i class="bi bi-clock"></i> Schedule</h6>
                        <p class="text-muted small mb-0">Schedule will be available soon</p>
                    </div>
                    
                    <div class="mb-4 p-3 border rounded bg-light" style="min-height: 100px;">
                        <h6 class="fw-bold mb-2 text-muted"><i class="bi bi-file-earmark-arrow-down"></i> Downloadable Files</h6>
                        <p class="text-muted small mb-0">Files will be available soon</p>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>

    </div>
    <!-- End Main Content -->

    <!-- Footer -->
    <?php include("includes/frontfooter.php"); ?>

</body>
<script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
</html>