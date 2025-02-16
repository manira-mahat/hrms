<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp&display=swap">
    <link rel="stylesheet" href="sidebar.css">
</head>
<body>
<aside>
    <div class="top">
        <!-- Logo -->
        <div class="logoimg">
            <img src="logo-img.png" alt="Company Logo">
        </div>
        <!-- Close icon -->
        <div class="close">
            <span class="material-symbols-sharp">close</span>
        </div>
    </div>
    <!-- Sidebar navigation -->
    <div class="sidebar">
        <a href="user_dashboard.php">
            <span class="material-symbols-sharp">grid_view</span>
            <h3>Dashboard</h3>
        </a><br>
        <a href="user_attendance.php">
            <span class="material-symbols-sharp">edit_calendar</span>
            <h3>Attendance</h3>
        </a><br>
        <a href="user_calender.php">
            <span class="material-symbols-sharp">calendar_month</span>
            <h3>Calendar</h3>
        </a><br>
        <a href="leave_request.php">
            <span class="material-symbols-sharp">drafts</span>
            <h3>Leave Request</h3>
        </a><br>
        <a href="userlogout.php">
            <span class="material-symbols-sharp">logout</span>
            <h3>Logout</h3>
        </a>
    </div>
</aside>
</body>
</html>