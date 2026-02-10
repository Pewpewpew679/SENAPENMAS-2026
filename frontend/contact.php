<?php
ob_start();
session_start();
include "includes/config.php";

// Fetch profile data
$profile_query = mysqli_query($conn, "SELECT * FROM profile ORDER BY profile_id DESC LIMIT 1");
$profile = mysqli_fetch_assoc($profile_query) ?: [];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - SENAPENMAS 2026</title>

    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
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
        
        /* Style untuk Breadcrumb di kanan atas */
        .header-breadcrumb {
            font-size: 1rem;
            color: rgb(255, 255, 255); /* Warna text Contact agak transparan */
        }
        
        .header-breadcrumb a {
            color: #ffc107; /* Warna Home (Kuning Emas agar kontras) */
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .header-breadcrumb a:hover {
            color: #fff; /* Warna saat di-hover jadi putih */
        }
        
        .header-breadcrumb .separator {
            margin: 0 8px; /* Jarak miring (/) */
            color: rgba(255, 255, 255, 0.5);
        }

        /* Responsif untuk HP: Agar turun ke bawah jika layar kecil */
        @media (max-width: 576px) {
            .page-header .container {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }
        }

        .contact-info-box {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
        }
        
        .contact-item:last-child {
            margin-bottom: 0;
        }
        
        .contact-icon {
            width: 45px;
            height: 45px;
            background-color: #bf0319;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .contact-icon i {
            color: white;
            font-size: 20px;
        }
        
        .contact-details h5 {
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            font-size: 1rem;
        }
        
        .contact-details p {
            margin: 0;
            color: #666;
            line-height: 1.6;
            font-size: 0.95rem;
        }
        
        .contact-details a {
            color: #666;
            text-decoration: none;
            font-size: 0.95rem;
        }
        
        .contact-details a:hover {
            color: #A01626;
        }
        
        .map-container {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            height: 500px;
        }
        
        .map-container iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }
        
        @media (max-width: 991px) {
            .contact-info-box {
                margin-bottom: 30px;
            }
            
            .map-container {
                height: 400px;
            }
        }
    </style>
</head>
<body>

    <?php include("includes/frontmenu.php"); ?>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container d-flex justify-content-between align-items-center">
            <h1>Contact Us</h1>
            
            <div class="header-breadcrumb">
                <a href="http://localhost/senapenmas-2026/frontend/main.php">Home</a>
                <span class="separator">/</span>
                <span class="current">Contact</span>
            </div>
        </div>
    </div>

    <!-- Contact Section -->
    <div class="container mb-5">
        <div class="row">
            
            <!-- Contact Information -->
            <div class="col-lg-4 mb-4">
                <div class="contact-info-box">
                    
                    <!-- Address -->
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div class="contact-details">
                            <h5>Address</h5>
                            <?php if(!empty($profile['secretariat_office'])): ?>
                                <p><?= nl2br(htmlspecialchars($profile['secretariat_office'])) ?></p>
                            <?php else: ?>
                                <p>Universitas Tarumanagara, Kampus 1<br>
                                Jl. Letjen S. Parman No.1<br>
                                Kota Jakarta Barat, DKI Jakarta<br>
                                Indonesia 11440<br>
                                Office : 021-5695-8751<br>
                                Fax : 021-5695-8738</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Call Us -->
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <div class="contact-details">
                            <h5>Call Us</h5>
                            <?php if(!empty($profile['phone1'])): ?>
                                <p><?= htmlspecialchars($profile['phone1']) ?></p>
                            <?php endif; ?>
                            <?php if(!empty($profile['phone2'])): ?>
                                <p><?= htmlspecialchars($profile['phone2']) ?></p>
                            <?php endif; ?>
                            <?php if(empty($profile['phone1']) && empty($profile['phone2'])): ?>
                                <p>(+62) 815 8433 6003 (Wulan)</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Email Us -->
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div class="contact-details">
                            <h5>Email Us</h5>
                            <?php if(!empty($profile['email'])): ?>
                                <p><a href="mailto:<?= htmlspecialchars($profile['email']) ?>"><?= htmlspecialchars($profile['email']) ?></a></p>
                            <?php else: ?>
                                <p><a href="mailto:senapenmas@untar.ac.id">@senapenmas@untar.ac.id</a></p>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Google Maps -->
            <div class="col-lg-8">
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3966.7136582266407!2d106.78918!3d-6.169084000000001!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f65c8572640d%3A0xc0a066d78372614e!2sUniversitas%20Tarumanagara!5e0!3m2!1sid!2sus!4v1770693932958!5m2!1sid!2sus" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>

        </div>
    </div>

    <?php include("includes/frontfooter.php"); ?>

</body>

<script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
</html>