
    <?php
    $conn = mysqli_connect('localhost', 'root', '', 'hrms');

    $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;

    $name = "";
    $address = "";
    $contact = "";
    $gender = "";
    $email="";
    $dob="";

    if ($id != 0) {
        $sql = "SELECT * FROM student WHERE id='$id'";

        $results = $conn->query($sql);
        if ($results->num_rows > 0) {
            $data = mysqli_fetch_assoc($results);
            $name = $data['name'];
            $address = $data['address'];
            $email = $data['email'];
            $dob = $data['DOB'];
            $contact = $data['contact'];
            $gender = $data['gender'];
        } else {
            echo "No record found with ID: $id";
        }
    }
    ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Form</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <script>
        function validate() {
            var name = document.getElementById("name").value;
            var dob = document.getElementById("dob").value;
            var address = document.getElementById("address").value;
            var contact = document.getElementById("contact").value;
            var gender = document.getElementById("gender").value;
            var email = document.getElementById("email").value;

            var isValid = true;

            // Reset previous error messages
            document.getElementById("nameValidator").innerText = "";
            document.getElementById("dobValidator").innerText = "";
            document.getElementById("addressValidator").innerText = "";
            document.getElementById("contactValidator").innerText = "";
            document.getElementById("genderValidator").innerText = "";
            document.getElementById("emailValidator").innerText = "";

            // Validate each field
            if (name === "") {
                document.getElementById("nameValidator").innerText = "Name is required";
                isValid = false;
            }

            if (dob === "") {
                document.getElementById("dobValidator").innerText = "DOB is required";
                isValid = false;
            }

            if (address === "") {
                document.getElementById("addressValidator").innerText = "Address is required";
                isValid = false;
            }

            // if (contact === "") {
            //     document.getElementById("contactValidator").innerText = "Contact is required";
            //     isValid = false;
            // } else if (isNaN(contact) || contact.length < 10) {
            //     document.getElementById("contactValidator").innerText = "Enter a valid contact number";
            //     isValid = false;
            // }

            if (!/^\d{10}$/.test(contact)) {
    document.getElementById("contactValidator").innerText = "Enter a valid 10-digit contact number";
    isValid = false;
}

            if (gender === "") {
                document.getElementById("genderValidator").innerText = "Gender is required";
                isValid = false;
            }

            if (email === "") {
                document.getElementById("emailValidator").innerText = "Email is required";
                isValid = false;
           
            }

            return isValid;
        }
    </script>

    <div class="container">

    <?php
      include 'sidebar.php';
    ?>

<script>
  document.querySelector('a[href="addemployee.php"]').classList.add('active-page');
</script>

<main>

<header>

    <h1>Employee Form</h1>
    </header>

    <form action="employee_controller.php" method="post" onsubmit="return validate()">
    <br><br>
        <label for="name">Name</label>
        <input type="text" name="name" id="name" value="<?php echo $name; ?>"
        style="width: 75rem; height: 35px; font-size: 16px; border: 0.2px solid lightgrey; border-radius: 5px;"><br>
        <span id="nameValidator" style="color:red;"></span>
        <br><br>

        <label for="dob">DOB</label><br>
<input type="date" name="dob" id="dob" value="<?php echo $dob; ?>" 
       style="width: 75rem; height: 35px; font-size: 16px; padding: 5px;border: 0.2px solid lightgrey ; border-radius: 5px;">
<br>
<span id="dobValidator" style="color:red;"></span>
<br><br>


        <label for="address">Address</label>
        <input type="text" name="address" id="address" value="<?php echo $address; ?>"
        style="width: 75rem; height: 35px; font-size: 16px; border: 0.2px solid lightgrey; border-radius: 5px;"><br>
        <span id="addressValidator" style="color:red;"></span>
        <br><br>

        <label for="contact">Contact</label>
        <input type="text" name="contact" id="contact" value="<?php echo $contact; ?>"
        style="width: 75rem; height: 35px; font-size: 16px; border: 0.2px solid lightgrey; border-radius: 5px;"><br>
        <span id="contactValidator" style="color:red;"></span>
        <br><br>

        <label for="gender">Gender</label><br>
<select name="gender" id="gender" 
        style="width: 75rem; height: 35px; font-size: 16px; border: 0.2px solid lightgrey; border-radius: 5px;">
    <option value="female">Female</option>
    <option value="male">Male</option>
    <option value="other">Other</option>
</select>
<br>
<span id="genderValidator" style="color:red;"></span>
<br><br>


        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?php echo $email; ?>"
        style="width: 75rem; height: 35px; font-size: 16px; border: 0.2px solid lightgrey; border-radius: 5px;"><br>
        <span id="emailValidator" style="color:red;"></span>
        <br><br>

        <a href="employeeDetails.php"><input type="submit" value="Submit" style="background-color: green; font-size: 20px; padding: 10px 20px; border-radius: 5px; color: white; border: none;"></a>

    </form>
    </main>
    </div>
</body>

</html>
