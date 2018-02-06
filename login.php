<?php 

//login.php
//Vikram Singh
//10/20/2017
//login portal for user to enter username and password
//hashes and compares with database, gives error if failed, sets session variables if successful


if ($error != ''){
  echo "<div class='alert alert-danger' role='alert'>".$error."</div>";
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
      //set in session
      $_SESSION["user"] = htmlspecialchars($user);
      $_SESSION["usertype"] = htmlspecialchars($row["type"]);
      if (isset($_SESSION['enter_url']) &&  !empty($_SESSION['enter_url']))
      	header("Location: " . $_SESSION['enter_url']);
      else
        header("Location: " . "/programsadmin.php");
      exit();
    }
    else {
      $loginer = "<div class='alert alert-danger' role='alert'>Username or Password is incorrect, please try again. 
      <a href='resetpassword.php'>Click here to reset your password.</a> </div>";
    }
  }      
  else {
    $loginer = "<div class='alert alert-danger' role='alert'>Username or Password is incorrect, please try again. 
    <a href='resetpassword.php'>Click here to reset your password.</a> </div>";
  }

  $conn->close();

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

  <div class="container">
    Welcome! Please enter your login details below.
    <br>
    <br>

    <div class="row">
      <div class="col-sm-6 col-md-4 col-2">
        
        <?php echo $loginer; ?>
        
        <form id="adduser" action="login.php" method="post" >
          Username:<br>
          <input type="text" name="username" class="form-control"/><br> 
          Password:<br>
          <input type="password" name="password" class="form-control"/><br>
          <input type="submit" value="Submit" class="btn btn-default"/> 
          <input type="hidden" name="redirurl" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
        </form> 
        <br>

        Don't have an account? Click <a href="newuser.php">here</a> to register.
      </div>
    </div>

  </div>

</body>
</html>