<?php
// Database Connection
$conn = mysqli_connect('localhost', 'root', '', 'hrms');

// Initialize Variables
$id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
$name = "";
$email = "";
$phone = "";
$department = "";
$job_position = "";
$qualification = "";
$join_date = "";
$address = "";
$gender = "";

if ($id != 0) {
    $sql = "SELECT id, name, email, contact AS phone, department, job_position, qualification, join_date, address, gender FROM employee WHERE id='$id'";
    $results = $conn->query($sql);

    if ($results && $results->num_rows > 0) {
        $data = mysqli_fetch_assoc($results);

        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $phone = $data['phone'] ?? '';
        $department = $data['department'] ?? '';
        $job_position = $data['job_position'] ?? '';
        $qualification = $data['qualification'] ?? '';
        $join_date = $data['join_date'] ?? '';
        $address = $data['address'] ?? '';
        $gender = $data['gender'] ?? '';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">
    <title>Add Employee</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background: #f4f4f4;
            min-height: 100vh;
        }

        header {
            display: flex;
            justify-content: center;
            align-items: center;
            padding-bottom: 15px;
            border-bottom: 1px solid #cfbebe;
            background-color: #ecf0f1;
            padding: 20px 0;
            width: 80vw;
            box-sizing: border-box;
            position: relative;
            margin: 0;
        }

        header h1 {
            font-size: 2rem;
            text-align: center;
            margin: 0;
        }

        /* Form Styles */
        .form-container {
            margin-left: 180px;
            background: #fff;
            /* Set the background color to white */
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            /* Added box shadow */
            max-width: 900px;
            /* Increased from original */
            width: 100%;
            /* Changed to ensure full width */
            margin-right: auto;
            margin-top: 20px;
        }


        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e1e8;
            border-radius: 5px;
            font-size: 16px;
          
            transition: all 0.3s ease;
        }

        .form-group input:hover,
        .form-group select:hover,
        .form-group textarea:hover {
            background-color: #fff;
            border-color: #7380ec;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            background-color: #fff;
            border-color: #7380ec;
            box-shadow: 0 0 5px rgba(115, 128, 236, 0.3);
            outline: none;
        }

        .form-container h2 {
            margin-bottom: 30px;
            font-size: 24px;
            text-align: center;
            color: #111e88;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }

        .form-group textarea {
            resize: vertical;
        }

        .form-actions {
            text-align: center;
        }

        .form-actions button {
            background: rgb(16, 155, 39);
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 10px;
            transition: all 0.3s ease;
        }

        .form-actions button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <script>
        function validateForm() {
            var name = document.getElementById("name").value;
            var email = document.getElementById("email").value;
            var dob = document.getElementById("dob").value;
            var gender = document.getElementById("gender").value;
            var phone = document.getElementById("phone").value;
            var department = document.getElementById("department").value;
            var jobPosition = document.getElementById("job_position").value;
            var qualification =document.getElementById("qualification").value;
            var joinDate = document.getElementById("join_date").value;
            var address = document.getElementById("address").value;

            var isValid = true;

            // Clear previous validation messages
            document.getElementById("nameValidation").innerText = "";
            document.getElementById("emailValidation").innerText = "";
            document.getElementById("dobValidation").innerText = "";
            document.getElementById("genderValidation").innerText = "";
            document.getElementById("phoneValidation").innerText = "";
            document.getElementById("departmentValidation").innerText = "";
            document.getElementById("positionValidation").innerText = "";
            document.getElementById("qualificationValidation").innerText = "";
            document.getElementById("dateValidation").innerText = "";
            document.getElementById("addressValidation").innerText = "";
        
            // Validate Full Name
            if (name === "") {
                document.getElementById("nameValidation").innerText = "Full name is required.";
                isValid = false;
            }

            // Validate Email
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                document.getElementById("emailValidation").innerText = "Enter a valid email address.";
                isValid = false;
            }

            // Validate Phone Number
            const phoneRegex = /^[0-9]{10}$/; // Adjust regex as needed
            if (!phoneRegex.test(phone)) {
                document.getElementById("phoneValidation").innerText = "Enter a valid 10-digit phone number.";
                isValid = false;
            }

            // Validate Department
            if (department === "") {
                document.getElementById("departmentValidation").innerText = "Please select a department.";
                isValid = false;
            }

            // Validate Job Position
           
            if (jobPosition === "") {
                document.getElementById("positionValidation").innerText = "Job position is required.";
                isValid = false;
            }

            // Validate academic Qualification
           
            if (Qualification === "") {
                document.getElementById("qualificationValidation").innerText = "Academic qualification is required.";
                isValid = false;
            }

            // Validate Joining Date
           
            if (joinDate === "") {
                document.getElementById("dateValidation").innerText = "Joining date is required.";
                isValid = false;
            }

            // Validate Address
            if (address === "") {
                document.getElementById("addressValidation").innerText = "Address is required.";
                isValid = false;
            }

            // Validate Profile Picture
            var profilePicture = document.getElementById("profile_picture").files[0];
            if (!profilePicture) {
                document.getElementById("imageValidation").innerText = "Please upload a profile picture.";
                isValid = false;
            } else {
                const validExtensions = ["image/jpeg", "image/png", "image/jpg"];
                if (!validExtensions.includes(profilePicture.type)) {
                    document.getElementById("imageValidation").innerText = "Only JPG, JPEG, and PNG files are allowed.";
                    isValid = false;
                } else if (profilePicture.size > 2 * 1024 * 1024) {
                    document.getElementById("imageValidation").innerText = "File size should not exceed 2MB.";
                    isValid = false;
                }
            }

            return isValid;
        }
    </script>

    <div class="container">
        <?php include 'sidebar.php'; ?>
        <script>
            document.querySelector('a[href="addemployee.php"]').classList.add('active-page');
        </script>

        <main>
            <header>
                <h1>Employee Form</h1>
            </header>
            <div class="form-container">
                <form action="employee_controller.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm();">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">

                    <!-- Full Name -->
                    <div class="form-group">
                        <label for="name"><b>Full Name</b></label>
                        <input type="text" id="name" name="name" value="<?php echo $name; ?>" placeholder="Enter full name" >
                        <span id="nameValidation" style="color:red;"></span>
                    </div>

                    <!-- Email Address -->
                    <div class="form-group">
                        <label for="email"><b>Email Address</b></label>
                        <input type="email" id="email" name="email" value="<?php echo $email; ?>" placeholder="Enter email">
                        <span id="emailValidation" style="color:red;"></span>
                    </div>

                    <!-- Date of Birth -->
                    <div class="form-group">
                        <label for="dob"><b>Date of Birth</b></label>
                        <input type="date" id="dob" name="dob">
                        <span id="dobValidation" style="color:red;"></span>
                    </div>

                    <!-- Gender Field -->
                    <div class="form-group">
                        <label for="gender"><b>Gender</b></label>
                        <select id="gender" name="gender">
                            <option value="">Select Gender</option>
                            <option value="Male" <?php echo $gender == "Male" ? "selected" : ""; ?>>Male</option>
                            <option value="Female" <?php echo $gender == "Female" ? "selected" : ""; ?>>Female</option>
                            <option value="Other" <?php echo $gender == "Other" ? "selected" : ""; ?>>Other</option>
                        </select>
                        <span id="genderValidation" style="color:red;"></span>
                    </div>

                    <!-- Phone Number -->
                    <div class="form-group">
                        <label for="phone"><b>Phone Number</b></label>
                        <input type="tel" id="phone" name="phone" value="<?php echo $phone; ?>" placeholder="Enter phone number" >
                        <span id="phoneValidation" style="color:red;"></span>
                    </div>

                    <!-- Department -->
                    <div class="form-group">
                        <label for="department"><b>Department</b></label>
                        <select id="department" name="department" >
                            <option value="">Select Department</option>
                            <option value="HR" <?php echo $department == "HR" ? "selected" : ""; ?>>Human Resources</option>
                            <option value="IT" <?php echo $department == "IT" ? "selected" : ""; ?>>Information Technology</option>
                            <option value="Finance" <?php echo $department == "Finance" ? "selected" : ""; ?>>Finance</option>
                            <option value="Marketing" <?php echo $department == "Marketing" ? "selected" : ""; ?>>Marketing</option>
                            <option value="Operations" <?php echo $department == "Operations" ? "selected" : ""; ?>>Operations</option>
                        </select>
                        <span id="departmentValidation" style="color:red;"></span>
                    </div>

                    <!-- Job Position -->
                    <div class="form-group">
                        <label for="jobPosition"><b>Job Position</b></label>
                        <input type="text" id="jobPosition" name="jobPosition" value="<?php echo $job_position; ?>" placeholder="Enter job position" >
                        <span id="positionValidation" style="color:red;"></span>
                    </div>

                    <!-- Job Qualification -->
                    <div class="form-group">
                        <label for="qualification"><b>Academic Qualification</b></label>
                        <input type="text" id="qualification" name="qualification" value="<?php echo $qualification; ?>" placeholder="Enter academic qualification" >
                        <span id="qualificationValidation" style="color:red;"></span>
                    </div>

                    <!-- Joining Date -->
                    <div class="form-group">
                        <label for="joinDate"><b>Joining Date</b></label>
                        <input type="date" id="joinDate" name="joinDate" value="<?php echo $join_date; ?>" >
                        <span id="dateValidation" style="color:red;"></span>
                    </div>

                    <!-- Address -->
                    <div class="form-group">
                        <label for="address"><b>Address</b></label>
                        <textarea id="address" name="address" rows="3" placeholder="Enter address"><?php echo $address; ?></textarea>
                        <span id="addressValidation" style="color:red;"></span>
                    </div>

                    <!-- Profile Picture -->
                    <div class="form-group">
                        <label for="profilePicture"><b>Profile Picture</b></label>
                        <input type="file" id="profilePicture" name="profilePicture" accept="image/*">
                        <span id="imageValidation" style="color:red;"></span>
                    </div>

                    <!-- Submit and Reset -->
                    <div class="form-actions">
                        <button type="submit"><b>Add Employee</b></button>
                    </div>
                </form>
            </div>
        </main>
    </div>

</body>

</html>