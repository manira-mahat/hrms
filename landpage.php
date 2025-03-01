<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Landing Page</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp&display=swap">
  <link rel="stylesheet" href="admin.css">

  <style>
    header {
      display: flex;
      justify-content: center;
      align-items: center;
      padding-bottom: 15px;
      border-bottom: 1px solid #cfbebe;
      background-color: #ecf0f1;
      padding: 20px 0;
      width: 80vw;
      box-sizing: border-box;
      position: relative;
      margin: 0;
    }

    header h1 {
      font-size: 2rem;
      text-align: center;
      margin: 0;
    }
  </style>
</head>

<body>
  <div class="container">
    <?php include 'sidebar.php'; ?>

    <main>
      <header>
        <h1>Welcome to HR Management Dashboard</h1>
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
    </main>
  </div>

  <script>
    // Make sure we only have one event listener
    document.addEventListener('DOMContentLoaded', function () {
      // Mark current page as active
      const activeLink = document.querySelector('a[href="landpage.php"]');
      if (activeLink) {
        activeLink.classList.add('active-page');
      }
      
      // Get reference to the elements
      const employeeCount = document.getElementById('employeeCount');
      const activeCount = document.getElementById('activeCount');
      const leaveCount = document.getElementById('leaveCount');
      
      // Add a timestamp to prevent caching
      const timestamp = new Date().getTime();
      const url = `getEmployeeCounts.php?t=${timestamp}`;
      
      // Fetch data with cache busting
      fetch(url, {
        method: 'GET',
        headers: {
          'Cache-Control': 'no-cache',
          'Pragma': 'no-cache'
        }
      })
      .then(response => response.json())
      .then(data => {
        console.log('Data received:', data);
        employeeCount.textContent = data.totalEmployees;
        activeCount.textContent = data.activeEmployees;
        leaveCount.textContent = data.pendingLeaves;
      })
      .catch(error => {
        console.error('Error fetching data:', error);
      });
    });
  </script>
</body>

</html>