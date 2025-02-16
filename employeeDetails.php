<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details</title>
    <link rel="stylesheet" href="admin.css">

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
            font-size: 4rem;
            margin-left: 20px;
        }

        .search-bar {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }

        .search-bar input[type="text"] {
            padding: 8px 12px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 250px;
        }

        .search-bar button {
            padding: 8px 12px;
            margin-left: 8px;
            border: none;
            background-color: #04AA6D;
            color: white;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
        }

        .search-bar button:hover {
            background-color: #039e5f;
        }

        #details {
            border-collapse: collapse;
            width: 100%;
            table-layout: auto;
            margin-bottom: 20px;
        }

        #details th,
        #details td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            word-wrap: break-word;
        }

        #details th {
            background-color: #04AA6D;
            color: white;
        }

        #details tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        #details tr:hover {
            background-color: #f1f1f1;
        }

        #details img.profile-pic {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .table-container {
            overflow-x: auto;
        }

        @media (max-width: 768px) {
            #details th,
            #details td {
                font-size: 12px;
                padding: 8px;
            }

            .btn {
                font-size: 12px;
                padding: 6px 10px;
            }

            .search-bar input[type="text"] {
                width: 150px;
            }
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
                <div class="search-bar">
                    <input type="text" placeholder="Search employees...">
                    <button>Search</button>
                </div>
            </header>
            <div class="table-container">
                <table id="details">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Picture</th>
                            <th>Name</th>
                            <th>DOB</th>
                            <th>Address</th>
                            <th>Contact</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>Join Date</th>
                            <th>Department</th>
                            <th>Qualification</th>
                            <th>Job Position</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $connection = new mysqli("localhost", "root", "", "hrms");

                        if ($connection->connect_error) {
                            die("Connection failed: " . $connection->connect_error);
                        }

                        $sql = "SELECT * FROM employee ORDER BY id ASC";
                        $result = $connection->query($sql);

                        if ($result->num_rows > 0) {
                            while ($data = $result->fetch_assoc()) {
                                $id = htmlspecialchars($data['id']);
                                $name = htmlspecialchars($data['name']);
                                $dob = htmlspecialchars($data['dob']);
                                $address = htmlspecialchars($data['address']);
                                $contact = htmlspecialchars($data['contact']);
                                $gender = htmlspecialchars($data['gender']);
                                $email = htmlspecialchars($data['email']);
                                $join_date = htmlspecialchars($data['join_date']);
                                $department = htmlspecialchars($data['department']);
                                $qualification = htmlspecialchars($data['qualification']);
                                $job_position = htmlspecialchars($data['job_position']);
                                $profile_picture = htmlspecialchars($data['profile_picture'] ?? 'uploads/default.jpg');

                                echo "<tr>
                                        <td>{$id}</td>
                                        <td><img class='profile-pic' src='{$profile_picture}' alt='Profile Picture'></td>
                                        <td>{$name}</td>
                                        <td>{$dob}</td>
                                        <td>{$address}</td>
                                        <td>{$contact}</td>
                                        <td>{$gender}</td>
                                        <td>{$email}</td>
                                        <td>{$join_date}</td>
                                        <td>{$department}</td>
                                        <td>{$qualification}</td>
                                        <td>{$job_position}</td>
                                        <td>
                                            <a href='addemployee.php?id={$id}'><button class='btn btn-primary'>Edit</button></a>
                                            <a href='delete.php?id={$id}' onclick='return deleteconfirm();'><button class='btn btn-danger'>Delete</button></a>
                                        </td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='13'>No records found</td></tr>";
                        }

                        $connection->close();
                        ?>
                    </tbody>
                </table>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const searchInput = document.querySelector('.search-bar input[type="text"]');
                    const searchButton = document.querySelector('.search-bar button');
                    let searchTimeout;

                    function performSearch() {
                        const searchQuery = searchInput.value.trim();
                        if (searchQuery) {
                            fetch(`searchEmployee.php?search=${encodeURIComponent(searchQuery)}`)
                                .then(response => response.text())
                                .then(data => {
                                    document.querySelector('#details tbody').innerHTML = data;
                                })
                                .catch(error => console.error('Error:', error));
                        } else {
                            console.warn("Search query is empty!");
                        }
                    }

                    // Debounced search on input
                    searchInput.addEventListener('input', function () {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(performSearch, 300);
                    });

                    // Search on button click
                    searchButton.addEventListener('click', performSearch);
                });

                function deleteconfirm() {
                    return confirm("Are you sure you want to delete this record?");
                }
            </script>
        </main>
    </div>
</body>

</html>
