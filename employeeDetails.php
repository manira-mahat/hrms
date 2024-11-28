<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title> Employee</title>
  <link rel="stylesheet" href="styles.css">

  <style>
  table, td, th {
  border: 1px solid black;
}

table {
  border-collapse: collapse;
  width: 100%;
}

th {
  height: 7rem;
}
#details {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#details td, #customers th {
  border: 1px solid #ddd;
  padding: 8px;
}

#details tr:nth-child(even){background-color: #f2f2f2;}

#details tr:hover {background-color: #ddd;}

#details th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #04AA6D;
  color: white;
}
</style>

</head>

<body>

<div class="container">
    
<?php
      include 'sidebar.php';
    ?>

<script>
  document.querySelector('a[href="employeeDetails.php"]').classList.add('active-page');
</script>

<div class="container">
    <h1 >Employee Details</h1>
    <table id="details">
      
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>DOB</th>
                <th>Address</th>
                <th>Contact</th>
                <th>Gender</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
<?php
    // Database connection
    $connection = mysqli_connect("localhost", "root", "", "hrms");
    if (!$connection) {
        die('Connection failed: ' . mysqli_connect_error());
    }

    // Read from database
    $sql = "SELECT * FROM employee";
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        while ($data = mysqli_fetch_assoc($result)) {
?>
            <tr>
                <td><?php echo $data['id']; ?></td>
                <td><?php echo $data['name']; ?></td>
                <td><?php echo $data['dob']; ?></td>
                <td><?php echo $data['address']; ?></td>
                <td><?php echo $data['contact']; ?></td>
                <td><?php echo $data['gender']; ?></td>
                <td><?php echo $data['email']; ?></td>
                
                <td>
                    <button type="button" class="btn btn-primary">Edit</button>
                    <button type="button" class="btn btn-danger">Delete</button>
                </td>
            </tr>
<?php
        }
    }
    $connection->close();
?>
        </tbody>
    </table>
</div>
</div>
</body>
</html>
