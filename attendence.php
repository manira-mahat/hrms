<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details</title>
    <link rel="stylesheet" href="admin.css">

    <style>
        /* Table styles */
        #details {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #details td, #details th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #details tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #details tr:hover {
            background-color: #ddd;
        }

        #details th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #04AA6D;
            color: white;
        }

        /* Button styles */
        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
</head>

<body>
    <div class="container">
        <?php include 'sidebar.php'; ?>

        <script>
            document.querySelector('a[href="attendence.php"]').classList.add('active-page');
        </script>

        <main>

        <header>

        <h1>Attendence Sheet</h1>
      </header>
      <table id="details">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                  // Database connection
                  $connection = new mysqli("localhost", "root", "", "hrms");

                  // Check connection
                  if ($connection->connect_error) {
                      die("Connection failed: " . $connection->connect_error);
                  }
  
                  // Read from database
                  $sql = "SELECT * FROM employee";
                  $result = $connection->query($sql);
  
                  // Display data
                  if ($result->num_rows > 0) {
                    while ($data = $result->fetch_assoc()) {
                        // Debugging Step: Check if 'id' exists in the current row
                        if (!isset($data['Id'])) {
                            echo "<tr><td colspan='8'>Warning: 'id' key is missing in the database result for this row.</td></tr>";
                            // Skip to the next row to avoid errors
                            continue;
                        }

                        // Assign 'id' or fallback to 'N/A' if undefined
                      $id = isset($data['Id']) ? $data['Id'] : 'N/A';
              
                      // Render the table row
                      echo "<tr>
                              <td>{$data['Id']}</td>
                              <td>{$data['Name']}</td>
                         <td>
    <select name='status' id='status-<?= {$data ['Id']}; ?>' class='status-dropdown' >
        <option value='Absent' selected>Absent</option>
        <option value='Present'>Present</option>
    </select>
</td>

                              
                            </tr>";
                  }
              } else {
                  echo "<tr><td colspan='8'>No records found</td></tr>";
              }
              
              

                $connection->close();
                ?>
                 </tbody>
        </table>
        </main>
    </div>
</body>
</html>