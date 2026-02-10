<?php
// Cek koneksi
if (!isset($conn)) {
    include 'config.php';
}

// Tentukan base URL frontend
$base_url = "/senapenmas-2026/frontend/";

// --- AMBIL DATA PROFILE ---
$profile_query = mysqli_query($conn, "SELECT * FROM profile ORDER BY profile_id DESC LIMIT 1");
$profile = mysqli_fetch_assoc($profile_query);

// Path gambar admin (Absolute Path)
$admin_img_path = "/senapenmas-2026/admin/images/";
// --------------------------

// Ambil menu
$menu_query = "SELECT * FROM menu WHERE parent_id IS NULL ORDER BY COALESCE(menu_order, 9999) ASC, menu_name ASC";
$main_menus = mysqli_query($conn, $menu_query);
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light py-2 sticky-top shadow-sm">
  <div class="container-fluid">
    
    <a class="navbar-brand ps-3 ps-lg-5" href="<?php echo $base_url; ?>main.php">
      <?php if (!empty($profile['logo_web'])): ?>
          <img src="<?php echo $admin_img_path . $profile['logo_web']; ?>" 
               alt="<?php echo htmlspecialchars($profile['web_name']); ?>" 
               height="60" 
               style="width: auto; object-fit: contain;">
      <?php else: ?>
          <span class="fw-bold fs-3"><?php echo htmlspecialchars($profile['web_name']); ?></span>
      <?php endif; ?>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 pe-3 pe-lg-5 align-items-center">
        
        <?php 
        // Logic untuk mendeteksi menu pertama (paling kiri)
        $menu_counter = 0;

        while($menu = mysqli_fetch_assoc($main_menus)): 
          $menu_counter++;
          
          // Jika ini menu pertama, set class bold
          $bold_class = ($menu_counter == 1) ? 'fw-bold' : '';

          // Cek Submenu
          $submenu_query = "SELECT * FROM menu WHERE parent_id = " . $menu['menu_id'] . " ORDER BY COALESCE(menu_order, 9999) ASC, menu_name ASC";
          $submenus = mysqli_query($conn, $submenu_query);
          $has_submenu = mysqli_num_rows($submenus) > 0;
          
          // Link Logic
          $menu_link = $menu['menu_link'];
          if ($menu['menu_type'] == 'internal-link' && !preg_match('/^https?:\/\//', $menu_link)) {
              $menu_link = $base_url . $menu_link;
          }
          ?>
          
          <?php if ($has_submenu): ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle <?php echo $bold_class; ?>" href="#" id="navbarDropdown<?php echo $menu['menu_id']; ?>" 
                 role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo htmlspecialchars($menu['menu_name']); ?>
              </a>
              <ul class="dropdown-menu" aria-labelledby="navbarDropdown<?php echo $menu['menu_id']; ?>">
                <?php while($submenu = mysqli_fetch_assoc($submenus)): ?>
                  <?php
                  $submenu_link = $submenu['menu_link'];
                  if ($submenu['menu_type'] == 'internal-link' && !preg_match('/^https?:\/\//', $submenu_link)) {
                      $submenu_link = $base_url . $submenu_link;
                  }
                  ?>
                  <li>
                    <a class="dropdown-item" href="<?php echo $submenu_link; ?>"
                       <?php echo ($submenu['menu_type'] == 'external-link') ? 'target="_blank"' : ''; ?>>
                      <?php echo htmlspecialchars($submenu['menu_name']); ?>
                    </a>
                  </li>
                <?php endwhile; ?>
              </ul>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link <?php echo $bold_class; ?>" href="<?php echo $menu_link; ?>"
                 <?php echo ($menu['menu_type'] == 'external-link') ? 'target="_blank"' : ''; ?>>
                <?php echo htmlspecialchars($menu['menu_name']); ?>
              </a>
            </li>
          <?php endif; ?>
        <?php endwhile; ?>
      </ul>
    </div>
  </div>
</nav>

<style>
/* Pastikan body tidak terpotong (opsional, tergantung struktur main.php) */
body {
    overflow-x: hidden; /* Mencegah scroll horizontal jika ada elemen terlalu lebar */
}

/* Mempercantik tampilan navbar saat sticky (opsional) */
.navbar.sticky-top {
    transition: all 0.3s ease;
}
</style>

<script>
// Script agar dropdown muncul saat hover
document.querySelectorAll('.nav-item.dropdown').forEach(function(item) {
    item.addEventListener('mouseover', function() {
        let menu = this.querySelector('.dropdown-menu');
        if(menu) menu.classList.add('show');
    });

    item.addEventListener('mouseout', function() {
        let menu = this.querySelector('.dropdown-menu');
        if(menu) menu.classList.remove('show');
    });
});
</script>