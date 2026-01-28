<?php
    include "includes/config.php";
    if(isset($_GET['hapus_schedule']))
    {
        $schedule_date = $_GET['hapus_schedule'];
        mysqli_query($conn, "DELETE FROM schedule
            WHERE date = '$schedule_date'");
        echo "<script>alert('DATA BERHASIL DIHAPUS');
            document.location='manage_schedule.php'</script>";
    }
?>