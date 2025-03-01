<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hrms";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize response array
$response = array(
    'totalEmployees' => 0,
    'activeEmployees' => 0,
    'pendingLeaves' => 0
);

// Get total employees - with explicit table selection
$totalQuery = "SELECT COUNT(*) as total FROM employee";
try {
    $totalResult = $conn->query($totalQuery);
    
    if ($totalResult && $totalResult->num_rows > 0) {
        $row = $totalResult->fetch_assoc();
        $response['totalEmployees'] = intval($row['total']);
    }
} catch (Exception $e) {
    // Just continue if there's an error
}

// Get active employees (present today in attendance)
$today = date('Y-m-d');
try {
    $activeQuery = "SELECT COUNT(DISTINCT user_id) as active FROM attendance 
                    WHERE DATE(date) = '$today' AND status = 'present'";
    $activeResult = $conn->query($activeQuery);
    
    if ($activeResult && $activeResult->num_rows > 0) {
        $row = $activeResult->fetch_assoc();
        $response['activeEmployees'] = intval($row['active']);
    }
} catch (Exception $e) {
    // Just continue if there's an error
}

// Get pending leave requests
try {
    $leaveQuery = "SELECT COUNT(*) as pending FROM leave_requests WHERE status = 'pending'";
    $leaveResult = $conn->query($leaveQuery);
    
    if ($leaveResult && $leaveResult->num_rows > 0) {
        $row = $leaveResult->fetch_assoc();
        $response['pendingLeaves'] = intval($row['pending']);
    }
} catch (Exception $e) {
    // Just continue if there's an error
}

// Disable any caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);

// Close connection
$conn->close();
?>