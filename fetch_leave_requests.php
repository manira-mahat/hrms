<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('db_connect.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT leave_type, start_date, end_date, reason, status FROM leave_requests WHERE user_id = ?";
if ($stmt = mysqli_prepare($conn, $query)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $leave_requests = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $leave_requests[] = $row;
    }

    echo json_encode(["status" => "success", "leave_requests" => $leave_requests]);
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(["status" => "error", "message" => "Database query error."]);
}

mysqli_close($conn);
?>
