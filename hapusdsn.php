<?php
    include "includes/config.php";
    if(isset($_GET['hapusdsn']))
    {
        $NIDN = $_GET['hapusdsn']; 
        mysqli_query($conn, "DELETE FROM dosen
                WHERE dosen_NIDN = '$NIDN'");
        echo "<script>alert('DATA BERHASIL DIHAPUS');
                document.location='inputdsn.php'</script>";
    }
?>