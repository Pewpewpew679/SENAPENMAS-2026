<!DOCTYPE html>
<html>
    <!--pengaman halaman, memastikan hanya user yg udh login yg bisa mengakses halaman Input ujianskripsi.-->
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
                        <h1 class="mt-4">Input Ujian Skripsi</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Input Data Ujian Skripsi</li>
                        </ol>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.9/css/select2.min.css">
</head>

<body>
<?php
include("includes/config.php"); 

/* untuk menyimpan data yg diinsert ke table ujianskripsi */
if(isset($_POST['Simpan'])) 
{
    $mhs_NPM = $_POST['mhs_NPM'];
    $ujian_TGL = $_POST['ujian_TGL'];
    $ujian_WAKTU = $_POST['ujian_WAKTU'];
    
    $ujian_FOTO = $_FILES['ujian_FOTO']['name'];
    $file_tmp = $_FILES['ujian_FOTO']['tmp_name'];
    $tipe_file = $_FILES['ujian_FOTO']['type'];

    if(!empty($ujian_FOTO)) {
        move_uploaded_file($file_tmp, 'images/'.$ujian_FOTO);
    }
    
    mysqli_query($conn, "INSERT INTO ujianskripsi VALUES ('$mhs_NPM', '$ujian_TGL', '$ujian_WAKTU', '$ujian_FOTO')"); 
    header("location:inputujianskripsi.php");
}

/* pencarian data */
if(isset($_POST["kirim"]))
{
    $search = $_POST["search"];
    $query = mysqli_query($conn, "SELECT * FROM ujianskripsi, mahasiswa, dosen, bimbinganskripsi
        WHERE ujianskripsi.mhs_NPM = mahasiswa.mhs_NPM 
        AND bimbinganskripsi.mhs_NPM = ujianskripsi.mhs_NPM
        AND bimbinganskripsi.dosen_NIDN = dosen.dosen_NIDN
        AND (ujianskripsi.mhs_NPM LIKE '%$search%' OR mahasiswa.mhs_Nama LIKE '%$search%')
        GROUP BY ujianskripsi.mhs_NPM");
}
else
{
    $query = mysqli_query($conn, "SELECT * FROM ujianskripsi, mahasiswa, dosen, bimbinganskripsi
        WHERE ujianskripsi.mhs_NPM = mahasiswa.mhs_NPM 
        AND bimbinganskripsi.mhs_NPM = ujianskripsi.mhs_NPM
        AND bimbinganskripsi.dosen_NIDN = dosen.dosen_NIDN
        GROUP BY ujianskripsi.mhs_NPM");
}
/* end pencarian data */

$data_bimbingan = mysqli_query($conn, "SELECT DISTINCT bimbinganskripsi.mhs_NPM, mahasiswa.mhs_Nama 
                                       FROM bimbinganskripsi, mahasiswa 
                                       WHERE bimbinganskripsi.mhs_NPM = mahasiswa.mhs_NPM");
?>

<div class="row">
<div class="col-1"></div>
<div class="col-10">
    <form method="POST" enctype="multipart/form-data">

    <!--form untuk input data ujianskripsi mengambil NPM mahasiswa yg udh melakukan bimbinganskripsi-->
    <div class="row mb-3 mt-5">
        <label for="mhs_NPM" class="col-sm-2 col-form-label">Mahasiswa</label>
        <div class="col-sm-10">
            <select class="form-control select2" id="mhs_NPM" name="mhs_NPM" required>
                <option value="">Pilih Mahasiswa yang sudah Bimbingan</option>
                <?php while ($row = mysqli_fetch_array($data_bimbingan)) { ?>
                    <option value="<?php echo $row['mhs_NPM']; ?>">
                        <?php echo $row['mhs_NPM']?> - 
                        <?php echo $row['mhs_Nama']?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="ujian_TGL" class="col-sm-2 col-form-label">Tanggal Ujian</label>
        <div class="col-sm-10">
            <input type="date" class="form-control" id="ujian_TGL" name="ujian_TGL" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="ujian_WAKTU" class="col-sm-2 col-form-label">Waktu (Jam) Ujian</label>
        <div class="col-sm-10">
            <input type="time" class="form-control" id="ujian_WAKTU" name="ujian_WAKTU" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="ujian_FOTO" class="col-sm-2 col-form-label">Foto Ujian</label>
        <div class="col-sm-10">
            <input type="file" class="form-control" id="ujian_FOTO" name="ujian_FOTO" accept="image/jpeg, .jpg, .jpeg" required>            
            <small class="text-muted">Format file harus JPG.</small>
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
                    <h1 class="display-5">Daftar Jadwal Ujian Skripsi</h1>
                </div>

                <!--mencari data npm dari tabel mahasiswa-->
                <form method="POST">
                    <div class="form-group row mt-5 mb-3">
                        <label for="search" class="col-sm-2">Cari Data</label>
                        <div class="col-sm-6">
                            <input type="text" name="search" class="form-control" id="search"
                            value="<?php if(isset($_POST["search"]))
                                {echo $_POST["search"];}?>" placeholder="Cari NPM atau Nama Mahasiswa">
                        </div>
                        <input type="submit" name="kirim" value="Cari" class="col-sm-1 btn btn-primary">
                    </div>
                </form>
                
                <!--menampilkan table ujianskripsi dari data yg udah diinsert-->
                <table class="table table-success table-striped table-hover">
                    <tr class="info"> 
                        <th>NPM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Dosen Pembimbing</th>
                        <th>Tanggal Ujian</th>
                        <th>Waktu Ujian</th>
                        <th>Foto</th>
                        <th colspan="2" style="text-align: center;">Aksi</th>
                    </tr>

                    <?php while ($row = mysqli_fetch_array($query)) { ?>
                        <tr class="danger">
                            <td><?php echo $row['mhs_NPM']; ?> </td>
                            <td><?php echo $row['mhs_Nama']; ?> </td>
                            <td><?php echo $row['dosen_Nama']; ?> </td> 
                            <td><?php echo $row['ujian_TGL']; ?> </td>
                            <td><?php echo $row['ujian_WAKTU']; ?> </td>
                            <td style="text-align: center;">
                                <?php if(!empty($row['ujian_FOTO'])) { ?>
                                    <img src="images/<?php echo $row['ujian_FOTO']; ?>" width="80px" height="auto">
                                <?php } else { echo "Tidak ada foto"; } ?>
                            </td>

                            <td>
                                <a href="editujianskripsi.php?ubahujian=<?php echo $row["mhs_NPM"]?>" class="btn btn-success" title="EDIT">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                    </svg>
                                </a>
                            </td>
                            <td>
                                <a href="hapusujianskripsi.php?hapusujianskripsi=<?php echo $row["mhs_NPM"]?>" class="btn btn-danger" title="HAPUS">
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