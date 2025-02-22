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
        <a href="user_calender.php">
            <span class="material-symbols-sharp">calendar_month</span>
            <h3>Calendar</h3>
        </a><br>
        <a href="leave_request.php">
            <span class="material-symbols-sharp">drafts</span>
            <h3>Leave Request</h3>
        </a><br>
        <a href="user_profile.php">
        <span class="material-symbols-sharp">account_circle</span>
            <h3>Profile</h3>
        </a><br>
        <button onclick="showLogoutModal()" class="logout-btn">
            <span class="material-symbols-sharp">logout</span>
            <h3>Logout</h3>
        </button>
    </div>
</aside>
<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="modal">
    <div class="modal-content">
        <p>Do you really want to logout?</p>
        <button onclick="logout()" class="yes-btn">Yes</button>
        <button onclick="hideLogoutModal()" class="no-btn">No</button>
    </div>
</div>

<script>
    document.querySelector('a[href="user_sidebar.php"]').classList.add('active-page');
function showLogoutModal() {
    document.getElementById("logoutModal").style.display = "flex"; // Show modal
}

function hideLogoutModal() {
    document.getElementById("logoutModal").style.display = "none"; // Hide modal
}

function logout() {
    window.location.href = "userlogout.php"; // Redirect to logout
}
</script>

</body>
</html>