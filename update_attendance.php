<?php
include 'db_connect.php';

$user_id = $_POST['user_id'];
$date = $_POST['date'];
$status = $_POST['status'];

$query = "UPDATE attendance SET status = ?, correction_request = 0 WHERE user_id = ? AND date = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("sis", $status, $user_id, $date);

$response = ["success" => $stmt->execute()];
echo json_encode($response);
?>