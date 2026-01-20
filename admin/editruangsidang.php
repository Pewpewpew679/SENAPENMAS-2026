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
                        <h1 class="mt-4">Edit Ruang Sidang Mahasiswa</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Edit Data Lokasi Sidang</li>
                        </ol>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Ruang Sidang</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.9/css/select2.min.css">
</head>

<body>
<?php
include("includes/config.php"); 

if (!isset($_GET["ubahruang"]) || empty($_GET["ubahruang"])) {
    echo "Error: NPM tidak ditemukan di URL. Harap kembali ke halaman daftar dan klik 'Edit' dengan benar.";
    exit; 
}

$NPM = $_GET["ubahruang"];

$edit_ruang = mysqli_query($conn, "SELECT * FROM ruangsidang WHERE mhs_NPM = '$NPM'");
$row_edit = mysqli_fetch_array($edit_ruang);

if(isset($_POST['Ubah']))
{
    $mhs_NPM = $_POST['mhs_NPM'];
    $ruangan_Nama = $_POST['ruangan_Nama'];
    $ruangan_Lokasi = $_POST['ruangan_Lokasi'];

    $query_update = "UPDATE ruangsidang SET mhs_NPM = '$mhs_NPM', ruangan_Nama = '$ruangan_Nama', ruangan_Lokasi = '$ruangan_Lokasi'
                     WHERE mhs_NPM = '$NPM'"; 

    mysqli_query($conn, $query_update);
    header("location:inputruangsidang.php");
    exit;
}

if(isset($_POST["kirim"]))
{
    $search = $_POST["search"];
    $query = mysqli_query($conn, "SELECT * FROM ruangsidang, mahasiswa 
        WHERE ruangsidang.mhs_NPM = mahasiswa.mhs_NPM 
        AND (ruangsidang.mhs_NPM LIKE '%$search%' OR mahasiswa.mhs_Nama LIKE '%$search%')");
}
else
{
    $query = mysqli_query($conn, "SELECT * FROM ruangsidang, mahasiswa
        WHERE ruangsidang.mhs_NPM = mahasiswa.mhs_NPM");
}

$data_mahasiswa = mysqli_query($conn, "SELECT DISTINCT ujianskripsi.mhs_NPM, mahasiswa.mhs_Nama 
    FROM ujianskripsi, mahasiswa 
    WHERE ujianskripsi.mhs_NPM = mahasiswa.mhs_NPM 
    ORDER BY mahasiswa.mhs_Nama ASC");

?>

<div class="row">
<div class="col-1"></div>
<div class="col-10">
    <form method="POST">
    
    <div class="row mb-3 mt-5">
        <label for="mhs_NPM" class="col-sm-2 col-form-label">Mahasiswa</label>
        <div class="col-sm-10">
            <select class="form-control select2" id="mhs_NPM" name="mhs_NPM" required>
                <option value="">-- Pilih Mahasiswa --</option>
                <?php while ($row_mhs = mysqli_fetch_array($data_mahasiswa)) { ?>
                    <option value="<?php echo $row_mhs['mhs_NPM']; ?>" 
                        <?php if($row_edit['mhs_NPM'] == $row_mhs['mhs_NPM']) echo 'selected'; ?>>
                        
                        <?php echo $row_mhs['mhs_NPM']?> - 
                        <?php echo $row_mhs['mhs_Nama']?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="ruangan_Nama" class="col-sm-2 col-form-label">Nama Ruangan</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="ruangan_Nama" name="ruangan_Nama" 
                   value="<?php echo $row_edit['ruangan_Nama']; ?>" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="ruangan_Lokasi" class="col-sm-2 col-form-label">Lokasi / Gedung</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="ruangan_Lokasi" name="ruangan_Lokasi" 
                   value="<?php echo $row_edit['ruangan_Lokasi']; ?>" required>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-2"></div>
        <div class="col-10">
            <input type="submit" class="btn btn-success" value="Ubah" name="Ubah"> 
            <a href="inputruangsidang.php" class="btn btn-danger">Batal</a>
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
                    <h1 class="display-5">Daftar Lokasi Sidang</h1>
                </div>

                <form method="POST">
                <div class="form-group row mt-5 mb-3">
                    <label for="search" class="col-sm-2">Cari Data</label>
                    <div class="col-sm-6">
                        <input type="text" name="search" class="form-control" id="search"
                        value="<?php if(isset($_POST["search"])) {echo $_POST["search"];}?>" placeholder="Cari NPM atau Nama">
                    </div>
                    <input type="submit" name="kirim" value="Cari" class="col-sm-1 btn btn-primary">
                </div>
                </form>

                <table class="table table-success table-striped table-hover">
                    <tr class="info"> 
                        <th>NPM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Nama Ruangan</th>
                        <th>Lokasi</th>
                        <th colspan="2" style="text-align: center;">Aksi</th>
                    </tr>

                    <?php while ($row = mysqli_fetch_array($query)) { ?>
                        <tr class="table-light">
                            <td><?php echo $row['mhs_NPM']; ?></td>
                            <td><?php echo $row['mhs_Nama']; ?></td> 
                            <td><?php echo $row['ruangan_Nama']; ?></td>
                            <td><?php echo $row['ruangan_Lokasi']; ?></td>
                            
                            <td style="text-align: center;">
                                <a href="editruangsidang.php?ubahruang=<?php echo $row["mhs_NPM"]?>" class="btn btn-success" title="EDIT">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                    </svg>
                                </a>
                            </td>
                            <td style="text-align: center;">
                                <a href="hapusruangsidang.php?hapusruangsidang=<?php echo $row["mhs_NPM"]?>" class="btn btn-danger" title="HAPUS">
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
<?php include "bagiankode/footer.php"; ?>
</div>
</div>
<?php include "bagiankode/jsscript.php"; ?>
</html>