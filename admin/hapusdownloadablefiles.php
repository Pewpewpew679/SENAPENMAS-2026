<?php
ob_start();
session_start();

// Cek Login
if (!isset($_SESSION['useremail'])) {
    header("Location: login.php");
    exit;
}

include "includes/config.php";

if (isset($_GET['id'])) {
    $file_id = (int) $_GET['id'];
    
    // Ambil data file untuk mendapatkan nama file
    $query = mysqli_query($conn, "SELECT file_link FROM downloadablefiles WHERE file_id='$file_id'");
    $data = mysqli_fetch_assoc($query);
    
    if ($data) {
        $file_name = $data['file_link'];
        $folder = "upload/unduhan/";
        
        // Hapus file fisik dari server
        if (file_exists($folder . $file_name) && $file_name != "") {
            unlink($folder . $file_name);
        }
        
        // Hapus data dari database
        mysqli_query($conn, "DELETE FROM downloadablefiles WHERE file_id='$file_id'");
    }
}

header("Location: inputdownloadablefiles.php?msg=deleted");
exit;
?>