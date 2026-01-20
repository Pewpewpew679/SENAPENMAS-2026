<!DOCTYPE html>
<html>
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
                        <h1 class="mt-4">Input Dosen</h1> <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Input Data Dosen</li> </ol>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title> <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css">
</head>
<body>

<?php
include("includes/config.php");

// Logika Simpan (dari inputdsn.php asli)
if(isset($_POST['Simpan'])) {
    if(isset($_REQUEST['nidnDSN'])){
        $dosen_NIDN = $_REQUEST['nidnDSN'];
    }
    if (!empty($dosen_NIDN)){
        $dosen_NIDN = $_POST['nidnDSN'];
    } else{
        ?><h1>Maaf anda salah input</h1><?php
        die("Anda harus mengisi NIDN");
    }

    $dosen_NIK = $_POST['nikDSN'];
    $dosen_Nama = $_POST['namaDSN'];
    $dosen_Ket = $_POST['ketDSN'];

    mysqli_query($conn, "INSERT INTO dosen VALUES('$dosen_NIDN', '$dosen_NIK', '$dosen_Nama', '$dosen_Ket')");
    header("location:inputdsn.php");
}

// Logika Pencarian (dari inputpenasihat.php, disesuaikan)
if(isset($_POST["kirim"]))
{
    $search = $_POST["search"];
    $query = mysqli_query($conn, "SELECT * FROM dosen
        WHERE dosen_NIDN LIKE '%".$search."%' OR dosen_Nama LIKE '%".$search."%'");
}
else
{
    // Query default
    $query = mysqli_query($conn, "SELECT * FROM dosen");
}
?>

<div class="row">
    <div class="col-1"></div>
    <div class="col-10">
        <form method="POST">
            
            <div class="row mb-3 mt-5">
                <label for="nidnDSN" class="col-sm-2 col-form-label">NIDN Dosen</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="nidnDSN" name="nidnDSN" placeholder="NIDN Dosen" maxlength="9" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="nikDSN" class="col-sm-2 col-form-label">NIK Dosen</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="nikDSN" name="nikDSN" placeholder="NIK Dosen" maxlength="9" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="namaDSN" class="col-sm-2 col-form-label">Nama Dosen</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="namaDSN" name="namaDSN" placeholder="Nama Dosen" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="ketDSN" class="col-sm-2 col-form-label">Keterangan</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="ketDSN" name="ketDSN" placeholder="Keterangan Dosen">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-2"></div>
                <div class="col-10">
                    <input type="submit" class="btn btn-primary" value="Simpan" name="Simpan">
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
            <h1 class="display-5">Daftar Dosen</h1>
        </div>

        <form method="POST">
        <div class="form-group row mt-5 mb-3">
            <label for="search" class="col-sm-2">Cari NIDN/Nama</label> <div class="col-sm-6">
                <input type="text" name="search" class="form-control" id="search"
                value="<?php if(isset($_POST["search"]))
                {echo $_POST["search"];}?>" placeholder="Cari NIDN atau Nama Dosen"> </div>
            <input type="submit" name="kirim" value="Cari" class="col-sm-1 btn btn-primary">
        </div>
        </form>
        <table class="table table-striped table-success table-hover"> <tr class="info"> <th>NIDN</th>
                <th>NIK</th>
                <th>Nama Dosen</th>
                <th>Keterangan</th>
                <th colspan="2" style="text-align: center;">Aksi</th> </tr>

            <?php { ?>
            <?php while ($row = mysqli_fetch_array($query)) 
                { ?>
                <tr class="danger"> <td><?php echo $row['dosen_NIDN']; ?></td>
                    <td><?php echo $row['dosen_NIK']; ?></td>
                    <td><?php echo $row['dosen_Nama']; ?></td>
                    <td><?php echo $row['dosen_Ket']; ?></td>
                    
                    <td>
                        <a href="editdsn.php?ubahdsn=<?php echo $row["dosen_NIDN"]?>" class="btn btn-success" title="EDIT">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                            </svg>
                        </a>
                    </td>
                    <td>
                        <a href="hapusdsn.php?hapusdsn=<?php echo $row["dosen_NIDN"]?>" class="btn btn-danger" title="HAPUS">
                            <i class="bi bi-trash3"></i>
                        </a>
                    </td>
                </tr>
            <?php } ?>
            <?php } ?>
        </table>
    </div>
    <div class="col-1"></div>
</div> <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
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