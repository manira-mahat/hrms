<?php
if($_SERVER['REQUEST_METHOD']==='POST'){
    $id=isset($_POST['id'])?(int)$_POST['id']:0;
    $name=$_POST['Name'];
    $address=$_POST['Address'];
    $gender=$_POST['Gender'];
    $contact=$_POST['Contact'];
    $dob = $_POST['dob'] ;
    $username = $_POST['username'];
    $email = $_POST['email'] ;
    $password = $_POST['password'] ;

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);


    $connection=mysqli_connect('localhost','root','','hrms');
    $sql="insert into userSignUp (Name, Address, Gender, Contact, dob, username, email, password) values('$name','$address','$gender','$contact','$dob','$username','$email','$hashedPassword')";

    if(mysqli_query($connection,$sql)){
        echo"successful";
 
        exit;
    }else{
        echo"error: ". mysqli_error($connection);
    }
    mysqli_close($connection);

}
?>