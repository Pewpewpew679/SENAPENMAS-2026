<?php
session_start();
include "includes/config.php";

if (!isset($_SESSION['useremail'])) {
    header("Location: login.php");
    exit;
}

$past_conf_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

if ($past_conf_id > 0 && $event_id > 0) {
    // Get event name for menu link
    $event_query = mysqli_query($conn, "SELECT event_name FROM events WHERE event_id = $event_id");
    $event = mysqli_fetch_assoc($event_query);
    
    if ($event) {
        // Generate menu link
        $event_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $event['event_name'])));
        $menu_link = "event/" . $event_id . "/" . $event_slug;
        
        // Delete from menu first
        mysqli_query($conn, "DELETE FROM menu WHERE menu_link = '$menu_link'");
    }
    
    // Delete from past_conferences
    $delete = mysqli_query($conn, "DELETE FROM past_conferences WHERE past_conf_id = $past_conf_id");
    
    if ($delete) {
        header("Location: inputpastconference.php?msg=deleted");
    } else {
        header("Location: inputpastconference.php?msg=error");
    }
} else {
    header("Location: inputpastconference.php?msg=error");
}
exit;
?>