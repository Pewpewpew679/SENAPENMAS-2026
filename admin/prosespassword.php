<?php
session_start();
include "includes/config.php"; 

if (isset($_POST['update'])) {
    $id_admin = $_SESSION['admin_id'];
    $new_pw   = $_POST['new_password'];
    $conf_pw  = $_POST['confirm_password'];

    if ($new_pw === $conf_pw && !empty($new_pw)) {
        // Update kolom admin_PASS berdasarkan admin_ID
        $sql = "UPDATE admin SET admin_PASS = '$new_pw' WHERE admin_ID = '$id_admin'";
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Password berhasil diperbarui!'); window.location='profile.php';</script>";
        } else {
            echo "<script>alert('Gagal update database!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Password tidak cocok!'); window.history.back();</script>";
    }
}
?>