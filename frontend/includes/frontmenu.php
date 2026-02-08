<?php
include 'config.php';

// Tentukan base URL - sesuaikan dengan folder project Anda
$base_url = "/senapenmas-2026/frontend/";

// Ambil semua menu dari database dengan urutan
$menu_query = "SELECT * FROM menu WHERE parent_id IS NULL ORDER BY COALESCE(menu_order, 9999) ASC, menu_name ASC";
$main_menus = mysqli_query($conn, $menu_query);
?>

<!--membuat tampilan menu-->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?php echo $base_url; ?>main.php">
      <img src="<?php echo $base_url; ?>images/UNTAR.png" alt="Logo UNTAR" width="140" height="40">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <!-- Home Link -->
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="<?php echo $base_url; ?>main.php">Beranda</a>
        </li>
        
        <?php while($menu = mysqli_fetch_assoc($main_menus)): ?>
          <?php
          // Cek apakah menu ini punya submenu
          $submenu_query = "SELECT * FROM menu WHERE parent_id = " . $menu['menu_id'] . " ORDER BY COALESCE(menu_order, 9999) ASC, menu_name ASC";
          $submenus = mysqli_query($conn, $submenu_query);
          $has_submenu = mysqli_num_rows($submenus) > 0;
          
          // Tentukan link
          $menu_link = $menu['menu_link'];
          // Jika internal link dan tidak dimulai dengan http, tambahkan base_url
          if ($menu['menu_type'] == 'internal-link' && !preg_match('/^https?:\/\//', $menu_link)) {
              $menu_link = $base_url . $menu_link;
          }
          ?>
          
          <?php if ($has_submenu): ?>
            <!-- Menu dengan Submenu (Dropdown) -->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown<?php echo $menu['menu_id']; ?>" 
                 role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo htmlspecialchars($menu['menu_name']); ?>
              </a>
              <ul class="dropdown-menu" aria-labelledby="navbarDropdown<?php echo $menu['menu_id']; ?>">
                <?php while($submenu = mysqli_fetch_assoc($submenus)): ?>
                  <?php
                  $submenu_link = $submenu['menu_link'];
                  // Jika internal link dan tidak dimulai dengan http, tambahkan base_url
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
            <!-- Menu tanpa Submenu -->
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $menu_link; ?>"
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

<script>
document.querySelectorAll('.nav-item.dropdown').forEach(function(item) {
    item.addEventListener('mouseover', function() {
        let menu = this.querySelector('.dropdown-menu');
        menu.classList.add('show');
    });

    item.addEventListener('mouseout', function() {
        let menu = this.querySelector('.dropdown-menu');
        menu.classList.remove('show');
    });
});
</script>