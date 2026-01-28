<?php
if (isset($_POST['submit'])) {
    include "includes/config.php";

    $date = $_POST['date'];
    $time = $_POST['time'];
    $desc = $_POST['desc'];
    $event = $_POST['event'];

    $query = "INSERT INTO schedule (date, time, description, event) VALUES ('$date', '$time', '$desc', '$event')";
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
                <h1 class="mt-4">Add Schedule</h1>
                <div style="text-align: right;">
                    <a href="dashboard.php" style="font-weight: bold; color: silver; text-decoration: none;"> DashBoard</a>
                    <a style="font-weight: bold; color: silver;"> > Schedule</a>
                </div>
                <br>
                <div style="background-color: white; padding: 30px; border-radius: 5px;">
                    <form method="POST">
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Date</label>
                            <input type="text" name="date" required style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 500px;">
                        </div>

                        

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Time</label>
                            <input type="time" name="time" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 500px;">
                        </div>
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Description</label>
                            <textarea name="desc" rows="5" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 500px;"></textarea>
                        </div>
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Event</label>
                            <input type="text" name="event" required style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 500px;">
                        </div>

                        <div>
                            <button type="submit" name="submit" class="btn btn-success">Save Schedule</button>
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
