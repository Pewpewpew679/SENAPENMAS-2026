<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

include "includes/config.php";

if (isset($_GET['id'])) {
    $page_id = (int) $_GET['id'];
    
    // === STEP 1: DELETE MENU ENTRIES (PENTING!) ===
    // Hapus semua menu yang link ke page ini
    $menu_pattern = "page/" . $page_id . "/%";
    $delete_menu = mysqli_query($conn, "DELETE FROM menu WHERE menu_link LIKE '" . mysqli_real_escape_string($conn, $menu_pattern) . "'");
    
    if (!$delete_menu) {
        error_log("Failed to delete menu for page_id: $page_id - " . mysqli_error($conn));
    }
    
    // === STEP 2: DELETE FROM DATABASE ===
    $query = "DELETE FROM pages WHERE page_id = $page_id";
    
    if (mysqli_query($conn, $query)) {
        if (mysqli_affected_rows($conn) > 0) {
            header("Location: inputpage.php?msg=deleted");
        } else {
            header("Location: inputpage.php?msg=notfound");
        }
    } else {
        error_log("Delete page failed: " . mysqli_error($conn));
        header("Location: inputpage.php?msg=error");
    }
} else {
    header("Location: inputpage.php?msg=invalid");
}
exit;
?>