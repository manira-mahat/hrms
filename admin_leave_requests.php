<?php
session_start();
include('db_connect.php');

// Handle AJAX request for updating leave status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['request_id']) && isset($_POST['status'])) {
    $request_id = intval($_POST['request_id']);
    $status = $_POST['status'];

    // Update leave status and set is_notified = FALSE
    $update_stmt = $conn->prepare("UPDATE leave_requests SET status = ?, is_notified = FALSE WHERE id = ?");
    $update_stmt->bind_param('si', $status, $request_id);

    if ($update_stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Leave request updated successfully!", "request_id" => $request_id]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error updating leave request!", "request_id" => $request_id]);
    }
    exit;
}

// Fetch pending leave requests
$requests_stmt = $conn->prepare("
    SELECT leave_requests.id, usersignup.name, leave_requests.leave_type, leave_requests.start_date, leave_requests.end_date, leave_requests.status
    FROM leave_requests 
    JOIN usersignup ON leave_requests.user_id = usersignup.id
    WHERE leave_requests.status = 'Pending'
");
$requests_stmt->execute();
$requests_result = $requests_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">
    <title>Admin - Manage Leave Requests</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

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

        header h1 {
            font-size: 2rem;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        button {
            padding: 5px 10px;
            margin: 5px;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 3px;
        }

        .approve-btn {
            background-color: #28a745;
        }

        .approve-btn:hover {
            background-color: #218838;
        }

        .reject-btn {
            background-color: #dc3545;
        }

        .reject-btn:hover {
            background-color: #c82333;
        }

        .message-box {
            display: none;
            text-align: center;
            padding: 10px;
            margin-bottom: 10px;
            font-weight: bold;
            border-radius: 5px;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>

    <div class="container">
        <?php include 'sidebar.php'; ?>
        <script>
            document.querySelector('a[href="admin_leave_requests.php"]').classList.add('active-page');
        </script>
        <main>
            <header>
                <h1>Manage Leave Requests</h1>
            </header>

            <div id="message-box" class="message-box"></div>

            <table>
                <tr>
                    <th>Employee Name</th>
                    <th>Leave Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $requests_result->fetch_assoc()): ?>
                    <tr id="row-<?= $row['id']; ?>">
                        <td><?= htmlspecialchars($row['name']); ?></td>
                        <td><?= htmlspecialchars($row['leave_type']); ?></td>
                        <td><?= htmlspecialchars($row['start_date']); ?></td>
                        <td><?= htmlspecialchars($row['end_date']); ?></td>
                        <td><?= htmlspecialchars($row['status']); ?></td>
                        <td>
                            <button class="approve-btn" onclick="updateRequest(<?= $row['id']; ?>, 'Approved')">Approve</button>
                            <button class="reject-btn" onclick="updateRequest(<?= $row['id']; ?>, 'Rejected')">Reject</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </main>
    </div>

    <script>
        function updateRequest(requestId, status) {
            let formData = new FormData();
            formData.append("request_id", requestId);
            formData.append("status", status);

            fetch("admin_leave_requests.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                let messageBox = document.getElementById("message-box");

                if (data.status === "success") {
                    messageBox.className = "message-box success-message";
                    messageBox.innerHTML = data.message;
                    messageBox.style.display = "block";

                    // Remove the row from the table
                    let rowElement = document.getElementById("row-" + requestId);
                    if (rowElement) {
                        rowElement.remove();
                    }
                } else {
                    messageBox.className = "message-box error-message";
                    messageBox.innerHTML = data.message;
                    messageBox.style.display = "block";
                }

                setTimeout(() => {
                    messageBox.style.display = "none";
                }, 3000);
            })
            .catch(error => console.error("Error:", error));
        }
    </script>

</body>
</html>