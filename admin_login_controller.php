<?php
// Database connection
$connection = mysqli_connect('localhost', 'root', '', 'hrms');

// Check connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validate input
    if (empty($username) || empty($password)) {
        echo "<script>
                alert('Please enter both username and password.');
                window.location.href = 'admin_login.php';
              </script>";
        exit;
    }

    // Query to check if the user exists
    $sql = "SELECT * FROM admin WHERE username = ?";
    $stmt = $connection->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch user data
            $user = $result->fetch_assoc();

            // Verify password
            if ($password === $user['password']) {
                // Login logic here


                // Start session
                session_start();
                $_SESSION['username'] = $username;

                echo "<script>
                        window.location.href = 'landpage.php';
                      </script>";
            } else {
                echo "<script>
                        alert('Invalid username or password!');
                        window.location.href = 'admin_login.php';
                      </script>";
            }
        } else {
            echo "<script>
                    alert('User not found. Please try again!');
                    window.location.href = 'admin_login.php';
                  </script>";
        }
        $stmt->close();
    } else {
        echo "Query error: " . $connection->error;
    }
}

$connection->close();
