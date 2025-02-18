<?php
include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $status = $_POST['status'];
    $date = date("Y-m-d");

    // Check if attendance already exists
    $check_stmt = $conn->prepare("SELECT id FROM attendance WHERE user_id = ? AND date = ?");
    $check_stmt->bind_param("is", $user_id, $date);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing attendance record
        $update_stmt = $conn->prepare("UPDATE attendance SET status = ? WHERE user_id = ? AND date = ?");
        $update_stmt->bind_param("sis", $status, $user_id, $date);
        if ($update_stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        // Insert new attendance record
        $insert_stmt = $conn->prepare("INSERT INTO attendance (user_id, date, status) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("iss", $user_id, $date, $status);
        if ($insert_stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }
    }
}
?>