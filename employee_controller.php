<?php
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Database connection with error handling
        $con = mysqli_connect('localhost', 'root', '', 'hrms');
        if (!$con) {
            throw new Exception("Database connection failed: " . mysqli_connect_error());
        }

        // Initialize variables with better sanitization
        $user_id = isset($_POST['user_id']) ? filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
        $name = htmlspecialchars(trim($_POST['name'] ?? ''));
        $dob = htmlspecialchars(trim($_POST['dob'] ?? ''));
        $address = htmlspecialchars(trim($_POST['address'] ?? ''));
        $contact = htmlspecialchars(trim($_POST['contact'] ?? ''));
        $gender = htmlspecialchars(trim($_POST['gender'] ?? ''));
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $department = htmlspecialchars(trim($_POST['department'] ?? ''));
        $job_position = htmlspecialchars(trim($_POST['job_position'] ?? ''));
        $qualification = htmlspecialchars(trim($_POST['qualification'] ?? ''));
        $join_date = htmlspecialchars(trim($_POST['join_date'] ?? ''));
        $username = htmlspecialchars(trim($_POST['username'] ?? ''));
        $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT); // Hash password

        // Validate required fields
        $required_fields = ['name', 'email', 'contact', 'department', 'job_position'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("$field is required");
            }
        }

        // File upload directories with proper permissions
        $uploadDir = 'uploads/';
        $cvDir = 'uploads/cv/';

        // Create directories if they don't exist
        foreach ([$uploadDir, $cvDir] as $dir) {
            if (!file_exists($dir)) {
                if (!mkdir($dir, 0755, true)) {
                    throw new Exception("Failed to create directory: $dir");
                }
            }
        }

        // Handle Profile Picture Upload with validation
        $profile_picture = "uploads/default.png";
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            $file_type = $_FILES['profile_picture']['type'];
            
            if (!in_array($file_type, $allowed_types)) {
                throw new Exception("Invalid file type for profile picture");
            }

            $max_size = 2 * 1024 * 1024; // 2MB
            if ($_FILES['profile_picture']['size'] > $max_size) {
                throw new Exception("Profile picture size exceeds 2MB limit");
            }

            $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $profile_picture = $uploadDir . uniqid('profile_', true) . '.' . $ext;
            
            if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture)) {
                throw new Exception("Failed to upload profile picture");
            }
        }

        // Handle CV Upload with validation
        $cv = null;
        if (isset($_FILES['cv']) && $_FILES['cv']['error'] == 0) {
            if ($_FILES['cv']['type'] !== 'application/pdf') {
                throw new Exception("Only PDF files are allowed for CV");
            }

            $max_size = 2 * 1024 * 1024; // 2MB
            if ($_FILES['cv']['size'] > $max_size) {
                throw new Exception("CV file size exceeds 2MB limit");
            }

            $cv = $cvDir . uniqid('cv_', true) . '.pdf';
            
            if (!move_uploaded_file($_FILES['cv']['tmp_name'], $cv)) {
                throw new Exception("Failed to upload CV");
            }
        }

        // Prepare and execute database query
        if ($user_id == 0) {
            $sql = "INSERT INTO employee (name, dob, address, contact, gender, email, department, 
                    job_position, qualification, join_date, profile_picture, cv, username, password) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("ssssssssssssss", $name, $dob, $address, $contact, $gender, $email, 
                            $department, $job_position, $qualification, $join_date, $profile_picture, 
                            $cv, $username, $password);
        } else {
            $sql = "UPDATE employee SET name=?, dob=?, address=?, contact=?, gender=?, email=?, 
                    department=?, job_position=?, qualification=?, join_date=?, username=?, 
                    password=?, profile_picture=?, cv=? WHERE user_id=?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("ssssssssssssssi", $name, $dob, $address, $contact, $gender, $email, 
                            $department, $job_position, $qualification, $join_date, $username, 
                            $password, $profile_picture, $cv, $user_id);
        }

        if (!$stmt->execute()) {
            throw new Exception("Database operation failed: " . $stmt->error);
        }

        // Send email with credentials
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'manira2061@gmail.com';
        $mail->Password = 'ntwv gage tsub fdrg';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('manira2061@gmail.com', 'HR Management System');
        $mail->addAddress($email, $name);
        $mail->isHTML(true);
        $mail->Subject = 'Your HRMS Account Details';
        
        // HTML email template
        $mail->Body = "
            <html>
            <body style='font-family: Arial, sans-serif; line-height: 1.6;'>
                <h2>Welcome to HR Management System</h2>
                <p>Dear $name,</p>
                <p>Your account has been created successfully. Please find your login credentials below:</p>
                <p><strong>Username:</strong> $username</p>
                <p><strong>Password:</strong> {$_POST['password']}</p>
                <p>Best regards,<br>HR Management System Team</p>
            </body>
            </html>
        ";

        $mail->send();

        // Success response
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "success",
            "message" => "Employee added successfully and credentials sent to $email"
        ]);

    } catch (Exception $e) {
        // Error handling
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    } finally {
        // Clean up
        if (isset($stmt)) $stmt->close();
        if (isset($con)) mysqli_close($con);
    }
}
?>