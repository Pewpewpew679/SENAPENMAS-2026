<?php
session_start();

if (!isset($_SESSION['useremail'])) {
    header("Location: login.php");
    exit;
}

include "includes/config.php";

if (isset($_GET['id']) && isset($_GET['cover'])) {
    $page_id = (int) $_GET['id'];
    $cover   = mysqli_real_escape_string($conn, $_GET['cover']);
    
    // === STEP 1: DELETE MENU ENTRIES (PENTING!) ===
    // Hapus semua menu yang link ke page ini
    $menu_pattern = "page/" . $page_id . "/%";
    $delete_menu = mysqli_query($conn, "DELETE FROM menu WHERE menu_link LIKE '" . mysqli_real_escape_string($conn, $menu_pattern) . "'");
    
    if (!$delete_menu) {
        error_log("Failed to delete menu for page_id: $page_id - " . mysqli_error($conn));
    }
    
    // === STEP 2: DELETE COVER FILE ===
    // Path harus ke frontend/images/pages/ karena cover disimpan di sana
    $cover_path = "../frontend/images/pages/" . $cover;
    
    if ($cover != "" && file_exists($cover_path)) {
        if (!unlink($cover_path)) {
            error_log("Failed to delete cover file: $cover_path");
        }
    }
    
    // === STEP 3: DELETE FROM DATABASE ===
    $query = "DELETE FROM pages WHERE page_id = $page_id";
    
    if (mysqli_query($conn, $query)) {
        // Check apakah benar-benar ada yang dihapus
        if (mysqli_affected_rows($conn) > 0) {
            header("Location: inputpage.php?msg=deleted");
        } else {
            // Page ID tidak ditemukan
            header("Location: inputpage.php?msg=notfound");
        }
    } else {
        // Log error untuk debugging
        error_log("Delete page failed: " . mysqli_error($conn));
        header("Location: inputpage.php?msg=error");
    }
} else {
    // Parameter tidak lengkap
    header("Location: inputpage.php?msg=invalid");
}
exit;
?>