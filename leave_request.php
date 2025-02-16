<?php
session_start();
include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $leave_type = $_POST['leave_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = $_POST['reason'];

    $query = "INSERT INTO leave_requests (user_id, leave_type, start_date, end_date, reason, status) 
              VALUES (?, ?, ?, ?, ?, 'Pending')";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "issss", $user_id, $leave_type, $start_date, $end_date, $reason);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["status" => "success", "message" => "Leave request submitted successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error submitting leave request."]);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit(); // Stop further execution
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leave Request Form</title>
  <link rel="stylesheet" href="user.css">

  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f8f9fa;
    }

    .form-container {
      max-width: 500px;
      margin: 0 auto;
      padding: 20px;
      border: 1px solid #ddd;
      border-radius: 5px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      background-color: #ffffff;
      margin-top: 3rem;
    }

    .form-group {
      margin-bottom: 15px;
    }

    label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }

    input, select, textarea {
      width: 100%;
      padding: 8px;
      box-sizing: border-box;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    button {
      width: 100%;
      padding: 10px;
      background-color: #007BFF;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }

    button:hover {
      background-color: #0056b3;
    }

    .success-message, .error-message {
      display: none;
      padding: 10px;
      margin-bottom: 15px;
      text-align: center;
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
    <?php include 'user_sidebar.php'; ?>

    <script>
      document.querySelector('a[href="leave_request.php"]').classList.add('active-page');
    </script>

    <main>
        <header style="background-color: #1ABC9C">
            <h1>Leave Request Form</h1>
        </header>

        <div class="form-container">
            <div id="message-box" class="success-message"></div>

            <form id="leaveForm">
                <div class="form-group">
                    <label for="leave_type">Leave Type</label>
                    <select id="leave_type" name="leave_type" required>
                        <option value="Sick Leave">Sick Leave</option>
                        <option value="Casual Leave">Casual Leave</option>
                        <option value="Earned Leave">Earned Leave</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" required>
                </div>

                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" required>
                </div>

                <div class="form-group">
                    <label for="reason">Reason</label>
                    <textarea id="reason" name="reason" rows="4" placeholder="Enter the reason for your leave" required></textarea>
                </div>

                <button type="submit">Submit Request</button>
            </form>
        </div>
    </main>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
        let today = new Date();
        today.setDate(today.getDate() + 1);
        let minDate = today.toISOString().split("T")[0];

        document.getElementById("start_date").setAttribute("min", minDate);
        document.getElementById("end_date").setAttribute("min", minDate);

        document.getElementById("start_date").addEventListener("change", function () {
            let startDate = this.value;
            document.getElementById("end_date").setAttribute("min", startDate);
        });

        // AJAX Form Submission
        document.getElementById("leaveForm").addEventListener("submit", function (event) {
            event.preventDefault();

            let formData = new FormData(this);

            fetch("leave_request.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                let messageBox = document.getElementById("message-box");

                if (data.status === "success") {
                    messageBox.className = "success-message";
                } else {
                    messageBox.className = "error-message";
                }

                messageBox.innerHTML = data.message;
                messageBox.style.display = "block";

                // Clear form fields after successful submission
                if (data.status === "success") {
                    document.getElementById("leaveForm").reset();
                }

                setTimeout(() => {
                    messageBox.style.display = "none";
                }, 3000);
            })
            .catch(error => console.error("Error:", error));
        });
    });
  </script>

</body>
</html>