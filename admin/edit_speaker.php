<?php
if (isset($_POST['submit'])) {
    include "includes/config.php";

    $speaker_name_old = $_POST['speaker_name_old'];
    $speaker_name = $_POST['speaker_name'];
    $information = $_POST['information'];
    $event = $_POST['event'];
    
    $photo = $_POST['photo_old'];
    
    // upload foto baru jika ada
    if ($_FILES['photo']['name'] != '') {
        $foto_name = $_FILES['photo']['name'];
        $foto_tmp = $_FILES['photo']['tmp_name'];
        $foto_path = "gambar/" . time() . "_" . $foto_name;
        
        if (move_uploaded_file($foto_tmp, $foto_path)) {
            // hapus foto lama
            if ($photo != "") {
                $old_path = "gambar/" . $photo;
                if (file_exists($old_path)) {
                    unlink($old_path);
                }
            }
            $photo = time() . "_" . $foto_name;
        }
    }

    $query = "UPDATE speaker SET photo = '$photo', speaker_name = '$speaker_name', information = '$information', event = '$event' WHERE speaker_name = '$speaker_name_old'";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        header("Location: manage_speaker.php");
        exit;
    } else {
        $error = mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>

<?php include "bagiankode/head.php"; ?>
<body class="sb-nav-fixed">
    <?php
        include "bagiankode/menunav.php";
    ?>
    <div id="layoutSidenav">
        
    <?php
        include "bagiankode/menu.php";
    ?>

    <div id="layoutSidenav_content" style="background-color: light gray;">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Edit Speaker</h1>
                <div style="text-align: right;">
                    <a href="dashboard.php" style="font-weight: bold; color: silver; text-decoration: none;"> DashBoard</a>
                    <a style="font-weight: bold; color: silver;"> > Speaker</a>
                </div>
                <br>
                <div style="background-color: white; padding: 30px; border-radius: 5px;">
                    <?php
                    if (isset($error)) {
                        echo "<div class='alert alert-danger'>Error: " . $error . "</div>";
                    }
                    ?>
                    <form method="POST" enctype="multipart/form-data">
                        <?php
                        include "includes/config.php";

                        if (!isset($_GET['speaker_name']) || empty($_GET['speaker_name'])) {
                            echo "Error: Speaker tidak ditemukan.";
                            exit;
                        }

                        $speaker_name = $_GET['speaker_name'];
                        $query = mysqli_query($conn, "SELECT * FROM speaker WHERE speaker_name = '$speaker_name'");
                        $data = mysqli_fetch_assoc($query);

                        if (!$data) {
                            echo "Error: Data speaker tidak ditemukan.";
                            exit;
                        }
                        ?>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Current Photo</label>
                            <?php if($data['photo'] != ""): ?>
                                <img src="gambar/<?php echo $data['photo']; ?>" width="100" height="100" style="object-fit:cover; border-radius: 4px; margin-bottom: 10px;">
                            <?php else: ?>
                                <span class="text-muted">No Photo</span>
                            <?php endif; ?>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Upload New Photo</label>
                            <input type="file" name="photo" accept="image/*" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 400px;">
                            <input type="hidden" name="photo_old" value="<?php echo $data['photo']; ?>">
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Speaker Name</label>
                            <input type="text" name="speaker_name" required value="<?php echo $data['speaker_name']; ?>" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 500px;">
                            <input type="hidden" name="speaker_name_old" value="<?php echo $data['speaker_name']; ?>">
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Information</label>
                            <textarea name="information" rows="5" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 500px;"><?php echo $data['information']; ?></textarea>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Event</label>
                            <input type="text" name="event" value="<?php echo $data['event']; ?>" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 500px;">
                        </div>

                        <div>
                            <button type="submit" name="submit" class="btn btn-success">Update Speaker</button>
                            <a href="manage_speaker.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>

        <?php include "bagiankode/footer.php"; ?>
    </div>
    </div>

    <?php include "bagiankode/jsscript.php"; ?>

</body>
</html>
