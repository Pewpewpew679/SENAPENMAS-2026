<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>

    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/foto.css">
</head>
<body>

    <!--membuat tampilan menu-->
    <?php include("includes/frontmenu.php"); ?>
    <!--akhir membuat tampilan menu-->

    <!--membuat tampilan slider-->
<div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
  </div>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="images/banner1.jpg" class="d-block w-100" alt="No Picture">
      <div class="carousel-caption d-none d-md-block">
      </div>
    </div>
    <div class="carousel-item">
      <img src="images/banner2.png" class="d-block w-100" alt="...">
      <div class="carousel-caption d-none d-md-block">
      </div>
    </div>
    <div class="carousel-item">
      <img src="images/banner3.png" class="d-block w-100" alt="...">
      <div class="carousel-caption d-none d-md-block">
      </div>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div><!--akhir membuat tampilan slider-->


<!--membuat halaman web lengkap-->
<div class="container">
  <div class="row atas mt-5">
    <div class="col-sm-8">
    <!--nampilin dari pesertaskripsi-->
    <?php
      $q = mysqli_query($conn, "SELECT * FROM pesertaskripsi, mahasiswa 
            WHERE pesertaskripsi.mhs_NPM = mahasiswa.mhs_NPM LIMIT 2");
      while($row = mysqli_fetch_assoc($q)) {
      ?>
        <div class="d-flex mb-4">
          <div class="flex-shrink-0">
            <img src="images/<?php echo $row['peserta_DOKUMEN']; ?>" width="100" height="140" alt="...">
          </div>
          <div class="flex-grow-1 ms-3" style="margin-left: 10px; text-align: justify;">
            <h2><?php echo $row['peserta_JUDUL']; ?></h2>
            <p>
              Nama Mahasiswa: <b><?php echo $row['mhs_Nama']; ?></b><br>
              Tahun Akademik: <b><?php echo $row['peserta_THAKD']; ?></b><br>
              Terdaftar pada semester: <b><?php echo $row['peserta_SEMT']; ?></b><br>
              Tanggal daftar: <b><?php echo $row['peserta_TGLDAFTAR']; ?></b>
            </p>
          </div>
        </div>
      <?php
      }
      ?>
    </div><!--akhir dari nampilin dari pesertaskripsi-->




    <!--nampilin dari ujianskripsi-->
    <div class="col-sm-4">
     <?php
      $ujianskripsi = mysqli_query($conn, "SELECT * FROM ujianskripsi u, mahasiswa m, bimbinganskripsi b, dosen d
          WHERE u.mhs_NPM = m.mhs_NPM AND b.mhs_NPM = u.mhs_NPM AND b.dosen_NIDN = d.dosen_NIDN
          ORDER BY u.ujian_TGL DESC LIMIT 3");
      ?>

      <div class="list-group">
        <?php foreach ($ujianskripsi as $key => $us) :
        // jika key adalah 0, maka set active. jika bukan, kosong.
        $status_class = ($key == 0) ? 'active' : ''; 
        
        // atur warna teks, jika biru (active), teks jadi putih, jika putih (biasa), teks jadi abu-abu.
        $text_pembimbing = ($key == 0) ? 'text-light' : 'text-muted';?> 
            <a href="#" class="list-group-item list-group-item-action <?php echo $status_class; ?>" aria-current="true">
                  <div class="d-flex w-100 justify-content-between">
                    <div>
                        <h5 class="mb-1">NPM: <?php echo $us['mhs_NPM']; ?></h5>
                        <small>Nama: <?php echo $us['mhs_Nama']; ?></small>
                    </div>
                        <small><?php echo $us['ujian_TGL']; ?> | Pukul <?php echo $us['ujian_WAKTU']; ?> WIB</small>
                  </div>
                <small>Pembimbing: <?php echo $us['dosen_Nama']; ?></small>
            </a>
        <?php endforeach; ?>
      </div>
    </div><!--akhir dari nampilin dari ujianskripsi-->




  <!--membuat galeri foto ujian skripsi-->
  <h1 class="mt-15 mb-15" style="text-align: center">Foto Ujian Skripsi</h1>
  <div class="galerifoto row g-4">
 <?php
  $q = mysqli_query($conn, "SELECT ujianskripsi.ujian_FOTO, pesertaskripsi.peserta_JUDUL FROM ujianskripsi, pesertaskripsi
    WHERE ujianskripsi.mhs_NPM = pesertaskripsi.mhs_NPM AND ujianskripsi.ujian_FOTO IS NOT NULL AND ujianskripsi.ujian_FOTO != '' LIMIT 6");

  while($row = mysqli_fetch_assoc($q)) { ?>
      <figure class="col-lg-4 col-sm-6 col-xs-12"> 
          <img style="width: 100%; height: auto;" src="images/<?php echo $row['ujian_FOTO']; ?>" class="figure-img img-fluid rounded" alt="Foto Ujian"> 
          <figcaption class="figure-caption text-start">
              <strong>Judul Skripsi: </strong><?php echo $row['peserta_JUDUL']; ?>
          </figcaption> 
      </figure>
  <?php } ?>
  </div><!--akhir galeri foto ujian skripsi-->




<!--berita-->
<h1 class="mt-15 mb-15" style="text-align: center">Berita Untar</h1>

<div class="row tengah mt-5 g-2">
    <?php
    $q = mysqli_query($conn, "SELECT * FROM berita LIMIT 3"); 
    while($row = mysqli_fetch_assoc($q)) {
    ?>
    <div class="col-md-4">
        <div class="card" style="width: 27rem;"> 
          <figure class="hover-zoom">
            <img src="images/<?php echo $row['berita_FOTO']; ?>" class="card-img-top img-fluid rounded" style="width: 100%; height: auto; object-fit: cover;">
          </figure>
            <div class="card-body">
              <h4 class="card-title"><?php echo $row['berita_JUDUL']; ?></h4> 
              <p class="text-danger d-block mb-3"><b><?php echo $row['berita_TGL']; ?></b></p>
              <p class="card-text"><?php echo $row['berita_ISI']; ?></p>
              <a href="#" class="btn btn-danger">See More News </a>
            </div>
        </div>
    </div>
    <?php } ?>
</div><!--akhir dari berita-->




<!--komentar mahasiswa tentang berita-->      
 <div class="row atas mt-5">
  <?php
  $query = "SELECT * FROM komentar_mahasiswa, mahasiswa, berita
            WHERE komentar_mahasiswa.mhs_NPM = mahasiswa.mhs_NPM AND komentar_mahasiswa.berita_JUDUL = berita.berita_JUDUL LIMIT 1";
    
    $q = mysqli_query($conn, $query); 
    while($row = mysqli_fetch_assoc($q)) {?>
    <div class="col-sm-4">
      <div class="card"> 
        <div class="card-header text-center bg-danger fw-bold text-white">
           <h4>Komentar Mahasiswa Pada Berita</h4>
        </div>
          <div class="p-3 border bg-light rounded h-100" style="max-height: 300px; overflow-y: auto;">
          <h5 id="scrollspyHeading1"><?php echo $row['berita_JUDUL']; ?></h5>
          <p class="text-muted d-block mb-3">
            <small style="color: red;">
                <b><?php echo $row['komentar_TGL']; ?> || <?php echo $row['komentar_WAKTU']; ?></b>
            </small>
          </p>
          <p class="fw-bold"><?php echo $row['mhs_Nama']; ?> - <?php echo $row['mhs_NPM']; ?></p>
          <p><?php echo $row['komentar_ISI']; ?></p>
        </div>
      </div>
    </div> 
  <?php }?><!--akhir komentar mahasiswa tentang berita-->      




    <!--YouTube UNTAR-->      
      <div class="col-sm-8">
        <div class="card bg-dark text-white">
        <img src="images/bannervideo.png" class="card-img" alt="...">
        <div class="card-img-overlay">
            <h5 class="card-title">YouTube Universitas Tarumanagara</h5>
            <p class="card-text">Akses konten eksklusif, seminar, dan update terbaru dari Universitas Tarumanagara. Jangan sampai ketinggalan!</p>
            <div class="youtube-wrapper">
              <a href="https://youtu.be/ko4BCK1H9eA?si=1O-GucjzJ8cDX2IU" target="_blank">
                  <img class="yt-thumb" src="images/tombolz.png" alt="Thumbnail">
              </a>
            </div>
        </div>
        </div><!--akhir YouTube UNTAR-->      




      <!--ruang sidang-->      
       <div class="row mt-3">
        <div class="col-sm-4 ">
          <?php
          $query = "SELECT * FROM ruangsidang, mahasiswa 
          WHERE ruangsidang.mhs_NPM = mahasiswa.mhs_NPM LIMIT 1";    

          $q = mysqli_query($conn, $query); 
          while($row = mysqli_fetch_assoc($q)) {?>
          <div class="card text-dark bg-light mb-3" style="max-width: 18rem;">
            <div class="card-header text-center bg-danger text-white"><h4>Ruangan Sidang</h4></div>
            <div class="card-body">
              <h5 class="card-title"><?php echo $row['mhs_NPM']; ?> - <?php echo $row['mhs_Nama']; ?></h5>
              <p class="card-text"><?php echo $row['ruangan_Nama']; ?></p>
              <p class="card-text"><?php echo $row['ruangan_Lokasi']; ?></p>
            </div>
          </div>
        </div>
        <?php }?><!--akhir ruang sidang-->      




      <!--data diri-->      
        <div class="col-sm-8">
          <div class="card mb-3" style="max-width: 455px;">
            <div class="row g-0">
              <div class="col-md-4">
                <img src="images/pasfoto.jpg" class="img-fluid rounded-start" alt="...">
              </div>
              <div class="col-md-8">
                <div class="card-body">
                  <h4 class="card-title">Ferdinand Justin</h4>
                  <h6 class="card-text">NIM : 825240125 <br> Kelas : SI C'24</h6>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div> <!-- penutup class row bawah -->
    </div> <!-- penutup class container-fluid -->
<!-- akhir membuat halaman web lengkap -->

</body>
<script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
</html>