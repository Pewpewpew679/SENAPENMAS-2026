<?php
ob_start();
session_start();

if (!isset($_SESSION['useremail'])) {
    header("Location: login.php");
    exit;
}

include "includes/config.php";

if (isset($_GET['image'])) {
    $image_name = mysqli_real_escape_string($conn, $_GET['image']);

    $file_path = "images/" . $image_name;
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    $query = "DELETE FROM sliders WHERE slider_image = '$image_name'";
    mysqli_query($conn, $query);

    header("Location: inputslider.php");
    exit;
} else {
    header("Location: inputslider.php");
    exit;
}
?>