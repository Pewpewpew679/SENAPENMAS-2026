<?php
ob_start();
session_start();

if (!isset($_SESSION['useremail'])) {
    header("Location: login.php");
    exit;
}

include "includes/config.php";

if (!isset($_GET['akun'])) {
    header("Location: inputsosmed.php");
    exit;
}

$akun = mysqli_real_escape_string($conn, $_GET['akun']);

mysqli_query($conn, "
    DELETE FROM sosmed 
    WHERE akun = '$akun'
");

header("Location: inputsosmed.php");
exit;
?>