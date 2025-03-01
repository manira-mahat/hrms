<?php
// Database Connection
$conn = mysqli_connect('localhost', 'root', '', 'hrms');

// Initialize Variables
$user_id = isset($_REQUEST['user_id']) ? (int)$_REQUEST['user_id'] : 0;
$name = "";
$email = "";
$contact = "";
$department = "";
$job_position = "";
$qualification = "";
$join_date = "";
$address = "";
$gender = "";
$cv_path = "";
$username = "";
$password = "";


if ($user_id != 0) {
    $sql = "SELECT user_id, name, email, contact, department, job_position, qualification, join_date, address, gender, cv ,username,password FROM employee WHERE user_id='$user_id'";
    $results = $conn->query($sql);


    if ($results && $results->num_rows > 0) {
        $data = mysqli_fetch_assoc($results);

        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $contact = $data['contact'] ?? '';
        $department = $data['department'] ?? '';
        $job_position = $data['job_position'] ?? '';
        $qualification = $data['qualification'] ?? '';
        $join_date = $data['join_date'] ?? '';
        $address = $data['address'] ?? '';
        $gender = $data['gender'] ?? '';
        $cv_path = $data['cv'] ?? '';
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
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
            background: rgb(45, 156, 225);
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
            let isValid = true;

            // Clear all validators
            document.querySelectorAll("span[id$='Validation']").forEach(span => (span.textContent = ""));

            // Validate Full Name - FIXED to handle multiple name formats like "Manira Mahat"
            const name = document.getElementById("name").value.trim();
            // New pattern: Requires first letter capital, allows multiple words with spaces
            const namePattern = /^[A-Z][a-zA-Z]+(?: [A-Za-z][a-zA-Z]+)*$/;

            if (name === "") {
                document.getElementById("nameValidation").textContent = "Name is required.";
                isValid = false;
            } else if (!namePattern.test(name)) {
                document.getElementById("nameValidation").textContent = "First name must start with a capital letter. Each name part must contain at least two letters.";
                isValid = false;
            } else {
                document.getElementById("nameValidation").textContent = ""; // Clear error message if valid
            }

            // Validate Email
            const email = document.getElementById("email").value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                document.getElementById("emailValidation").textContent = "Enter a valid email address.";
                isValid = false;
            }

            // Validate Phone Number
            const contact = document.getElementById("contact").value.trim();
            const nepalPhoneRegex = /^(?:9[678]\d{8}|0[1-9]\d{6,8})$/; // Mobile (96/97/98) + Landline

            if (!nepalPhoneRegex.test(contact)) {
                document.getElementById("contactValidation").textContent = "Enter a valid Nepalese phone number.";
                isValid = false;
            } else {
                document.getElementById("contactValidation").textContent = ""; // Clear error if valid
            }


            // Validate Department
            const department = document.getElementById("department").value;
            if (department === "") {
                document.getElementById("departmentValidation").textContent = "Please select a department.";
                isValid = false;
            }

            // Validate Job Position
            const jobPosition = document.getElementById("job_position").value.trim();
            if (jobPosition === "") {
                document.getElementById("positionValidation").textContent = "Job position is required.";
                isValid = false;
            }

            // Validate academic Qualification
            const Qualification = document.getElementById("qualification").value.trim();
            if (Qualification === "") {
                document.getElementById("qualificationValidation").textContent = "Academic qualification is required.";
                isValid = false;
            }

            // Validate Joining Date
            const joinDate = document.getElementById("join_date").value;
            if (joinDate === "") {
                document.getElementById("dateValidation").textContent = "Joining date is required.";
                isValid = false;
            } else {
                // Get date of birth (already validated above)
                const dob = document.getElementById("dob").value;
                if (dob !== "") {
                    const dobDate = new Date(dob);
                    const joinDateObj = new Date(joinDate);

                    // Calculate the age at joining date
                    let ageAtJoining = joinDateObj.getFullYear() - dobDate.getFullYear();
                    const monthDiff = joinDateObj.getMonth() - dobDate.getMonth();

                    if (monthDiff < 0 || (monthDiff === 0 && joinDateObj.getDate() < dobDate.getDate())) {
                        ageAtJoining--;
                    }

                    // Check if employee is at least 18 years old at joining date
                    if (ageAtJoining < 18) {
                        document.getElementById("dateValidation").textContent = "Employee must be at least 18 years old at the time of joining.";
                        isValid = false;
                    }
                }
            }

            // Validate Address
            const address = document.getElementById("address").value.trim();
            if (address === "") {
                document.getElementById("addressValidation").textContent = "Address is required.";
                isValid = false;
            }

            // Validate Profile Picture
            const profilePicture = document.getElementById("profile_picture").files[0];
            if (!profilePicture) {
                document.getElementById("imageValidation").textContent = "Please upload a profile picture.";
                isValid = false;
            } else {
                const validExtensions = ["image/jpeg", "image/png", "image/jpg"];
                if (!validExtensions.includes(profilePicture.type)) {
                    document.getElementById("imageValidation").textContent = "Only JPG, JPEG, and PNG files are allowed.";
                    isValid = false;
                } else if (profilePicture.size > 2 * 1024 * 1024) {
                    document.getElementById("imageValidation").textContent = "File size should not exceed 2MB.";
                    isValid = false;
                }
            }

            // Validate Date of Birth (DOB)
            const dob = document.getElementById("dob").value;
            if (dob === "") {
                document.getElementById("dobValidation").textContent = "Date of Birth is required.";
                isValid = false;
            } else {
                // Check if the user is at least 18 years old
                const dobDate = new Date(dob);
                const today = new Date();
                let age = today.getFullYear() - dobDate.getFullYear();
                const monthDifference = today.getMonth() - dobDate.getMonth();

                if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < dobDate.getDate())) {
                    age--;
                }

                if (age < 18) {
                    document.getElementById("dobValidation").textContent = "You must be at least 18 years old.";
                    isValid = false;
                }
            }

            // Validate Gender
            const gender = document.getElementById("gender").value;
            if (gender === "") {
                document.getElementById("genderValidation").textContent = "Please select a gender.";
                isValid = false;
            }

            //Validate cv
            const cv = document.getElementById("cv").files[0];
            const idField = document.querySelector('input[name="id"]');
            const isUpdate = idField && idField.value > 0;

            if (!cv && !isUpdate) {
                document.getElementById("cvValidation").textContent = "Please upload a CV.";
                isValid = false;
            } else if (cv) {
                if (cv.type !== "application/pdf") {
                    document.getElementById("cvValidation").textContent = "Only PDF files are allowed.";
                    isValid = false;
                } else if (cv.size > 2 * 1024 * 1024) { // 2MB limit
                    document.getElementById("cvValidation").textContent = "File size should not exceed 2MB.";
                    isValid = false;
                }
            }
            //validate username
            const username = document.getElementById("username").value.trim();
            const usernamePattern = /^[a-z][a-z0-9@_]*$/; // Starts with a lowercase letter, allows lowercase, numbers, @, _

            if (!usernamePattern.test(username)) {
                document.getElementById("usernameValidation").textContent =
                    "Username must start with a lowercase letter and can contain lowercase letters, numbers, '@', or '_'.";
                isValid = false;
            } else {
                document.getElementById("usernameValidation").textContent = ""; // Clear error message if valid
            }


            //validate password
            const password = document.getElementById("password").value.trim();
            const passwordPattern = /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

            if (!passwordPattern.test(password)) {
                document.getElementById("passwordValidation").textContent =
                    "Password must be at least 8 characters long, contain at least one uppercase letter, one number, and one special character (@, $, !, %, *, ?, &).";
                isValid = false;
            } else {
                document.getElementById("passwordValidation").textContent = ""; // Clear error message if valid
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
                <form id="employeeForm" action="employee_controller.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm();">
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

                    <!-- Full Name -->
                    <div class="form-group">
                        <label for="name"><b>Full Name</b></label>
                        <input type="text" id="name" name="name" value="<?php echo $name; ?>" placeholder="Enter full name">
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
                        <label for="contact"><b>Phone Number</b></label>
                        <input type="tel" id="contact" name="contact" value="<?php echo $contact; ?>" placeholder="Enter phone number">
                        <span id="contactValidation" style="color:red;"></span>
                    </div>

                    <!-- Department -->
                    <div class="form-group">
                        <label for="department"><b>Department</b></label>
                        <select id="department" name="department">
                            <option value="">Select Department</option>
                            <option value="HR" <?php echo $department == "HR" ? "selected" : ""; ?>>Human Resources</option>
                            <option value="IT" <?php echo $department == "IT" ? "selected" : ""; ?>>Information Technology</option>
                            <option value="Finance" <?php echo $department == "Finance" ? "selected" : ""; ?>>Administration</option>
                            <option value="Marketing" <?php echo $department == "Marketing" ? "selected" : ""; ?>>Marketing</option>
                            <option value="Operations" <?php echo $department == "Operations" ? "selected" : ""; ?>>Accounting</option>
                        </select>
                        <span id="departmentValidation" style="color:red;"></span>
                    </div>

                    <!-- Job Position -->
                    <div class="form-group">
                        <label for="job_position"><b>Job Position</b></label>
                        <input type="text" id="job_position" name="job_position" value="<?php echo $job_position; ?>" placeholder="Enter job position">
                        <span id="positionValidation" style="color:red;"></span>
                    </div>

                    <!-- Job Qualification -->
                    <div class="form-group">
                        <label for="qualification"><b>Academic Qualification</b></label>
                        <input type="text" id="qualification" name="qualification" value="<?php echo $qualification; ?>" placeholder="Enter academic qualification">
                        <span id="qualificationValidation" style="color:red;"></span>
                    </div>

                    <!-- Joining Date -->
                    <div class="form-group">
                        <label for="join_date"><b>Joining Date</b></label>
                        <input type="date" id="join_date" name="join_date" value="<?php echo $join_date; ?>">
                        <span id="dateValidation" style="color:red;"></span>
                    </div>

                    <!-- Address -->
                    <div class="form-group">
                        <label for="address"><b>Address</b></label>
                        <textarea id="address" name="address" rows="1" placeholder="Enter address"><?php echo $address; ?></textarea>
                        <span id="addressValidation" style="color:red;"></span>
                    </div>

                    <!-- Profile Picture -->
                    <div class="form-group">
                        <label for="profile_picture"><b>Profile Picture</b></label>
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                        <span id="imageValidation" style="color:red;"></span>
                    </div>
                    <!--CV-->
                    <div class="form-group">
                        <label for="cv"><b>Upload CV (PDF only)</b></label>
                        <input type="file" id="cv" name="cv" accept=".pdf">
                        <span id="cvValidation" style="color:red;"></span>
                    </div>

                    <!--Username-->
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Enter username for employee">
                        <span id="usernameValidation" style="color:red;" class="error"></span>
                    </div>


                    <!--Password-->
                    <div class="form-group">
                        <label for="password">Password:</label>

                        <input type="password" id="password" name="password" placeholder="Enter password for employee">

                        <span id="passwordValidation" style="color:red;" class="error"></span>
                    </div>


                    <!-- Submit and Reset -->
                    <div class="form-actions">
                        <button type="submit"><b>Add Employee</b></button>
                    </div>
                </form>

                <!-- Modal for showing messages - CHANGED: display:none by default -->
                <div id="messageModal" style="display: none; padding: 20px; background-color: rgba(0,0,0,0.5); position: fixed; top: 0; left: 0; right: 0; bottom: 0; justify-content: center; align-items: center;">
                    <div style="background: #fff; padding: 30px; border-radius: 10px; text-align: center;">
                        <p id="modalMessage">Username and password has been sent to users!</p>
                        <button onclick="success()" class="yes-btn">OK</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.querySelector('a[href="addemployee.php"]').classList.add('active-page');

        // Function to show success modal
        function showSuccessModal(message) {
            document.getElementById("modalMessage").textContent = message || "Username and password has been sent to users!";
            document.getElementById("messageModal").style.display = "flex";
        }

        function success() {
            window.location.href = "employeeDetails.php"; // Redirect after clicking OK
        }

        // Add event listener to form submission
        document.addEventListener('DOMContentLoaded', function() {
            // Check for success parameter in URL to display modal if redirected after successful processing
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success') && urlParams.get('success') === 'true') {
                showSuccessModal();
            }

            // Handle form submission with AJAX
            document.getElementById('employeeForm').addEventListener('submit', function(e) {
                if (validateForm()) {
                    e.preventDefault(); // Prevent default form submission

                    var formData = new FormData(this);

                    // Create AJAX request
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'employee_controller.php', true);

                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            try {
                                var response = JSON.parse(xhr.responseText);
                                if (response.status === "success") {
                                    showSuccessModal(response.message);
                                } else {
                                    alert("Error: " + response.message);
                                }
                            } catch (e) {
                                console.error("Error parsing JSON: ", e);

                            }
                        } else {
                            alert("Request failed. Please try again.");
                        }
                    };

                    xhr.onerror = function() {
                        alert("Request failed. Please check your connection.");
                    };

                    xhr.send(formData);
                }
            });
        });
    </script>
</body>

</html>