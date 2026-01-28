<?php
if (isset($_POST['submit'])) {
    include "includes/config.php";

    $speaker_name = $_POST['speaker_name'];
    $information = $_POST['information'];
    $event = $_POST['event'];
    
    // buat upload foto 
    $photo = '';
    if ($_FILES['photo']['name'] != '') {
        $foto_name = $_FILES['photo']['name'];
        $foto_tmp = $_FILES['photo']['tmp_name'];
        $foto_path = "gambar/" . time() . "_" . $foto_name;
        
        move_uploaded_file($foto_tmp, $foto_path);
        $photo = time() . "_" . $foto_name;
    }

    $query = "INSERT INTO speaker (photo, speaker_name, information, event) VALUES ('$photo', '$speaker_name', '$information', '$event')";
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
                <h1 class="mt-4">Add Speaker</h1>
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
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Photo</label>
                            <input type="file" name="photo" accept="image/*" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 400px;">
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Speaker Name</label>
                            <input type="text" name="speaker_name" required style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 500px;">
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Information</label>
                            <textarea name="information" rows="5" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 500px;"></textarea>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Event</label>
                            <input type="text" name="event" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 500px;">
                        </div>

                        <div>
                            <button type="submit" name="submit" class="btn btn-success">Save Speaker</button>
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
