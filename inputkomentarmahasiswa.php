<!DOCTYPE html>
<html>
    <!--pengaman halaman, memastikan hanya user yg udh login yg bisa mengakses halaman Input komentarmahasiswa.-->
    <?php
    ob_start();
    session_start();
    if (!isset($_SESSION['useremail'])) {
        header("Location: login.php");
    }
    ?>
    <?php include "bagiankode/head.php"; ?>
    <body class="sb-nav-fixed">
        <?php
            include "bagiankode/menunav.php";
        ?>
        <div id="layoutSidenav">
            
        <?php
            include "bagiankode/menu.php";
        ?>

            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Input Komentar Mahasiswa</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Input Data Komentar</li>
                        </ol>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.9/css/select2.min.css">
</head>

<body>
  <?php
  include("includes/config.php"); 

  /* untuk menyimpan data yg diinsert ke table komentarmahasiswa */
if(isset($_POST['Simpan'])) 
{
    $mhs_NPM = $_POST['mhs_NPM'];
    $berita_JUDUL = $_POST['berita_JUDUL'];
    $komentar_ISI = $_POST['komentar_ISI'];
    $komentar_TGL = $_POST['komentar_TGL'];
    $komentar_WAKTU = $_POST['komentar_WAKTU'];

    mysqli_query($conn, "insert into komentar_mahasiswa values('$mhs_NPM', '$berita_JUDUL', '$komentar_ISI', '$komentar_TGL', '$komentar_WAKTU')"); 
    header("location:inputkomentarmahasiswa.php");
}

/* untuk cari data NPM/nama mahasiswa dan judul berita */
  if(isset($_POST["kirim"]))
  {
      $search = $_POST["search"];
      $query = mysqli_query($conn, "SELECT * FROM komentar_mahasiswa, mahasiswa, berita 
      WHERE komentar_mahasiswa.mhs_NPM = mahasiswa.mhs_NPM 
      AND komentar_mahasiswa.berita_JUDUL = berita.berita_JUDUL 
      AND komentar_mahasiswa.berita_JUDUL LIKE '%".$search."%'");
  }
  else
  {
      $query = mysqli_query($conn, "SELECT * FROM komentar_mahasiswa, mahasiswa, berita 
      WHERE komentar_mahasiswa.mhs_NPM = mahasiswa.mhs_NPM 
      AND komentar_mahasiswa.berita_JUDUL = berita.berita_JUDUL");
  }

  $datamhs = mysqli_query($conn, "select * from mahasiswa"); 
  $databerita = mysqli_query($conn, "select * from berita"); 
  ?>

    <div class="row">
    <div class="col-1"></div>
    <div class="col-10">
      <form method="POST" enctype="multipart/form-data">
        
<!--form untuk input data komentarmahasiswa mengambil mhs_NPM dari table mahasiswa dan berita_JUDUl dri table berita-->      
    <div class="row mb-3 mt-5">
      <label for="mhs_NPM" class="col-sm-2 col-form-label">NPM Mahasiswa</label>
      <div class="col-sm-10">
        <select class="form-control" id="mhs_NPM" name="mhs_NPM">
          <option>Pilih NPM Mahasiswa</option>
          <?php while($row = mysqli_fetch_array($datamhs))
          { ?>
          <option value="<?php echo $row["mhs_NPM"]?>">
            <?php echo $row["mhs_NPM"]?> - <?php echo $row["mhs_Nama"]?>
          </option>
          <?php } ?>
        </select>
      </div>
  </div>

  <div class="row mb-3">
    <label for="berita_JUDUL" class="col-sm-2 col-form-label">Judul Berita</label>
    <div class="col-sm-10">
      <select class="form-control" id="berita_JUDUL" name="berita_JUDUL">
        <option>Pilih Judul Berita</option>
        <?php while($row_berita = mysqli_fetch_array($databerita))
        { ?>
        <option value="<?php echo $row_berita["berita_JUDUL"]?>">
          <?php echo $row_berita["berita_JUDUL"]?>
        </option>
        <?php } ?>
      </select>
    </div>
  </div>

    <div class="row mb-3">
      <label for="komentar_ISI" class="col-sm-2 col-form-label">Isi Komentar</label>
      <div class="col-sm-10">
        <textarea class="form-control" id="komentar_ISI" name="komentar_ISI" rows="5" placeholder="Tulis komentar..."></textarea>      </div>
    </div>

    <div class="row mb-3">
      <label for="komentar_TGL" class="col-sm-2 col-form-label">Tanggal</label>
      <div class="col-sm-10">
        <input type="date" class="form-control" id="komentar_TGL" name="komentar_TGL">
      </div>
    </div>

    <div class="row mb-3">
      <label for="komentar_WAKTU" class="col-sm-2 col-form-label">Waktu</label>
      <div class="col-sm-10">
        <input type="time" class="form-control" id="komentar_WAKTU" name="komentar_WAKTU">
      </div>
    </div>

  <div class="form-group row">
    <div class="col-2"></div>
    <div class="col-10">
      <input type="submit" class="btn btn-success" value="Simpan" name="Simpan">
      <input type="reset" class="btn btn-danger" value="Batal" name="Batal">
    </div>
  </div>
  </form>

   <div class="col-1"></div>
        </div> </div>
        </div>

        <div class="row">
            <div class="col-1"></div>
            <div class="col-10">
                <div class="jumbotron mt-5 mb-3">
                    <h1 class="display-5">Daftar Komentar Mahasiswa</h1>
                </div>

                <!--mencari data judul berita dari tabel berita-->
                <form method="POST">
                  <div class="form-group row mt-5 mb-3">
                    <label for="search" class="col-sm-2">Cari Judul Berita</label>
                    <div class="col-sm-6">
                      <input type="text" name="search" class="form-control" id="search" 
                      value="<?php if(isset($_POST["search"]))
                        {echo $_POST["search"];}?>" placeholder="Cari judul berita">
                    </div>
                    <input type="submit" name="kirim" value="Cari" class="col-sm-1 btn btn-primary">
                  </div>
                </form>

                <!--menampilkan table komentarmahasiswa dari data yg udah diinsert-->               
                <table class="table table-striped table-success table-hover">
                    <tr class="info"> 
                        <th>NPM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Judul Berita</th>
                        <th>Isi Komentar</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th colspan="2" style="text-align: center;">Aksi</th>
                    </tr>

                    <?php { ?>
                    <?php while ($row = mysqli_fetch_array($query))
                    { ?>
                        <tr class="danger">
                            <td><?php echo $row['mhs_NPM']; ?> </td>
                            <td><?php echo $row['mhs_Nama']; ?> </td>
                            <td><?php echo $row['berita_JUDUL']; ?> </td>
                            <td><?php echo $row['komentar_ISI']; ?> </td>
                            <td><?php echo $row['komentar_TGL']; ?> </td>
                            <td><?php echo $row['komentar_WAKTU']; ?> </td>
                            
                            <td>
                                <a href="editkomentarmahasiswa.php?ubahkomentarmahasiswa=<?php echo $row["mhs_NPM"]?>&judul=<?php echo $row["berita_JUDUL"]?>" class="btn btn-success" title="EDIT">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                </svg>
                                </a>
                            </td>
                            
                            <td>
                              <a href="hapuskomentarmahasiswa.php?hapuskomentarmahasiswa=<?php echo $row["mhs_NPM"]?>&judul=<?php echo $row["berita_JUDUL"]?>" class="btn btn-danger" title="HAPUS">
                              <i class="bi bi-trash3"></i>
                              </a>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php } ?>
                  </table>
                </div>
              </div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
</body>
 </main>
                <?php
                    include "bagiankode/footer.php";
                ?>
            </div>
        </div>
        <?php
            include "bagiankode/jsscript.php";
        ?>

</html>