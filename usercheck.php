<?php
session_start();
require_once 'config.php';

// Handle signup form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Check username availability
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if($stmt->get_result()->num_rows > 0) {
        $_SESSION['error'] = "Username already taken. Please choose another.";
        header("Location: usersignup.php");
        exit();
    }
    
    // Check if employee exists and doesn't have an account
    $stmt = $conn->prepare("SELECT e.id, e.email FROM employee e 
                           LEFT JOIN users u ON e.email = u.email 
                           WHERE e.name = ? AND e.email = ? AND u.id IS NULL");
    $stmt->bind_param("ss", $name, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Generate confirmation code
        $confirmation_code = sprintf("%06d", mt_rand(0, 999999));
        
        // Create user account
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, confirmation_code, is_confirmed, created_at) 
                               VALUES (?, ?, ?, ?, FALSE, NOW())");
        $stmt->bind_param("ssss", $username, $password, $email, $confirmation_code);
        
        if ($stmt->execute()) {
            // Send confirmation code via email
            $to = $email;
            $subject = "Account Verification Code";
            $message = "Your verification code is: " . $confirmation_code;
            $headers = "From: your-email@domain.com";

            mail($to, $subject, $message, $headers);
            
            $_SESSION['temp_email'] = $email;
            header("Location: userconfirm.php");
            exit();
        } else {
            $_SESSION['error'] = "Error creating account. Please try again.";
            header("Location: usersignup.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "No matching employee record found or account already exists";
        header("Location: usersignup.php");
        exit();
    }
}
?>