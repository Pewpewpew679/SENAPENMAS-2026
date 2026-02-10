<?php
ob_start();
session_start();

if (!isset($_SESSION['useremail'])) {
    header("Location: login.php");
    exit;
}

include "includes/config.php";

if (isset($_GET['id'])) {
    $social_id = mysqli_real_escape_string($conn, $_GET['id']);

    $query = "DELETE FROM sosmed WHERE social_id = '$social_id'";
    
    if (mysqli_query($conn, $query)) {
        header("Location: inputsosmed.php?status=success_delete");
    } else {
        header("Location: inputsosmed.php?status=error_delete");
    }
    exit;
} else {
    header("Location: inputsosmed.php");
    exit;
}
?>