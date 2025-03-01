<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request Status</title>
    <link rel="stylesheet" href="user.css">
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

        .hidden {
            display: none;
        }
    </style>
</head>

<body>
<div class="container">
    <?php include 'user_sidebar.php'; ?>

    <script>
        document.querySelector('a[href="leave_status.php"]').classList.add('active-page');
    </script>

    <main>
        <header>
            <h1>Leave Request Status</h1>
        </header>
        <div id="message-box" class="message-box"></div>
        <div id="leave-status" class="hidden">
            <table border="1" cellpadding="5">
                <thead>
                    <tr>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Reason</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="statusBody"></tbody>
            </table>
        </div>
    </main>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    console.log("Fetching leave requests..."); // Debugging

    fetch("fetch_leave_requests.php")
    .then(response => response.json())
    .then(data => {
        console.log("Fetched Data:", data); // Debugging response

        const statusBody = document.getElementById("statusBody");
        statusBody.innerHTML = ""; // Clear previous data

        if (data.status === "success") {
            if (data.leave_requests.length > 0) {
                data.leave_requests.forEach(request => {
                    let row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${request.leave_type}</td>
                        <td>${request.start_date}</td>
                        <td>${request.end_date}</td>
                        <td>${request.reason}</td>
                        <td>${request.status}</td>
                    `;
                    statusBody.appendChild(row);
                });

                // Make table visible
                document.getElementById("leave-status").classList.remove("hidden");
            } else {
                statusBody.innerHTML = `<tr><td colspan="5">No leave requests found.</td></tr>`;
                document.getElementById("leave-status").classList.remove("hidden");
            }
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => {
        console.error("Error fetching leave requests:", error);
        alert("Failed to fetch leave requests. Try again.");
    });
});
</script>

</body>
</html>
