<?php 
if ($error != '')
{
echo "div class='alert alert-danger' role='alert'>".$error."</div>";
}
$loginer = "";

//check if its a post ??

// connect to the database
if (isset($_POST['username'])){
include('config.php');
$user =  $conn->real_escape_string($_POST['username']);
$p1 =  $_POST['password'];

 //$encrypass = password_hash($p1, PASSWORD_DEFAULT);
 $sql = "SELECT password, type FROM events_all.usertbl WHERE username = '$user'";
 $result = mysqli_query($conn, $sql);

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  if (password_verify($p1, $row["password"])){
    //echo "Login was successful. <br>";
    session_start();
    $_SESSION["user"] = $user;
    $_SESSION["usertype"] = $row["type"];
    header("Location: " . "/submitprogram.php");
     exit();
  }
  else {
   $loginer = "<div class='alert alert-danger' role='alert'>Username or Password is incorrect, please try again. </div>";
  }
}      
else {
       $loginer =  "<div class='alert alert-danger' role='alert'>Username or Password is incorrect, please try again. </div>";
}


$conn->close();

      //set in session
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="user-scalable=yes, width=device-width" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
 
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

 <link href="navbar.css" rel="stylesheet">
  </head>
<body>
 <?php include('header.html'); ?>  
  <div style="padding:10px;">
  Welcome! Please enter your login details below.
  <br>
  <br>
<div class="row">
    <div class="col-sm-6 col-md-4 col-2">
        <?php echo $loginer; ?>
<form id="adduser" action="login.php" method="post" >
  Username:<br>
  <input type="text" name="username" class="form-control"><br> 
   Password:<br>
  <input type="password" name="password" class="form-control"><br>
  <input type="submit" value="Submit" class="btn btn-default"> 
</form> 
<br>
Don't have an account? Click <a href="newuser.php">here</a> to register.

</div>

</div>

</div>

</body>
</html>