<?php
ob_start();
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

include "includes/config.php";

// Prefer delete by sponsor id if provided
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $res = mysqli_query($conn, "SELECT sponsor_logo FROM sponsors WHERE sponsor_id='$id'");
    $image_name = '';
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $image_name = $row['sponsor_logo'];
    }

    if ($image_name) {
        $file_path = "images/" . $image_name;
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    $q = "DELETE FROM sponsors WHERE sponsor_id='$id'";
    if (mysqli_query($conn, $q)) {
        $_SESSION['success'] = 'Sponsor berhasil dihapus.';
    } else {
        $_SESSION['error'] = 'Gagal menghapus sponsor: ' . mysqli_error($conn);
    }

    header("Location: inputsponsor.php");
    exit;
}

// Fallback: delete by image filename (used by UI link)
if (isset($_GET['image'])) {
    $image = mysqli_real_escape_string($conn, $_GET['image']);
    $file_path = "images/" . $image;
    if ($image && file_exists($file_path)) {
        unlink($file_path);
    }

    $q = "DELETE FROM sponsors WHERE sponsor_logo='$image'";
    if (mysqli_query($conn, $q)) {
        $_SESSION['success'] = 'Sponsor berhasil dihapus.';
    } else {
        $_SESSION['error'] = 'Gagal menghapus sponsor: ' . mysqli_error($conn);
    }

    header("Location: inputsponsor.php");
    exit;
}

// Nothing to delete -> redirect back
header("Location: inputsponsor.php");
exit;