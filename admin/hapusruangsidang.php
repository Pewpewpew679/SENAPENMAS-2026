<?php
    include "includes/config.php";
    if(isset($_GET['hapusruangsidang']))
    {
        $NPM = $_GET['hapusruangsidang'];
        mysqli_query($conn, "DELETE FROM ruangsidang
            WHERE mhs_NPM = '$NPM'");
        echo "<script>alert('DATA BERHASIL DIHAPUS');
            document.location='inputruangsidang.php'</script>";
    }
?>