<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details</title>
    <link rel="stylesheet" href="admin.css">

    <style>
        header {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid #cfbebe;
            background-color: #ecf0f1;
            width: 80vw;
            box-sizing: border-box;
            margin: 0 auto;
        }

        /* Center the table */
        main {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        header h1 {
            font-size: 4rem;
            text-align: center;
            margin: 0;
        }

        /* Table styles */
        /* Set table width and align it */
        #details {
            width: 80%;
            /* Adjust width as needed */
            margin: 0 auto;
            /* Center the table */
            table-layout: fixed;
            /* Ensures equal column width */
            border-collapse: collapse;
        }

        /* Add a border around the entire table */
        /* Style the table */
        /* Style the table */
        table {
            width: 80%;
            margin: 20px auto;
            table-layout: fixed;
            border-collapse: collapse;
            border-top: 2px solid #000;
            /* Top border */
            border-bottom: 2px solid #000;
            /* Bottom border */
        }

        /* Style table header and data cells */
        th,
        td {
            text-align: center;
            padding: 10px;
            border-right: 2px solid #000;
            /* Vertical separator between columns */
        }

        /* Add left border to the first column */
        th:first-child,
        td:first-child {
            border-left: 2px solid #000;
        }

        /* Add right border to the last column */
        th:last-child,
        td:last-child {
            border-right: 2px solid #000;
        }



        /* Ensure equal column width */
        #details th,
        #details td {
            width: 33.33%;
            /* Adjust based on the number of columns */
            text-align: center;
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
            text-align: center;
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

        /* Style the dropdown */
        .status-dropdown {
            font-size: 1.2rem;
            padding: 8px;
            width: 100%;
            max-width: 150px;
            /* Limit width for better alignment */
            height: 40px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: transparent;
            /* Makes the dropdown background blend with the row */
            color: inherit;
            /* Inherits text color from the row */
            text-align: center;
            appearance: none;
            /* Removes default browser styling */
            -webkit-appearance: none;
            -moz-appearance: none;
            position: relative;
            padding-right: 25px;
            /* Space for the custom dropdown arrow */
            cursor: pointer;
        }

        /* Custom dropdown arrow */
        .status-dropdown::after {
            content: "▼";
            font-size: 1rem;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }

        /* Remove default dropdown arrow in some browsers */
        select::-ms-expand {
            display: none;
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
            <br>
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
                            if (!isset($data['id'])) {
                                echo "<tr><td colspan='8'>Warning: 'id' key is missing in the database result for this row.</td></tr>";
                                // Skip to the next row to avoid errors
                                continue;
                            }

                            // Assign 'id' or fallback to 'N/A' if undefined
                            $id = isset($data['id']) ? $data['id'] : 'N/A';

                            // Render the table row
                            echo "<tr>
                              <td>{$data['id']}</td>
                              <td>{$data['name']}</td>
                         <td>
    <select name='status' id='status-<?= {$data['id']}; ?>' class='status-dropdown' >
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