<?php
session_start();
include('db_connect.php');

// Fetch all users
$sql = "SELECT * FROM usersignup";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance</title>
    <link rel="stylesheet" href="admin.css">
    <!-- Include jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

        main {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        header h1 {
            font-size: 2rem;
            text-align: center;
            margin: 0;
        }

        #details {
            width: 80%;
            margin: 0 auto;
            table-layout: fixed;
            border-collapse: collapse;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            border: 2px solid #cfbebe;
            /* Makes the outer table border white */
        }

        th,
        td {
            text-align: center;
            padding: 10px;
            border: 2px solid #cfbebe;
            /* Makes all row and column borders white */
            color: black;
            /* Keeps text visible */
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
            background-color: #04AA6D;
            color: white;
        }

        .status-dropdown {
            font-size: 1.2rem;
            padding: 8px;
            width: 100%;
            max-width: 150px;
            height: 40px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: transparent;
            color: inherit;
            text-align: center;
            cursor: pointer;
        }

        select::-ms-expand {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Include Sidebar -->
        <?php include 'sidebar.php'; ?>

        <script>
            document.querySelector('a[href="attendence.php"]').classList.add('active-page');
        </script>

        <main>
            <header>
                <h1>Attendance Sheet</h1>
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
                    // Fetch attendance data for each user
                    while ($data = $result->fetch_assoc()) {
                        $user_id = $data['id'];
                        $date = date("Y-m-d");

                        // Check if the user has already marked attendance
                        $attendance_stmt = $conn->prepare("SELECT status FROM attendance WHERE user_id = ? AND date = ?");
                        $attendance_stmt->bind_param('is', $user_id, $date);
                        $attendance_stmt->execute();
                        $attendance_result = $attendance_stmt->get_result();
                        $attendance = $attendance_result->fetch_assoc();
                        $status = $attendance ? $attendance['status'] : 'Not Marked';
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($data['id']); ?></td>
                            <td><?= htmlspecialchars($data['Name']); ?></td>
                            <td>
                                <?php if ($status === 'Not Marked'): ?>
                                    <select class="status-dropdown" data-id="<?= $data['id']; ?>">
                                        <option value="" selected disabled>Select Status</option>
                                        <option value="Present">Present</option>
                                        <option value="Absent">Absent</option>
                                        <option value="Leave">Leave</option>
                                    </select>

                                <?php else: ?>
                                    <p><?= htmlspecialchars($status); ?></p>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </main>
    </div>

    <script>
        $(document).ready(function() {
            $('.status-dropdown').on('change', function() {
                var userId = $(this).data('id');
                var status = $(this).val();
                var dropdown = $(this); // Store reference to dropdown

                $.ajax({
                    url: 'update_attendance_status.php',
                    type: 'POST',
                    data: {
                        user_id: userId,
                        status: status
                    },
                    success: function(response) {
                        if (response == 'success') {
                            // Replace the dropdown with plain text
                            dropdown.replaceWith('<p>' + status + '</p>');
                            alert('Status updated successfully!');
                        } else {
                            alert('Failed to update status.');
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>