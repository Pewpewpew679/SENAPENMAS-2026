<?php
    include "includes/config.php";
    if(isset($_GET['hapusberita']))
    {
        $berita_JUDUL = $_GET['hapusberita'];
        mysqli_query($conn, "DELETE FROM berita
            WHERE berita_JUDUL = '$berita_JUDUL'");
        echo "<script>alert('DATA BERHASIL DIHAPUS');
            document.location='inputberita.php'</script>";
    }
?>