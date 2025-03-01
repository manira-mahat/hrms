<?php
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $date = $_POST['date'];
    $status = $_POST['status'];

    // Check if an attendance record already exists for the user on that date
    $check_sql = "SELECT * FROM attendance WHERE user_id = ? AND date = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("is", $user_id, $date);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing attendance record
        $update_sql = "UPDATE attendance SET status = ? WHERE user_id = ? AND date = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sis", $status, $user_id, $date);
    } else {
        // Insert new attendance record
        $update_sql = "INSERT INTO attendance (user_id, date, status) VALUES (?, ?, ?)";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("iss", $user_id, $date, $status);
    }

    if ($update_stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $update_stmt->close();
    $conn->close();
}
?>