<?php
    include "includes/config.php";
    if(isset($_GET['hapuspesertaskripsi']))
    {
        $NPM = $_GET['hapuspesertaskripsi'];
        mysqli_query($conn, "DELETE FROM pesertaskripsi
            WHERE mhs_NPM = '$NPM'");
        echo "<script>alert('DATA BERHASIL DIHAPUS');
            document.location='inputpesertaskripsi.php'</script>";
    }
?>