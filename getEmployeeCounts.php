<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";  // Change if different
$password = "";      // Change if different
$database = "hrms";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Query to count total employees
$sql = "SELECT COUNT(*) AS totalEmployees FROM employee";
$result = $conn->query($sql);

if (!$result) {
    die(json_encode(["error" => "Query failed: " . $conn->error]));
}

$row = $result->fetch_assoc();
$totalEmployees = $row['totalEmployees'];

// Check if the 'status' column exists in 'employee' table
$sqlCheck = "SHOW COLUMNS FROM employee LIKE 'status'";
$resultCheck = $conn->query($sqlCheck);
if ($resultCheck->num_rows > 0) {
    // If 'status' column exists, count active employees
    $sqlActive = "SELECT COUNT(*) AS activeEmployees FROM employee WHERE status = 'active'";
} else {
    // If 'status' column does not exist, count all employees
    $sqlActive = "SELECT COUNT(*) AS activeEmployees FROM employee";
}

$resultActive = $conn->query($sqlActive);
$rowActive = $resultActive->fetch_assoc();
$activeEmployees = $rowActive['activeEmployees'];

// Query to count pending leave requests
$sqlLeaveCheck = "SHOW COLUMNS FROM leave_requests LIKE 'status'";
$resultLeaveCheck = $conn->query($sqlLeaveCheck);
if ($resultLeaveCheck->num_rows > 0) {
    // If 'status' column exists in leave_request table
    $sqlLeave = "SELECT COUNT(*) AS pendingLeaves FROM leave_requests WHERE status = 'pending'";
} else {
    // If no 'status' column in leave_request table, count all leave requests
    $sqlLeave = "SELECT COUNT(*) AS pendingLeaves FROM leave_requests";
}

$resultLeave = $conn->query($sqlLeave);

if (!$resultLeave) {
    die(json_encode(["error" => "Leave Query failed: " . $conn->error]));
}

$rowLeave = $resultLeave->fetch_assoc();
$pendingLeaves = $rowLeave['pendingLeaves'];

// Check if the count is returning 0 and update accordingly
if ($pendingLeaves === null || $pendingLeaves === '') {
    $pendingLeaves = 0; // Set to 0 if no rows match
}

$conn->close();

// Return data as JSON
echo json_encode([
    "totalEmployees" => $totalEmployees,
    "activeEmployees" => $activeEmployees,
    "pendingLeaves" => $pendingLeaves
]);
?>