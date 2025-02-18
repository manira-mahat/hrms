<?php
// Load PHPMailer classes manually
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['Name'];
    $address = $_POST['Address'];
    $gender = $_POST['Gender'];
    $contact = $_POST['Contact'];
    $dob = $_POST['dob'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Database connection
    $connection = mysqli_connect('localhost', 'root', '', 'hrms');

    if (!$connection) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Check if email and name exist in the employee table
    $checkEmployeeSql = "SELECT * FROM employee WHERE email = '$email' AND name = '$name'";
    $employeeResult = mysqli_query($connection, $checkEmployeeSql);

    if (mysqli_num_rows($employeeResult) > 0) {
        // Employee exists, proceed with OTP generation and sending

        // Generate a 6-digit OTP
        $otp = rand(100000, 999999);

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert user details into the database with is_verified = 0 (pending verification) and store the OTP
        $sql = "INSERT INTO usersignup (name, address, gender, contact, dob, username, email, password, otp, otp_sent_time, is_verified) 
                VALUES ('$name', '$address', '$gender', '$contact', '$dob', '$username', '$email', '$hashedPassword', '$otp', NOW(), 0)";

        if (mysqli_query($connection, $sql)) {
            // Send OTP to user's email using PHPMailer
            $mail = new PHPMailer(true);

            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';  // Gmail SMTP server
                $mail->SMTPAuth = true;
                $mail->Username   = 'manira2061@gmail.com';                // Your Gmail address
                $mail->Password   =  'ntwv gage tsub fdrg'; 
                // App password if 2FA enabled
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // TLS encryption
                $mail->Port = 587;  // Port 587 for TLS

                // Recipients
                $mail->setFrom('manira2061@gmail.com', 'manira');
                $mail->addAddress($email, $name);   // Send OTP to the user's email

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Verify Your Account';
                $mail->Body    = "Hello $name,\n\nYour OTP for account verification is: $otp\n\nPlease enter this code to verify your account.";

                $mail->send();
                  // Redirect to login page after OTP is sent
                  header("Location: verify_otp.php?message=OTP_sent");
                  exit();  // Make sure the script stops after redirection
              } catch (Exception $e) {
                  echo "Signup successful, but we couldn't send the OTP. Mailer Error: {$mail->ErrorInfo}";
              }
          } else {
              echo "Error: " . mysqli_error($connection);
          }
      } else {
          // If the email and name are not found in the employee table
          echo "You are not eligible for OTP verification. Please check your details.";
      }
  
      mysqli_close($connection);
  }
  ?>