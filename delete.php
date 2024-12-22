<?php

$conn= mysqli_connect('localhost', 'root', '', 'hrms');
$id =(int) $_REQUEST['id'];
$sql= "DELETE FROM Employee where id=$id";

if (mysqli_query($conn, $sql)) {
header('Location: employeeDetails.php');
exit;
} else {
echo "Error: ";
}
?>