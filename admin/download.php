<?php
ob_start();
session_start();

include "includes/config.php";

if (isset($_GET['id'])) {
    $file_id = (int) $_GET['id'];
    
    // Ambil data file
    $query = mysqli_query($conn, "SELECT * FROM downloadablefiles WHERE file_id='$file_id'");
    $data = mysqli_fetch_assoc($query);
    
    if ($data) {
        $file_stored = $data['file_link']; // Nama file di server (dengan timestamp)
        $file_original = $data['original_filename']; // Nama file asli
        $file_path = "upload/unduhan/" . $file_stored;
        
        if (file_exists($file_path)) {
            // Set headers untuk download dengan nama file asli
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $file_original . '"');
            header('Content-Length: ' . filesize($file_path));
            header('Pragma: public');
            
            // Clear output buffer
            ob_clean();
            flush();
            
            // Baca dan kirim file
            readfile($file_path);
            exit;
        } else {
            echo "File tidak ditemukan di server: " . $file_path;
        }
    } else {
        echo "Data file tidak ditemukan.";
    }
} else {
    header("Location: inputdownloadablefiles.php");
}
?>