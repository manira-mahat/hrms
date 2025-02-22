<?php
session_start();
include('db_connect.php');

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Set user status to offline
    $update_status = $conn->prepare("UPDATE employee SET active_status = 'Offline' WHERE id = ?");
    $update_status->bind_param('i', $user_id);
    $update_status->execute();
}

// Destroy session properly
$_SESSION = array();
session_unset();
session_destroy();

// Prevent caching (Ensures logged-out users can’t go back)
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to login page
header("Location: homepage.html");
exit;
?>