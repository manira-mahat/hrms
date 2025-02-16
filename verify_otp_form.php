<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $otp = $_POST['otp'];

    // Database connection
    $connection = mysqli_connect('localhost', 'root', '', 'hrms');

    if (!$connection) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Rate limiting: Check the number of OTP attempts in the last 1 hour
    $rateLimitSql = "SELECT COUNT(*) as attempt_count FROM otp_attempts WHERE email = '$email' AND attempt_time > NOW() - INTERVAL 1 HOUR";
    $rateLimitResult = mysqli_query($connection, $rateLimitSql);
    $rateLimitRow = mysqli_fetch_assoc($rateLimitResult);

    if ($rateLimitRow['attempt_count'] >= 5) {
        echo "You have reached the maximum number of attempts. Please try again later.";
    } else {
        // Add OTP attempt to the database
        $attemptSql = "INSERT INTO otp_attempts (email, attempt_time) VALUES ('$email', NOW())";
        mysqli_query($connection, $attemptSql);

        // Verify the OTP entered by the user
        $sql = "SELECT * FROM userSignUp WHERE email = '$email' AND otp = '$otp' AND is_verified = 0";
        $result = mysqli_query($connection, $sql);

        if (mysqli_num_rows($result) > 0) {
            // Update is_verified to 1 if OTP matches
            $updateSql = "UPDATE userSignUp SET is_verified = 1 WHERE email = '$email' AND otp = '$otp'";
            if (mysqli_query($connection, $updateSql)) {
                echo "Your account has been verified! You can now <a href='login.php'>log in</a>.";
            } else {
                echo "Error: " . mysqli_error($connection);
            }
        } else {
            echo "Invalid OTP or the account is already verified.";
        }
    }

    mysqli_close($connection);
}
?>

<!-- OTP Form -->
<form method="POST">
    <input type="email" name="email" placeholder="Enter your email" required><br>
    <input type="text" name="otp" placeholder="Enter the OTP" required><br>
    <input type="submit" value="Verify OTP">
</form>