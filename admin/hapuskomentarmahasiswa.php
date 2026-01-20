<?php
    include "includes/config.php";
    if(isset($_GET['hapuskomentarmahasiswa']))
    {
        $NPM = $_GET['hapuskomentarmahasiswa'];
        mysqli_query($conn, "DELETE FROM komentar_mahasiswa
            WHERE mhs_NPM = '$NPM'");
        echo "<script>alert('DATA BERHASIL DIHAPUS');
            document.location='inputkomentarmahasiswa.php'</script>";
    }
?>