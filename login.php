<?php 
if ($error != '')
{
echo '<div style="padding:4px; border:1px solid red; color:red;">'.$error.'</div>';
}

//check if its a post ??

// connect to the database
if (isset($_POST['username'])){
include('config.php');
$user =  $conn->real_escape_string($_POST['username']);
$p1 =  $_POST['password'];

 //$encrypass = password_hash($p1, PASSWORD_DEFAULT);
  $sql = "SELECT password FROM events_all.usertbl WHERE username = '$user'";
  $result = mysqli_query($conn, $sql);

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
 if (password_verify($p1, $row["password"])){
  //echo "Login was successful. <br>";
  session_start();
  $_SESSION["user"] = $user;
  header("Location: " . "http://sikh.events/submitprogram.php");
     exit();

 }
 else {
  echo '<div style="padding:4px; border:1px solid red; color:red;">password is incorrect, please try again. </div>';
 }

}      //check if password matches result
else {
    echo "username is incorrect <br>";
}

$conn->close();

      //set in session
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="user-scalable=yes, width=device-width" />
  </head>
<body>

Welcome! Please enter your login details below.
<br>



<form id="adduser" action="login.php" method="post" >
  username:<br>
  <input type="text" name="username"><br> 
   Password:<br>
  <input type="password" name="password"><br>
  <input type="submit" value="Submit"> 
</form> 

Don't have an account? Click <a href="newuser.php">here</a> to register.



</body>
</html>