<?php
session_start();
include('db_connect.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$date = date("Y-m-d");
$reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';

// Validate input
if (empty($reason)) {
    echo json_encode(['status' => 'error', 'message' => 'Correction reason is required.']);
    exit();
}

// Check if attendance for today exists
$check_stmt = $conn->prepare("SELECT * FROM attendance WHERE user_id = ? AND date = ?");
$check_stmt->bind_param('is', $user_id, $date);
$check_stmt->execute();
$check_res = $check_stmt->get_result();
$attendance = $check_res->fetch_assoc();

if ($attendance) {
    // If correction has already been requested
    if ($attendance['correction_requested'] == 1) {
        echo json_encode(['status' => 'error', 'message' => 'Correction request already sent.']);
        exit();
    }
    
    // Update existing attendance with correction request
    $update_stmt = $conn->prepare("
        UPDATE attendance 
        SET correction_requested = 1, correction_reason = ?, correction_processed = 0 
        WHERE user_id = ? AND date = ?
    ");
    $update_stmt->bind_param('sis', $reason, $user_id, $date);
    $update_stmt->execute();
    
    if ($update_stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to request correction.']);
    }
} else {
    // If attendance not marked yet, optionally you could prevent correction or insert a new record
    echo json_encode(['status' => 'error', 'message' => 'No attendance record found for today.']);
}

$check_stmt->close();
$conn->close();
?>