<!DOCTYPE html>
<html>
<head>
    <script src="datetimepicker_css.js"></script>
  </head>
<body>

Welcome! Please please fill out the fields below to register. 
<br>

<?php 
if ($error != '')
{
echo '<div style="padding:4px; border:1px solid red; color:red;">'.$error.'</div>';
}

//check if its a post
// connect to the database
include('config.php');
$user =  $conn->real_escape_string($_POST['username']);
$email =$conn->real_escape_string($_POST['email']);
$p1 =  $_POST['password'];
$p2 =  $_POST['password2'];

if ($p1!= $p2){
  echo "<br>Passwords do not match</br>";
}else {


 $encrypass = password_hash($p1, PASSWORD_DEFAULT);
  $sql = "INSERT INTO dharamkh_programs.usertbl (username, email, password)
  VALUES ('$user', '$email', '$encrypass')";
  if ($conn->query($sql) === TRUE) {
      echo "\nNew user registered successfully";
  } else {
      echo "Error: " . $conn->error . "<br>";
  }
}

?>


<form id="adduser" action="newuser.php" method="post" >
  username:<br>
  <input type="text" name="username"><br>
  email address:<br>
  <input type="text" name="email"><br>
   Password:<br>
  <input type="password" name="password"><br>
   Confirm Password:<br>
  <input type="password" name="password2"><br>
  <input type="submit" value="Submit"> 
</form> 



</body>
</html>