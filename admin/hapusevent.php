<?php
session_start();
include "includes/config.php";

// Cek Login
if (!isset($_SESSION['useremail'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $poster = $_GET['poster']; // Mengambil nama file gambar dari URL

    // 1. Hapus file fisik gambar jika ada
    $path = "images/events/" . $poster;
    if ($poster != "" && file_exists($path)) {
        unlink($path);
    }

    // 2. Hapus data dari database
    $query = "DELETE FROM events WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        // Redirect kembali dengan pesan sukses
        header("Location: manage_events.php?msg=deleted");
    } else {
        echo "Gagal menghapus data: " . mysqli_error($conn);
    }
} else {
    header("Location: manage_events.php");
}
?>