<?php
    include "includes/config.php";
    if(isset($_GET['hapus_speaker']))
    {
        $speaker_name = $_GET['hapus_speaker'];
        mysqli_query($conn, "DELETE FROM speaker
            WHERE speaker_name = '$speaker_name'");
        echo "<script>alert('DATA BERHASIL DIHAPUS');
            document.location='manage_speaker.php'</script>";
    }
?>