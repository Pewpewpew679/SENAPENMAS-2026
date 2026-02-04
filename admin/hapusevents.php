<?php
ob_start();
session_start();

if (!isset($_SESSION['useremail'])) {
    header("Location: login.php");
    exit;
}

include "includes/config.php";

if (isset($_GET['id']) && isset($_GET['poster'])) {
    $event_id = (int) $_GET['id'];
    $poster = mysqli_real_escape_string($conn, $_GET['poster']);

    // Hapus file poster
    $file_path = "images/events/" . $poster;
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // Hapus data dari database
    $query = "DELETE FROM events WHERE event_id = $event_id";
    
    if (mysqli_query($conn, $query)) {
        header("Location: inputevents.php?msg=deleted");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
        exit;
    }
} else {
    header("Location: inputevents.php");
    exit;
}
?>