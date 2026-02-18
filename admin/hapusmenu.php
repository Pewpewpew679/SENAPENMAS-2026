<?php
ob_start();
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

include "includes/config.php";

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    $query = "DELETE FROM menu WHERE menu_id = '$id'";
    mysqli_query($conn, $query);

    header("Location: inputmenu.php");
    exit;
} else {
    header("Location: inputmenu.php");
    exit;
}
?>