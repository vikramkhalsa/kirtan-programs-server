<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="user-scalable=yes, width=device-width" />
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

if (isset($_POST['submit'])){

if ($p1!= $p2){
  echo "<br>Passwords do not match</br>";
}else {

 $encrypass = password_hash($p1, PASSWORD_DEFAULT);
  $sql = "INSERT INTO dharamkh_programs.usertbl (username, email, password)
  VALUES ('$user', '$email', '$encrypass')";
  if ($conn->query($sql) === TRUE) {
      echo "\nNew user registered successfully.</br>";
      echo 'Visit <a href="submitprogram.php"> this page </a> to submit a program. ';
$email;
$subject = "Bay Area Kirtans Registration successful!";
$body =  sprintf("WJKK WJKF,\n\n
  Welcome %s, You have been successfully registered. You may now visit 
  http://vikramkhalsa.com/kirtanapp/submitprogram.php to submit programs." $user);

 if (mail($to, $subject, $body)) {
  } else {
  }

  } else {
      echo "Error: " . $conn->error . "<br>";
  }
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
  <input type="submit" value="Submit" name="submit"> 
</form> 



</body>
</html>