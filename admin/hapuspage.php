<?php
session_start();

if (!isset($_SESSION['useremail'])) {
    header("Location: login.php");
    exit;
}

include "includes/config.php";

if (isset($_GET['id']) && isset($_GET['cover'])) {
    $page_id = (int) $_GET['id'];
    $cover   = $_GET['cover'];
    
    // Delete cover file if exists
    if ($cover != "" && file_exists("images/pages/" . $cover)) {
        unlink("images/pages/" . $cover);
    }
    
    // Delete from database
    $query = "DELETE FROM pages WHERE page_id = $page_id";
    
    if (mysqli_query($conn, $query)) {
        header("Location: inputpage.php?msg=deleted");
    } else {
        header("Location: inputpage.php?msg=error");
    }
} else {
    header("Location: inputpage.php");
}
exit;
?>