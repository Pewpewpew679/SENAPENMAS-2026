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
            <?php include "bagiankode/menu.php"; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Edit Bimbingan Skripsi</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Edit Data Bimbingan Skripsi</li>
                        </ol>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.9/css/select2.min.css">
</head>

<body>
<?php
include("includes/config.php"); 

if (!isset($_GET["ubahbimbingan"]) || empty($_GET["ubahbimbingan"])) {
    echo "Error: ID/NPM tidak ditemukan. Harap kembali ke halaman daftar.";
    exit; 
}

$NPM_TARGET = $_GET["ubahbimbingan"];

$edit_bim = mysqli_query($conn, "SELECT * FROM bimbinganskripsi WHERE mhs_NPM = '$NPM_TARGET'");
$row_edit = mysqli_fetch_array($edit_bim);

if (!$row_edit) {
    echo "Data bimbingan tidak ditemukan.";
    exit;
}

if(isset($_POST['Ubah']))
{
    $dosen_NIDN = $_POST['dosen_NIDN'];
    $mhs_NPM = $_POST['mhs_NPM'];
    $bimbingan_TGL = $_POST['bimbingan_TGL'];
    $bimbingan_ISI = $_POST['bimbingan_ISI'];
    
    $nama_file_baru = $_FILES['bimbingan_DOKUMEN']['name'];
    $tmp_file = $_FILES['bimbingan_DOKUMEN']['tmp_name'];
    
    if(!empty($nama_file_baru)) {
        $ext = pathinfo($nama_file_baru, PATHINFO_EXTENSION);
        if(strtolower($ext) == 'pdf') {
            move_uploaded_file($tmp_file, 'images/' . $nama_file_baru);
            
            $query_update = "UPDATE bimbinganskripsi SET dosen_NIDN = '$dosen_NIDN', mhs_NPM = '$mhs_NPM', bimbingan_TGL = '$bimbingan_TGL', bimbingan_ISI = '$bimbingan_ISI', bimbingan_DOKUMEN = '$nama_file_baru'
                             WHERE mhs_NPM = '$NPM_TARGET'"; 
        } else {
            echo "<script>alert('Format file harus PDF! Data gagal diubah.');</script>";
            echo "<script>window.location.href='editbimbinganskripsi.php?ubahbimbingan=$NPM_TARGET';</script>";
            exit;
        }
    } else {
        $query_update = "UPDATE bimbinganskripsi SET dosen_NIDN = '$dosen_NIDN', mhs_NPM = '$mhs_NPM', bimbingan_TGL = '$bimbingan_TGL', bimbingan_ISI = '$bimbingan_ISI'
                         WHERE mhs_NPM = '$NPM_TARGET'"; 
    }

    mysqli_query($conn, $query_update);
    header("location:inputbimbinganskripsi.php");
    exit;
}


if(isset($_POST["kirim"]))
{
    $search = $_POST["search"];
    $query = mysqli_query($conn, "SELECT * FROM bimbinganskripsi, dosen, mahasiswa, pesertaskripsi
        WHERE bimbinganskripsi.dosen_NIDN = dosen.dosen_NIDN
        AND bimbinganskripsi.mhs_NPM = mahasiswa.mhs_NPM
        AND bimbinganskripsi.mhs_NPM = pesertaskripsi.mhs_NPM
        AND (bimbinganskripsi.mhs_NPM LIKE '%$search%' 
            OR mahasiswa.mhs_Nama LIKE '%$search%' 
            OR dosen.dosen_Nama LIKE '%$search%')");
}
else
{
    $query = mysqli_query($conn, "SELECT * FROM bimbinganskripsi, dosen, mahasiswa, pesertaskripsi
        WHERE bimbinganskripsi.dosen_NIDN = dosen.dosen_NIDN
        AND bimbinganskripsi.mhs_NPM = mahasiswa.mhs_NPM
        AND bimbinganskripsi.mhs_NPM = pesertaskripsi.mhs_NPM
        ORDER BY bimbinganskripsi.bimbingan_TGL DESC");
}


$datadosen = mysqli_query($conn, "SELECT * FROM dosen"); 
$datapeserta = mysqli_query($conn, "SELECT ps.mhs_NPM, m.mhs_Nama, ps.peserta_JUDUL 
                                    FROM pesertaskripsi ps 
                                    JOIN mahasiswa m ON ps.mhs_NPM = m.mhs_NPM");
?>

<div class="row">
<div class="col-1"></div>
<div class="col-10">
    <form method="POST" enctype="multipart/form-data">
    
    <div class="row mb-3 mt-5">
        <label for="dosen_NIDN" class="col-sm-2 col-form-label">Dosen Pembimbing</label>
        <div class="col-sm-10">
            <select class="form-control select2" id="dosen_NIDN" name="dosen_NIDN" required>
                <option value="">Pilih Dosen</option>
                <?php 
                mysqli_data_seek($datadosen, 0);
                while ($row = mysqli_fetch_array($datadosen)) { 
                    $selected = ($row['dosen_NIDN'] == $row_edit['dosen_NIDN']) ? 'selected' : '';
                ?>
                    <option value="<?php echo $row['dosen_NIDN']; ?>" <?php echo $selected; ?>>
                        <?php echo $row['dosen_NIDN']?> - 
                        <?php echo $row['dosen_Nama']?> 
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="mhs_NPM" class="col-sm-2 col-form-label">Mahasiswa</label>
        <div class="col-sm-10">
            <select class="form-control select2" id="mhs_NPM" name="mhs_NPM" required>
                <option value="">Pilih Mahasiswa</option>
                <?php 
                mysqli_data_seek($datapeserta, 0);
                while ($row = mysqli_fetch_array($datapeserta)) { 
                    $selected = ($row['mhs_NPM'] == $row_edit['mhs_NPM']) ? 'selected' : '';
                ?>
                    <option value="<?php echo $row['mhs_NPM']; ?>" <?php echo $selected; ?>>
                        <?php echo $row['mhs_NPM']?> - 
                        <?php echo $row['mhs_Nama']?> 
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="bimbingan_TGL" class="col-sm-2 col-form-label">Tanggal Bimbingan</label>
        <div class="col-sm-10">
            <input type="date" class="form-control" id="bimbingan_TGL" name="bimbingan_TGL" 
                   value="<?php echo $row_edit['bimbingan_TGL']; ?>" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="bimbingan_ISI" class="col-sm-2 col-form-label">Isi Bimbingan</label>
        <div class="col-sm-10">
            <textarea class="form-control" id="bimbingan_ISI" name="bimbingan_ISI" rows="3" required><?php echo $row_edit['bimbingan_ISI']; ?></textarea>
        </div>
    </div>

    <div class="row mb-3">
        <label for="bimbingan_DOKUMEN" class="col-sm-2 col-form-label">Dokumen</label>
        <div class="col-sm-10">
            <input type="file" class="form-control" id="bimbingan_DOKUMEN" name="bimbingan_DOKUMEN" accept="application/pdf">
            <p class="text-muted mt-1">
                File saat ini: 
                <?php if(!empty($row_edit['bimbingan_DOKUMEN'])) { ?>
                    <a href="images/<?php echo $row_edit['bimbingan_DOKUMEN']; ?>" target="_blank">
                        <i class="bi bi-file-earmark-pdf"></i> <?php echo $row_edit['bimbingan_DOKUMEN']; ?>
                    </a>
                <?php } else { echo "Belum ada file."; } ?>
                <br>
                <small>*Biarkan kosong jika tidak ingin mengubah file.</small>
            </p>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-2"></div>
        <div class="col-10">
            <input type="submit" class="btn btn-success" value="Ubah" name="Ubah"> 
            <a href="inputbimbinganskripsi.php" class="btn btn-danger">Batal</a>
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
                    <h1 class="display-5">Daftar Bimbingan Skripsi</h1>
                </div>

                <form method="POST">
                <div class="form-group row mt-5 mb-3">
                <label for="search" class="col-sm-2">Cari Data</label>
                <div class="col-sm-6">
                <input type="text" name="search" class="form-control" id="search"
                value="<?php if(isset($_POST["search"]))
                {echo $_POST["search"];}?>" placeholder="Cari NPM, Nama Mahasiswa atau Dosen">
                </div>
                <input type="submit" name="kirim" value="Cari" class="col-sm-1 btn btn-primary">
                </div>
                </form>
                <table class="table table-success table-striped table-hover">
                    <tr class="info"> 
                        <th>Dosen Pembimbing</th>
                        <th>Mahasiswa</th>
                        <th>Judul Skripsi</th>
                        <th>Tanggal</th>
                        <th>Isi Bimbingan</th>
                        <th>Dokumen</th>
                        <th colspan="2" style="text-align: center;">Aksi</th>
                    </tr>

                    <?php mysqli_data_seek($query, 0); ?>
                    <?php while ($row = mysqli_fetch_array($query)) { ?>
                        <tr class="danger">
                            <td><?php echo $row['dosen_Nama']; ?> </td>
                            <td><?php echo $row['mhs_Nama']; ?> </td>
                            <td><?php echo $row['peserta_JUDUL']; ?> </td>
                            <td><?php echo $row['bimbingan_TGL']; ?> </td>
                            <td><?php echo $row['bimbingan_ISI']; ?> </td>
                            <td>
                                <?php if(!empty($row['bimbingan_DOKUMEN'])) { ?>
                                    <a href="images/<?php echo $row['bimbingan_DOKUMEN'] ?>" target="_blank" class="btn btn-sm btn-info"><i class="bi bi-file-earmark-pdf"></i></a>
                                <?php } else { echo "-"; } ?>
                            </td>

                            <td>
                                <a href="editbimbinganskripsi.php?ubahbimbingan=<?php echo $row["mhs_NPM"]?>" class="btn btn-success" title="EDIT">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                    </svg>
                                </a>
                            </td>
                            <td>
                                <a href="hapusbimbinganskripsi.php?hapusbimbingan=<?php echo $row["mhs_NPM"]?>" class="btn btn-danger" title="HAPUS">
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