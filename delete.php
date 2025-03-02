<?php

$conn = mysqli_connect('localhost', 'root', '', 'hrms');
$user_id = (int) $_REQUEST['user_id'];
$sql = "DELETE FROM employee where user_id=$user_id";

if (mysqli_query($conn, $sql)) {
    header('Location: employeeDetails.php');
    exit;
} else {
    echo "Error: ";
}