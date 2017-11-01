<?php 
//commitprogram.php
//Vikram Singh
//10/31/2017
//This file receives the post data to create a new event and actually inserts it into the database
//it performs a little validation and shows error or success messages. It also sends the moderation request. 
//This will eventually be used as an API endpoint so it will need to return a json message rather than html. 

session_start();
if ($_SESSION['user'] == null){

	header("Location:" . "login.php");
	exit();
}
	$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="user-scalable=yes, width=device-width" />
	<script src="datetimepicker_css.js"></script>
	<meta name="viewport" content="user-scalable=yes, width=device-width" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	<link href="navbar.css" rel="stylesheet">
</head>
<body>
<?php 
include('header.html');
?>
	<div style="padding:10px">

	You submitted the following: <br><br>

	<?php 

	echo "Title: ",$_POST["title"]; 
	print "<br>";
	echo "Location: ",$_POST["subtitle"]; 
	print "<br>";
	//echo "Address: ",$_POST["address"]; 
	//print "<br>";
	echo "Phone Number: ",$_POST["phone"];
	print "<br>"; 
	echo "Start: ",$_POST["sd"]; 
	print "<br>";
	echo "End: ",$_POST["ed"]; 
	print "<br>";
	echo "Type: ",$_POST["type"];
	print "<br>";
	//echo "Zip Code: ",$_POST["zip"];
	//print "<br>";
	//echo "Source: ",$_POST["source"]; 
	//print "<br>";
	echo "Description: ",$_POST["description"];
	print "<br><br>";

	//echo "Repeat",$_POST['repeat'];

	$id="";

	if (isset($_POST['id'])){ 
		//echo $_POST['id'];
		// confirm that the 'id' value is a valid integer before getting the form data
		if (is_numeric($_POST['id']))
		{
			// get form data, making sure it is valid
			$id = $_POST['id'];
		}
	}
	// connect to the database
	include('config.php');

	//Do some server side validation first. 
	$errors = array();      // array to hold validation er
	
	if (empty($_POST['title']))
		$errors['title'] = 'Title is required';
	
	if (empty($_POST['subtitle']))
		$errors['location'] = 'Location is required';
	
	if (empty($_POST['sd']))
		$errors['startdate'] = 'Start Date/Time is required';
	

	//validate phone number?
	//validate type

	// if there are any errors in our errors array, show them (for now.. ultimately make it more like an API and return them)
	if (!empty($errors)) {

   		// if there are items in our errors array, return those errors
		echo "<div class='alert alert-danger' role='alert'>Error: ";
		foreach ($errors as $err){
			echo $err."<br>";
		}
		print "<br>Please try again.</div>";
	   	return;

	}


	$clone = $_POST["clone"];
	$title = $conn->real_escape_string($_POST["title"]);
	$phone = $conn->real_escape_string($_POST["phone"]);
	$sd = $conn->real_escape_string($_POST["sd"]);
	$sd1 = strtotime($sd);
	$ed = $conn->real_escape_string($_POST["ed"]);
	$ed1 = strtotime($ed);

	//check if end date is before start date, don't allow
	if ($sd1 > $ed1) {
		echo "<div class='alert alert-danger' role='alert'>Error: End time must be later than start time.";
		print "<br>Please try again.</div>";
	   	return;
	}

	$type = $conn->real_escape_string($_POST["type"]);
	//$source = $conn->real_escape_string($_POST["source"]);
	$description = $conn->real_escape_string($_POST["description"]);
	$imageurl = $conn->real_escape_string($_POST["imageurl"]);
	$siteurl = $conn->real_escape_string($_POST["siteurl"]);
	$locationid = $conn->real_escape_string($_POST["locationid"]);
	$rrule= $conn->real_escape_string($_POST["repeat"]);
	if ($rrule ==""){
		$rrule = null;
	}




	if ($id == "" or ($clone=="Clone")){

		$sql = "INSERT INTO events_all.programtbl (title, locationid, phone, sd, ed, user, description, type, rrule, imageurl, siteurl)
		VALUES ('$title', '$locationid','$phone', '$sd','$ed', '$user', '$description', '$type', '$rrule', '$imageurl', '$siteurl')";
		if ($conn->query($sql) === TRUE) 
		{
			echo "<div class='alert alert-success' role='alert'>New event submitted successfully! <br>";
			
			$to = "vsk@sikh.events";
			$subject = "New BayAreaKirtan Program Submitted";
			$body =  sprintf("WJKK WJKF,\n\nThe following program has been submitted: 
				\n\n Title: %s \n Location: %s \n Phone: %s\n Start: %s\n End: %s\n  User: %s\n Description: %s \n\n 
				To moderate, visit: http://sikh.events/programsadmin.php",
				$title, $locationid, $phone, $sd,$ed, $user, $description );

			if (mail($to, $subject, $body)) 
			{
				echo("<p>Your submission has been sent for moderation.</p>");
			} 
			else 
			{
				echo("<p>Failed to send moderation request.</p>");
			}

			echo "</div>";
		}
		else 
		{
			echo "<div class='alert alert-danger' role='alert'>Error: ". $conn->error;
			print "<br>Please try again.</div>";
		}

	} 
	else 
	{
		$sql = "UPDATE events_all.programtbl SET title ='$title', locationid='$locationid', phone ='$phone', 
		sd = '$sd', ed ='$ed', description ='$description', type ='$type', rrule='$rrule', imageurl='$imageurl', siteurl='$siteurl' WHERE id = '$id'";
		if ($conn->query($sql) === TRUE) {
			echo "<div class='alert alert-info' role='alert'>Event updated successfully!<br></div>";
		} else {
			echo "<div class='alert alert-danger' role='alert'> Error: " . $sql . "<br>" . $conn->error."</div>";
		}

	}

	//} else {
	//	echo "Invalid Password.";
	//}

	?>
	<br>

	</div>

</body>
</html>