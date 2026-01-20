<?php
    include "includes/config.php";
    if(isset($_GET['hapuspenasihat']))
    {
        $NPM = $_GET['hapuspenasihat'];
        mysqli_query($conn, "DELETE FROM penasihat
            WHERE mhs_NPM = '$NPM'");
        echo "<script>alert('DATA BERHASIL DIHAPUS');
            document.location='inputpenasihat.php'</script>";
    }
?>