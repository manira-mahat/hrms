<?php
session_start();
include('db_connect.php');
 include ('user_sidebar.php');
// Fetch leave requests with user names and reasons
$query = "SELECT lr.id, lr.user_id, lr.leave_type, lr.start_date, lr.end_date, lr.status, lr.reason, u.username 
          FROM leave_requests lr 
          JOIN usersignup u ON lr.user_id = u.id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    echo "<table>
            <tr>
                <th>Username</th>
                <th>Leave Type</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Reason</th> <!-- Added Reason column -->
                <th>Status</th>
                <th>Action</th>
            </tr>";

    // Display leave requests
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['username']}</td>
                <td>{$row['leave_type']}</td>
                <td>{$row['start_date']}</td>
                <td>{$row['end_date']}</td>
                <td>{$row['reason']}</td> <!-- Display Reason -->
                <td>{$row['status']}</td>
                <td>";

        // Check if the leave request is not approved or rejected
        if ($row['status'] == 'Pending') {
            echo "<form method='POST'>
                    <input type='hidden' name='request_id' value='{$row['id']}'>
                    <select name='status'>
                        <option value='Approved' " . ($row['status'] == 'Approved' ? 'selected' : '') . ">Approved</option>
                        <option value='Rejected' " . ($row['status'] == 'Rejected' ? 'selected' : '') . ">Rejected</option>
                    </select>
                    <input type='submit' name='update_status' value='Update'>
                  </form>";
        } else {
            echo "No action available";
        }

        echo "</td></tr>";
    }
    echo "</table>";
} else {
    echo "No leave requests found.";
}

// Update leave request status and send notification
if (isset($_POST['update_status'])) {
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];

    // Update the status in the database
    $update_query = "UPDATE leave_requests SET status = '$status' WHERE id = $request_id";
    if (mysqli_query($conn, $update_query)) {
        // Fetch the user's email to send notification
        $user_query = "SELECT u.email FROM leave_requests lr JOIN usersignup u ON lr.user_id = u.id WHERE lr.id = $request_id";
        $user_result = mysqli_query($conn, $user_query);
        $user = mysqli_fetch_assoc($user_result);

        // Send email notification
        $subject = "Leave Request Status Update";
        $message = "Your leave request has been " . $status . ".";
        $headers = "From: underground.rabit09@gmail.com";
        mail($user['email'], $subject, $message, $headers);

        echo "<div class='success-message'>Leave request updated successfully and notification sent.</div>";
    } else {
        echo "<div class='error-message'>Error updating leave request.</div>";
    }
}

mysqli_close($conn);
?>