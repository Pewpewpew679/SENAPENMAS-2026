<?php
    include "includes/config.php";
    if(isset($_GET['hapusujianskripsi']))
    {
        $NPM = $_GET['hapusujianskripsi'];
        mysqli_query($conn, "DELETE FROM ujianskripsi
            WHERE mhs_NPM = '$NPM'");
        echo "<script>alert('DATA BERHASIL DIHAPUS');
            document.location='inputujianskripsi.php'</script>";
    }
?>