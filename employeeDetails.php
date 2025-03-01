<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp">
    <title>Employee Details</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 0;
            border-bottom: 1px solid #cfbebe;
            background-color: #ecf0f1;
            width: 80vw;
            box-sizing: border-box;
            margin: 0 auto;
        }

        header h1 {
            font-size: 2rem;
            margin-left: 20px;
        }

        .search-bar {
            display: flex;
            align-items: center;
            margin-right: 35px;
        }

        .search-bar input[type="text"] {
            padding: 8px 12px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 300px;
        }

        /* #details {
            border-collapse: collapse;
            width: 100%;
            table-layout: auto;
            margin-bottom: 20px;
        } */
        #employeeTable {
            min-width: 1500px;
            /* Ensures table is wider than container */
            border-collapse: collapse;
        }

        .container {
            grid-template-columns: 18rem minmax(600px, 1fr) 0rem;
            /* Middle column can grow */
        }



        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            /* Ensures borders between cells are merged */
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            border: 1px solid #ddd;
            /* Adds a border around the table */
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #ddd;
            /* Adds border to each table cell */
        }

        th {
            background-color: #04AA6D;
            color: white;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .profile-pic {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .action-icons {
            display: flex;
            align-items: center;
            /* Align items vertically in the center */
            gap: 5px;
            /* Add some space between the buttons */
        }

        .material-symbols-sharp {
            padding: 5px;
            border-radius: 4px;
            cursor: pointer;
        }

        .search-bar {
            padding: 8px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .table-container {
            grid-column: span 3;
            /* Forces the table to use the full available space */
            width: 100%;
            overflow-x: auto;
            display: block;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php include 'sidebar.php'; ?>

        <script>
            document.querySelector('a[href="employeeDetails.php"]').classList.add('active-page');
        </script>

        <main>

            <header>
                <h1>Employee Details</h1>
                <input type="text" class="search-bar" id="searchInput" placeholder="Search employees...">


            </header>
            <div class="table-container">
                <table id="employeeTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Avatar</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Gender</th>
                            <th>DOB</th>
                            <th>Address</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Qualification</th>
                            <th>Join Date</th>
                            <th>CV</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Database connection
                        $conn = new mysqli("localhost", "root", "", "hrms");

                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // Updated query to order by ID ascending
                        $sql = "SELECT user_id,name,email,contact,gender,dob,address,department,job_position,qualification,join_date,profile_picture,cv FROM employee ORDER BY name ASC";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                // Sanitize the data
                                $user_id = htmlspecialchars($row['user_id']);
                                $name = htmlspecialchars($row['name']);
                                $email = htmlspecialchars($row['email']);
                                $contact = htmlspecialchars($row['contact']);
                                $gender = htmlspecialchars($row['gender']);
                                $dob = htmlspecialchars($row['dob']);
                                $address = htmlspecialchars($row['address']);
                                $department = htmlspecialchars($row['department']);
                                $position = htmlspecialchars($row['job_position']);
                                $qualification = htmlspecialchars($row['qualification']);
                                $join_date = htmlspecialchars($row['join_date']);

                                // Handle profile picture
                                $profile_pic = !empty($row['profile_picture']) ?
                                    htmlspecialchars($row['profile_picture']) :
                                    'uploads/default.png';

                                // Handle CV
                                $cv = !empty($row['cv']) ? htmlspecialchars($row['cv']) : '';

                                echo "<tr>
                                    <td>{$user_id}</td>
                                    <td><img src='{$profile_pic}' alt='Profile' class='profile-pic'></td>
                                    <td>{$name}</td>
                                    <td>{$email}</td>
                                    <td>{$contact}</td>
                                    <td>{$gender}</td>
                                    <td>{$dob}</td>
                                    <td>{$address}</td>
                                    <td>{$department}</td>
                                    <td>{$position}</td>
                                    <td>{$qualification}</td>
                                    <td>{$join_date}</td>
                                    <td>";

                                // Display CV link if available
                                if (!empty($cv)) {
                                    echo "<a href='{$cv}' target='_blank'>
                                        <span class='material-symbols-sharp' 
                                              style='background-color: #007bff; color: white;'>
                                            description
                                        </span>
                                     </a>";
                                } else {
                                    echo "<span style='color: #666;'>No CV</span>";
                                }

                                echo "</td>
                                    <td class='action-icons'>
                                        <a href='editEmployee.php?user_id={$user_id}'>
                                            <span class='material-symbols-sharp' 
                                                  style='background-color: #28a745; color: white;'>
                                                edit
                                            </span>
                                        </a>
                                        <a href='deleteEmployee.php?user_id={$user_id}' 
                                           onclick='return confirm(\"Are you sure you want to delete this employee?\");'>
                                            <span class='material-symbols-sharp' 
                                                  style='background-color: #dc3545; color: white;'>
                                                delete
                                            </span>
                                        </a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='14' style='text-align: center;'>No employees found</td></tr>";
                        }

                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            // Get the search input value and convert it to lowercase
            const searchValue = this.value.toLowerCase();

            // Get all table rows except the header
            const tbody = document.querySelector('#employeeTable tbody');
            const rows = tbody.getElementsByTagName('tr');

            // Loop through each row
            for (let row of rows) {
                // Get the name column (the second column, index 1)
                const nameCell = row.cells[2]; // Name is in the third column (index 2)

                // If the name column exists and contains the search value, show the row
                if (nameCell && nameCell.textContent.toLowerCase().includes(searchValue)) {
                    row.style.display = ""; // Show row
                } else {
                    row.style.display = "none"; // Hide row
                }
            }
        });
    </script>


</body>

</html>