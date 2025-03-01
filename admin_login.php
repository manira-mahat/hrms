<?php
// Initialize variables for form data and error message
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$error = $_GET['error'] ?? ''; // Fetch error message passed via GET parameter if any
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp&display=swap">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <style>
        .alert {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }

        .error {
            color: red;
            font-size: 12px;
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

        /* Logo */
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
            justify-content: flex-end;
            /* Align items to the right */
            margin: 0;
            padding-right: 2px;
            /* Space to the right */
        }

        /* Navbar list items */
        #navbar li {
            margin-left: 1px;
            /* Reduced the gap between items */
            display: flex;
            align-items: center;
            /* Vertically align items */
        }

        /* Navbar icon */
        #navbar li img {
            height: 30px;
            cursor: pointer;
        }

        /* Navbar links */
        #navbar li a {
            text-decoration: none;
            font-weight: bold;
            padding: 8px 15px;
            border-radius: 10px;
            color: white;
        }

        #navbar li a:hover {
            background-color: skyblue;
        }

        #navbar li a.active {
            background-color: rgba(0, 115, 177, 255);
        }



        /* Container Styling */
        #login {
            display: flex;
            justify-content: center;
            /* Centers horizontally */
            align-items: center;
            /* Centers vertically */
            height: 100vh;
            /* Full viewport height for perfect vertical centering */
            margin: 0;
            /* Remove any default margin */
            padding: 0;
            box-sizing: border-box;
        }

        .login-container {
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 300px;
            text-align: center;
        }


        /* Heading */
        .login-container h1 {
            color: #333;
            margin-bottom: 20px;
        }

        /* Labels */
        .login-container label {
            display: block;
            text-align: left;
            color: #555;
            margin-bottom: 8px;
            font-size: 14px;
        }

        /* Inputs */
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        /* Button */
        .login-container input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-container input[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Error Messages */
        .error {
            font-size: 12px;
            color: red;
            display: block;
            text-align: left;
        }

        /* Message */
        .message {
            margin-top: 15px;
            font-size: 14px;
            color: #555;
        }

        .message a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .message a:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        function validate() {
            var username = document.getElementById("username").value.trim();
            var password = document.getElementById("password").value.trim();

            var isValid = true;

            // Clear previous validation messages
            document.getElementById("usernameValidation").innerText = "";
            document.getElementById("passwordValidation").innerText = "";

            // Validate username
            if (username === "") {
                document.getElementById("usernameValidation").innerText = "Username is required.";
                isValid = false;
            }

            // Validate password
            if (password === "") {
                document.getElementById("passwordValidation").innerText = "Password is required.";
                isValid = false;
            }

            return isValid;
        }
    </script>
</head>

<body>

    <section id="nav">
        <img src="logo-img.png" alt="Logo" class="logo">
        <div>
            <ul id="navbar">
                <li>
                    <a href="homepage.html"><span class="material-symbols-sharp" style="color:white;">
                            home
                        </span>
                    </a>
                </li>
                <li><a class="active">Admin</a></li>
            </ul>
        </div>
    </section>


    <!-- Login Section -->
    <section id="login">

        <div class="login-container">
            <h1>Login</h1>
            <form action="admin_login_controller.php" method="POST" onsubmit="return validate()">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>"><br>
                <span id="usernameValidation" class="error"></span>
                <br><br>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>"><br>
                <span id="passwordValidation" class="error"></span>
                <br><br>

                <a href="landpage.php"><input type="submit" value="Login"></a>

            </form>

            <!-- Display alert if there's an error -->
            <?php if (!empty($error)): ?>
                <div class="alert"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
        </div>
    </section>
</body>

</html>