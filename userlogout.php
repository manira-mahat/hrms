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

// Destroy session and redirect
session_destroy();
header("Location: userlogin.php");
exit;
?>
