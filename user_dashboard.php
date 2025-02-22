<?php
// Start the session
session_start();

// Include database connection
include('db_connect.php'); 

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: userlogin.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details from usersignup table
$stmt = $conn->prepare("SELECT username, name, email FROM usersignup WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// If user data is not found, redirect to login
if (!$user) {
    header("Location: userlogin.php");
    exit;
}

// Fetch job position and department using email and name
$employee_stmt = $conn->prepare("
    SELECT job_position, department 
    FROM employee 
    WHERE email = ? AND name = ?
");
$employee_stmt->bind_param('ss', $user['email'], $user['name']);
$employee_stmt->execute();
$employee_result = $employee_stmt->get_result();
$employee = $employee_result->fetch_assoc();

// Set default values if no employee record is found
$job_position = $employee['job_position'] ?? 'Not Assigned';
$department = $employee['department'] ?? 'Not Assigned';

// Fetch today's attendance
$date = date("Y-m-d");
$attendance_stmt = $conn->prepare("SELECT status FROM attendance WHERE user_id = ? AND date = ?");
$attendance_stmt->bind_param('is', $user_id, $date);
$attendance_stmt->execute();
$attendance_result = $attendance_stmt->get_result();
$attendance = $attendance_result->fetch_assoc();
$attendance_status = $attendance['status'] ?? 'Not Marked';

// Update active status using email
$update_status = $conn->prepare("UPDATE employee SET active_status = 'Online' WHERE email = ?");
$update_status->bind_param('s', $user['email']);
$update_status->execute();

// Fetch attendance summary (Present, Absent, Leave)
$summary_stmt = $conn->prepare("
    SELECT 
        COALESCE(SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END), 0) AS present_days,
        COALESCE(SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END), 0) AS absent_days,
        COALESCE(SUM(CASE WHEN status = 'Leave' THEN 1 ELSE 0 END), 0) AS leave_days
    FROM attendance WHERE user_id = ?
");
$summary_stmt->bind_param('i', $user_id);
$summary_stmt->execute();
$summary_result = $summary_stmt->get_result();
$summary = $summary_result->fetch_assoc();

// Fetch leave request notifications for the user
$leave_notifications_stmt = $conn->prepare("
    SELECT id, leave_type, start_date, end_date, status 
    FROM leave_requests
    WHERE user_id = ? AND is_notified = FALSE
");
$leave_notifications_stmt->bind_param('i', $user_id);
$leave_notifications_stmt->execute();
$leave_notifications_result = $leave_notifications_stmt->get_result();
$leave_notifications = $leave_notifications_result->fetch_all(MYSQLI_ASSOC);

// Mark leave requests as notified
$mark_notified_stmt = $conn->prepare("
    UPDATE leave_requests SET is_notified = TRUE WHERE user_id = ? AND is_notified = FALSE
");
$mark_notified_stmt->bind_param('i', $user_id);
$mark_notified_stmt->execute();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp&display=swap">
  <link rel="stylesheet" href="user.css">
  <style>
  .summary-container {
    display: flex;
    justify-content: center;
    align-items: center;
    gap:50px;
    flex-wrap: wrap;
    margin: 80px auto;
    max-width: 900px;
}

.summary-box {
    flex: 1;
    min-width: 240px;
    height: 220px;
    padding: 30px;
    text-align: center;
    border-radius: 12px;
    color: white;
    font-size: 22px;
    font-weight: bold;
    box-shadow: 4px 4px 12px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.present-box { background-color: #4CAF50; }
.absent-box { background-color: #FF5733; }
.leave-box { background-color: #FFC107; }

.notification-box {
    background-color: #0178A4;
    color: white;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.notification-box p { margin: 0; }
.notification-box a { color: white; text-decoration: underline; }
  </style>
</head>
<body>
  <div class="container">
    <div><?php include 'user_sidebar.php'; ?></div>

    <script>
      document.addEventListener("DOMContentLoaded", function() {
        document.querySelector('a[href="user_dashboard.php"]').classList.add('active-page');
      });

      // Auto-refresh every 10 seconds to update the attendance summary
      setInterval(fetchAttendanceSummary, 10000);

      function fetchAttendanceSummary() {
          fetch("fetch_attendance_summary.php")
          .then(response => response.json())
          .then(data => {
              document.getElementById("presentCount").innerText = data.present_days;
              document.getElementById("absentCount").innerText = data.absent_days;
              document.getElementById("leaveCount").innerText = data.leave_days;
          });
      }
    </script>

    <!-- Main Section -->
    <main>
    <header >
        <h1>Welcome, <?= htmlspecialchars($user['username']) ?></h1>
      </header>
      <br>
      <!-- <h3><p>- Email: <?= htmlspecialchars($user['email']) ?></p></h3><br>
      <h3><p>- Position: <?= htmlspecialchars($job_position) ?></p></h3><br>
      <h3><p>- Department: <?= htmlspecialchars($department) ?></p></h3><br> -->

      <section class="attendance-section">
        <?php if ($leave_notifications): ?>
            <div class="notification-box">
                <h3>Leave Requests:</h3>
                <?php foreach ($leave_notifications as $notification): ?>
                    <p>
                        Leave Type: <?= htmlspecialchars($notification['leave_type']) ?> <br>
                        Start Date: <?= htmlspecialchars($notification['start_date']) ?> <br>
                        End Date: <?= htmlspecialchars($notification['end_date']) ?> <br>
                        Status: <?= htmlspecialchars($notification['status']) ?>
                    </p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="summary-container">
            <div class="summary-box present-box">
                 Present<br> <span id="presentCount"><br><?= $summary['present_days'] ?? 0; ?></span>
            </div>
            <div class="summary-box absent-box">
                 Absent<br> <span id="absentCount"><br><?= $summary['absent_days'] ?? 0; ?></span>
            </div>
            <div class="summary-box leave-box">
                 Leave<br> <span id="leaveCount"><br><?= $summary['leave_days'] ?? 0; ?></span>
            </div>
        </div>
      </section>
    </main>
  </div>
</body>
</html>