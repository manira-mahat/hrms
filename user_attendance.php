<?php
session_start();
include('db_connect.php');

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: userlogin.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$date = date("Y-m-d");

// Fetch today's attendance for the user
$attendance_stmt = $conn->prepare("SELECT status FROM attendance WHERE user_id = ? AND date = ?");
$attendance_stmt->bind_param('is', $user_id, $date);
$attendance_stmt->execute();
$attendance_result = $attendance_stmt->get_result();
$attendance = $attendance_result->fetch_assoc();
$attendance_status = $attendance['status'] ?? 'Not Marked';

// Fetch attendance summary (total present, absent, and leave days)
$summary_stmt = $conn->prepare("SELECT 
    SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS present_days,
    SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) AS absent_days,
    SUM(CASE WHEN status = 'Leave' THEN 1 ELSE 0 END) AS leave_days
    FROM attendance WHERE user_id = ?");
$summary_stmt->bind_param('i', $user_id);
$summary_stmt->execute();
$summary_result = $summary_stmt->get_result();
$summary = $summary_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Attendance</title>
    <link rel="stylesheet" href="user.css">
    <style>
     
        .main-content {
            max-width: 850px;
            margin: 0 auto;
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .attendance-status {
            background: #04AA6D;
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .attendance-status h2 {
            margin-bottom: 15px;
            font-size: 2rem;
        }

        .status {
            font-size: 1.8rem;
            font-weight: bold;
        }

        .attendance-summary h2 {
            font-size: 1.8rem;
            margin-bottom: 15px;
        }

        .summary-box {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .summary-item {
            flex: 1;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .present { border-top: 5px solid #2ecc71; }
        .absent { border-top: 5px solid #e74c3c; }
        .leave { border-top: 5px solid #f1c40f; }
    </style>
</head>

<body>
    <div class="container">
        <!-- Include Sidebar -->
        <?php include 'user_sidebar.php'; ?>

        <script>
            document.querySelector('a[href="user_attendance.php"]').classList.add('active-page');
        </script>

        <main>
        <header style="background-color: #1ABC9C">
                <h1>Your Attendance</h1>
            </header>

            <div class="main-content">
                <div class="attendance-status">
                    <h2>Today's Attendance</h2>
                    <p class="status"><?= htmlspecialchars($attendance_status); ?></p>
                </div>

                <div class="attendance-summary">
                    <h2>Attendance Summary</h2>
                    <div class="summary-box">
                        <div class="summary-item present">✅ Present: <?= $summary['present_days'] ?? 0; ?></div>
                        <div class="summary-item absent">❌ Absent: <?= $summary['absent_days'] ?? 0; ?></div>
                        <div class="summary-item leave">🌴 Leave: <?= $summary['leave_days'] ?? 0; ?></div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>