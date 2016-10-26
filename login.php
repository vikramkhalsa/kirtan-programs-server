<!DOCTYPE html>
<html>
<head>
    <script src="datetimepicker_css.js"></script>
  </head>
<body>

Welcome! Please enter your login details below.
<br>

<?php 
if ($error != '')
{
echo '<div style="padding:4px; border:1px solid red; color:red;">'.$error.'</div>';
}

//check if its a post ??

// connect to the database
include('config.php');
$user =  $conn->real_escape_string($_POST['username']);
$p1 =  $_POST['password'];

 //$encrypass = password_hash($p1, PASSWORD_DEFAULT);
  $sql = "SELECT password FROM dharamkh_programs.usertbl WHERE username = '$user'";
  $result = mysqli_query($conn, $sql);

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
 if (password_verify($p1, $row["password"])){
  echo "Login was successful. <br>";
  session_start();
  $_SESSION["user"] = $user;
  header("Location: " . "http://vikramkhalsa.com/kirtanapp/submitprogram.php");
     exit();

 }
 else {
  echo "password is incorrect, please try again. <br>";
 }

}      //check if password matches result
else {
    echo "username is incorrect <br>";
}

$conn->close();

      //set in session


?>


<form id="adduser" action="login.php" method="post" >
  username:<br>
  <input type="text" name="username"><br> 
   Password:<br>
  <input type="password" name="password"><br>
  <input type="submit" value="Submit"> 
</form> 



</body>
</html>