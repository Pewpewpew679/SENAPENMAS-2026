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
    <title>TICASH 2025 - Universitas Tarumanagara</title>

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
        <!-- Featured Event (Homepage Event) -->
        <section class="mb-5">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <img src="images/events/<?= $homepage_event['poster'] ?>" class="img-fluid rounded shadow" alt="<?= htmlspecialchars($homepage_event['event_name']) ?>">
                </div>
                <div class="col-lg-6">
                    <div class="badge bg-danger mb-2"><?= $homepage_event['event_category'] ?> <?= $homepage_event['event_year'] ?></div>
                    <h2 class="fw-bold text-danger"><?= htmlspecialchars($homepage_event['event_name']) ?></h2>
                    <h4 class="text-muted mb-3"><?= htmlspecialchars($homepage_event['event_topic']) ?></h4>
                    
                    <div class="mb-3">
                        <strong><i class="bi bi-calendar-event"></i> Event Date:</strong><br>
                        <?= date('F d, Y', strtotime($homepage_event['start_date'])) ?> - <?= date('F d, Y', strtotime($homepage_event['end_date'])) ?>
                    </div>
                    
                    <div class="mb-4">
                        <?= $homepage_event['description'] ?>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <?php if(!empty($homepage_event['link_registration'])): ?>
                            <a href="<?= htmlspecialchars($homepage_event['link_registration']) ?>" class="btn btn-danger" target="_blank">
                                <i class="bi bi-box-arrow-up-right"></i> Register Now
                            </a>
                        <?php endif; ?>
                        
                        <?php if(!empty($homepage_event['linkpage_event'])): ?>
                            <a href="<?= htmlspecialchars($homepage_event['linkpage_event']) ?>" class="btn btn-outline-danger">
                                <i class="bi bi-info-circle"></i> More Info
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
        <hr class="my-5">
        <?php endif; ?>

        <!-- Upcoming Events Section -->
        <section class="mb-5">
            <h2 class="text-center fw-bold mb-4">Upcoming Events</h2>
            <div class="row g-4">
                <?php
                // Ambil event yang aktif dan upcoming (belum lewat tanggal end_date), exclude yang sudah jadi homepage
                $today = date('Y-m-d');
                $events_query = "SELECT * FROM events 
                                WHERE status = 1 
                                AND end_date >= '$today'";
                
                if($homepage_event) {
                    $events_query .= " AND event_id != " . $homepage_event['event_id'];
                }
                
                $events_query .= " ORDER BY start_date ASC LIMIT 6";
                
                $events = mysqli_query($conn, $events_query);
                
                while($event = mysqli_fetch_assoc($events)):
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card event-card h-100">
                        <img src="images/events/<?= $event['poster'] ?>" class="card-img-top" alt="<?= htmlspecialchars($event['event_name']) ?>" style="height: 250px; object-fit: cover;">
                        <div class="card-body">
                            <div class="badge bg-primary mb-2"><?= $event['event_category'] ?> <?= $event['event_year'] ?></div>
                            <h5 class="card-title"><?= htmlspecialchars($event['event_name']) ?></h5>
                            <p class="card-text text-muted"><?= htmlspecialchars($event['event_topic']) ?></p>
                            <p class="text-danger"><small><strong>
                                <i class="bi bi-calendar"></i> <?= date('M d, Y', strtotime($event['start_date'])) ?>
                            </strong></small></p>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <div class="d-flex gap-2">
                                <?php if(!empty($event['linkpage_event'])): ?>
                                    <a href="<?= htmlspecialchars($event['linkpage_event']) ?>" class="btn btn-sm btn-outline-danger flex-fill">
                                        More Info
                                    </a>
                                <?php endif; ?>
                                
                                <?php if(!empty($event['link_registration'])): ?>
                                    <a href="<?= htmlspecialchars($event['link_registration']) ?>" class="btn btn-sm btn-danger flex-fill" target="_blank">
                                        Register
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </section>

        <!-- Past Events Section -->
        <section class="mb-5">
            <h2 class="text-center fw-bold mb-4">Past Events</h2>
            <div class="row g-4">
                <?php
                // Ambil event yang sudah lewat (past events)
                $past_events = mysqli_query($conn, "SELECT * FROM events 
                                                    WHERE status = 1 
                                                    AND end_date < '$today' 
                                                    ORDER BY end_date DESC 
                                                    LIMIT 3");
                
                while($past_event = mysqli_fetch_assoc($past_events)):
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card event-card h-100">
                        <img src="images/events/<?= $past_event['poster'] ?>" class="card-img-top" alt="<?= htmlspecialchars($past_event['event_name']) ?>" style="height: 200px; object-fit: cover; filter: grayscale(30%);">
                        <div class="card-body">
                            <div class="badge bg-secondary mb-2"><?= $past_event['event_category'] ?> <?= $past_event['event_year'] ?></div>
                            <h6 class="card-title"><?= htmlspecialchars($past_event['event_name']) ?></h6>
                            <p class="card-text text-muted small"><?= htmlspecialchars($past_event['event_topic']) ?></p>
                            <p class="text-secondary"><small>
                                <i class="bi bi-calendar-check"></i> <?= date('M d, Y', strtotime($past_event['end_date'])) ?>
                            </small></p>
                        </div>
                        <?php if(!empty($past_event['linkpage_event'])): ?>
                        <div class="card-footer bg-white border-0">
                            <a href="<?= htmlspecialchars($past_event['linkpage_event']) ?>" class="btn btn-sm btn-outline-secondary w-100">
                                View Details
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </section>

        <!-- About Section (Optional) -->
        <section class="mb-5 py-5 bg-light rounded">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4">
                        <h2 class="fw-bold text-danger">About TICASH</h2>
                        <p class="lead">The International Conference on Applied Social Sciences and Humanities</p>
                        <p>TICASH is an annual international conference organized by Universitas Tarumanagara, bringing together researchers, academics, and practitioners from around the world to share knowledge and innovations in social sciences and humanities.</p>
                        <a href="page/1/about-us" class="btn btn-danger">Learn More</a>
                    </div>
                    <div class="col-lg-6">
                        <img src="images/UNTAR.png" class="img-fluid" alt="UNTAR Logo">
                    </div>
                </div>
            </div>
        </section>

    </div>
    <!-- End Main Content -->

    <!-- Footer -->
    <?php include("includes/frontfooter.php"); ?>

</body>
<script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
</html>