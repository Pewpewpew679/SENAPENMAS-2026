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
    $slider_id  = (int) $_GET['id']; // Cast ke integer untuk keamanan
    $image_name = mysqli_real_escape_string($conn, $_GET['image']);

    // 1. Hapus file fisik gambar di folder frontend/images/sliders/
    $file_path = "../frontend/images/sliders/" . $image_name;
    if (!empty($image_name) && file_exists($file_path)) {
        unlink($file_path);
    }

    // 2. Hapus data dari database (tabel sliders, kolom slider_id)
    $query = "DELETE FROM sliders WHERE slider_id = '$slider_id'";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = 'Slider berhasil dihapus.';
    } else {
        $_SESSION['error'] = 'Gagal menghapus slider: ' . mysqli_error($conn);
    }

    header("Location: inputslider.php");
    exit;
    
} else {
    // Jika parameter tidak lengkap, kembalikan ke halaman input
    $_SESSION['error'] = 'Parameter tidak lengkap.';
    header("Location: inputslider.php");
    exit;
}
?>