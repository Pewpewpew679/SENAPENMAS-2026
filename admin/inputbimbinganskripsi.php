<!DOCTYPE html>
<html>
    <!--pengaman halaman, memastikan hanya user yg udh login yg bisa mengakses halaman Input bimbinganskripsi.-->
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
                        <h1 class="mt-4">Input Bimbingan Skripsi</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Input Data Bimbingan Skripsi</li>
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
/* untuk menyimpan data yg diinsert ke table bimbinganskripsi */
if(isset($_POST['Simpan'])) 
{
    $dosen_NIDN = $_POST['dosen_NIDN'];
    $mhs_NPM = $_POST['mhs_NPM'];
    $bimbingan_TGL = $_POST['bimbingan_TGL'];
    $bimbingan_ISI = $_POST['bimbingan_ISI'];

    /*harus upload dokumen dengan format file PDF*/
    $bimbingan_DOKUMEN = $_FILES['bimbingan_DOKUMEN']['name'];
    $tmp_file = $_FILES['bimbingan_DOKUMEN']['tmp_name'];
    
    if(!empty($bimbingan_DOKUMEN)) {
        $ext = pathinfo($bimbingan_DOKUMEN, PATHINFO_EXTENSION);
        if(strtolower($ext) == 'pdf') {
            move_uploaded_file($tmp_file, 'images/' . $bimbingan_DOKUMEN); 
        } else {
            echo "<script>alert('Format file harus PDF');</script>";
        }
    }

    $insert = mysqli_query($conn, "INSERT INTO bimbinganskripsi (dosen_NIDN, mhs_NPM, bimbingan_TGL, bimbingan_ISI, bimbingan_DOKUMEN) 
             VALUES ('$dosen_NIDN', '$mhs_NPM', '$bimbingan_TGL', '$bimbingan_ISI', '$bimbingan_DOKUMEN')"); 

    header("location:inputbimbinganskripsi.php");
}

/* untuk cari data NIDN/nama dosen dari table dosen dan npm/nama mahasiswa dari table pesertaskripsi*/
if(isset($_POST["kirim"]))
{
    $search = $_POST["search"];
    $query = mysqli_query($conn, "SELECT * FROM bimbinganskripsi, dosen, mahasiswa, pesertaskripsi
        WHERE bimbinganskripsi.dosen_NIDN = dosen.dosen_NIDN AND bimbinganskripsi.mhs_NPM = mahasiswa.mhs_NPM AND bimbinganskripsi.mhs_NPM = pesertaskripsi.mhs_NPM AND (bimbinganskripsi.mhs_NPM LIKE '%$search%'
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
                                    FROM pesertaskripsi ps JOIN mahasiswa m ON ps.mhs_NPM = m.mhs_NPM");
?>

<div class="row">
<div class="col-1"></div>
<div class="col-10">
    <form method="POST" enctype="multipart/form-data">

    <!--form untuk input data nidn_dosen dari table dosen dan mhs_npm dari table pesertaskripsi-->
    <div class="row mb-3 mt-5">
        <label for="dosen_NIDN" class="col-sm-2 col-form-label">Dosen Pembimbing</label>
        <div class="col-sm-10">
            <select class="form-control select2" id="dosen_NIDN" name="dosen_NIDN" required>
                <option value="">Pilih Dosen</option>
                <?php while ($row = mysqli_fetch_array($datadosen)) { ?>
                    <option value="<?php echo $row['dosen_NIDN']; ?>">
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
                <?php while ($row = mysqli_fetch_array($datapeserta)) { ?>
                    <option value="<?php echo $row['mhs_NPM']; ?>">
                        <?php echo $row['mhs_NPM']?> - 
                        <?php echo $row['mhs_Nama']?> - 
                        <?php echo $row['peserta_JUDUL']?> 
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="bimbingan_TGL" class="col-sm-2 col-form-label">Tanggal Bimbingan</label>
        <div class="col-sm-10">
            <input type="date" class="form-control" id="bimbingan_TGL" name="bimbingan_TGL" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="bimbingan_ISI" class="col-sm-2 col-form-label">Isi Bimbingan</label>
        <div class="col-sm-10">
            <textarea class="form-control" id="bimbingan_ISI" name="bimbingan_ISI" rows="3" placeholder="Catatan hasil bimbingan..." required></textarea>
        </div>
    </div>

    <div class="row mb-3">
        <label for="bimbingan_DOKUMEN" class="col-sm-2 col-form-label">Dokumen Bimbingan</label>
        <div class="col-sm-10">
            <input type="file" class="form-control" id="bimbingan_DOKUMEN" name="bimbingan_DOKUMEN" accept="application/pdf">
            <p class="text-muted">Unggah file dokumen (Format PDF)</p>
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
        </div> 
    </div> <div class="row">
        <div class="col-1"></div>
        <div class="col-10">
            <div class="jumbotron mt-5 mb-3">
                <h1 class="display-5">Daftar Bimbingan Skripsi</h1>
            </div>

            <!--mencari data npm/nidn dari tabel mahasiswa/dosen-->
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

            <!--menampilkan table bimbinganskripsi dari data yg udah diinsert-->
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

                <?php while ($row = mysqli_fetch_array($query)) { ?>
                    <tr class="danger">
                        <td><?php echo $row['dosen_Nama']; ?> </td>
                        <td><?php echo $row['mhs_Nama']; ?></td>
                        <td><?php echo $row['peserta_JUDUL']; ?> </td>
                        <td><?php echo $row['bimbingan_TGL']; ?> </td>
                        <td><?php echo $row['bimbingan_ISI']; ?> </td>
                        <td>
                            <?php if(!empty($row['bimbingan_DOKUMEN'])) { ?>
                                <a href="images/<?php echo $row['bimbingan_DOKUMEN'] ?>" target="_blank" class="btn btn-sm btn-info"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                            <?php } else { echo "-"; } ?>
                        </td>
                        
                        <td>
                            <a href="editbimbinganskripsi.php?ubahbimbingan=<?php echo $row['mhs_NPM']; ?>" class="btn btn-success" title="EDIT">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                </svg>
                            </a>
                        </td>
                        <td>
                            <a href="hapusbimbinganskripsi.php?hapusbimbinganskripsi=<?php echo $row['mhs_NPM']; ?>" class="btn btn-danger" title="HAPUS">
                                <i class="bi bi-trash3"></i>
                            </a>
                        </td>
                    </tr>
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