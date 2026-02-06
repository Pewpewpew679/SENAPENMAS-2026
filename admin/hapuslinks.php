<?php
session_start();
if (!isset($_SESSION['useremail'])) {
    header("Location: login.php");
    exit;
}

include "includes/config.php";

if (isset($_GET['id'])) {
    $link_id = mysqli_real_escape_string($conn, $_GET['id']);

    mysqli_query($conn, "DELETE FROM links WHERE link_id = '$link_id'");

    header("Location: inputlinks.php");
    exit;
} else {
    header("Location: inputlinks.php");
    exit;
}
?>