<?php
ob_start();
session_start();
include "includes/config.php";

$base_url = "/senapenmas-2026/frontend/";
$admin_img_path = "/senapenmas-2026/admin/images/";

// Ambil events yang punya sponsor DAN status publish
$event_sections = [];
$ev_res = mysqli_query($conn, "
    SELECT DISTINCT e.event_id, e.event_name, e.event_year, 
           COALESCE(sc.content, '') AS section_desc,
           COALESCE(sc.status, 1) AS section_status
    FROM sponsors s
    JOIN events e ON s.event_id = e.event_id
    LEFT JOIN sponsor_content sc ON sc.event_id = e.event_id
    WHERE COALESCE(sc.status, 1) = 1
    ORDER BY e.event_year DESC, e.event_name ASC
");

if ($ev_res) {
    while ($ev = mysqli_fetch_assoc($ev_res)) {
        // Ambil sponsor untuk event ini
        $eid = intval($ev['event_id']);
        $sp_res = mysqli_query($conn, "SELECT * FROM sponsors WHERE event_id = $eid ORDER BY COALESCE(order_number, 9999) ASC, sponsor_name ASC");
        $sponsors = [];
        if ($sp_res) {
            while ($sp = mysqli_fetch_assoc($sp_res)) { $sponsors[] = $sp; }
        }
        if (!empty($sponsors)) {
            $ev['sponsors'] = $sponsors;
            $event_sections[] = $ev;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sponsors - SENAPENMAS 2026</title>

    <link rel="stylesheet" type="text/css" href="<?php echo $base_url; ?>css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url; ?>css/foto.css">
    
    <style>
        /* --- STYLE HEADER --- */
        .page-header {
            background: linear-gradient(135deg, #A01626 0%, #7a1a00 100%);
            padding: 40px 0;
            margin-bottom: 60px;
        }
        .page-header h1 { color: white; font-weight: bold; margin: 0; font-size: 1.75rem; }
        .header-breadcrumb { font-size: 1rem; color: #fff; }
        .header-breadcrumb a { color: #ffc107; text-decoration: none; font-weight: 600; transition: color 0.3s; }
        .header-breadcrumb a:hover { color: #fff; }
        .header-breadcrumb .separator { margin: 0 8px; color: rgba(255,255,255,0.5); }
        @media (max-width: 576px) {
            .page-header .container { flex-direction: column; text-align: center; gap: 10px; }
        }

        /* --- EVENT SECTION --- */
        .event-section { margin-bottom: 60px; }
        .event-section:last-child { margin-bottom: 0; }

        .sponsored-by {
            text-align: center;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #888;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .event-title {
            text-align: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: #A01626;
            margin-bottom: 30px;
        }

        .section-divider {
            border: none;
            height: 2px;
            background: linear-gradient(90deg, transparent, #A01626, transparent);
            margin: 50px 0;
        }

        /* --- SPONSOR GRID --- */
        .sponsor-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
        }
        @media (max-width: 992px) { .sponsor-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 768px) { .sponsor-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px) { .sponsor-grid { grid-template-columns: repeat(1, 1fr); } }

        .sponsor-card {
            display: flex; flex-direction: column; align-items: center; text-align: center;
            padding: 20px; border-radius: 12px; background: #fff;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none; color: #333;
        }
        .sponsor-card:hover { transform: translateY(-5px); box-shadow: 0 8px 24px rgba(0,0,0,0.15); color: #A01626; }
        .sponsor-card img { width: 120px; height: 120px; object-fit: contain; margin-bottom: 15px; }
        .sponsor-card .sponsor-name { font-weight: 600; font-size: 0.95rem; }

        /* --- STYLE KONTEN DESKRIPSI --- */
        .event-desc {
            font-size: 1.05rem; line-height: 1.8; color: #333;
            text-align: justify; margin-top: 30px;
        }
        .event-desc h1, .event-desc h2, .event-desc h3,
        .event-desc h4, .event-desc h5, .event-desc h6 {
            color: #ac0404; margin-top: 20px; margin-bottom: 10px; font-weight: bold;
        }
        .event-desc p { margin-bottom: 15px; }
        .event-desc img { max-width: 100%; height: auto; border-radius: 8px; margin: 15px 0; }
        .event-desc .image-left { display: block; margin-right: auto; margin-bottom: 10px; text-align: left; clear: both; }
        .event-desc .image-right { display: block; margin-left: auto; margin-bottom: 10px; text-align: right; clear: both; }
        .event-desc .image-center { display: block; margin-left: auto; margin-right: auto; margin-bottom: 10px; text-align: center; clear: both; }
    </style>
</head>
<body>

    <?php include("includes/frontmenu.php"); ?>
    
    <div class="page-header">
        <div class="container d-flex justify-content-between align-items-center">
            <h1>Sponsors</h1>
            <div class="header-breadcrumb">
                <a href="<?php echo $base_url; ?>main.php">Home</a>
                <span class="separator">/</span>
                <span class="current">Sponsors</span>
            </div>
        </div>
    </div>
    
    <div class="container mb-5">
        <div class="row">
            <div class="col-lg-10 offset-lg-1">

                <?php if (!empty($event_sections)): ?>
                    <?php foreach ($event_sections as $idx => $section): ?>
                        
                        <?php if ($idx > 0): ?>
                            <hr class="section-divider">
                        <?php endif; ?>

                        <div class="event-section">
                            <div class="sponsored-by">Sponsored by</div>
                            <div class="event-title"><?= htmlspecialchars($section['event_name']) ?></div>

                            <div class="sponsor-grid">
                                <?php foreach ($section['sponsors'] as $s): ?>
                                    <?php 
                                        $link = !empty($s['website_link']) ? $s['website_link'] : '#';
                                        $has_link = !empty($s['website_link']);
                                    ?>
                                    <a href="<?= htmlspecialchars($link) ?>" 
                                       class="sponsor-card"
                                       <?= $has_link ? 'target="_blank"' : '' ?>>
                                        <img src="<?= $admin_img_path . htmlspecialchars($s['sponsor_logo']) ?>" 
                                             alt="<?= htmlspecialchars($s['sponsor_name']) ?>">
                                        <div class="sponsor-name"><?= htmlspecialchars($s['sponsor_name']) ?></div>
                                    </a>
                                <?php endforeach; ?>
                            </div>

                            <?php if (!empty($section['section_desc'])): ?>
                            <div class="event-desc">
                                <?= $section['section_desc'] ?>
                            </div>
                            <?php endif; ?>
                        </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted text-center mt-4">Belum ada sponsor.</p>
                <?php endif; ?>

            </div>
        </div>
    </div>
    
    <?php include("includes/frontfooter.php"); ?>

<script type="text/javascript" src="<?php echo $base_url; ?>js/bootstrap.bundle.min.js"></script>
</body>
</html>
