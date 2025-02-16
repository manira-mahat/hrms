<?php
// Set default values for variables if not already set
$name = isset($_POST['Name']) ? $_POST['Name'] : '';
$address = isset($_POST['Address']) ? $_POST['Address'] : '';
$gender = isset($_POST['Gender']) ? $_POST['Gender'] : '';
$contact = isset($_POST['Contact']) ? $_POST['Contact'] : '';
$dob = isset($_POST['dob']) ? $_POST['dob'] : '';
$username = isset($_POST['username']) ? $_POST['username'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup Form</title>
    <link rel="stylesheet" href="ssstyle.css">
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            min-height: 100vh;
        }

        #nav {
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 0;
            /* Removed padding for full control */
            background-color: rgba(0, 115, 177, 255);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.06);
            height: 70px;
            /* Fixed navbar height */
            z-index: 100;
        }

        .logo {
            background-color: white;
            /* White background for the logo */
            padding: 5px;
            /* Reduced padding for smaller space inside */
            margin: 5px 0 5px 10px;
            /* Small gap: top, right, bottom, left */
            height: calc(100% - 10px);
            /* Adjust height to leave top and bottom gap */
            box-sizing: border-box;
            /* Ensures padding fits within the height */
            display: flex;
            align-items: center;
            /* Vertically center the logo */
            justify-content: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }#navbar {
    list-style: none;
    display: flex;
    align-items: center;
    justify-content: center;
    padding-right: 30%;
}

#navbar li {
    margin-left: 20px;
}

#navbar li img {
    height: 30px;
    cursor: pointer;
}

#navbar li a {
    
    text-decoration: none;
    font-weight: bold;
    padding: 8px 15px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

#navbar li a:hover {
    background-color: #555;
}

#navbar li a.active {
    background-color:rgb(163, 129, 226);
}


.facebook-logo {
    height: 32px;
    width: 32px;
    border-radius: 50%; /* Makes the logo circular */
    overflow: hidden;   /* Ensures the content fits inside the circle */
    cursor: pointer;
    transition: opacity 0.2s;
    margin-left: 20px;  /* Adds gap from "Admin" text */
}



        .facebook-logo:hover {
            opacity: 0.8;
        }


        .active {
            color: white;
            font-weight: 500;
            text-decoration: none;
            font-size: 1.2rem;
        }

        

        /* Main Content */
        .main-content {
            padding-top: 100px;
            min-height: calc(100vh - 80px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Form Container */
        .form-container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            margin: 2rem;
        }

        .form-container h1 {
            text-align: center;
            color: #1a1a1a;
            margin-bottom: 2rem;
            font-size: 1.75rem;
            font-weight: 600;
        }

        .form-row {
            display: flex;
            flex-direction: column;
            margin-bottom: 1.5rem;
        }

        .form-container label {
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #4a4a4a;
            font-size: 0.95rem;
        }

        .form-container input:not([type="submit"]),
        .form-container select {
            padding: 0.75rem 1rem;
            border: 1.5px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            width: 100%;
        }

        .form-container input:not([type="submit"]):focus,
        .form-container select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-container select {
            background-color: white;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
            padding-right: 2.5rem;
        }

        .error {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }

        .form-container input[type="submit"] {
            width: 100%;
            padding: 0.875rem;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
            margin-top: 1rem;
        }

        .form-container input[type="submit"]:hover {
            background-color: #1d4ed8;
        }

        .form-container input[type="submit"]:active {
            transform: translateY(1px);
        }

        .message {
            text-align: center;
            margin-top: 1.5rem;
            color: #6b7280;
            font-size: 0.95rem;
        }

        .message a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .message a:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        @media (min-width: 640px) {
            .form-row {
                flex-direction: row;
                align-items: center;
            }
            
            .form-container label {
                width: 140px;
                margin-bottom: 0;
                margin-right: 1rem;
            }
            
            .form-container input:not([type="submit"]),
            .form-container select {
                flex: 1;
            }
            
            .error {
                margin-left: 140px;
            }
        }
    </style>
   
</head>
<body>
     <!-- Navigation Section -->
     <section id="nav">
        <img src="logo-img.png" alt="NIST Logo" class="logo">
        <div>
            <ul id="navbar">
                         <li>
                    <img src="facebooklogo.jpg" alt="Facebook Logo" class="facebook-logo"
                        onclick="window.open('https://www.facebook.com/nistcollegebanepaa/', '_blank');">
                </li>
                <li><a class="active">User</a></li>
            </ul>
        </div>
    </section>

    <main class="main-content">
        <div class="form-container">
            <h1>Signup Form</h1>
            <form action="signup_controller.php" method="POST" onsubmit="return validate()">
                <!-- Your existing form fields -->
                <div class="form-row">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="Name" value="<?php echo htmlspecialchars($name); ?>">
                </div>
                <span id="nameValidation" class="error"></span>

                <div class="form-row">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="Address" value="<?php echo htmlspecialchars($address); ?>">
                </div>
                <span id="addressValidation" class="error"></span>

                <div class="form-row">
                    <label for="gender">Gender:</label>
                    <select id="gender" name="Gender">
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo ($gender === 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($gender === 'Female') ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($gender === 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                <span id="genderValidation" class="error"></span>

                <div class="form-row">
                    <label for="contact">Contact:</label>
                    <input type="tel" id="contact" name="Contact" value="<?php echo htmlspecialchars($contact); ?>" pattern="[0-9]{10}">
                </div>
                <span id="contactValidation" class="error"></span>

                <div class="form-row">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($dob); ?>">
                </div>
                <span id="dobValidation" class="error"></span>

                <div class="form-row">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>">
                </div>
                <span id="usernameValidation" class="error"></span>

                <div class="form-row">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                </div>
                <span id="emailValidation" class="error"></span>

                <div class="form-row">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password">
                </div>
                <span id="passwordValidation" class="error"></span>

                <div class="form-row">
                    <input type="submit" value="Signup">
                </div>
                <p class="message">Already have an account? <a href="userlogin.php">Sign in here</a></p>
            
                
            </form>
        </div>
    </main>

    <script>
        function validate() {
            var name = document.getElementById("name").value;
            var address = document.getElementById("address").value;
            var gender = document.getElementById("gender").value;
            var contact = document.getElementById("contact").value;
            var dob = document.getElementById("dob").value;
            var username = document.getElementById("username").value;
            var email = document.getElementById("email").value;
            var password = document.getElementById("password").value;

            var isValid = true;

            // Clear previous validation messages
            document.getElementById("nameValidation").innerText = "";
            document.getElementById("addressValidation").innerText = "";
            document.getElementById("genderValidation").innerText = "";
            document.getElementById("contactValidation").innerText = "";
            document.getElementById("dobValidation").innerText = "";
            document.getElementById("usernameValidation").innerText = "";
            document.getElementById("emailValidation").innerText = "";
            document.getElementById("passwordValidation").innerText = "";

            // Validate fields
            if (name === "") {
                document.getElementById("nameValidation").innerText = "Name is required";
                isValid = false;
            }
            if (address === "") {
                document.getElementById("addressValidation").innerText = "Address is required";
                isValid = false;
            }
            if (gender === "") {
                document.getElementById("genderValidation").innerText = "Gender is required";
                isValid = false;
            }
            if (contact === "") {
                document.getElementById("contactValidation").innerText = "Contact is required";
                isValid = false;
            }
            if (dob === "") {
                document.getElementById("dobValidation").innerText = "Date of Birth is required";
                isValid = false;
            } else {
                // Check if age is 18 or above
                const dobDate = new Date(dob);
                const today = new Date();
                let age = today.getFullYear() - dobDate.getFullYear();
                const monthDifference = today.getMonth() - dobDate.getMonth();

                if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < dobDate.getDate())) {
                    age--;
                }

                if (age < 18) {
                    document.getElementById("dobValidation").innerText = "You must be at least 18 years old.";
                    isValid = false;
                }
            }

            if (email === "") {
                document.getElementById("emailValidation").innerText = "Email is required";
                isValid = false;
            }
            if (username === "") {
                document.getElementById("usernameValidation").innerText = "Username is required";
                isValid = false;
            }
            if (password === "") {
                document.getElementById("passwordValidation").innerText = "Password is required";
                isValid = false;
            }

            return isValid;
        }
    </script>
</body>
</html>