<!DOCTYPE html>
<html>
    <?php ob_start();
    session_start();
    if (!isset($_SESSION['useremail'])) {
        header("Location: login.php");
    }
    ?>
    <?php include "bagiankode/head.php"; ?>
    <body class="sb-nav-fixed">
        <?php include "bagiankode/menunav.php"; ?>
        <div id="layoutSidenav">   
            <?php include "bagiankode/menu.php";?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Edit Peserta Skripsi</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Edit Data Peserta Skripsi</li>
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

if (!isset($_GET["ubahpeserta"]) || empty($_GET["ubahpeserta"])) {
    echo "Error: NPM tidak ditemukan di URL. Harap kembali ke halaman daftar dan klik 'Edit' dengan benar.";
    exit; 
}

$NPM = $_GET["ubahpeserta"];

$edit_ps = mysqli_query($conn, "SELECT * FROM pesertaskripsi WHERE mhs_NPM = '$NPM'");
$row_edit = mysqli_fetch_array($edit_ps);

$edit_mhs = mysqli_query($conn, "SELECT * FROM mahasiswa WHERE mhs_NPM = '$NPM'");
$row_edit_mhs = mysqli_fetch_array($edit_mhs);


if(isset($_POST['Ubah']))
{
    $mhs_NPM = $_POST['mhs_NPM'];
    $peserta_SEMT = $_POST['peserta_SEMT'];
    $peserta_THAKD = $_POST['peserta_THAKD'];
    $peserta_TGLDAFTAR = $_POST['peserta_TGLDAFTAR'];
    $peserta_JUDUL = $_POST['peserta_JUDUL'];
    $peserta_PENJELASAN = $_POST['peserta_PENJELASAN'];   

     if(!empty($_FILES['peserta_DOKUMEN']['name'])) {
        $peserta_DOKUMEN = $_FILES['peserta_DOKUMEN']['name'];
        $tmp_file = $_FILES['peserta_DOKUMEN']['tmp_name'];
        move_uploaded_file($tmp_file, 'images/' . $peserta_DOKUMEN);

        $fileQuery = ", peserta_DOKUMEN = '$peserta_DOKUMEN'";
    } else {
        $fileQuery = ""; 
    }

    $query_update = "UPDATE pesertaskripsi SET mhs_NPM = '$mhs_NPM', peserta_SEMT = '$peserta_SEMT', peserta_THAKD = '$peserta_THAKD', peserta_TGLDAFTAR = '$peserta_TGLDAFTAR', peserta_JUDUL = '$peserta_JUDUL', peserta_PENJELASAN = '$peserta_PENJELASAN' $fileQuery 
                     WHERE mhs_NPM = '$NPM'"; 

    mysqli_query($conn, $query_update);
    header("location:inputpesertaskripsi.php");
    exit;
}

if(isset($_POST["kirim"]))
{
    $search = $_POST["search"];
    $query = mysqli_query($conn, "SELECT * FROM pesertaskripsi, mahasiswa 
        WHERE pesertaskripsi.mhs_NPM = mahasiswa.mhs_NPM AND (pesertaskripsi.mhs_NPM LIKE '%$search%' OR mahasiswa.mhs_Nama LIKE '%$search%')");
}
else
{
    $query = mysqli_query($conn, "SELECT * FROM pesertaskripsi ps, mahasiswa mhs
        WHERE ps.mhs_NPM = mhs.mhs_NPM");
}

$datamhs = mysqli_query($conn, "select * from mahasiswa"); 
?>

<div class="row">
<div class="col-1"></div>
<div class="col-10">
    <form method="POST" enctype="multipart/form-data">
    
    <div class="row mb-3 mt-5">
        <label for="mhs_NPM" class="col-sm-2 col-form-label">NPM Mahasiswa</label>
        <div class="col-sm-10">
            <select class="form-control select2" id="mhs_NPM" name="mhs_NPM" required>
                <option value="<?php echo $row_edit_mhs['mhs_NPM']; ?>">
                    <?php echo $row_edit_mhs['mhs_NPM']?> - 
                    <?php echo $row_edit_mhs['mhs_Nama']?>
                </option>
                
                <?php mysqli_data_seek($datamhs, 0); ?>
                <?php while ($row = mysqli_fetch_array($datamhs)) { 
                    if($row['mhs_NPM'] != $NPM) {
                ?>
                    <option value="<?php echo $row['mhs_NPM']; ?>">
                        <?php echo $row['mhs_NPM']?> - 
                        <?php echo $row['mhs_Nama']?>
                    </option>
                <?php } } ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="peserta_SEMT" class="col-sm-2 col-form-label">Semester</label>
        <div class="col-sm-10">
            <select class="form-control" id="peserta_SEMT" name="peserta_SEMT" required>
                <option value="">Pilih Semester</option>
                <option value="Ganjil" <?php if($row_edit['peserta_SEMT'] == 'Ganjil') echo 'selected'; ?>>Ganjil</option>
                <option value="Genap" <?php if($row_edit['peserta_SEMT'] == 'Genap') echo 'selected'; ?>>Genap</option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="peserta_THAKD" class="col-sm-2 col-form-label">Tahun Akademik</label>
        <div class="col-sm-10">
            <select class="form-control" id="peserta_THAKD" name="peserta_THAKD" required>
                <option value="">Pilih Tahun Akademik</option>
                <option value="2024-2025" <?php if($row_edit['peserta_THAKD'] == '2024-2025') echo 'selected'; ?>>2024-2025</option>
                <option value="2025-2026" <?php if($row_edit['peserta_THAKD'] == '2025-2026') echo 'selected'; ?>>2025-2026</option>
                <option value="2026-2027" <?php if($row_edit['peserta_THAKD'] == '2026-2027') echo 'selected'; ?>>2026-2027</option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="peserta_TGLDAFTAR" class="col-sm-2 col-form-label">Tanggal Daftar</label>
        <div class="col-sm-10">
            <input type="date" class="form-control" id="peserta_TGLDAFTAR" name="peserta_TGLDAFTAR" value="<?php echo $row_edit['peserta_TGLDAFTAR']; ?>" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="peserta_JUDUL" class="col-sm-2 col-form-label">Judul</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="peserta_JUDUL" name="peserta_JUDUL" placeholder="Masukkan Judul" value="<?php echo $row_edit['peserta_JUDUL']; ?>" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="peserta_PENJELASAN" class="col-sm-2 col-form-label">Penjelasan Singkat</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="peserta_PENJELASAN" name="peserta_PENJELASAN" placeholder="Masukkan Penjelasan Singkat" value="<?php echo $row_edit['peserta_PENJELASAN']; ?>" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="peserta_DOKUMEN" class="col-sm-2 col-form-label">Unggah Dokumen</label>
        <div class="col-sm-10">
            <input type="file" class="form-control" id="peserta_DOKUMEN" name="peserta_DOKUMEN">
            <p class="text-muted">
                File saat ini: <?php echo $row_edit['peserta_DOKUMEN']; ?> <br/>
                Unggah file baru untuk mengganti (biarkan kosong jika tidak ingin diubah).
            </p>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-2"></div>
        <div class="col-10">
            <input type="submit" class="btn btn-success" value="Ubah" name="Ubah"> 
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

                <form method="POST">
                <div class="form-group row mt-5 mb-3">
                <label for="search" class="col-sm-2">Cari NPM/Nama Mahasiswa</label>
                <div class="col-sm-6">
                <input type="text" name="search" class="form-control" id="search"
                value="<?php if(isset($_POST["search"]))
                {echo $_POST["search"];}?>" placeholder="Cari NPM atau Nama">
                </div>
                <input type="submit" name="kirim" value="Cari" class="col-sm-1 btn btn-primary">
                </div>
                </form>
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

                    <?php mysqli_data_seek($query, 0); ?>
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
                                <a href="hapuspesertaskripsi.php?hapuspeserta=<?php echo $row["mhs_NPM"]?>" class="btn btn-danger" title="HAPUS">
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