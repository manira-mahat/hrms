<?php
// Start session for any potential session variables
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "hrms");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$user_id = "";
$name = "";
$email = "";
$contact = "";
$gender = "";
$dob = "";
$address = "";
$department = "";
$job_position = "";
$qualification = "";
$join_date = "";
$profile_picture = "";
$cv = "";
$errorMsg = "";
$successMsg = "";
$formErrors = array(); // Array to store validation errors

// Check if user_id is provided in the URL
if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    
    // Get employee details from database
    $stmt = $conn->prepare("SELECT * FROM employee WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $email = $row['email'];
        $contact = $row['contact'];
        $gender = $row['gender'];
        $dob = $row['dob'];
        $address = $row['address'];
        $department = $row['department'];
        $job_position = $row['job_position'];
        $qualification = $row['qualification'];
        $join_date = $row['join_date'];
        $profile_picture = $row['profile_picture'];
        $cv = $row['cv'];
    } else {
        $errorMsg = "Employee not found";
    }
    $stmt->close();
} else {
    $errorMsg = "Invalid request";
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $department = $_POST['department'];
    $job_position = $_POST['job_position'];
    $qualification = $_POST['qualification'];
    $join_date = $_POST['join_date'];
    
    // Validate required fields
    if (empty($name)) {
        $formErrors['name'] = "Full Name is required";
    }
    
    if (empty($email)) {
        $formErrors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $formErrors['email'] = "Invalid email format";
    }
    
    if (empty($contact)) {
        $formErrors['contact'] = "Contact Number is required";
    }
    
    if (empty($gender)) {
        $formErrors['gender'] = "Gender selection is required";
    }
    
    if (empty($dob)) {
        $formErrors['dob'] = "Date of Birth is required";
    } else {
        // Validate that DOB makes employee at least 18 years old
        $dobDate = new DateTime($dob);
        $today = new DateTime();
        $age = $dobDate->diff($today)->y;
        
        if ($age < 18) {
            $formErrors['dob'] = "Employee must be at least 18 years old";
        }
    }
    
    if (empty($address)) {
        $formErrors['address'] = "Address is required";
    }
    
    if (empty($department)) {
        $formErrors['department'] = "Department selection is required";
    }
    
    if (empty($job_position)) {
        $formErrors['job_position'] = "Job Position is required";
    }
    
    if (empty($qualification)) {
        $formErrors['qualification'] = "Qualification is required";
    }
    
    if (empty($join_date)) {
        $formErrors['join_date'] = "Join Date is required";
    } elseif (!empty($dob)) {
        // Validate that join date is at least 18 years after DOB
        $dobDate = new DateTime($dob);
        $joinDate = new DateTime($join_date);
        $yearsSinceBirth = $dobDate->diff($joinDate)->y;
        
        if ($yearsSinceBirth < 18) {
            $formErrors['join_date'] = "Join date must be at least 18 years after date of birth";
        }
    }
    
    // Only proceed if there are no validation errors
    if (empty($formErrors)) {
        // Initialize update query
        $updateFields = [];
        $params = [];
        $paramTypes = "";
        
        // Basic fields
        $updateFields[] = "name = ?";
        $updateFields[] = "email = ?";
        $updateFields[] = "contact = ?";
        $updateFields[] = "gender = ?";
        $updateFields[] = "dob = ?";
        $updateFields[] = "address = ?";
        $updateFields[] = "department = ?";
        $updateFields[] = "job_position = ?";
        $updateFields[] = "qualification = ?";
        $updateFields[] = "join_date = ?";
        
        $params[] = $name;
        $params[] = $email;
        $params[] = $contact;
        $params[] = $gender;
        $params[] = $dob;
        $params[] = $address;
        $params[] = $department;
        $params[] = $job_position;
        $params[] = $qualification;
        $params[] = $join_date;
        $paramTypes .= "ssssssssss"; // 10 string parameters
        
        // Handle profile picture upload
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['size'] > 0 && $_FILES['profile_picture']['error'] == 0) {
            $target_dir = "uploads/";
            
            // Make sure the upload directory exists
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION);
            $new_file_name = "profile_" . $user_id . "_" . time() . "." . $file_extension;
            $target_file = $target_dir . $new_file_name;
            
            // Check if file is an actual image
            $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
            if ($check !== false) {
                // Upload file
                if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                    // Add profile picture to update fields
                    $updateFields[] = "profile_picture = ?";
                    $params[] = $target_file;
                    $paramTypes .= "s";
                } else {
                    $errorMsg = "Sorry, there was an error uploading your profile picture. Error: " . $_FILES['profile_picture']['error'];
                }
            } else {
                $errorMsg = "File is not an image.";
            }
        }
        
        // Handle CV upload
        if (isset($_FILES['cv']) && $_FILES['cv']['size'] > 0 && $_FILES['cv']['error'] == 0) {
            $target_dir = "uploads/";
            
            // Make sure the upload directory exists
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES["cv"]["name"], PATHINFO_EXTENSION);
            $new_file_name = "cv_" . $user_id . "_" . time() . "." . $file_extension;
            $target_file = $target_dir . $new_file_name;
            
            // Check if file is a valid document
            $allowed_extensions = array("pdf", "doc", "docx");
            if (in_array(strtolower($file_extension), $allowed_extensions)) {
                // Upload file
                if (move_uploaded_file($_FILES["cv"]["tmp_name"], $target_file)) {
                    // Add CV to update fields
                    $updateFields[] = "cv = ?";
                    $params[] = $target_file;
                    $paramTypes .= "s";
                } else {
                    $errorMsg = "Sorry, there was an error uploading your CV. Error: " . $_FILES['cv']['error'];
                }
            } else {
                $errorMsg = "Invalid file format for CV. Only PDF, DOC, and DOCX are allowed.";
            }
        }
        
        // If no errors, proceed with the update
        if (empty($errorMsg)) {
            // Prepare the final query
            $sql = "UPDATE employee SET " . implode(", ", $updateFields) . " WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            
            // Add user_id to params array and types
            $params[] = $user_id;
            $paramTypes .= "i";
            
            // Dynamically bind all parameters
            $stmt->bind_param($paramTypes, ...$params);
            
            // Execute the query
            if ($stmt->execute()) {
                $successMsg = "Employee details updated successfully";
                
                // Refresh employee data after update
                $stmt = $conn->prepare("SELECT * FROM employee WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows == 1) {
                    $row = $result->fetch_assoc();
                    $name = $row['name'];
                    $email = $row['email'];
                    $contact = $row['contact'];
                    $gender = $row['gender'];
                    $dob = $row['dob'];
                    $address = $row['address'];
                    $department = $row['department'];
                    $job_position = $row['job_position'];
                    $qualification = $row['qualification'];
                    $join_date = $row['join_date'];
                    $profile_picture = $row['profile_picture'];
                    $cv = $row['cv'];
                }
            } else {
                $errorMsg = "Error updating employee details: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $errorMsg = "Please fix the errors in the form";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp">
    <title>Edit Employee</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        
        .form-container {
            background-color: #fff;
            margin: 20px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .form-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }
        
        .form-col {
            flex: 1 0 250px;
            padding: 0 10px;
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="date"],
        input[type="tel"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .btn-container {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }
        
        .btn-primary {
            background-color: #04AA6D;
            color: white;
        }
        
        .btn-secondary {
            background-color: #f44336;
            color: white;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .current-files {
            margin-top: 10px;
            font-size: 14px;
            color: #666;
        }
        
        .profile-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-top: 10px;
            border: 1px solid #ddd;
        }
        
        .error-text {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }
        
        .input-error {
            border: 1px solid #dc3545 !important;
        }
        
        header h1 {
            font-size: 2rem;
            margin-left: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'sidebar.php'; ?>

        <script>
            document.querySelector('a[href="employeeDetails.php"]').classList.add('active-page');
        </script>

        <main>
            <header>
                <h1>Edit Employee</h1>
                <a href="employeeDetails.php" class="btn btn-secondary" style="display: inline-block; text-decoration: none; margin-right: 20px;">
                    Back to Employees
                </a>
            </header>
            
            <div class="form-container">
                <?php if (!empty($errorMsg)): ?>
                    <div class="alert alert-danger"><?php echo $errorMsg; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($successMsg)): ?>
                    <div class="alert alert-success"><?php echo $successMsg; ?></div>
                <?php endif; ?>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?user_id=" . $user_id; ?>" method="post" enctype="multipart/form-data" novalidate>
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                    
                    <div class="form-title">Personal Information</div>
                    
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="name">Full Name *</label>
                                <input type="text" id="name" name="name" value="<?php echo $name; ?>" class="<?php echo isset($formErrors['name']) ? 'input-error' : ''; ?>" required>
                                <?php if (isset($formErrors['name'])): ?>
                                    <div class="error-text"><?php echo $formErrors['name']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="form-col">
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" value="<?php echo $email; ?>" class="<?php echo isset($formErrors['email']) ? 'input-error' : ''; ?>" required>
                                <?php if (isset($formErrors['email'])): ?>
                                    <div class="error-text"><?php echo $formErrors['email']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="form-col">
                            <div class="form-group">
                                <label for="contact">Contact Number *</label>
                                <input type="tel" id="contact" name="contact" value="<?php echo $contact; ?>" class="<?php echo isset($formErrors['contact']) ? 'input-error' : ''; ?>" required>
                                <?php if (isset($formErrors['contact'])): ?>
                                    <div class="error-text"><?php echo $formErrors['contact']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="gender">Gender *</label>
                                <select id="gender" name="gender" class="<?php echo isset($formErrors['gender']) ? 'input-error' : ''; ?>" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?php if ($gender == "Male") echo "selected"; ?>>Male</option>
                                    <option value="Female" <?php if ($gender == "Female") echo "selected"; ?>>Female</option>
                                    <option value="Other" <?php if ($gender == "Other") echo "selected"; ?>>Other</option>
                                </select>
                                <?php if (isset($formErrors['gender'])): ?>
                                    <div class="error-text"><?php echo $formErrors['gender']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="form-col">
                            <div class="form-group">
                                <label for="dob">Date of Birth * (Employee must be at least 18 years old)</label>
                                <input type="date" id="dob" name="dob" value="<?php echo $dob; ?>" class="<?php echo isset($formErrors['dob']) ? 'input-error' : ''; ?>" required>
                                <?php if (isset($formErrors['dob'])): ?>
                                    <div class="error-text"><?php echo $formErrors['dob']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address *</label>
                        <textarea id="address" name="address" class="<?php echo isset($formErrors['address']) ? 'input-error' : ''; ?>" required><?php echo $address; ?></textarea>
                        <?php if (isset($formErrors['address'])): ?>
                            <div class="error-text"><?php echo $formErrors['address']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-title">Employment Information</div>
                    
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="department">Department *</label>
                                <select id="department" name="department" class="<?php echo isset($formErrors['department']) ? 'input-error' : ''; ?>" required>
                                    <option value="">Select Department</option>
                                    <option value="HR" <?php if ($department == "HR") echo "selected"; ?>>HR</option>
                                    <option value="IT" <?php if ($department == "IT") echo "selected"; ?>>IT</option>
                                    <option value="Finance" <?php if ($department == "Finance") echo "selected"; ?>>Finance</option>
                                    <option value="Marketing" <?php if ($department == "Marketing") echo "selected"; ?>>Marketing</option>
                                    <option value="Operations" <?php if ($department == "Operations") echo "selected"; ?>>Operations</option>
                                    <option value="Sales" <?php if ($department == "Sales") echo "selected"; ?>>Sales</option>
                                </select>
                                <?php if (isset($formErrors['department'])): ?>
                                    <div class="error-text"><?php echo $formErrors['department']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="form-col">
                            <div class="form-group">
                                <label for="job_position">Job Position *</label>
                                <input type="text" id="job_position" name="job_position" value="<?php echo $job_position; ?>" class="<?php echo isset($formErrors['job_position']) ? 'input-error' : ''; ?>" required>
                                <?php if (isset($formErrors['job_position'])): ?>
                                    <div class="error-text"><?php echo $formErrors['job_position']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="form-col">
                            <div class="form-group">
                                <label for="join_date">Join Date * (Must be at least 18 years after DOB)</label>
                                <input type="date" id="join_date" name="join_date" value="<?php echo $join_date; ?>" class="<?php echo isset($formErrors['join_date']) ? 'input-error' : ''; ?>" required>
                                <?php if (isset($formErrors['join_date'])): ?>
                                    <div class="error-text"><?php echo $formErrors['join_date']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="qualification">Qualification *</label>
                        <input type="text" id="qualification" name="qualification" value="<?php echo $qualification; ?>" class="<?php echo isset($formErrors['qualification']) ? 'input-error' : ''; ?>" required>
                        <?php if (isset($formErrors['qualification'])): ?>
                            <div class="error-text"><?php echo $formErrors['qualification']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-title">Documents</div>
                    
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="profile_picture">Profile Picture</label>
                                <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                                <p style="font-size: 12px; color: #666;">Leave empty to keep current picture</p>
                                <?php if (!empty($profile_picture)): ?>
                                    <div class="current-files">
                                        <p>Current profile picture:</p>
                                        <img src="<?php echo $profile_picture; ?>" alt="Profile" class="profile-preview">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="form-col">
                            <div class="form-group">
                                <label for="cv">CV/Resume</label>
                                <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx">
                                <p style="font-size: 12px; color: #666;">Leave empty to keep current CV</p>
                                <?php if (!empty($cv)): ?>
                                    <div class="current-files">
                                        <p>Current CV: <a href="<?php echo $cv; ?>" target="_blank"><?php echo basename($cv); ?></a></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="btn-container">
                        <a href="employeeDetails.php" class="btn btn-secondary" style="text-decoration: none;">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Employee</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Client-side form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = document.querySelectorAll('input[required], select[required], textarea[required]');
            
            requiredFields.forEach(field => {
                // Remove existing error styling
                field.classList.remove('input-error');
                const errorDiv = field.nextElementSibling;
                if (errorDiv && errorDiv.classList.contains('error-text')) {
                    errorDiv.remove();
                }
                
                // Check if field is empty
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('input-error');
                    
                    // Create error message
                    const errorMessage = document.createElement('div');
                    errorMessage.className = 'error-text';
                    errorMessage.textContent = 'This field is required';
                    
                    // Insert error message after the field
                    field.parentNode.insertBefore(errorMessage, field.nextSibling);
                }
                
                // Validate email format
                if (field.type === 'email' && field.value.trim()) {
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailPattern.test(field.value)) {
                        isValid = false;
                        field.classList.add('input-error');
                        
                        // Create error message
                        const errorMessage = document.createElement('div');
                        errorMessage.className = 'error-text';
                        errorMessage.textContent = 'Please enter a valid email address';
                        
                        // Insert error message after the field
                        field.parentNode.insertBefore(errorMessage, field.nextSibling);
                    }
                }
            });
            
            // Additional validation for DOB and Join Date
            const dobInput = document.getElementById('dob');
            const joinDateInput = document.getElementById('join_date');
            
            if (dobInput.value && joinDateInput.value) {
                const dob = new Date(dobInput.value);
                const joinDate = new Date(joinDateInput.value);
                const today = new Date();
                
                // Check if employee is at least 18 years old
                const eighteenYearsAgo = new Date();
                eighteenYearsAgo.setFullYear(today.getFullYear() - 18);
                
                if (dob > eighteenYearsAgo) {
                    isValid = false;
                    dobInput.classList.add('input-error');
                    
                    // Create error message
                    const dobErrorDiv = dobInput.nextElementSibling;
                    if (dobErrorDiv && dobErrorDiv.classList.contains('error-text')) {
                        dobErrorDiv.textContent = 'Employee must be at least 18 years old';
                    } else {
                        const errorMessage = document.createElement('div');
                        errorMessage.className = 'error-text';
                        errorMessage.textContent = 'Employee must be at least 18 years old';
                        dobInput.parentNode.insertBefore(errorMessage, dobInput.nextSibling);
                    }
                }
                
                // Check if join date is at least 18 years after DOB
                const minJoinDate = new Date(dob);
                minJoinDate.setFullYear(dob.getFullYear() + 18);
                
                if (joinDate < minJoinDate) {
                    isValid = false;
                    joinDateInput.classList.add('input-error');
                    
                    // Create error message
                    const joinDateErrorDiv = joinDateInput.nextElementSibling;
                    if (joinDateErrorDiv && joinDateErrorDiv.classList.contains('error-text')) {
                        joinDateErrorDiv.textContent = 'Join date must be at least 18 years after date of birth';
                    } else {
                        const errorMessage = document.createElement('div');
                        errorMessage.className = 'error-text';
                        errorMessage.textContent = 'Join date must be at least 18 years after date of birth';
                        joinDateInput.parentNode.insertBefore(errorMessage, joinDateInput.nextSibling);
                    }
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                
                // Show error alert at the top of the form
                const formContainer = document.querySelector('.form-container');
                const existingAlert = document.querySelector('.alert-danger');
                
                if (existingAlert) {
                    existingAlert.textContent = 'Please fill in all required fields correctly';
                } else {
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger';
                    alertDiv.textContent = 'Please fill in all required fields correctly';
                    formContainer.insertBefore(alertDiv, formContainer.firstChild);
                }
                
                // Scroll to the top of the form
                window.scrollTo({
                    top: formContainer.offsetTop - 20,
                    behavior: 'smooth'
                });
            }
        });
    </script>
</body>
</html>