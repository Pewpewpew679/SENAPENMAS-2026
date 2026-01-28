<?php
ob_start();
session_start();

if (!isset($_SESSION['useremail'])) {
    header("Location: login.php");
    exit;
}

include "includes/config.php";

if (!isset($_GET['link_name'])) {
    header("Location: inputlinks.php");
    exit;
}

$link_name = mysqli_real_escape_string($conn, $_GET['link_name']);

mysqli_query($conn, "
    DELETE FROM links
    WHERE link_name = '$link_name'
");

header("Location: inputlinks.php");
exit;
