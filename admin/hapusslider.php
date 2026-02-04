<?php
ob_start();
session_start();

// Cek Login untuk keamanan agar tidak sembarang orang bisa akses URL ini
if (!isset($_SESSION['useremail'])) {
    header("Location: login.php");
    exit;
}

include "includes/config.php";

// Menangkap parameter id dan image dari URL
if (isset($_GET['id']) && isset($_GET['image'])) {
    $slider_id  = mysqli_real_escape_string($conn, $_GET['id']);
    $image_name = mysqli_real_escape_string($conn, $_GET['image']);

    // 1. Hapus file fisik gambar di folder images/
    $file_path = "images/" . $image_name;
    if (!empty($image_name) && file_exists($file_path)) {
        unlink($file_path);
    }

    // 2. Hapus data di database berdasarkan slider_id
    // Sesuai dengan primary key di gambar database Anda
    $query = "DELETE FROM slider WHERE slider_id = '$slider_id'";
    
    if (mysqli_query($conn, $query)) {
        // Redirect dengan pesan sukses
        header("Location: inputslider.php?msg=deleted");
    } else {
        // Jika gagal hapus di database
        echo "Error: " . mysqli_error($conn);
    }
    exit;
    
} else {
    // Jika parameter tidak lengkap, kembalikan ke halaman input
    header("Location: inputslider.php");
    exit;
}
?>