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
Welcome! Please please fill out the fields below to register. 
<br>
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
  $sql = "INSERT INTO events_all.usertbl (username, email, password)
  VALUES ('$user', '$email', '$encrypass')";
  if ($conn->query($sql) === TRUE) {

    echo "<div class='alert alert-success' role='alert'>
     </br>New user registered successfully.
     </br>Visit <a href='submitprogram.php'> this page</a> to submit a program. 
     </div>";
      
      //first send email to user
      $to = $email;
      $subject = "Sikh.Events Registration successful!";
      $body =  sprintf("Waheguru Ji Ka Khalsa Waheguru Ji Ki Fateh,\n\n
         Welcome %s, You have been successfully registered. You may now visit 
         http://sikh.events/submitprogram.php to submit programs.", $user);

      if (mail($to, $subject, $body)) {
         echo "";
      }

      //then send email to admin
      $to = "vsk@sikh.events";
      $subject = "New User Registered!";
      $body =  sprintf("WJKK WJKF,\n\n
         New user %s has been successfully registered.", $user);
      mail($to, $subject, $body);

  } else {
     echo "<div class='alert alert-danger' role='alert'> 
      Error: " . $conn->error . 
      "<br> Please try again. </div>";
  }
}
}
?>

<div class="row">
    <div class="col-sm-6 col-md-4 col-2">
<form id="adduser" action="newuser.php" method="post" >
  Username:<br>
  <input type="text" name="username" class="form-control" maxlength='16' required><br>
  Email Address:<br>
  <input type="email" name="email" class="form-control" maxlength='36' required><br>
   Password:<br>
  <input type="password" name="password" class="form-control" required><br>
   Confirm Password:<br>
  <input type="password" name="password2" class="form-control" required><br>
  <input type="submit" value="Submit" name="submit" class="btn btn-default"> 
</form> 
</div>
</div>
</div>

</body>
</html>