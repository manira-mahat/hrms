<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Landing Page</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp&display=swap">
  <link rel="stylesheet" href="styles.css">


</head>
<body>

  <div class="container">
    <?php
      include 'sidebar.php';
    ?>

<script>
  document.querySelector('a[href="landpage.php"]').classList.add('active-page');
</script>

    <!-- Main section start -->
    <main>
      <!-- <div class="main-content"> -->
      <header>
        <h1>Welcome to HR Management Dashboard</h1>

        <!-- <button id="logoutBtn">Logout</button> -->
      </header>

      <section class="dashboard">
        <div class="card">
          <h3>Total Employees</h3>
          <p id="employeeCount">0</p>
        </div>
        <div class="card">
          <h3>Active Employees</h3>
          <p id="activeCount">0</p>
        </div>
        <div class="card">
          <h3>Pending Leave Requests</h3>
          <p id="leaveCount">0</p>
        </div>
      </section>

      <!-- </div> -->
  <!-- </div> -->



  </main>
  <!-- Main section end -->
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const employeeCount = document.getElementById('employeeCount');
      const activeCount = document.getElementById('activeCount');
      const leaveCount = document.getElementById('leaveCount');

      setTimeout(() => {
        employeeCount.textContent = '0';
        activeCount.textContent = '0';
        leaveCount.textContent = '0';
      }, 1000);

      document.getElementById('logoutBtn').addEventListener('click', function () {
        alert("Logged out!");
        window.location.href = 'login.html';
      });
    });
  </script>



</body>

</html>