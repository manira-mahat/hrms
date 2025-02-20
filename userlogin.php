<?php
session_start();

// Prevent page caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: user_dashboard.php');
    exit;
}

include('db_connect.php'); // Include the database connection

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$error = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($username) || empty($password)) {
        $error = 'All fields are required.';
    } else {
        // Prepare and execute query to check user credentials
        $stmt = $conn->prepare("SELECT * FROM usersignup WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Check if the user has verified OTP
            if ($user['is_verified'] == 1) {
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Start session and set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];

                    // Redirect to dashboard
                    header('Location: user_dashboard.php');
                    exit;
                } else {
                    $error = 'Invalid password.';
                }
            } else {
                $error = 'Your OTP is not verified. Please verify your OTP first.';
            }
        } else {
            $error = 'Username not found.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
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
            background-color: rgb(163, 129, 226);
        }

        .facebook-logo {
            height: 32px;
            width: 32px;
            border-radius: 50%;
            overflow: hidden;
            cursor: pointer;
            transition: opacity 0.2s;
            margin-left: 20px;
        }

        .facebook-logo:hover {
            opacity: 0.8;
        }

        /* Login Section */
        .login-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            margin-top: 100px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 10px 1opx 0;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .message {
            text-align: center;
        }
        
        input[type="submit"] {
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

         input[type="submit"]:hover {
            background-color: #1d4ed8;
        }

         input[type="submit"]:active {
            transform: translateY(1px);
        }
    </style>
</head>
<body>
    <!-- Navigation Section -->
    <section id="nav">
        <img src="logo-img.png" alt="Logo" class="logo">
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

    <div class="login-container">
        <h1>Login</h1>

        <?php if ($error): ?>
            <div class="error"><?= $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST" onsubmit="return validateLogin()">
        <label>Username:</label>
        <input type="text" id="username" name="username">
        <span id="usernameError" class="error"></span><br><br>
        
        <label>Password:</label>
        <input type="password" id="password" name="password">
        <span id="passwordError" class="error"></span><br><br>
        
        <input type="submit" value="Login">
    </form>
    

        <p class="message">Don't have an account? <a href="usersignup.php">Sign Up here</a></p>
    </div>

    <script>
        function validateLogin() {
            let isValid = true;
            
            function setError(id, message) {
                document.getElementById(id).innerText = message;
                isValid = false;
            }
            
            document.getElementById("usernameError").innerText = "";
            document.getElementById("passwordError").innerText = "";
            
            if (document.getElementById("username").value.trim() === "") {
                setError("usernameError", "Username is required");
            }
            if (document.getElementById("password").value.trim() === "") {
                setError("passwordError", "Password is required");
            }
            
            return isValid;
        }
    </script>
</body>
</html>
