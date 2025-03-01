<?php
session_start();
include('db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$date = date("Y-m-d");
$status = isset($_POST['status']) ? $_POST['status'] : null;

// Validate status
if (!in_array($status, ['Present', 'Absent'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid status']);
    exit();
}

// Check if attendance already marked for today
$check_stmt = $conn->prepare("SELECT user_id FROM attendance WHERE user_id = ? AND date = ?");
$check_stmt->bind_param('is', $user_id, $date);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Attendance already marked for today']);
    exit();
}

// Insert new attendance record
$insert_stmt = $conn->prepare("INSERT INTO attendance (user_id, date, status, correction_requested, correction_processed) VALUES (?, ?, ?, 0, 0)");
$insert_stmt->bind_param('iss', $user_id, $date, $status);

if ($insert_stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to mark attendance: ' . $conn->error]);
}

$insert_stmt->close();
$conn->close();
?>
