<?php
$connection = mysqli_connect('localhost', 'root', '', 'hrms');

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Query to check if the user exists
    $sql = "SELECT * FROM userSignUp WHERE username = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch user data
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Start session
            session_start();
            $_SESSION['username'] = $username;

            echo "<script>
                    
                    window.onload = function() {
                        window.location.href = 'landpage.php';
                    }
                  </script>";
        } else {
            echo "<script>
                    alert('Invalid username or password!');
                    window.onload = function() {
                        window.location.href = 'login.php';
                    }
                  </script>";
        }
    } else {
        echo "<script>
                alert('User not found. Please sign up!');
                window.onload = function() {
                    window.location.href = 'login.php';
                }
              </script>";
    }
}
