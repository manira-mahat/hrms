<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $con = mysqli_connect('localhost', 'root', '', 'hrms');
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = $_POST['name'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $address = $_POST['address'] ?? '';
    $contact = $_POST['phone'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $email = $_POST['email'] ?? '';
    $department = $_POST['department'] ?? '';
    $job_position = $_POST['job_position'] ?? '';
    $qualification = $_POST['qualification'] ?? '';
    $join_date = $_POST['join_date'] ?? '';
    $profile_picture = '';

    // Check if the email already exists (for both insert and update)
    $stmt_check = $con->prepare("SELECT id FROM employee WHERE email = ? AND id != ?");
    $stmt_check->bind_param("si", $email, $id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        die("Error: Email already exists in the database.");
    }

    $stmt_check->close();

    // File Upload Logic
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $profile_picture = $uploadDir . basename($_FILES['profile_picture']['name']);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture);
    }

    // Insert or Update Logic
    if ($id == 0) {
        // Insert New Employee
        $stmt = $con->prepare("INSERT INTO employee (name, dob, address, contact, gender, email, department, job_position, qualification, join_date, profile_picture) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssss", $name, $dob, $address, $contact, $gender, $email, $department, $job_position, $qualification, $join_date, $profile_picture);
    } else {
        // Update Existing Employee
        $stmt = $con->prepare("UPDATE employee 
                               SET name=?, dob=?, address=?, contact=?, gender=?, email=?, department=?, job_position=?, qualification=?, join_date=?, profile_picture=?
                               WHERE id=?");
        $stmt->bind_param("sssssssssssi", $name, $dob, $address, $contact, $gender, $email, $department, $job_position, $qualification, $join_date, $profile_picture, $id);
    }

    // Execute Query
    if ($stmt->execute()) {
        header('Location: employeeDetails.php');
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    mysqli_close($con);
}
