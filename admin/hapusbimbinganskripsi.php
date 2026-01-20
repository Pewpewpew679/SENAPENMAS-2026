<?php
    include "includes/config.php";
    if(isset($_GET['hapusbimbinganskripsi']))
    {
        $NPM = $_GET['hapusbimbinganskripsi'];
        mysqli_query($conn, "DELETE FROM bimbinganskripsi
            WHERE mhs_NPM = '$NPM'");
        echo "<script>alert('DATA BERHASIL DIHAPUS');
            document.location='inputbimbinganskripsi.php'</script>";
    }
?>
