<?php
if (isset($_GET['id'])) {
    $userId = (int)$_GET['id'];

    // Database connection
    $connection = mysqli_connect('localhost', 'root', '', 'hrms');

    if (!$connection) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Check if the user exists and is not already verified
    $stmt = $connection->prepare("SELECT is_verified FROM userSignUp WHERE id = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user['is_verified'] == 1) {
            echo "User account is already verified.";
        } else {
            // Approve the user account
            $updateStmt = $connection->prepare("UPDATE userSignUp SET is_verified = 1 WHERE id = ?");
            $updateStmt->bind_param('i', $userId);

            if ($updateStmt->execute()) {
                echo "User account has been approved successfully!";
            } else {
                echo "Error: " . $connection->error;
            }

            $updateStmt->close();
        }
    } else {
        echo "Invalid user ID.";
    }

    $stmt->close();
    mysqli_close($connection);
} else {
    echo "Invalid verification request.";
}
?>