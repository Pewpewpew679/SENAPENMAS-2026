<?php
ob_start();
session_start();

if (!isset($_SESSION['useremail'])) {
    header("Location: login.php");
    exit;
}

include "includes/config.php";

if (isset($_GET['id'])) {
    $supported_id = mysqli_real_escape_string($conn, $_GET['id']);

    $query_select = mysqli_query($conn, "SELECT supported_image FROM supported WHERE supported_id = '$supported_id'");
    $data = mysqli_fetch_assoc($query_select);

    if ($data) {
        $nama_file = $data['supported_image'];
        $path_file = "images/" . $nama_file;

        if (file_exists($path_file)) {
            unlink($path_file);
        }

        $query_delete = "DELETE FROM supported WHERE supported_id = '$supported_id'";
        
        if (mysqli_query($conn, $query_delete)) {
            header("Location: inputsupported.php?status=success_delete");
        } else {
            header("Location: inputsupported.php?status=error_delete");
        }
    } else {
        header("Location: inputsupported.php?status=not_found");
    }
    exit;
} else {
    header("Location: inputsupported.php");
    exit;
}
?>