<!DOCTYPE html>
<html>
    <!--pengaman halaman, memastikan hanya user yg udh login yg bisa mengakses halaman Input penasihat.-->
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
                        <h1 class="mt-4">Input Penasihat</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Input Data Penasihat</li>
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

  /* untuk menyimpan data yg diinsert ke table penasihat */
if(isset($_POST['Simpan'])) 

{
    $mhs_NPM = $_POST['npmMHS'];
    $dosen_NIDN = $_POST['nidnDOSEN'];
    
    $penasihat_FILE = $_FILES['penasihatFILE']['name'];
    $dokumen_tmp = $_FILES['penasihatFILE']['tmp_name'];

    $typefoto = pathinfo($penasihat_FILE, PATHINFO_EXTENSION);

      $ukuranfoto = $_FILES ["penasihatFILE"]["size"];
      $path = "images/" .$penasihat_FILE;

      if($ukuranfoto <= 500000){
        if(($typefoto=="jpg") or ($typefoto=='png')){
          if(!file_exists($path)){
              move_uploaded_file($dokumen_tmp, 'images/'.$penasihat_FILE);
          }
        }
      }
    $penasihat_KET= $_POST['penasihatKET'];

   mysqli_query($conn, "insert into penasihat values('$mhs_NPM', '$dosen_NIDN', '$penasihat_FILE', '$penasihat_KET')"); 
    header("location:inputpenasihat.php");
}

/* pencarian data npm mahasiswa yang diambil dri table mahasiswa dan nidn dosen yang diambil dri table dosen */
if(isset($_POST["kirim"]))
  {
      $search = $_POST["search"];
      $query = mysqli_query($conn, "SELECT * FROM penasihat,dosen,mahasiswa
      WHERE mahasiswa.mhs_NPM = penasihat.mhs_NPM AND penasihat.dosen_NIDN = dosen.
      dosen_NIDN AND penasihat.mhs_NPM LIKE '%".$search."%'");
  }
  else
  {
      $query = mysqli_query($conn, "SELECT * FROM penasihat,dosen,mahasiswa
      WHERE mahasiswa.mhs_NPM = penasihat.mhs_NPM AND penasihat.dosen_NIDN = dosen.
      dosen_NIDN");
  }

  $datamhs = mysqli_query($conn, "select * from mahasiswa"); 
  $datadosen = mysqli_query($conn, "select * from dosen"); 
  ?>

    <div class="row">
    <div class="col-1"></div>
    <div class="col-10">
      <form method="POST" enctype="multipart/form-data">
      
      <!--form untuk input data penasihat mengambil NPM mahasiswa dan NIDN dosen-->
    <div class="row mb-3 mt-5">
      <label for="npmMHS" class="col-sm-2 col-form-label">NPM Mahasiswa</label>
      <div class="col-sm-10">
    <select class="form-control" id="npmMHS" name="npmMHS">
      <option>Cari NPM Mahasiswa</option>
      <?php while($row = mysqli_fetch_array($datamhs))
      { ?>
      <option value="<?php echo $row["mhs_NPM"]?>">
        <?php echo $row["mhs_NPM"]?> - 
        <?php echo $row["mhs_Nama"]?>
      </option>
      <?php } ?>
    </select>
  </div>
  </div>

  <div class="row mb-3">
    <label for="nidnDOSEN" class="col-sm-2 col-form-label">NIDN Dosen</label>
    <div class="col-sm-10">
      <select class="form-control" id="nidnDOSEN" name="nidnDOSEN">
        <option>Cari NIDN Dosen</option>
        <?php while($row_dosen = mysqli_fetch_array($datadosen))
        { ?>
        <option value="<?php echo $row_dosen["dosen_NIDN"]?>">
          <?php echo $row_dosen["dosen_NIDN"]?> - 
          <?php echo $row_dosen["dosen_Nama"]?>
        </option>
        <?php } ?>
      </select>
    </div>
  </div>
    <div class="row mb-3">
      <label for="penasihatFILE" class="col-sm-2 col-form-label">Unggah Dokumen</label>
      <div class="col-sm-10">
        <input type="file" class="form-control" id="penasihatFILE" name="penasihatFILE">
        <p class="help-block">Unggah Fill Dokumen</p>
      </div>
    </div>
    <div class="row mb-3">
      <label for="penasihatKET" class="col-sm-2 col-form-label">Keterangan</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="penasihatKET" name="penasihatKET">
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
                    <h1 class="display-5">Daftar Penasihat Akademik</h1>
                </div>

                <!--mencari data npm dari tabel mahasiswa-->
                <form method="POST">
                  <div class="form-group row mt-5 mb-3">
                    <label for="search" class="col-sm-2">Cari NPM Mahasiswa</label>
                    <div class="col-sm-6">
                      <input type="text" name="search" class="form-control" id="search" 
                      value="<?php if(isset($_POST["search"]))
                        {echo $_POST["search"];}?>" placeholder="Cari judul berita">
                    </div>
                    <input type="submit" name="kirim" value="Cari" class="col-sm-1 btn btn-primary">
                  </div>
                </form>

                <!--menampilkan table penasihat dari data yg udah diinsert-->               
                <table class="table table-striped table-success table-hover">
                    <tr class="info"> 
                        <th>NPM</th>
                        <th>Nama Mahasiswa</th>
                        <th>NIDN</th>
                        <th>Nama Dosen</th>
                        <th>Dokumen</th>
                        <th>Keterangan</th>
                        <th colspan="2" style="text-align: center;">Aksi</th>
                    </tr>

                    <?php { ?>
                    <?php while ($row = mysqli_fetch_array($query))
                    { ?>
                        <tr class="danger">
                            <td><?php echo $row['mhs_NPM']; ?> </td>
                            <td><?php echo $row['mhs_Nama']; ?> </td>
                            <td><?php echo $row['dosen_NIDN']; ?> </td>
                            <td><?php echo $row['dosen_Nama']; ?> </td>
                            <td>
                              <?php if($row['penasihat_FILE'] == "") { echo "<img src='images/noimage.jpg' 
                                width='88'/>";} else { ?>
                              <img src="images/<?php echo $row['penasihat_FILE'] ?>" width="88" class="
                                img-responsive" />
                              <?php }?>
                              </td>
                              <td><?php echo $row['penasihat_KET']; ?> </td>
                              <td>
                                <a href="editpenasihat.php?ubahpenasihat=<?php echo $row["mhs_NPM"]?>" class="btn btn-success" title="EDIT">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                </svg>
                                </td>
                              <td>
                              <a href="hapuspenasihat.php?hapuspenasihat=<?php echo $row["mhs_NPM"]?>" class="btn btn-danger" title="HAPUS">
                              <i class="bi bi-trash3"></i>
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