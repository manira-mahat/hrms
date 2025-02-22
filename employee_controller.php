<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection
    $con = mysqli_connect('localhost', 'root', '', 'hrms');
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Initialize variables with proper sanitization
    $id = isset($_POST['id']) ? filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $name = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_STRING);
    $dob = filter_var($_POST['dob'] ?? '', FILTER_SANITIZE_STRING);
    $address = filter_var($_POST['address'] ?? '', FILTER_SANITIZE_STRING);
    $contact = filter_var($_POST['phone'] ?? '', FILTER_SANITIZE_STRING);
    $gender = filter_var($_POST['gender'] ?? '', FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $department = filter_var($_POST['department'] ?? '', FILTER_SANITIZE_STRING);
    $job_position = filter_var($_POST['job_position'] ?? '', FILTER_SANITIZE_STRING);
    $qualification = filter_var($_POST['qualification'] ?? '', FILTER_SANITIZE_STRING);
    $join_date = filter_var($_POST['join_date'] ?? '', FILTER_SANITIZE_STRING);

    // Get existing file paths if updating
    $profile_picture = '';
    $cv = '';
    if ($id > 0) {
        $stmt = $con->prepare("SELECT profile_picture, cv FROM employee WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $profile_picture = $row['profile_picture'];
            $cv = $row['cv'];
        }
        $stmt->close();
    }

    // Validate email uniqueness
    $stmt_check = $con->prepare("SELECT id FROM employee WHERE email = ? AND id != ?");
    $stmt_check->bind_param("si", $email, $id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        die("Error: Email already exists in the database.");
    }
    $stmt_check->close();

    // File upload directories
    $uploadDir = 'uploads/';
    $cvDir = 'uploads/cv/';
    
    // Create directories if they don't exist
    foreach ([$uploadDir, $cvDir] as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $uploadedFileType = finfo_file($fileInfo, $_FILES['profile_picture']['tmp_name']);
        finfo_close($fileInfo);

        if (in_array($uploadedFileType, $allowedTypes)) {
            // Generate safe filename
            $fileExtension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $newFileName = uniqid('profile_', true) . '.' . $fileExtension;
            $profile_picture = $uploadDir . $newFileName;

            // Delete old profile picture if exists
            if (!empty($profile_picture) && file_exists($profile_picture) && $profile_picture != 'uploads/default.png') {
                unlink($profile_picture);
            }

            if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture)) {
                die("Error: Failed to upload profile picture.");
            }
        } else {
            die("Error: Invalid file type for profile picture. Only JPEG, PNG, and GIF are allowed.");
        }
    }

    // Handle CV upload
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] == 0) {
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $uploadedFileType = finfo_file($fileInfo, $_FILES['cv']['tmp_name']);
        finfo_close($fileInfo);

        if ($uploadedFileType === 'application/pdf') {
            // Generate safe filename
            $newFileName = uniqid('cv_', true) . '.pdf';
            $cv = $cvDir . $newFileName;

            // Delete old CV if exists
            if (!empty($cv) && file_exists($cv)) {
                unlink($cv);
            }

            if (!move_uploaded_file($_FILES['cv']['tmp_name'], $cv)) {
                die("Error: Failed to upload CV file.");
            }
        } else {
            die("Error: Invalid file type for CV. Only PDF files are allowed.");
        }
    }

    // Prepare SQL statement based on operation type (insert/update)
    if ($id == 0) {
        // Insert new employee
        $sql = "INSERT INTO employee (name, dob, address, contact, gender, email, department, 
                job_position, qualification, join_date, profile_picture, cv) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $types = "ssssssssssss";
        $params = [$name, $dob, $address, $contact, $gender, $email, $department, 
                   $job_position, $qualification, $join_date, 
                   $profile_picture ?: 'uploads/default.png', 
                   $cv ?: null];
    } else {
        // Update existing employee
        $sql = "UPDATE employee SET 
                name=?, dob=?, address=?, contact=?, gender=?, email=?, 
                department=?, job_position=?, qualification=?, join_date=?";
        $types = "ssssssssss";
        $params = [$name, $dob, $address, $contact, $gender, $email, $department, 
                   $job_position, $qualification, $join_date];

        // Add profile picture and CV to update if they were uploaded
        if (!empty($profile_picture)) {
            $sql .= ", profile_picture=?";
            $types .= "s";
            $params[] = $profile_picture;
        }
        if (!empty($cv)) {
            $sql .= ", cv=?";
            $types .= "s";
            $params[] = $cv;
        }
        
        $sql .= " WHERE id=?";
        $types .= "i";
        $params[] = $id;
    }

    // Execute query with error handling
    try {
        $stmt = $con->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Failed to prepare statement: " . $con->error);
        }

        $stmt->bind_param($types, ...$params);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to execute statement: " . $stmt->error);
        }

        // Redirect on success
        header('Location: employeeDetails.php');
        exit;
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
        mysqli_close($con);
    }
}
?>