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
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        
        <style>
            .event-card {
                transition: transform 0.3s;
            }
            .event-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            }
            .carousel-item img {
                height: 650px;
                object-fit: cover;
            }
            
            /* --- AREA DESKRIPSI UTAMA --- */
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

            /* --- END CSS TAMBAHAN --- */

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
            <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="images/banner1.jpg" class="d-block w-100" alt="Banner 1">
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="container mt-5">
            
            <?php
            // Ambil event yang aktif (status = 1) untuk ditampilkan di homepage
            // Mengambil event terbaru yang aktif
            $active_event_query = mysqli_query($conn, "SELECT * FROM events WHERE status = 1 ORDER BY event_id DESC LIMIT 1");
            $active_event = mysqli_fetch_assoc($active_event_query);
            
            // Ambil schedule untuk event aktif jika ada
            $schedules = [];
            $downloadable_files = [];
            if ($active_event) {
                $schedule_query = mysqli_query($conn, "SELECT * FROM schedules WHERE event_id = " . $active_event['event_id'] . " ORDER BY date_new ASC");
                while($schedule_row = mysqli_fetch_assoc($schedule_query)) {
                    $schedules[] = $schedule_row;
                }
                
                // Ambil downloadable files untuk event aktif yang statusnya Publish
                $files_query = mysqli_query($conn, "SELECT * FROM downloadablefiles WHERE event_id = " . $active_event['event_id'] . " AND status = 'Publish' ORDER BY file_id DESC");
                while($file_row = mysqli_fetch_assoc($files_query)) {
                    $downloadable_files[] = $file_row;
                }
            }
            ?>
            
            <?php if($active_event): ?>
            <section class="mb-5">
                <div class="row">
        
                    <div class="col-lg-7 mb-4">
                        <div class="mb-4">
                        <div style="border-top: 4px solid #FFC107; width: 80px; margin-bottom: 10px;"></div>
                        <div class="event-description" style="font-weight: 800; font-size: 2.5rem; line-height: 1.2; margin-bottom: 0.5rem; color: #ac0404;">
                            <?= htmlspecialchars($active_event['event_name']) ?>
                        </div>
                    </div>
                        
                        <div class="mb-4">
                            <h3 class="fw-bold text-dark">Topic</h3>
                            <p class="text-muted fs-5"><?= htmlspecialchars($active_event['event_topic']) ?></p>
                        </div>
                        
                        <div class="mb-4">
                            <div class="event-description">
                                <?= $active_event['description'] ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 offset-lg-1">
                        <div class="mb-4">
                            <div style="border-top: 4px solid #FFC107; width: 80px; margin-bottom: 10px;"></div>
                            <h3 class="fw-bold text-dark mb-3" style="color: #495057;">POSTER</h3>
                            <div class="mb-3">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#posterModal" style="display: block; cursor: pointer;">
                                <img src="../admin/images/events/<?= $active_event['poster'] ?>" 
                                    class="img-fluid rounded shadow" 
                                    alt="<?= htmlspecialchars($active_event['event_name']) ?>"
                                    style="width: 100%; height: auto; object-fit: cover; display: block;"
                                    onerror="this.onerror=null; this.src='../admin/images/no-image.jpg';">
                                </a>
                                <p class="text-muted small mt-2">*Klik gambar untuk memperbesar</p>
                            </div>
                            
                            <div class="text-center">
                                <a href="../admin/images/events/<?= $active_event['poster'] ?>" 
                                download="<?= htmlspecialchars($active_event['event_name']) ?>_Poster.<?= pathinfo($active_event['poster'], PATHINFO_EXTENSION) ?>"
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
                                    <p class="text-muted small mb-0">Schedule will be available soon</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <br>
                        <div class="mb-4">
                            <div style="border-top: 4px solid #FFC107; width: 80px; margin-bottom: 20px;"></div>
                            
                            <h3 class="fw-bold text-dark mb-4">DOWNLOADABLE FILES</h3>
                            
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
                                    <p class="text-muted small mb-0">Files will be available soon</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <?php endif; ?>

        </div>
        <?php include("includes/frontfooter.php"); ?>

        <div class="modal fade" id="posterModal" tabindex="-1" aria-labelledby="posterModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content bg-transparent border-0">
                
                <div class="modal-header border-0">
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-0 text-center">
                    <img src="../admin/images/events/<?= $active_event['poster'] ?>" 
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