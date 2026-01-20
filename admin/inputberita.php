<!DOCTYPE html>
<html>
    <!--pengaman halaman, memastikan hanya user yg udh login yg bisa mengakses halaman Input berita.-->
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
                        <h1 class="mt-4">Input Berita</h1> 
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Input Data Berita</li> 
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
/* untuk menyimpan data yg diinsert ke table berita */
if(isset($_POST['Simpan'])) {
    $berita_JUDUL = $_POST['beritajudul'];
    $berita_TGL = $_POST['beritatgl'];
    $berita_ISI = $_POST['beritaisi'];
    
    $berita_FOTO = $_FILES['beritafoto']['name'];
    $dokumen_tmp = $_FILES['beritafoto']['tmp_name'];
    $typefoto = pathinfo($berita_FOTO, PATHINFO_EXTENSION);
    $ukuranfoto = $_FILES["beritafoto"]["size"];
    
    $path = "images/" . $berita_FOTO;

    if($ukuranfoto <= 500000){
        if(($typefoto=="jpg") or ($typefoto=='png')){
          if(!file_exists($path)){
              move_uploaded_file($dokumen_tmp, 'images/'.$berita_FOTO);
          }
        }
      }

    mysqli_query($conn, "INSERT INTO berita VALUES('$berita_JUDUL', '$berita_TGL', '$berita_ISI', '$berita_FOTO')");
    header("location:inputberita.php");
}
/* untuk cari data berita*/
if(isset($_POST["kirim"])) {
    $search = $_POST["search"];
    $query = mysqli_query($conn, "SELECT * FROM berita WHERE berita_JUDUL LIKE '%".$search."%'");
} else {
    $query = mysqli_query($conn, "SELECT * FROM berita");
}
?>

<div class="row">
    <div class="col-1"></div>
    <div class="col-10">
        <form method="POST" enctype="multipart/form-data">

            <!--form untuk input data berita-->
            <div class="row mb-3 mt-3">
                <label class="col-sm-2 col-form-label">Judul Berita</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="beritajudul" placeholder="Masukkan judul berita" required>
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Tanggal</label>
                <div class="col-sm-10">
                    <input type="date" class="form-control" name="beritatgl" required>
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Isi Berita</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="beritaisi" rows="5" placeholder="Masukkan isi berita..." required></textarea>
                </div>
            </div>

            <div class="row mb-4">
                <label class="col-sm-2 col-form-label">Foto Berita</label>
                <div class="col-sm-10">
                    <input type="file" class="form-control" name="beritafoto" accept=".jpg,.png" required>
                    <small class="text-muted">Format: JPG/PNG — Maks. 500KB</small>
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
    </div>
    <div class="col-1"></div>
</div> 

<div class="row">
    <div class="col-1"></div>
    <div class="col-10">
        <div class="jumbotron mt-5 mb-3">
            <h1 class="display-5">Daftar Berita</h1>
        </div>

        <!--mencari data judul berita dari tabel berita-->
        <form method="POST">
            <div class="form-group row mt-5 mb-3">
                <label for="search" class="col-sm-2 col-form-label">Cari Berita</label> 
                <div class="col-sm-6">
                    <input type="text" name="search" class="form-control" id="search"
                    value="<?php if(isset($_POST["search"])) { echo $_POST["search"]; } ?>" placeholder="Cari Berita"> 
                </div>
                <div class="col-sm-2">
                    <input type="submit" name="kirim" value="Cari" class="btn btn-primary">
                </div>
            </div>
        </form>

        <!--menampilkan table berita dari data yg udah diinsert-->
        <table class="table table-success table-striped table-hover">
            <thead>
                <tr class="info">
                    <th>Judul Berita</th>
                    <th>Tanggal</th>
                    <th>Isi</th>
                    <th>Foto</th>
                    <th colspan="2" style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php { ?>
            <?php while ($row = mysqli_fetch_array($query)) 
                { ?>
                <tr class="danger">
                    <td><?php echo $row['berita_JUDUL']; ?></td>
                    <td><?php echo $row['berita_TGL']; ?></td>
                    <td><?php echo $row['berita_ISI']; ?></td>
                    
                    <td>
                        <?php if(!empty($row['berita_FOTO']) && file_exists("images/" . $row['berita_FOTO'])) { ?>
                            <img src="images/<?php echo $row['berita_FOTO']; ?>" width="80" alt="Foto Berita">
                        <?php } else { echo "No Image"; } ?>
                    </td>

                    <td>
                        <a href="editberita.php?ubahberita=<?php echo $row["berita_JUDUL"]?>" class="btn btn-success" title="EDIT">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                            </svg>
                        </a>
                    </td>
                    <td>
                        <a href="hapusberita.php?hapusberita=<?php echo $row["berita_JUDUL"]?>" class="btn btn-danger" title="HAPUS">
                            <i class="bi bi-trash3"></i>
                        </a>
                    </td>
                 </tr>
            <?php } ?>
            <?php } ?>
        </table>
    </div>
    <div class="col-1"></div>
</div> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
</body>

</div>
                </main>
                <?php
                    include "bagiankode/footer.php";
                ?>
            </div>
        </div>
        <?php
            include "bagiankode/jsscript.php";
        ?>

    </body>
</html>