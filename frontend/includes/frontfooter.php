<?php
// Config sudah di-include dari halaman utama, tapi kita pastikan $conn tersedia
if (!isset($conn)) {
    include 'config.php';
}
?>

<footer class="bg-dark text-white mt-5 pt-5 pb-3">
    <div class="container">
        <div class="row">
            <!-- About Section -->
            <div class="col-md-4 mb-4">
                <h5 class="fw-bold mb-3">TICASH 2025</h5>
                <p class="text-muted">
                    The International Conference on Applied Social Sciences and Humanities
                </p>
                <p class="text-muted">
                    Universitas Tarumanagara<br>
                    Jakarta, Indonesia
                </p>
            </div>
            
            <!-- Quick Links -->
            <div class="col-md-4 mb-4">
                <h5 class="fw-bold mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="main.php" class="text-muted text-decoration-none">
                            <i class="bi bi-chevron-right"></i> Home
                        </a>
                    </li>
                    <?php
                    // Ambil beberapa menu utama untuk footer
                    $footer_menu_query = "SELECT * FROM menu WHERE parent_id IS NULL ORDER BY COALESCE(menu_order, 9999) ASC LIMIT 4";
                    $footer_menus = mysqli_query($conn, $footer_menu_query);
                    while($fmenu = mysqli_fetch_assoc($footer_menus)):
                    ?>
                    <li class="mb-2">
                        <a href="<?php echo $fmenu['menu_link']; ?>" class="text-muted text-decoration-none"
                           <?php echo ($fmenu['menu_type'] == 'external-link') ? 'target="_blank"' : ''; ?>>
                            <i class="bi bi-chevron-right"></i> <?php echo htmlspecialchars($fmenu['menu_name']); ?>
                        </a>
                    </li>
                    <?php endwhile; ?>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div class="col-md-4 mb-4">
                <h5 class="fw-bold mb-3">Contact Us</h5>
                <ul class="list-unstyled text-muted">
                    <li class="mb-2">
                        <i class="bi bi-geo-alt-fill"></i> 
                        Universitas Tarumanagara, Jakarta
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-envelope-fill"></i> 
                        <a href="mailto:ticash@untar.ac.id" class="text-muted text-decoration-none">
                            ticash@untar.ac.id
                        </a>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-globe"></i> 
                        <a href="http://ticash.untar.ac.id" class="text-muted text-decoration-none" target="_blank">
                            ticash.untar.ac.id
                        </a>
                    </li>
                </ul>
                
                <!-- Social Media -->
                <div class="mt-3">
                    <a href="#" class="text-white me-3" style="font-size: 1.5rem;"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-white me-3" style="font-size: 1.5rem;"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="text-white me-3" style="font-size: 1.5rem;"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-white" style="font-size: 1.5rem;"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
        </div>
        
        <hr class="bg-secondary">
        
        <!-- Copyright -->
        <div class="row">
            <div class="col-md-12 text-center">
                <p class="text-muted mb-0">
                    &copy; <?php echo date('Y'); ?> TICASH - Universitas Tarumanagara. All Rights Reserved.
                </p>
            </div>
        </div>
    </div>
</footer>

<style>
footer a:hover {
    color: #DC143C !important;
}
</style>