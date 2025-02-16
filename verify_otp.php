<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $otp = $_POST['otp'];

    // Database connection
    $connection = mysqli_connect('localhost', 'root', '', 'hrms');

    if (!$connection) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Prepare the query to avoid SQL injection
    $sql = "SELECT * FROM userSignUp WHERE email = ? AND otp = ? AND is_verified = 0";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, 'ss', $email, $otp);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $otp_sent_time = strtotime($user['otp_sent_time']);
        $current_time = time();
        $time_difference = ($current_time - $otp_sent_time) / 60; // Difference in minutes

        // Check if OTP has expired (15 minutes expiration)
        if ($time_difference > 15) {
            echo "OTP has expired. Please request a new one.";
        } else {
            // OTP is valid and within time limit, so update verification status
            $updateSql = "UPDATE userSignUp SET is_verified = 1 WHERE email = ? AND otp = ?";
            $updateStmt = mysqli_prepare($connection, $updateSql);
            mysqli_stmt_bind_param($updateStmt, 'ss', $email, $otp);
            if (mysqli_stmt_execute($updateStmt)) {
                // Redirect to login page with success message
                header("Location: userlogin.php?message=Verification_success");
                exit();  // Ensure no further code is executed
            } else {
                echo "Error: " . mysqli_error($connection);
            }
        }
    } else {
        echo "Invalid OTP or the account is already verified.";
    }

    mysqli_close($connection);
}
?>
<html>
<head>
<style>
    /* General styling for the page */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f7fc;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Wrapper for the form */
.container {
    background-color: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 300px;
    text-align: center;
}

/* Form title */
h2 {
    color: #4caf50;
    margin-bottom: 20px;
}

/* Input fields styling */
input[type="email"], input[type="text"], input[type="submit"] {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 14px;
}

/* Focus effect for inputs */
input[type="email"]:focus, input[type="text"]:focus {
    border-color: #4caf50;
    outline: none;
}

/* Submit button styling */
input[type="submit"] {
    background-color: #4caf50;
    color: #fff;
    border: none;
    cursor: pointer;
    font-size: 16px;
}

input[type="submit"]:hover {
    background-color: #45a049;
}

/* Error and success messages */
.message {
    color: red;
    font-size: 14px;
    margin-top: 15px;
}

.success-message {
    color: green;
    font-size: 16px;
    margin-top: 15px;
}

</style>
</head>
<body>
<div class="container">
    <h2>Verify Your OTP</h2>

    <?php if (isset($_GET['message']) && $_GET['message'] == 'Verification_success'): ?>
        <p class="success-message">OTP verified successfully! You can now log in.</p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Enter your email" required><br>
        <input type="text" name="otp" placeholder="Enter the OTP" required><br>
        <input type="submit" value="Verify OTP">
    </form>

    <?php if (isset($_GET['message']) && $_GET['message'] == 'Verification_failed'): ?>
        <p class="message">OTP verification failed. Please check your OTP and try again.</p>
    <?php endif; ?>
</div>
</body>
</html>