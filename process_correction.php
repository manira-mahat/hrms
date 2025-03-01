<?php
// Include your database connection
include 'db_connect.php'; // Make sure this file establishes $conn

// Check if required POST parameters are set
if (!isset($_POST['user_id'], $_POST['date'], $_POST['action'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
    exit;
}

$user_id = $_POST['user_id'];
$date = $_POST['date'];
$action = $_POST['action'];

// Validate action
if ($action === 'approve' || $action === 'reject') {
    // Update status if approved (toggle Present/Absent)
    if ($action === 'approve') {
        // Get current status
        $statusSql = "SELECT status FROM attendance WHERE user_id = ? AND date = ?";
        $statusStmt = $conn->prepare($statusSql);
        $statusStmt->bind_param('is', $user_id, $date);
        $statusStmt->execute();
        $statusResult = $statusStmt->get_result();

        if ($statusResult->num_rows > 0) {
            $currentStatus = $statusResult->fetch_assoc()['status'];
            $newStatus = ($currentStatus === 'Present') ? 'Absent' : 'Present';

            // Update the status
            $updateSql = "UPDATE attendance SET status = ? WHERE user_id = ? AND date = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param('sis', $newStatus, $user_id, $date);
            $updateStmt->execute();
            
            if ($updateStmt->affected_rows > 0) {
                echo json_encode(['success' => true]);
                exit;
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to update record']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'No record found']);
            exit;
        }
    }

    // Always reset the correction_requested flag for both approve and reject actions
    $resetSql = "UPDATE attendance SET correction_requested = 0 WHERE user_id = ? AND date = ?";
    $resetStmt = $conn->prepare($resetSql);
    $resetStmt->bind_param('is', $user_id, $date);
    $resetStmt->execute();

    echo json_encode(['success' => true]);
    exit;

} else {
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
    exit;
}

?>