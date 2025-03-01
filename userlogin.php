<?php
session_start();
include('db_connect.php'); // Include database connection

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($username) || empty($password)) {
        $error = 'Username and password are required.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM employee WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                header('Location: user_dashboard.php'); // Redirect to dashboard
                exit;
            } else {
                $error = 'Invalid password.';
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp&display=swap">
    <title>Login</title>
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
    justify-content: flex-end; /* Align items to the right */
    margin: 0;
    padding-right: 20px; /* Space to the right */
}

/* Navbar list items */
#navbar li {
    margin-left: 1px;
    display: flex;
    align-items: center; /* Vertically align items */
    gap:0.1rem;
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
    background-color:  rgba(0, 115, 177, 255);
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
                    <a href="homepage.html"><span class="material-symbols-sharp" style="color:white;">
                            home
                        </span>
                    </a>
                </li>
                <li><a class="active">User</a></li>
            </ul>
        </div>
    </section>
    <div class="login-container">
        <h1>Login</h1>
        <?php if ($error): ?>
            <div style="color:red"><?= $error; ?></div>
        <?php endif; ?>


        <form action="" method="POST" onsubmit="return validateLogin()">
            <label>Username:</label>
            <input type="text" id="username" name="username">
            <span id="usernameError" style="color:red;"></span><br><br>

            <label>Password:</label>
            <input type="password" id="password" name="password">
            <span id="passwordError" style="color:red;"></span><br><br>

            <input type="submit" value="Login">
        </form>

        <p><a href="forgot_password.php">Forgot Password?</a></p>
    </div>
</body>

</html>