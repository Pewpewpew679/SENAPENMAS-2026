<!DOCTYPE html>
<html lang="en">
    <?php include "bagiankode/head.php"; ?>

<?php ob_start();
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
}
?>
    <body class="sb-nav-fixed">
        <?php include "bagiankode/menunav.php"; ?>
        <div id="layoutSidenav">   
        <?php include "bagiankode/menu.php"; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Dashboard</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                        
                    </div>
                </main>
                <?php include "bagiankode/footer.php"; ?>
            </div>
        </div>
        <?php include "bagiankode/jsscript.php"; ?>
    </body>
</html>
