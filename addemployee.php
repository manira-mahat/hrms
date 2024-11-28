
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

    <h1>Employee Form</h1>

    <form action="employee_controller.php" method="post" onsubmit="return validate()">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value="<?php echo $name; ?>"><br>
        <span id="nameValidator" style="color:red;"></span>
        <br><br>

        <label for="dob">DOB:</label>
        <input type="date" name="dob" id="dob" value="<?php echo $dob; ?>"><br>
        <span id="dobValidator" style="color:red;"></span>
        <br><br>

        <label for="address">Address:</label>
        <input type="text" name="address" id="address" value="<?php echo $address; ?>"><br>
        <span id="addressValidator" style="color:red;"></span>
        <br><br>

        <label for="contact">Contact:</label>
        <input type="text" name="contact" id="contact" value="<?php echo $contact; ?>"><br>
        <span id="contactValidator" style="color:red;"></span>
        <br><br>

        <label for="gender">Gender:</label>
        <input type="text" name="gender" id="gender" value="<?php echo $gender; ?>"><br>
        <span id="genderValidator" style="color:red;"></span>
        <br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo $email; ?>"><br>
        <span id="emailValidator" style="color:red;"></span>
        <br><br>

        <input type="submit" value="Submit">
    </form>
</body>

</html>
