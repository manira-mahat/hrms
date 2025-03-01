<?php
// Start session at the very beginning
session_start();
require 'db_connect.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';
$password_reset_success = false;
$debug_output = '';
$is_debug_mode = false; // Set to false in production

// Super verbose debugging function
function debug_log($label, $value)
{
    global $debug_output, $is_debug_mode;
    if ($is_debug_mode) {
        error_log("DEBUG: $label: " . print_r($value, true));
        $debug_output .= "<strong>$label:</strong> " . print_r($value, true) . "<br>";
    }
}

// Log session details
debug_log("Session status", session_status());
debug_log("Session ID", session_id());
debug_log("SESSION data", $_SESSION);

// Log all submitted values
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    debug_log("POST data", $_POST);
}

// Step 1: Request OTP
if (isset($_POST['request_otp'])) {
    $email = trim($_POST['email']);
    debug_log("Email submitted", $email);

    // Check if email exists in employee table
    $stmt = $conn->prepare("SELECT * FROM employee WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    debug_log("Email lookup result count", $result->num_rows);

    if ($result->num_rows > 0) {
        // Generate a simple numeric OTP
        $otp = (string)rand(100000, 999999);
        debug_log("Generated OTP", $otp);

        // Store OTP directly in session
        $_SESSION['actual_otp'] = $otp;
        $_SESSION['reset_email'] = $email;
        $_SESSION['otp_created_time'] = time();
        $_SESSION['show_otp_form'] = true; // Explicitly set this flag

        debug_log("Session after setting OTP", $_SESSION);

        // Store email in password_resets for marking that a reset is in progress
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, created_at) 
                                VALUES (?, 'SESSION_STORED_OTP', NOW())
                                ON DUPLICATE KEY UPDATE token = VALUES(token), created_at = NOW()");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        debug_log("DB Insert Result", $stmt->affected_rows);

        // Send OTP via email
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'manira2061@gmail.com';
        $mail->Password = 'ntwv gage tsub fdrg'; // Consider using environment variables in production
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('lastp9241@gmail.com', 'HR Management System');
        $mail->addAddress($email);
        $mail->isHTML(true);

        $mail->Subject = 'Password Reset OTP';
        $mail->Body    = "Your OTP is: <strong>$otp</strong>";

        if ($mail->send()) {
            $message = "OTP has been sent to your email.";
            debug_log("Mail send result", "Success");
        } else {
            $message = "Failed to send OTP. Please try again. Error: " . $mail->ErrorInfo;
            debug_log("Mail error", $mail->ErrorInfo);
            $_SESSION['show_otp_form'] = false; // Don't show OTP form if email failed
        }
    } else {
        $message = "Email not registered.";
    }
}

// Step 2: Verify OTP and Reset Password
if (isset($_POST['reset_password'])) {
    // Get values
    $email = isset($_SESSION['reset_email']) ? $_SESSION['reset_email'] : '';
    $entered_otp = trim($_POST['otp']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $session_otp = isset($_SESSION['actual_otp']) ? $_SESSION['actual_otp'] : '';

    debug_log("Reset password attempt", [
        'email' => $email,
        'entered_otp' => $entered_otp,
        'session_otp' => $session_otp
    ]);

    // Check password match
    if ($new_password !== $confirm_password) {
        $message = "Passwords do not match.";
    }
    // Direct string comparison of entered OTP with session stored OTP
    else if ($entered_otp === $session_otp) {
        debug_log("OTP validation", "Success - OTP matches");

        // Hash the new password
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password directly
        $update_stmt = $conn->prepare("UPDATE employee SET password = ? WHERE email = ?");
        $update_stmt->bind_param("ss", $new_password_hash, $email);
        $update_stmt->execute();

        debug_log("Password update affected rows", $update_stmt->affected_rows);

        if ($update_stmt->affected_rows > 0) {
            // Clean up
            $delete_stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
            $delete_stmt->bind_param("s", $email);
            $delete_stmt->execute();

            // Set success flag - we'll show JavaScript alert at the end of the page
            $password_reset_success = true;

            // Clear only the reset-related session variables
            unset($_SESSION['actual_otp']);
            unset($_SESSION['reset_email']);
            unset($_SESSION['otp_created_time']);
            unset($_SESSION['show_otp_form']);
        } else {
            $message = "Password update failed. Please try again.";
            debug_log("Update error", $conn->error);
        }
    } else {
        $message = "Invalid OTP. Please try again.";
        debug_log("OTP validation", "Failed - OTP mismatch");
    }
}

// If user clicks "Back" in the reset form
if (isset($_GET['reset'])) {
    $_SESSION['show_otp_form'] = false;
    unset($_SESSION['actual_otp']);
    unset($_SESSION['reset_email']);
    unset($_SESSION['otp_created_time']);
    header("Location: forgot_password.php");
    exit;
}

// If user wants to request a new OTP
if (isset($_GET['resend']) && isset($_SESSION['reset_email'])) {
    $email = $_SESSION['reset_email'];

    // Generate a new OTP
    $otp = (string)rand(100000, 999999);
    $_SESSION['actual_otp'] = $otp;
    $_SESSION['otp_created_time'] = time();
    $_SESSION['show_otp_form'] = true; // Ensure OTP form is shown

    debug_log("Resending OTP", $otp);

    // Send new OTP via email
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'manira2061@gmail.com';
    $mail->Password = 'ntwv gage tsub fdrg';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('manira2061@gmail,com', 'HR Management System');
    $mail->addAddress($email);
    $mail->isHTML(true);

    $mail->Subject = 'New Password Reset OTP';
    $mail->Body    = "Your new OTP is: <strong>$otp</strong>";

    if ($mail->send()) {
        $message = "New OTP has been sent to your email.";
    } else {
        $message = "Failed to send new OTP. Please try again.";
    }
}

// Final session check before rendering
debug_log("Final session state", $_SESSION);
debug_log("show_otp_form value", isset($_SESSION['show_otp_form']) ? $_SESSION['show_otp_form'] : 'not set');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp&display=swap">
    <title>Forgot Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 350px;
            margin-bottom: 20px;
        }

        input[type="email"],
        input[type="text"],
        input[type="password"],
        button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            background: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background: #218838;
        }

        .message {
            color: #dc3545;
            text-align: center;
            margin-bottom: 15px;
            font-size: 14px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .back-link {
            text-align: center;
            margin-top: 10px;
        }

        .back-link a {
            color: #007bff;
            text-decoration: none;
            margin: 0 5px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .debug-box {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
            width: 100%;
            max-width: 800px;
            overflow-x: auto;
        }

        /* Navigation Section */
        #nav {
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 0;
            background-color: rgba(0, 115, 177, 255);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.06);
            height: 70px;
            z-index: 100;
        }

        /* Logo */
        .logo {
            background-color: white;
            padding: 5px;
            margin: 5px 0 5px 10px;
            height: calc(100% - 10px);
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Navbar List */
        #navbar {
            list-style: none;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            /* Align items to the right */
            margin: 0;
            padding-right: 20px;
            /* Space to the right */
        }

        /* Navbar list items */
        #navbar li {
            margin-left: 10px;
            /* Reduced the gap between items */
            display: flex;
            align-items: center;
            /* Vertically align items */
        }

        /* Navbar icon */
        #navbar li img {
            height: 30px;
            cursor: pointer;
        }

        /* Navbar links */
        #navbar li a {
            text-decoration: none;
            font-weight: bold;
            padding: 8px 15px;
            border-radius: 10px;
            color: white;
        }

        #navbar li a:hover {
            background-color: skyblue;
        }

        #navbar li a.active {
            background-color: rgb(163, 129, 226);
        }
    </style>
</head>

<body>
    <?php
    // Make sure we have a definitive condition for showing the OTP form
    $should_show_otp_form = isset($_SESSION['show_otp_form']) && $_SESSION['show_otp_form'] === true;
    debug_log("Should show OTP form", $should_show_otp_form);
    ?>
    <section id="nav">
        <img src="logo-img.png" alt="Logo" class="logo">
        <div>
            <ul id="navbar">
                <li>
                    <a href="homepage.html"><span class="material-symbols-sharp" style="color:white;">
                            home
                        </span>
                    </a>
                </li>
                <li><a class="active">User</a></li>
            </ul>
        </div>
    </section>

    <!-- Request OTP Form -->
    <?php if (!$should_show_otp_form): ?>
        <form method="POST" action="forgot_password.php">
            <h2>Forgot Password</h2>
            <?php if (!empty($message)): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit" name="request_otp">Request OTP</button>
            <div class="back-link">
                <a href="userlogin.php">Back to Login</a>
            </div>
        </form>
    <?php else: ?>
        <!-- Reset Password Form -->
        <form method="POST" action="forgot_password.php">
            <h2>Reset Password</h2>
            <?php if (!empty($message)): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
            <input type="text" name="otp" placeholder="Enter OTP" required>
            <input type="password" name="new_password" placeholder="Enter new password" required minlength="6">
            <input type="password" name="confirm_password" placeholder="Confirm new password" required minlength="6"
                oninput="if(this.value !== document.getElementsByName('new_password')[0].value) {this.setCustomValidity('Passwords do not match')} else {this.setCustomValidity('')}">
            <button type="submit" name="reset_password">Reset Password</button>
            <div class="back-link">
                <a href="forgot_password.php?resend=true">Resend OTP</a>
                <a href="forgot_password.php?reset=true">Back to Email Form</a>
            </div>
        </form>
    <?php endif; ?>

    <!-- Debug output - ONLY DISPLAY IN DEBUG MODE -->
    <?php if ($is_debug_mode && (!empty($debug_output) || isset($_SESSION['actual_otp']))): ?>
        <div class="debug-box">
            <h3>Debug Information (REMOVE IN PRODUCTION)</h3>
            <?php if (isset($_SESSION['actual_otp'])): ?>
                <p><strong>Current OTP in Session:</strong> <?php echo $_SESSION['actual_otp']; ?></p>
            <?php endif; ?>

            <?php if (!empty($debug_output)): ?>
                <div><?php echo $debug_output; ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- JavaScript for success alert -->
    <?php if ($password_reset_success): ?>
        <script>
            // Show alert and redirect when user clicks OK
            alert("Password has been reset successfully!");
            window.location.href = "userlogin.php";
        </script>
    <?php endif; ?>
</body>

</html>