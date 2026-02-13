<?php
ob_start();
session_start();

// Cek Login
if (!isset($_SESSION['useremail'])) {
    header("Location: login.php");
    exit;
}

// Cek apakah user sudah login (Security)
if (!isset($_SESSION['useremail'])) {
    header("Location: login.php");
    exit;
}

include "includes/config.php";

if (isset($_GET['id'])) {
    // Mengambil ID dan membersihkan input
    $schedule_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Menghapus data dari tabel schedules berdasarkan schedule_id
    mysqli_query($conn, "DELETE FROM schedules WHERE schedule_id = '$schedule_id'");

    // Redirect kembali ke halaman input
    header("Location: inputschedule.php");
    exit;
} else {
    // Jika tidak ada ID, langsung kembalikan
    header("Location: inputschedule.php");
    exit;
}
?>