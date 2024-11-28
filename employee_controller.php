<?php
if($_SERVER['REQUEST_METHOD']==='POST'){
    $id=isset($_POST['id'])?(int)$_POST['id']:0;
    $name=$_POST['name']??'';
    $dob=$_POST['dob']??'';
    $address=$_POST['address']??'';
    $contact=$_POST['contact']??'';
    $gender=$_POST['gender']??'';
    $email=$_POST['email']??'';

    $con=mysqli_connect('localhost','root','','hrms');
    $sql="";
    if($id==0){
        $sql="INSERT INTO employee(name,dob,address,contact,gender,email) VALUES('$name','$dob','$address','$contact','$gender',' $email')";
    }else{
            $sql="UPDATE employee SET name='$name',dob='$dob',address='$address',contact='$contact,gender='$gender',email='$email' WHERE id='$id'";
    }
    if(mysqli_query($con,$sql)){
        echo"Successfully executed!";
        header('Location:student_details.php');
        exit;
    }else{
        echo"Error:".mysqli_error($con);
    }
        mysqli_close($con);
    }

?>