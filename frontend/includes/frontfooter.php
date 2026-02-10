<?php
// --- PHP Logic Section ---
if (!isset($conn)) { include 'config.php'; }

// Fetch Data
$profile = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM profile ORDER BY profile_id DESC LIMIT 1")) ?: [];
$sosmed_query = mysqli_query($conn, "SELECT * FROM sosmed ORDER BY created_at DESC");
$links_query = mysqli_query($conn, "SELECT * FROM links ORDER BY link_order ASC");

// Icon Map
$iconMap = [
    "Twitter" => "bi-twitter-x", "Instagram" => "bi-instagram", "Facebook" => "bi-facebook",
    "Tiktok" => "bi-tiktok", "Youtube" => "bi-youtube", "Linkedin" => "bi-linkedin",
    "Whatsapp" => "bi-whatsapp", "Telegram" => "bi-telegram", "Web" => "bi-globe"
];
?>

<footer class="footer-custom text-white">
    <div class="container">
        <div class="row">
            
            <div class="col-lg-4 col-md-12 mb-4">
                <?php if(!empty($profile['logo_profile'])): ?>
                    <img src="/senapenmas-2026/admin/images/<?= $profile['logo_profile'] ?>"
                         alt="Logo" class="mb-3" style="max-height: 50px;">
                <?php endif; ?>
                
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <?php while($sosmed = mysqli_fetch_assoc($sosmed_query)): ?>
                        <?php $icon = $iconMap[$sosmed['platform_name']] ?? 'bi-globe'; ?>
                        <a href="<?= htmlspecialchars($sosmed['social_link']) ?>" target="_blank" class="social-btn">
                            <i class="bi <?= $icon ?>"></i>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="col-lg-5 col-md-6 mb-4">
                <h5 class="fw-bold mb-3">Secretariat</h5>
                
                <div class="footer-content">
                    <?php if(!empty($profile['secretariat_office'])): ?>
                        <div class="d-flex mb-2">
                            <span class="me-2"><i class="bi bi-geo-alt-fill"></i></span>
                            <span><?= nl2br(htmlspecialchars($profile['secretariat_office'])) ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($profile['phone1'])): ?>
                        <div class="mb-1">
                            <i class="bi bi-telephone-fill me-2"></i>
                            <span><?= htmlspecialchars($profile['phone1']) ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($profile['phone2'])): ?>
                        <div class="mb-1">
                            <i class="bi bi-telephone-fill me-2" style="visibility: hidden;"></i>
                            <span><?= htmlspecialchars($profile['phone2']) ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($profile['email'])): ?>
                        <div class="mt-2">
                            <i class="bi bi-envelope-fill me-2"></i>
                            <a href="mailto:<?= htmlspecialchars($profile['email']) ?>" class="text-white text-decoration-none">
                                <?= htmlspecialchars($profile['email']) ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="fw-bold mb-3">Links</h5>
                <ul class="list-unstyled mb-0">
                    <?php while($link = mysqli_fetch_assoc($links_query)): ?>
                    <li class="mb-2">
                        <a href="<?= htmlspecialchars($link['link_url']) ?>" target="_blank" class="highlight-link d-flex align-items-center">
                            <i class="bi bi-arrow-right-short fs-5 me-1 arrow-icon"></i>
                            <?= htmlspecialchars($link['link_name']) ?>
                        </a>
                    </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>

        <hr class="mt-3 mb-3" style="opacity: 0.2; border-color: white;">

        <div class="row">
            <div class="col-md-12 text-center">
                <p class="mb-0 small">
                    © Copyright <strong>SENAPENMAS <?= date('Y') ?></strong> All Rights Reserved
                </p>
            </div>
        </div>
    </div>
</footer>

<a href="#" class="btn-scroll-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;">
    <i class="bi bi-arrow-up"></i>
</a>

<style>
/* CSS Custom */
.footer-custom {
    background-color: #A01626;
    padding-top: 3rem;
    padding-bottom: 1.5rem;
    font-size: 0.95rem; /* Ukuran font standar */
}

/* Mengatur jarak antar baris agar tidak terlalu jauh (Compact) */
.footer-content {
    line-height: 1.4;
}

/* Social Media Buttons */
.social-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 35px;
    height: 35px;
    border: 1px solid rgba(255,255,255,0.5);
    border-radius: 50%;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
}

.social-btn:hover {
    background-color: white;
    color: #A01626;
    transform: translateY(-2px);
}

/* Highlight Links (Kuning Emas) */
.highlight-link {
    color: #ffc107; /* Warna Kuning Emas */
    text-decoration: none;
    font-weight: 600; /* Sedikit tebal agar kebaca */
    transition: all 0.3s ease;
}

.highlight-link:hover {
    color: #fff; /* Berubah putih saat hover */
    padding-left: 5px; /* Geser kanan sedikit */
    text-decoration: underline;
}

.highlight-link .arrow-icon {
    transition: transform 0.3s ease;
}

.highlight-link:hover .arrow-icon {
    transform: translateX(3px);
}

/* Scroll Top Button */
.btn-scroll-top {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 45px;
    height: 45px;
    background-color: #dc3545; /* Merah sesuai request awal btn-danger */
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    box-shadow: 0 2px 5px rgba(0,0,0,0.3);
    text-decoration: none;
    transition: background-color 0.3s;
}

.btn-scroll-top:hover {
    background-color: #bb2d3b;
    color: white;
}
</style>