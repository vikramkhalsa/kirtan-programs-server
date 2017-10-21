<?php 
//resetpassword.php
//Vikram Singh
//Modified 10/20/2017

//this page is responsible for all functions related to password reset. 
//a user is directed to this page from login.php when their login fails. 
//by default the page will show a box for email to send recovery link to and a button
// it first handles the post condition for this form to generate the token and send the recovery email
// it also handles the email link click (get) and checks if the token is valid and then displays fields for setting a new password.
//finally, this page handles the POST new password setting and redirects user back to the login page. 


if ($error != '')
{
echo "<div class='alert alert-danger' role='alert'>".$error."</div>";
}

$displaydata = '
	Forgot your password? Please enter your registered email address below and you will be sent a link to reset your password.
	<br>
	<br>
	<form id="resetpassword" action="resetpassword.php" method="post" >
	    Email Address:<br>
	    <input type="email" name="email" class="form-control"><br> 
	    <input type="submit" value="Send Email" class="btn btn-default"> 
	</form> ';

include('config.php');


function crypto_rand_secure($min, $max) {
	$range = $max - $min;
	if ($range < 0) return $min; // not so random...
	$log = log($range, 2);
	$bytes = (int) ($log / 8) + 1; // length in bytes
	$bits = (int) $log + 1; // length in bits
	$filter = (int) (1 << $bits) - 1; // set all lower bits to 1
	do {
	    $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
	    $rnd = $rnd & $filter; // discard irrelevant bits
	} while ($rnd >= $range);
	return $min + $rnd;
}

function getToken($length=24){
	$token = "";
	$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
	$codeAlphabet.= "0123456789";
	for($i=0;$i<$length;$i++){
	    $token .= $codeAlphabet[crypto_rand_secure(0,strlen($codeAlphabet))];
	}
	return $token;
}

function checkToken($conn, $token){

	//check if token valid and expiry date valid 
	$sql = "SELECT email, expiredate FROM events_all.tokentbl WHERE tokentbl.token = '$token'";
	$result = mysqli_query($conn, $sql);

	if ($result->num_rows > 0) {
		$row = mysqli_fetch_assoc($result);
		$expiredate = $row['expiredate'];
		//is expiry date still valid?
		//echo $expiredate;

		$email = $row['email']; 
		//echo $email;
		return $email;
	}
	//else token has expired!
	else return null;
}


//handle reset password link case
if (isset($_GET['token'])){

	//if recieve link with these query parameters,
	// check expiry date, if still valid, allow 
	$token =  $conn->real_escape_string($_GET['token']);

	$email = checkToken($conn, $token);
	if ($email != null){
		//create 2 new password boxes and allow user to overwrite existing password. 
		$displaydata ='<form id="resetpassword" action="resetpassword.php" method="post" >
	            Enter new password:<br>
	            <input type="hidden" name="token" value="'.$token.'"/>
	            <input type="password" name="password" class="form-control"><br> 
	            Confirm new password:<br>
	            <input type="password" class="form-control"><br> 
	            <input type="submit" value="Save Password" class="btn btn-default">
	            </form>
	            '; 
	    //echo $fields;

	}
	else {
		$displaydata = "<h3>This link has expired, please try <a href='login.php'>logging in</a> again.</h3>";
	}

}

else if (isset($_POST['password'])){

	$password =  $conn->real_escape_string($_POST['password']);

	$token =  $conn->real_escape_string($_POST['token']);

	$email = checkToken($conn, $token);
	if ($email != null){

		//if token is valid and not expired, update password with new password

		$password = password_hash($password, PASSWORD_DEFAULT);

		$sql = "UPDATE events_all.usertbl SET password = '$password' WHERE usertbl.email = '$email'";
		$result = mysqli_query($conn, $sql);

		//delete token now from db!

		$sql = "DELETE FROM tokentbl WHERE token ='$token'";
		$result = mysqli_query($conn, $sql);


		$displaydata = "<h3>Password was updated successfully. Please <a href='login.php'>log in</a> again.</h3>";
	}else {
		$displaydata = "<h3>This link has expired, please try <a href='login.php'>logging in</a> again.</h3>";
	}

}

//handle initial reset password case
else if (isset($_POST['email'])){
	
	$email =  $conn->real_escape_string($_POST['email']);
	//$p1 =  $_POST['password'];

	//$encrypass = password_hash($p1, PASSWORD_DEFAULT);
	$sql = "SELECT username FROM events_all.usertbl WHERE usertbl.email = '$email'";
	$result = mysqli_query($conn, $sql);

	if ($result->num_rows > 0) {
		//if email found, generate token and send email to link.
		$token = getToken();
		//send email with link to this token and user's email address. 
		//echo "token is ".$token;
		//current datetime + 24 hours
		$expiry = date_add(date_create(),date_interval_create_from_date_string("24 hours"));
		//in mysql format
		$expiredate = date_format($expiry,'Y-m-d H:i:s');
		//echo $expiredate;
		//need token table with email, token, expiry date of 24 hrs. 

		$sql = "INSERT INTO events_all.tokentbl (email, token, expiredate) VALUES ('$email', '$token', '$expiredate')";

		//check if command was successful
		if ($conn->query($sql) === TRUE) {
			$resetlink = "www.sikh.events/resetpassword.php?token=".$token;
			//send email link with this info
			$subject = "SikhEvents Password Reset.";
			$body = "WJKK WJKF,\n\nA password reset request has been recieved for your SikhEvents account. If you did not request this, you can ignore this email. Otherwise please visit the following link to create a new password : ".$resetlink;

			if (mail($email, $subject, $body)) 
			{
				$displaydata ="<h4>An email with instructions to reset your password has been sent to you.</h4>";
			} 
			else 
			{
				$displaydata = "<div class='alert alert-danger' role='alert'>Failed to send reset email link. Please <a href='resetpassword.php' try again.</a></div>";
			}

		}
		else {
			// echo "Error: " . $conn->error; 
		}

	}      
	else {
		//if email not found, don't tell user? security issues?
		$displaydata ="<h4>An email with instructions to reset your password has been sent to you.</h4>";
		// $displaydata =  "<div class='alert alert-danger' role='alert'>Invalid email, please try again. </div>";
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
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity=" sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    <link href="navbar.css" rel="stylesheet">
  </head>
  <body>
   <?php include('header.html'); ?>  
    <div style="padding:10px;">

   
      <div class="row">

        <div class="col-sm-6 col-md-4 col-2">

          <?php 
          echo $loginer;
          echo $displaydata; ?>

          <br>
          
          Don't have an account? Click <a href="newuser.php">here</a> to register.
        </div>
      
      </div>

    </div>
  </body>
</html>
