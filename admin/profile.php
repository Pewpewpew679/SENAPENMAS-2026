<?php
ob_start();
session_start();

// 1. HUBUNGKAN KE DATABASE
// Keluar dari folder admin (../) lalu masuk ke folder includes/config.php
if (file_exists("includes/config.php")) {
    include "includes/config.php"; 
} else {
    die("Error: File includes/config.php tidak ditemukan.");
}

// 2. PROTEKSI HALAMAN
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// 3. AMBIL DATA ADMIN
// Menggunakan variabel $conn dari config.php dan kolom admin_ID
$id_admin = $_SESSION['admin_id'];
$query = mysqli_query($conn, "SELECT * FROM admin WHERE admin_ID = '$id_admin'"); 
$data = mysqli_fetch_assoc($query);

// Mapping data sesuai kolom database
$admin_name = $data['admin_USER']; 
$username   = $data['admin_USER'];
$status     = "Aktif"; 

$view = isset($_GET['action']) ? $_GET['action'] : 'profile';
?>

<!DOCTYPE html>
<html lang="en">
    <?php include "bagiankode/head.php"; ?>
    <style>
        .form-control-plaintext { border: 1px solid #dee2e6 !important; padding: 8px 12px; background-color: #f8f9fa !important; }
        .form-control { border-radius: 0; }
        .btn-update { background-color: #3182ce; color: white; border: none; }
        .btn-update:hover { background-color: #2b6cb0; color: white; }
        .breadcrumb-item + .breadcrumb-item::before { content: ">" !important; }
    </style>
    <body class="sb-nav-fixed">
        <?php include "bagiankode/menunav.php"; ?>
        <div id="layoutSidenav">   
            <?php include "bagiankode/menu.php"; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
                            <h1 class="h3"><?php echo ($view == 'profile') ? 'Profile Useradmin' : 'Change Password'; ?></h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 small">
                                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Dashboard</a></li>
                                    <li class="breadcrumb-item active"><?php echo ($view == 'profile') ? 'Profile Useradmin' : 'Change Password'; ?></li>
                                </ol>
                            </nav>
                        </div>

                        <div class="card mb-4 shadow-sm border-0">
                            <div class="card-body p-4">
                                <?php if ($view == 'profile'): ?>
                                    <div class="mb-4">
                                        <a href="?action=change_password" class="btn btn-light border px-3">Change Password</a>
                                    </div>
                                    <div class="row mb-3 align-items-center">
                                        <label class="col-sm-3 fw-bold">Admin Name</label>
                                        <div class="col-sm-9"><input type="text" readonly class="form-control-plaintext" value="<?= $admin_name; ?>"></div>
                                    </div>
                                    <div class="row mb-3 align-items-center">
                                        <label class="col-sm-3 fw-bold">Username</label>
                                        <div class="col-sm-9"><input type="text" readonly class="form-control-plaintext" value="<?= $username; ?>"></div>
                                    </div>
                                    <div class="row mb-3 align-items-center">
                                        <label class="col-sm-3 fw-bold">Status</label>
                                        <div class="col-sm-9"><input type="text" readonly class="form-control-plaintext" value="<?= $status; ?>"></div>
                                    </div>

                                <?php elseif ($view == 'change_password'): ?>
                                    <form action="proses_password.php" method="POST">
                                        <div class="row mb-2 align-items-center">
                                            <label class="col-sm-3 fw-bold text-dark">Admin Name</label>
                                            <div class="col-sm-9 text-muted"><?= $admin_name; ?></div>
                                        </div>
                                        <div class="row mb-2 align-items-center border-top pt-2">
                                            <label class="col-sm-3 fw-bold text-dark">Username</label>
                                            <div class="col-sm-9 text-muted"><?= $username; ?></div>
                                        </div>
                                        <div class="row mb-2 align-items-center border-top pt-2">
                                            <label class="col-sm-3 fw-bold text-dark">New Password</label>
                                            <div class="col-sm-9"><input type="password" name="new_password" class="form-control" required></div>
                                        </div>
                                        <div class="row mb-4 align-items-center border-top pt-2">
                                            <label class="col-sm-3 fw-bold text-dark">Confirmation New Password</label>
                                            <div class="col-sm-9"><input type="password" name="confirm_password" class="form-control" required></div>
                                        </div>
                                        <div class="d-flex gap-2 border-top pt-4">
                                            <a href="profile.php" class="btn btn-light border px-4">Cancel</a>
                                            <button type="submit" name="update" class="btn btn-update px-4">Update</button>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </main>
                <?php include "bagiankode/footer.php"; ?>
            </div>
        </div>
        <?php include "bagiankode/jsscript.php"; ?>
    </body>
</html>