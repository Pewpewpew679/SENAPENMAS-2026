<?php

include "includes/config.php";

// Query untuk mengambil event yang aktif (status = 1)
$queryActiveEvent = "SELECT event_name FROM events WHERE status = '1' LIMIT 1";
$resultActiveEvent = $conn->query($queryActiveEvent);
$eventName = "No Active Events";

if ($resultActiveEvent->num_rows > 0) {
    $rowEvent = $resultActiveEvent->fetch_assoc();
    $eventName = $rowEvent['event_name'];
}

// Query untuk menghitung total events
$queryTotalEvents = "SELECT COUNT(*) as total FROM events";
$resultTotalEvents = $conn->query($queryTotalEvents);
$rowTotal = $resultTotalEvents->fetch_assoc();
$totalEvents = $rowTotal['total'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<?php include "bagiankode/head.php"; ?>

<body class="sb-nav-fixed">
<?php include "bagiankode/menunav.php"; ?>

<div id="layoutSidenav">
    <?php include "bagiankode/menu.php"; ?>

    <div id="layoutSidenav_content" style="background-color: #EFF6FF;">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Dashboard</h1>

                <div class="row" style="background-color: white; padding: 20px; border-radius: 5px;">

                <!-- Welcome Alert -->
                <div class="alert alert-success" style="background-color: green; color: white">
                    <strong>WELCOME!!!</strong><br>
                    This is the administrator page for managing your website
                </div>

                <!-- Dashboard Cards -->
                <div class="row mb-4">
                    <!-- Active Events Card -->
                    <div class="col-md-4 mb-4">
                        <div class="card bg-success text-white h-100" style="min-height: 200px;">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <h2 class="card-title mb-2" style="font-size: 48px; font-weight: bold;">
                                        <?php echo strtoupper($eventName); ?>
                                    </h2>
                                    <p class="card-text mb-3">Events that are now active</p>
                                </div>
                                <div class="text-center" style="background: rgba(17, 50, 2, 0.2); padding: 10px; border-radius: 5px;">
                                <a href="inputevents.php" class="text-white" style="text-decoration: none; text-align: center;">
                                    More info <i class="fas fa-info-circle"></i>
                                </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Events Card -->
                    <div class="col-md-4 mb-4">
                        <div class="card bg-warning text-white h-100" style="min-height: 200px;">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <h2 class="card-title mb-2" style="font-size: 48px; font-weight: bold;">
                                        <?php echo $totalEvents; ?>
                                    </h2>
                                    <p class="card-text mb-3">Number of Events</p>
                                </div>
                                <div class="text-center" style="background: rgba(146, 92, 21, 0.2); padding: 10px; border-radius: 5px;">
                                <a href="inputevents.php" class="text-white" style="text-decoration: none;">
                                    More info <i class="fas fa-info-circle"></i>
                                </a>
                            </div>
                            </div>
                        </div>
                    </div>

                    <!-- Web Statistics Card -->
                    <div class="col-md-4 mb-4">
                        <div class="card bg-danger text-white h-100" style="min-height: 200px;">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <h2 class="card-title mb-2" style="font-size: 32px; font-weight: bold;">
                                        Web statistics
                                    </h2>
                                    <p class="card-text">Visitor</p>
                                </div>
                            </div>
                        </div>
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