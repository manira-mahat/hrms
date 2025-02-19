<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
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
      <a href="landpage.php">
        <span class="material-symbols-sharp">grid_view</span>
        <h3>Dashboard</h3>
      </a><br>
      <a href="employeeDetails.php">
        <span class="material-symbols-sharp">group</span>
        <h3>Employees</h3>
      </a><br>
      <a href="attendence.php">
        <span class="material-symbols-sharp">edit_calendar</span>
        <h3>Attendence</h3>
      </a><br>
      <a href="calender.php">
        <span class="material-symbols-sharp">calendar_month</span>
        <h3>Calender</h3>
      </a><br>
      <a href="addemployee.php">
        <span class="material-symbols-sharp">add</span>
        <h3>AddEmployee</h3>
      </a><br>
      <a href="admin_leave_requests.php">
        <span class="material-symbols-sharp">check_box</span>
        <h3>User Request</h3>
      </a><br>

      <a href="javascript:void(0)" onclick="showLogoutModal()" class="logout-btn">
        <span class="material-symbols-sharp">logout</span>
        <h3>Logout</h3>
      </a>

      <!-- Modal Structure -->
      <div id="logoutModal" class="modal">
        <div class="modal-content">
          <p>Do you really want to logout?</p>
          <button onclick="logout()" class="yes-btn">Yes</button>
          <button onclick="hideLogoutModal()" class="no-btn">No</button>
        </div>
      </div>

      <!-- JavaScript -->
      <script>
        function showLogoutModal() {
          document.getElementById("logoutModal").style.display = "flex";
        }

        function hideLogoutModal() {
          document.getElementById("logoutModal").style.display = "none";
        }

        function logout() {
          window.location.href = "homepage.html";
        }
      </script>

    </div>
  </aside>
</body>

</html>