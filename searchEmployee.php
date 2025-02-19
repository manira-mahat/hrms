<?php
$connection = new mysqli("localhost", "root", "", "hrms");
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Use prepared statements to prevent SQL injection
$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '';
$stmt = $connection->prepare("SELECT * FROM employee WHERE name LIKE ? ORDER BY id ASC");
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($data = $result->fetch_assoc()) {
        // Escape output to prevent XSS
        $id = htmlspecialchars($data['id'], ENT_QUOTES, 'UTF-8');
        $name = htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8');
        $dob = htmlspecialchars($data['dob'], ENT_QUOTES, 'UTF-8');
        $address = htmlspecialchars($data['address'], ENT_QUOTES, 'UTF-8');
        $contact = htmlspecialchars($data['contact'], ENT_QUOTES, 'UTF-8');
        $gender = htmlspecialchars($data['gender'], ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8');
        $join_date = htmlspecialchars($data['join_date'], ENT_QUOTES, 'UTF-8');
        $department = htmlspecialchars($data['department'], ENT_QUOTES, 'UTF-8');
        $qualification = htmlspecialchars($data['qualification'], ENT_QUOTES, 'UTF-8');
        $job_position = htmlspecialchars($data['job_position'], ENT_QUOTES, 'UTF-8');
        $profile_picture = htmlspecialchars($data['profile_picture'] ?? 'uploads/default.jpg', ENT_QUOTES, 'UTF-8');

        echo "<tr>
                <td>{$id}</td>
                <td><img class='profile-pic' src='" . filter_var($profile_picture, FILTER_SANITIZE_URL) . "' alt='Profile Picture'></td>
                <td>{$name}</td>
                <td>{$dob}</td>
                <td>{$address}</td>
                <td>{$contact}</td>
                <td>{$gender}</td>
                <td>{$email}</td>
                <td>{$join_date}</td>
                <td>{$department}</td>
                <td>{$qualification}</td>
                <td>{$job_position}</td>
               <td>
                    <a href='addemployee.php?id={$id}'><span class='material-symbols-sharp'style='color:white;background-color:green;'>edit</span></a>
                    <a href='delete.php?id={$id}' onclick='return deleteconfirm();'><span class='material-symbols-sharp' style='color:white;background-color:red;'>delete</span></a>
                </td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='13'>No records found</td></tr>";
}

$stmt->close();
$connection->close();
