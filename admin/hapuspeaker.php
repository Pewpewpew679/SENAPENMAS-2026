<?php
ob_start();
session_start();

// Cek Login
if (!isset($_SESSION['useremail'])) {
    header("Location: login.php");
    exit;
}

include "includes/config.php";

if (isset($_GET['speaker_name'])) {
    $speaker_name = mysqli_real_escape_string($conn, $_GET['speaker_name']);

    // Ambil foto terlebih dahulu untuk dihapus
    $query_get = "SELECT photo FROM speaker WHERE speaker_name = '$speaker_name'";
    $result_get = mysqli_query($conn, $query_get);
    $data = mysqli_fetch_array($result_get);

    if ($data['photo'] != "") {
        $file_path = "images/" . $data['photo'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    $query = "DELETE FROM speaker WHERE speaker_name = '$speaker_name'";
    mysqli_query($conn, $query);

    header("Location: inputspeaker.php");
    exit;
} else {
    header("Location: inputspeaker.php");
    exit;
}
?>\