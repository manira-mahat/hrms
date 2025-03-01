<?php
include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("SELECT * FROM employee WHERE verification_code = ? AND code_expiry > NOW()");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE employee SET password = ?, verification_code = NULL, code_expiry = NULL WHERE verification_code = ?");
        $stmt->bind_param('ss', $new_password, $token);
        $stmt->execute();

        echo "Password reset successful. <a href='login.php'>Login now</a>";
    } else {
        echo "Invalid or expired token.";
    }
}

if (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    die("Invalid request.");
}
?>

<form action="" method="POST">
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
    <label>New Password:</label>
    <input type="password" name="password" required>
    <input type="submit" value="Reset Password">
</form>