<?php
include 'config.php';

    $query = mysqli_query($conn, "SELECT dosen_Nama FROM dosen");
    $dosen = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $dosen[] = $row;
    }
?>

<!--membuat tampilan menu-->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
      <img src="images/UNTAR.png" alt="Logo UNTAR" weight ="60" height="40">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Beranda</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="tentangKamiDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Tentang Kami
          </a>
          <ul class="dropdown-menu" aria-labelledby="tentangKamiDropdown">
            <li><a class="dropdown-item" href="#">Sambutan Dekan</a></li>
            <li><a class="dropdown-item" href="#">Visi & Misi Fakultas</a></li>
            <li><a class="dropdown-item" href="#">Akreditasi</a></li>
            <li><a class="dropdown-item" href="#">Pimpinan</a></li>
            <li><a class="dropdown-item" href="#">Fasilitas</a></li>
            <li><a class="dropdown-item" href="#">Berita Fakultas</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="akademikDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Akademik
          </a>
          <ul class="dropdown-menu" aria-labelledby="akademikDropdown">
            <li><a class="dropdown-item" href="#">Program Studi</a></li>
            <li><a class="dropdown-item" href="#">Kurikulum</a></li>
            <li><a class="dropdown-item" href="#">Kalender Akademik</a></li>
            </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="programDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Program
          </a>
          <ul class="dropdown-menu" aria-labelledby="programDropdown">
            <li><a class="dropdown-item" href="#">Sarjana Arsitektur</a></li>
            <li><a class="dropdown-item" href="#">Sarjana Teknik Sipil</a></li>
            <li><a class="dropdown-item" href="#">Sarjana Teknik Mesin</a></li>
            <li><a class="dropdown-item" href="#">Sarjana Teknik Industri</a></li>
            <li><a class="dropdown-item" href="#">Sarjana Teknologi Informasi</a></li>
            <li><a class="dropdown-item" href="#">Sarjana Kedokteran</a></li>
          </ul>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="#">Admisi</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="https://maps.app.goo.gl/HFG2KQnz81D6XFQj9" target="_blank">Peta</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Dosen
          </a>
          <ul class="dropdown-menu">
            <?php foreach ($dosen as $d) :?> 
                <li><a class="dropdown-item" href="#"><?= $d['dosen_Nama']; ?></a></li>
            <?php endforeach; ?>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
        </li>
      </ul>
      <form class="d-flex">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
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
