<?php
if (isset($_POST['submit'])) {
    include "includes/config.php";

    $date_old = $_POST['date_old'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $desc = $_POST['desc'];
    $event = $_POST['event'];

    $query = "UPDATE schedule SET date = '$date', time = '$time', description = '$desc', event = '$event' WHERE date = '$date_old'";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        header("Location: manage_schedule.php");
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
                <h1 class="mt-4">Edit Schedule</h1>
                <div style="text-align: right;">
                    <a href="dashboard.php" style="font-weight: bold; color: silver; text-decoration: none;"> DashBoard</a>
                    <a style="font-weight: bold; color: silver;"> > Schedule</a>
                </div>
                <br>
                <div style="background-color: white; padding: 30px; border-radius: 5px;">
                    <?php
                    if (isset($error)) {
                        echo "<div class='alert alert-danger'>Error: " . $error . "</div>";
                    }
                    ?>
                    <form method="POST">
                        <?php
                        include "includes/config.php";

                        if (!isset($_GET['date']) || empty($_GET['date'])) {
                            echo "Error: Schedule tidak ditemukan.";
                            exit;
                        }

                        $date = $_GET['date'];
                        $query = mysqli_query($conn, "SELECT * FROM schedule WHERE date = '$date'");
                        $data = mysqli_fetch_assoc($query);

                        if (!$data) {
                            echo "Error: Data schedule tidak ditemukan.";
                            exit;
                        }
                        ?>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Date</label>
                            <input type="text" name="date" required value="<?php echo $data['date']; ?>" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 500px;">
                            <input type="hidden" name="date_old" value="<?php echo $data['date']; ?>">
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Time</label>
                            <input type="time" name="time" value="<?php echo $data['time']; ?>" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 500px;">
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Description</label>
                            <textarea name="desc" rows="5" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 500px;"><?php echo $data['description']; ?></textarea>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Event</label>
                            <input type="text" name="event" required value="<?php echo $data['event']; ?>" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 500px;">
                        </div>

                        <div>
                            <button type="submit" name="submit" class="btn btn-success">Update Schedule</button>
                            <a href="manage_schedule.php" class="btn btn-secondary">Cancel</a>
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
