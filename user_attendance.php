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

// Check if the user exists in the users table
$check_user_stmt = $conn->prepare("SELECT id FROM usersignup WHERE id = ?");
$check_user_stmt->bind_param("i", $user_id);
$check_user_stmt->execute();
$check_user_result = $check_user_stmt->get_result();

if ($check_user_result->num_rows === 0) {
    // User doesn't exist, display an error message
    echo "User does not exist in the database.";
    exit;
}

// Fetch today's attendance
$attendance_stmt = $conn->prepare("SELECT * FROM attendance WHERE user_id = ? AND date = ?");
$attendance_stmt->bind_param('is', $user_id, $date);
$attendance_stmt->execute();
$attendance_result = $attendance_stmt->get_result();
$attendance = $attendance_result->fetch_assoc();

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mark attendance
    if (isset($_POST['attendance_status']) && !$attendance) {
        $status = $_POST['attendance_status'];

        $insert_stmt = $conn->prepare("INSERT INTO attendance (user_id, date, status) VALUES (?, ?, ?)");
        $insert_stmt->bind_param('iss', $user_id, $date, $status);

        if ($insert_stmt->execute()) {
            header("Location: user_attendance.php");
            exit;
        } else {
            echo "Error: " . $insert_stmt->error;
        }
    }

    // Handle correction request
    if (isset($_POST['correction_request']) && isset($_POST['reason'])) {
        $reason = $_POST['reason'];
        $correction_stmt = $conn->prepare("UPDATE attendance SET status = 'Pending Correction', correction_request = ? WHERE user_id = ? AND date = ?");
        $correction_stmt->bind_param('sis', $reason, $user_id, $date);
        $correction_stmt->execute();
        header("Location: user_attendance.php");
        exit;
    }
}

// Fetch attendance summary
$summary_stmt = $conn->prepare("
    SELECT 
        SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS present_days,
        SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) AS absent_days,
        SUM(CASE WHEN status = 'Correction(present)' THEN 1 ELSE 0 END) AS mistake_days
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
  <title>Attendence Page</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp&display=swap">
  <link rel="stylesheet" href="user.css">
  <style>
        select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: #f4f4f4 url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIiIGhlaWdodD0iOCIgdmlld0JveD0iMCAwIDEyIDgiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGcgZmlsbD0iIzAwMCI+PHBhdGggZD0iTTUgN2wtNCAzVjZoNGwzLTRoMi4xbC0zIDR6Ii8+PC9nPjwvc3ZnPg==') no-repeat right center;
            background-size: 12px;
            padding: 10px 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            border-color: #333;
            width: 15%;
        }

        .main-content {
            flex: 1;
            padding: 20px;
        }

        h1,
        h3 {
            color: #333;
        }

        form {
            margin: 20px 0;
        }

        textarea,
        button,
        select {
            display: block;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        button[type="submit"] {
            background-color: rgb(11, 148, 20);
            color: black;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: rgb(167, 232, 170);
        }

        button[type="submit"]:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(14, 21, 15, 0.6);
        }
    </style>
</head>

<body>
  <div class="container">
    <?php
    include 'user_sidebar.php';
    ?>

    <script>
      document.querySelector('a[href="user_attendance.php"]').classList.add('active-page');
    </script>

    <!-- Main section start -->
    <main>
       <header style="background-color: #1ABC9C">
                <h1>Attendance</h1>
            </header>
            <br>
            <div class="main-content">
                <h3>Mark Today's Attendance</h3>
                <?php if (!$attendance): ?>
                    <form method="post">
                        <label for="attendance_status">Select Status:</label>
                        <select id="attendance_status" name="attendance_status" required>
                            <option value="Present">Present</option>
                            <option value="Absent">Absent</option>
                            <option value="mistake">Correction(present)</option>
                        </select>

                        <button type="submit">Submit</button>
                    </form>
                   
                <?php else: ?>
                    <p><strong>Status:</strong> <?= htmlspecialchars($attendance['status']); ?></p>
                    <?php if ($attendance['status'] === 'Pending Correction'): ?>
                        <p><em>Correction request submitted.</em></p>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Correction Request -->
                 <br>
                <h3>Request Attendance Correction</h3>
                <form method="post">
                    <textarea name="reason" placeholder="Explain your correction request..." required></textarea>
                    <button type="submit" name="correction_request">Submit Request</button>
                </form>

                <!-- Attendance Summary -->
                <h3>Attendance Insights</h3><br>
                <p>✅ Present Days: <?= $summary['present_days'] ?? 0; ?></p>
                <p>❌ Absent Days: <?= $summary['absent_days'] ?? 0; ?></p>
                <p>🌴 Leave Days: <?= $summary['mistake_days'] ?? 0; ?></p>
                <br>

                <!-- Attendance History -->
                <h3>Recent Attendance</h3>
                <table>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                    <?php
                    $history_stmt = $conn->prepare("SELECT * FROM attendance WHERE user_id = ? ORDER BY date DESC LIMIT 10");
                    $history_stmt->bind_param('i', $user_id);
                    $history_stmt->execute();
                    $history_result = $history_stmt->get_result();

                    while ($row = $history_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['date']); ?></td>
                            <td><?= htmlspecialchars($row['status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
    </main>
    <!-- Main section end -->
  </div>
</body>

</html>
