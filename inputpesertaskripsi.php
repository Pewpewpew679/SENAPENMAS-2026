<!DOCTYPE html>
<html>
    <!--pengaman halaman, memastikan hanya user yg udh login yg bisa mengakses halaman Input pesertaskripsi.-->
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
                        <h1 class="mt-4">Input Peserta Skripsi</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Input Data Peserta Skripsi</li>
                        </ol>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.9/css/select2.min.css">
</head>
<body>
    
<?php
include("includes/config.php"); 

/* untuk menyimpan data yg diinsert ke table pesertaskripsi */
if(isset($_POST['Simpan'])) 
{
    $mhs_NPM = $_POST['mhs_NPM'];
    $peserta_SEMT = $_POST['peserta_SEMT'];
    $peserta_THAKD = $_POST['peserta_THAKD'];
    $peserta_TGLDAFTAR = $_POST['peserta_TGLDAFTAR'];
    $peserta_JUDUL = $_POST['peserta_JUDUL'];
    $peserta_PENJELASAN = $_POST['peserta_PENJELASAN'];
    
    $peserta_DOKUMEN = $_FILES['peserta_DOKUMEN']['name'];
    $tmp_file = $_FILES['peserta_DOKUMEN']['tmp_name'];
    
    if(!empty($peserta_DOKUMEN)) {
        move_uploaded_file($tmp_file, 'images/' . $peserta_DOKUMEN);
    }

    mysqli_query($conn, "INSERT INTO pesertaskripsi VALUES ('$mhs_NPM', '$peserta_SEMT', '$peserta_THAKD', '$peserta_TGLDAFTAR', '$peserta_JUDUL', '$peserta_PENJELASAN', '$peserta_DOKUMEN')"); 
    header("location:inputpesertaskripsi.php");
}

/* pencarian data npm mahasiswa */
if(isset($_POST["kirim"]))
{
    $search = $_POST["search"];
    $query = mysqli_query($conn, "SELECT * FROM pesertaskripsi, mahasiswa 
        WHERE pesertaskripsi.mhs_NPM = mahasiswa.mhs_NPM AND (pesertaskripsi.mhs_NPM LIKE '%$search%' OR mahasiswa.mhs_Nama LIKE '%$search%')");
}
else
{
    $query = mysqli_query($conn, "SELECT * FROM pesertaskripsi, mahasiswa
        WHERE pesertaskripsi.mhs_NPM = mahasiswa.mhs_NPM");
}

$datamhs = mysqli_query($conn, "select * from mahasiswa"); 
?>

<div class="row">
<div class="col-1"></div>
<div class="col-10">
    <form method="POST" enctype="multipart/form-data">

    <!--form untuk input data pesertaskripsi mengambil NPM mahasiswa-->
    <div class="row mb-3 mt-5">
        <label for="mhs_NPM" class="col-sm-2 col-form-label">NPM Mahasiswa</label>
        <div class="col-sm-10">
            <select class="form-control select2" id="mhs_NPM" name="mhs_NPM" required>
                <option value="">Cari NPM Mahasiswa</option>
                <?php while ($row = mysqli_fetch_array($datamhs)) { ?>
                    <option value="<?php echo $row['mhs_NPM']; ?>">
                        <?php echo $row['mhs_NPM']?> - 
                        <?php echo $row['mhs_Nama']?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="peserta_SEMT" class="col-sm-2 col-form-label">Semester</label>
        <div class="col-sm-10">
            <select class="form-control" id="peserta_SEMT" name="peserta_SEMT" required>
                <option value="">Pilih Semester</option>
                <option value="Ganjil">Ganjil</option>
                <option value="Genap">Genap</option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="peserta_THAKD" class="col-sm-2 col-form-label">Tahun Akademik</label>
        <div class="col-sm-10">
            <select class="form-control" id="peserta_THAKD" name="peserta_THAKD" required>
                <option value="">Pilih Tahun Akademik</option>
                <option value="2024-2025">2024-2025</option>
                <option value="2025-2026">2025-2026</option>
                <option value="2026-2027">2026-2027</option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="peserta_TGLDAFTAR" class="col-sm-2 col-form-label">Tanggal Daftar</label>
        <div class="col-sm-10">
            <input type="date" class="form-control" id="peserta_TGLDAFTAR" name="peserta_TGLDAFTAR" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="peserta_JUDUL" class="col-sm-2 col-form-label">Judul</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="peserta_JUDUL" name="peserta_JUDUL" placeholder="Masukkan Judul" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="peserta_PENJELASAN" class="col-sm-2 col-form-label">Penjelasan Singkat</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="peserta_PENJELASAN" name="peserta_PENJELASAN" placeholder="Masukkan Penjelasan Singkat" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="peserta_DOKUMEN" class="col-sm-2 col-form-label">Unggah Dokumen</label>
        <div class="col-sm-10">
            <input type="file" class="form-control" id="peserta_DOKUMEN" name="peserta_DOKUMEN">
            <p class="text-muted">Unggah file dokumen</p>
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
                    <h1 class="display-5">Daftar Peserta Skripsi</h1>
                </div>
                
                <!--mencari data npm dari tabel mahasiswa-->
                <form method="POST">
                    <div class="form-group row mt-5 mb-3">
                        <label for="search" class="col-sm-2">Cari NPM Mahasiswa</label>
                        <div class="col-sm-6">
                            <input type="text" name="search" class="form-control" id="search"
                            value="<?php if(isset($_POST["search"]))
                                {echo $_POST["search"];}?>" placeholder="Cari NPM Mahasiswa">
                        </div>
                        <input type="submit" name="kirim" value="Cari" class="col-sm-1 btn btn-primary">
                    </div>
                </form>

                <!--menampilkan table pesertaskripsi dari data yg udah diinsert-->               
                <table class="table table-success table-striped table-hover">
                    <tr class="info"> 
                        <th>NPM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Semester</th>
                        <th>Tahun Akademik</th>
                        <th>Tanggal Daftar</th>
                        <th>Judul</th>
                        <th>Penjelasan Singkat</th>
                        <th>Dokumen</th>
                        <th colspan="2" style="text-align: center;">Aksi</th>
                    </tr>

                    <?php while ($row = mysqli_fetch_array($query)) { ?>
                        <tr class="danger">
                            <td><?php echo $row['mhs_NPM']; ?> </td>
                            <td><?php echo $row['mhs_Nama']; ?> </td>
                            <td><?php echo $row['peserta_SEMT']; ?> </td>
                            <td><?php echo $row['peserta_THAKD']; ?> </td>
                            <td><?php echo $row['peserta_TGLDAFTAR']; ?> </td>
                            <td><?php echo $row['peserta_JUDUL']; ?> </td>
                            <td><?php echo $row['peserta_PENJELASAN']; ?> </td>
                            <td>
                                <?php if($row['peserta_DOKUMEN'] == "") { 
                                    echo "<img src='images/noimage.jpg' width='88' class='img-responsive'/>";
                                } else { ?>
                                    <img src="images/<?php echo $row['peserta_DOKUMEN'] ?>" width="88" class="img-responsive" />
                                <?php }?>
                            </td>
                            <td>
                                <a href="editpesertaskripsi.php?ubahpeserta=<?php echo $row["mhs_NPM"]?>" class="btn btn-success" title="EDIT">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                    </svg>
                                </a>
                            </td>
                            <td>
                                <a href="hapuspesertaskripsi.php?hapuspesertaskripsi=<?php echo $row["mhs_NPM"]?>" class="btn btn-danger" title="HAPUS">
                                    <i class="bi bi-trash3"></i>
                                </a>
                            </td>
                        </tr>
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