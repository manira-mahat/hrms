<?php
session_start();
include('db_connect.php');

// Handle AJAX Request for Approve/Reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if this is an update status request
    if (isset($_POST['action']) && $_POST['action'] === 'update_status') {
        $user_id = $_POST['user_id'];
        $date = $_POST['date'];
        $new_status = $_POST['status'];
        
        // Validate status
        if ($new_status !== 'Present' && $new_status !== 'Absent') {
            echo json_encode(['success' => false, 'message' => 'Invalid status']);
            exit;
        }
        
        // Update the status
        $stmt = $conn->prepare("UPDATE attendance SET status = ? WHERE user_id = ? AND date = ?");
        $stmt->bind_param('sis', $new_status, $user_id, $date);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating status: ' . $conn->error]);
        }
        exit;
    }
    // Handle original approve/reject functionality
    else if (isset($_POST['action'])) {
        $user_id = $_POST['user_id'];
        $date = $_POST['date'];
        $action = $_POST['action'];

        if ($action === 'approve') {
            // Toggle status logic
            $stmt = $conn->prepare("SELECT status FROM attendance WHERE user_id = ? AND date = ?");
            $stmt->bind_param('is', $user_id, $date);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row) {
                $current_status = $row['status'];
                $status = ($current_status === 'Present') ? 'Absent' : 'Present';
            } else {
                echo "Attendance record not found.";
                exit;
            }

            $correction_requested = 0;
            $correction_processed = 1;
            $correction_action = 'approved';
        } elseif ($action === 'reject') {
            // Get current status without changing it
            $stmt = $conn->prepare("SELECT status FROM attendance WHERE user_id = ? AND date = ?");
            $stmt->bind_param('is', $user_id, $date);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            if ($row) {
                $status = $row['status']; // Keep current status
            } else {
                echo "Attendance record not found.";
                exit;
            }
            
            $correction_requested = 0;
            $correction_processed = 1;
            $correction_action = 'rejected';
        } else {
            echo "Invalid action.";
            exit;
        }

        $stmt = $conn->prepare("
            UPDATE attendance 
            SET status = ?, 
                correction_requested = ?, 
                correction_processed = ?,
                correction_action = ?
            WHERE user_id = ? AND date = ?
        ");
        $stmt->bind_param('siisss', $status, $correction_requested, $correction_processed, $correction_action, $user_id, $date);

        if ($stmt->execute()) {
            echo "Attendance updated successfully.";
        } else {
            echo "Error updating attendance.";
        }
        exit;
    }
}

// Fetch all users with their attendance for today
$date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");

$sql = "SELECT e.user_id, e.name, a.status, a.correction_reason, a.correction_requested 
        FROM employee e 
        LEFT JOIN attendance a ON e.user_id = a.user_id AND a.date = ?
        ORDER BY e.name ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $date);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance</title>
    <link rel="stylesheet" href="admin.css">
    <style>
        /* Header */
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 0;
            border-bottom: 1px solid #cfbebe;
            background-color: #ecf0f1;
            width: 80vw;
            box-sizing: border-box;
            margin: 0 auto;
        }

        header h1 {
            font-size: 2rem;
            margin-left: 20px;
        }


        /* Table */
        table {
            width: 80%;
            border-collapse: collapse;
            background: white;
            border-radius: 5px;
            margin: 0 auto;
        }

        th,
        td {
            text-align: center;
            padding: 12px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #04AA6D;
            color: white;
        }

        /* Status Colors */
        .status-present {
            color: green;
            font-weight: bold;
        }

        .status-absent {
            color: red;
            font-weight: bold;
        }

        .status-notmarked {
            color: gray;
            font-style: italic;
        }

        .status-correction {
            color: orange;
            font-weight: bold;
        }

        /* Buttons */
        button {
            padding: 6px 12px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin: 2px;
        }

        .update-btn {
            background-color: #ffc107;
            color: black;
        }

        .approve-btn {
            background-color: #28a745;
            color: white;
        }

        .reject-btn {
            background-color: #dc3545;
            color: white;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            width: 400px;
            border-radius: 8px;
            text-align: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .close {
            position: absolute;
            right: 15px;
            top: 10px;
            font-size: 24px;
            cursor: pointer;
            color: #888;
            transition: color 0.3s;
        }

        .close:hover {
            color: #333;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 15px;
        }

        /* Enhanced Radio Options Styling */
        .radio-options {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 15px 0;
        }

        .radio-options label {
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 8px 15px;
            border-radius: 4px;
            border: 2px solid #ddd;
            transition: all 0.3s;
        }

        .radio-options label:hover {
            background-color: #f5f5f5;
        }

        .radio-options input[type="radio"] {
            margin-right: 8px;
        }

        /* Style for the Present option */
        .radio-options label:has(input[value="Present"]) {
            border-color: #28a745;
        }

        .radio-options label:has(input[value="Present"]):hover,
        .radio-options label:has(input[value="Present"]:checked) {
            background-color: #e8f5e9;
        }

        /* Style for the Absent option */
        .radio-options label:has(input[value="Absent"]) {
            border-color: #dc3545;
        }

        .radio-options label:has(input[value="Absent"]):hover,
        .radio-options label:has(input[value="Absent"]:checked) {
            background-color: #ffebee;
        }

        /* Alternative styling for browsers that don't support :has */
        .radio-option-present {
            border-color: #28a745 !important;
        }

        .radio-option-present.selected,
        .radio-option-present:hover {
            background-color: #e8f5e9 !important;
        }

        .radio-option-absent {
            border-color: #dc3545 !important;
        }

        .radio-option-absent.selected,
        .radio-option-absent:hover {
            background-color: #ffebee !important;
        }

        .submit-btn {
            background-color: #04AA6D;
            color: white;
            padding: 10px 20px;
            margin-top: 10px;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .submit-btn:hover {
            background-color: #038857;
        }

        /* Date selector */
        .date-selector {
         
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
            margin-right: 35px;
        }

        #attendance-date {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        #load-date {
            background-color: #04AA6D;
            color: white;
            transition: background-color 0.3s;
        }

        #load-date:hover {
            background-color: #038857;
        }

        /* Custom Alert */
        .custom-alert {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .alert-content {
            background-color: white;
            padding: 25px;
            width: 350px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .alert-btn {
            background-color: #04AA6D;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 4px;
            margin-top: 20px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .alert-btn:hover {
            background-color: #038857;
        }

        /* Custom Confirm */
        .custom-confirm {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .confirm-content {
            background-color: white;
            padding: 25px;
            width: 380px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .confirm-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .confirm-yes {
            background-color: #28a745;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .confirm-yes:hover {
            background-color: #218838;
        }

        .confirm-no {
            background-color: #dc3545;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .confirm-no:hover {
            background-color: #c82333;
        }

        /* Status update button */
        .status-toggle {
            background-color: #17a2b8;
            color: white;
        }

        .status-toggle:hover {
            background-color: #138496;
        }

        /* Status update dropdown */
        .status-dropdown {
            display: none;
            position: absolute;
            background-color: white;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            z-index: 10;
            border-radius: 4px;
            overflow: hidden;
        }

        .status-option {
            padding: 8px 12px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .status-option:hover {
            background-color: #f1f1f1;
        }

        .status-present-option {
            color: #28a745;
        }

        .status-absent-option {
            color: #dc3545;
        }

        /* Toast notification */
        .toast {
            visibility: hidden;
            min-width: 250px;
            margin-left: -125px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 4px;
            padding: 16px;
            position: fixed;
            z-index: 1002;
            left: 50%;
            bottom: 30px;
            font-size: 16px;
        }

        .toast.show {
            visibility: visible;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }

        @keyframes fadein {
            from {bottom: 0; opacity: 0;}
            to {bottom: 30px; opacity: 1;}
        }

        @keyframes fadeout {
            from {bottom: 30px; opacity: 1;}
            to {bottom: 0; opacity: 0;}
        }
    </style>
</head>
<div class="container">
    <?php include 'sidebar.php'; ?>
    <script>
        document.querySelector('a[href="admin_attendance.php"]').classList.add('active-page');
    </script>

    <main>
        <header>
            <h1>Attendance Sheet</h1>
            <div class="date-selector">
                <input type="date" id="attendance-date" value="<?= $date ?>">
                <button id="load-date">Load Date</button>
            </div>
        </header>
        <br>

        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Correction Reason</th>
                    <th>Actions</th>
                    <th>Update</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($data = $result->fetch_assoc()) {
                        $status = $data['status'] ?? 'Not Marked';
                        $correctionRequested = $data['correction_requested'] ?? 0;
                        
                        // Determine status class for styling
                        if ($status == 'Present') {
                            $statusClass = 'status-present';
                        } else if ($status == 'Absent') {
                            $statusClass = 'status-absent';
                        } else {
                            $statusClass = 'status-notmarked';
                        }
                ?>
                        <tr id="row-<?= $data['user_id']; ?>">
                            <td><?= htmlspecialchars($data['user_id']); ?></td>
                            <td><?= htmlspecialchars($data['name']); ?></td>
                            <td class="status-cell <?= $statusClass; ?>" id="status-<?= $data['user_id']; ?>"><?= htmlspecialchars($status); ?></td>
                            <td><?= !empty($data['correction_reason']) ? htmlspecialchars($data['correction_reason']) : '-'; ?></td>
                            <td>
                                <?php if ($correctionRequested == 1): ?>
                                    <button class="approve-btn" data-id="<?= $data['user_id']; ?>"
                                        data-date="<?= $date ?>">Approve</button>
                                    <button class="reject-btn" data-id="<?= $data['user_id']; ?>"
                                        data-date="<?= $date ?>">Reject</button>
                                <?php else: ?>
                                    No Actions
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="position: relative;">
                                    <button class="status-toggle" data-id="<?= $data['user_id']; ?>" data-date="<?= $date ?>">Update</button>
                                    <div id="dropdown-<?= $data['user_id']; ?>" class="status-dropdown">
                                        <div class="status-option status-present-option" data-status="Present" data-id="<?= $data['user_id']; ?>" data-date="<?= $date ?>">Present</div>
                                        <div class="status-option status-absent-option" data-status="Absent" data-id="<?= $data['user_id']; ?>" data-date="<?= $date ?>">Absent</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="6">No employees found</td></tr>';
                }
                ?>
            </tbody>
        </table>

        <!-- Toast notification for status updates -->
        <div id="toast" class="toast"></div>
    </main>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Handle load date button
        document.getElementById('load-date').addEventListener('click', function() {
            const selectedDate = document.getElementById('attendance-date').value;
            window.location.href = `admin_attendance.php?date=${selectedDate}`;
        });

        // Handle approve buttons
        document.querySelectorAll(".approve-btn").forEach(button => {
            button.addEventListener("click", function() {
                const userId = this.getAttribute("data-id");
                const date = this.getAttribute("data-date");
                if (confirm("Are you sure you want to approve this correction?")) {
                    updateAttendance(userId, date, "approve");
                }
            });
        });

        // Handle reject buttons
        document.querySelectorAll(".reject-btn").forEach(button => {
            button.addEventListener("click", function() {
                const userId = this.getAttribute("data-id");
                const date = this.getAttribute("data-date");
                if (confirm("Are you sure you want to reject this correction?")) {
                    updateAttendance(userId, date, "reject");
                }
            });
        });

        // Handle status toggle buttons (new feature)
        document.querySelectorAll(".status-toggle").forEach(button => {
            button.addEventListener("click", function(e) {
                e.stopPropagation(); // Prevent event bubbling
                const userId = this.getAttribute("data-id");
                const dropdown = document.getElementById(`dropdown-${userId}`);
                
                // Close all other dropdowns first
                document.querySelectorAll('.status-dropdown').forEach(menu => {
                    if (menu.id !== `dropdown-${userId}`) {
                        menu.style.display = 'none';
                    }
                });
                
                // Toggle this dropdown
                if (dropdown.style.display === 'block') {
                    dropdown.style.display = 'none';
                } else {
                    dropdown.style.display = 'block';
                    
                    // Position the dropdown
                    const buttonRect = this.getBoundingClientRect();
                    dropdown.style.top = '100%';
                    dropdown.style.left = '0';
                }
            });
        });

        // Handle status option selection
        document.querySelectorAll(".status-option").forEach(option => {
            option.addEventListener("click", function() {
                const userId = this.getAttribute("data-id");
                const date = this.getAttribute("data-date");
                const newStatus = this.getAttribute("data-status");
                
                // Close dropdown
                document.getElementById(`dropdown-${userId}`).style.display = 'none';
                
                // Update status without page refresh
                updateStatus(userId, date, newStatus);
            });
        });

        // Close dropdown when clicking elsewhere
        document.addEventListener('click', function(e) {
            const dropdowns = document.querySelectorAll('.status-dropdown');
            dropdowns.forEach(dropdown => {
                if (!dropdown.contains(e.target) && 
                    !e.target.classList.contains('status-toggle')) {
                    dropdown.style.display = 'none';
                }
            });
        });
    });

    // Function for handling original approve/reject actions
    function updateAttendance(userId, date, action) {
        fetch("admin_attendance.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `user_id=${userId}&date=${date}&action=${action}`
            })
            .then(response => response.text())
            .then(result => {
                alert(result);
                location.reload();
            })
            .catch(error => console.error("Error:", error));
    }

    // New function for updating status without page refresh
    function updateStatus(userId, date, newStatus) {
        fetch("admin_attendance.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `user_id=${userId}&date=${date}&status=${newStatus}&action=update_status`
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // Update the status cell without refreshing the page
                    const statusCell = document.getElementById(`status-${userId}`);
                    
                    // Remove current status classes
                    statusCell.classList.remove('status-present', 'status-absent', 'status-notmarked');
                    
                    // Add the appropriate class for the new status
                    if (newStatus === 'Present') {
                        statusCell.classList.add('status-present');
                    } else if (newStatus === 'Absent') {
                        statusCell.classList.add('status-absent');
                    }
                    
                    // Update the status text
                    statusCell.textContent = newStatus;
                    
                    // Show toast notification
                    showToast(`Status updated to "${newStatus}" successfully`);
                } else {
                    // Show error message
                    showToast(`Error: ${result.message}`);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                showToast("An error occurred while updating status");
            });
    }

    // Toast notification function
    function showToast(message) {
        const toast = document.getElementById("toast");
        toast.textContent = message;
        toast.className = "toast show";
        
        // After 3 seconds, remove the show class
        setTimeout(function() { 
            toast.className = toast.className.replace("show", ""); 
        }, 3000);
    }
</script>
</body>

</html>