<?php
session_start();
include('db_connect.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$date = date("Y-m-d");

// Check if attendance is already marked
$check_stmt = $conn->prepare("
    SELECT status, correction_requested, correction_action, correction_processed 
    FROM attendance 
    WHERE user_id = ? AND date = ?
");
$check_stmt->bind_param('is', $user_id, $date);
$check_stmt->execute();
$check_res = $check_stmt->get_result();
$attendance = $check_res->fetch_assoc();
$status = $attendance ? $attendance['status'] : "Not Marked";

// Fetch the latest correction request that is still pending
$correction_stmt = $conn->prepare("
    SELECT a.*, e.username, DATE_FORMAT(a.corrected_at, '%M %d, %Y %h:%i %p') as formatted_date 
    FROM attendance a 
    JOIN employee e ON a.user_id = e.user_id
    WHERE a.user_id = ? AND a.correction_requested = 1 AND a.correction_processed = 0
    ORDER BY a.corrected_at DESC 
    LIMIT 1
");
$correction_stmt->bind_param('i', $user_id);
$correction_stmt->execute();
$correction_res = $correction_stmt->get_result();
$correction_data = $correction_res->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance</title>
    <link rel="stylesheet" href="user.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>/* Main container styling */
.attendance-container {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin: 20px 0;
}

/* Status box styling */
.status-box {
    margin-bottom: 20px;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 6px;
    border-left: 4px solid #6c757d;
}

#current-status {
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 4px;
    display: inline-block;
    margin-left: 8px;
}

.status-not-marked {
    background-color: #f8f9fa;
    color: #6c757d;
}

.status-present {
    background-color: #d4edda;
    color: #155724;
}

.status-absent {
    background-color: #f8d7da;
    color: #721c24;
}

/* Form container styling */
.form-container, 
.correction-container {
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 6px;
}

/* Radio button group styling */
.checkbox-group {
    display: flex;
    margin-bottom: 20px;
    gap: 25px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-size: 16px;
    padding: 10px 15px;
    border-radius: 6px;
    background-color: #e9ecef;
    transition: all 0.2s ease;
}

.checkbox-label:hover {
    background-color: #dee2e6;
}

.checkbox-label input[type="radio"] {
    margin-right: 8px;
    cursor: pointer;
    width: 18px;
    height: 18px;
}

/* Checked state styling */
.checkbox-label input[type="radio"]:checked + .checkbox-label {
    background-color: #007bff;
    color: white;
}

/* Form group styling */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #495057;
}

/* Textarea styling */
#correction-reason {
    width: 100%;
    padding: 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    resize: vertical;
    font-family: inherit;
    transition: border-color 0.2s ease;
}

#correction-reason:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Button styling */
.btn {
    display: inline-block;
    font-weight: 500;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    user-select: none;
    border: 1px solid transparent;
    padding: 10px 20px;
    font-size: 16px;
    line-height: 1.5;
    border-radius: 4px;
    transition: all 0.15s ease-in-out;
    cursor: pointer;
}

.btn:focus {
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-primary {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0069d9;
    border-color: #0062cc;
}

.btn-secondary {
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
}

/* Custom Alert Modal styling */
.custom-alert-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}

.custom-alert-box {
    background-color: white;
    padding: 25px 30px;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    max-width: 400px;
    width: 90%;
    text-align: center;
}

.custom-alert-title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 15px;
    color: #333;
}

.custom-alert-message {
    font-size: 16px;
    margin-bottom: 20px;
    color: #555;
}

.custom-alert-btn {
    padding: 8px 25px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.custom-alert-btn:hover {
    background-color: #0069d9;
}

/* Correction Status Message styling */
.correction-status {
    margin-top: 15px;
    padding: 15px;
    border-radius: 6px;
    background-color: #f8f9fa;
    border-left: 4px solid;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: opacity 0.5s ease-out;
}

.correction-status-pending {
    border-left-color: #ffc107;
    background-color: #fff3cd;
}

.correction-status-approved {
    border-left-color: #28a745;
    background-color: #d4edda;
}

.correction-status-rejected {
    border-left-color: #dc3545;
    background-color: #f8d7da;
}

.correction-status-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
}

.correction-status-title {
    font-weight: 600;
    font-size: 15px;
}

.correction-status-date {
    font-size: 13px;
    color: #6c757d;
}

.correction-status-reason {
    font-size: 14px;
    margin-bottom: 8px;
    color: #495057;
}

.correction-status-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
}

.badge-pending {
    background-color: #ffc107;
    color: #212529;
}

.badge-approved {
    background-color: #28a745;
    color: #fff;
}

.badge-rejected {
    background-color: #dc3545;
    color: #fff;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .checkbox-group {
        flex-direction: column;
        gap: 10px;
    }
    
    .btn {
        width: 100%;
    }
} </style>
</head>
<body>
    <div class="container">
        <?php include 'user_sidebar.php'; ?>

        <script>
            document.querySelector('a[href="user_attendance.php"]').classList.add('active-page');
        </script>

        <main>
            <header>
                <h1>Mark Attendance</h1>
            </header>

            <div class="attendance-container">
                <div class="status-box">
                    <p><strong>Today's Status:</strong> 
                        <span id="current-status" class="status-<?= strtolower(str_replace(" ", "-", $status)); ?>">
                            <?= htmlspecialchars($status); ?>
                        </span>
                    </p>
                </div>
                
                <!-- Attendance Form -->
                <?php if ($status == "Not Marked"): ?>
                    <div class="form-container">
                        <form id="attendance-form">
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="radio" name="attendance" value="Present" required> Present
                                </label>
                                <label class="checkbox-label">
                                    <input type="radio" name="attendance" value="Absent" required> Absent
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Attendance</button>
                        </form>
                    </div>
                <?php else: ?>
                    <!-- Correction Request Form -->
                    <div class="correction-container">
                        <form id="correction-form">
                            <div class="form-group">
                                <label for="correction-reason">Reason for correction:</label>
                                <textarea id="correction-reason" name="reason" rows="3" required></textarea>
                            </div>
                            <button type="submit" id="request-correction" class="btn btn-secondary">Request Correction</button>
                        </form>

                        <!-- Correction Status Display -->
                        <?php if ($correction_data): ?>
                            <div id="correction-status" class="correction-status correction-status-<?= strtolower($correction_data['correction_action'] ?: 'pending'); ?>">
                                <div class="correction-status-header">
                                    <div class="correction-status-title">Correction Request</div>
                                    <div class="correction-status-date"><?= htmlspecialchars($correction_data['formatted_date']); ?></div>
                                </div>
                                <div class="correction-status-reason">
                                    <strong>Reason:</strong> <?= htmlspecialchars($correction_data['correction_reason']); ?>
                                </div>
                                <div>
                                    <span class="correction-status-badge badge-<?= strtolower($correction_data['correction_action'] ?: 'pending'); ?>">
                                        <?= htmlspecialchars(ucfirst($correction_data['correction_action'] ?: 'Pending')); ?>
                                    </span>
                                </div>
                            </div>
                        <?php else: ?>
                            <p>No pending correction requests.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        $(document).ready(function() {
            // Auto-hide correction status message after 10 seconds
            setTimeout(function() {
                $("#correction-status").fadeOut(500);
            }, 10000);

            // Submit attendance
            $('#attendance-form').on('submit', function(event) {
                event.preventDefault();
                var status = $('input[name="attendance"]:checked').val();
                
                if (!status) {
                    alert("Please select Present or Absent.");
                    return;
                }
                
                $.ajax({
                    url: 'mark_attendance.php',
                    type: 'POST',
                    data: { status: status },
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);
                            if (result.status === "success") {
                                alert("Attendance marked successfully!");
                                location.reload();
                            } else {
                                alert(result.message || "Failed to mark attendance.");
                            }
                        } catch (e) {
                            alert("Unexpected error occurred.");
                        }
                    },
                    error: function() {
                        alert("Error connecting to the server. Please try again.");
                    }
                });
            });

            // Request correction
            $('#correction-form').on('submit', function(event) {
                event.preventDefault();
                var reason = $('#correction-reason').val().trim();
                
                if (!reason) {
                    alert("Please provide a reason for the correction request.");
                    return;
                }
                
                $.ajax({
                    url: 'request_correction.php',
                    type: 'POST',
                    data: { reason: reason },
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);
                            if (result.status === "success") {
                                alert("Correction request sent successfully!");
                                location.reload();
                            } else {
                                alert(result.message || "Failed to send correction request.");
                            }
                        } catch (e) {
                            alert("Unexpected error occurred.");
                        }
                    },
                    error: function() {
                        alert("Error connecting to the server. Please try again.");
                    }
                });
            });
        });
    </script>
</body>
</html>