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
	<div class="container">

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
		// confirm that the 'id' value is a valid integer before getting the form data
		if (is_numeric($_POST['id']))
		{
			// get form data, making sure it is valid
			$id = $_POST['id'];
		}
		else {
			//echo "<div class='alert alert-danger' role='alert'>Error:Invalid ID </div>";
		}
	}


	//Do some server side validation first. 
	$errors = array();      // array to hold validation er
	
	if (empty($_POST['title']))
		$errors['title'] = 'Title is required';
	if (empty($_POST['locationid']))
		$errors['location'] = 'Location ID is required';
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

	// connect to the database
	include('config.php');

	$clone = $_POST["clone"];
	$title = $conn->real_escape_string($_POST["title"]);
	$phone = $conn->real_escape_string($_POST["phone"]);
	$sd = $conn->real_escape_string($_POST["sd"]);
	// $sd1 = strtotime($sd);
	$ed = $conn->real_escape_string($_POST["ed"]);
	// $ed1 = strtotime($ed);

	$type = $conn->real_escape_string($_POST["type"]);
	//$source = $conn->real_escape_string($_POST["source"]);
	$description = $conn->real_escape_string($_POST["description"]);
	$imageurl = $conn->real_escape_string($_POST["imageurl"]);
	$siteurl = $conn->real_escape_string($_POST["siteurl"]);
	$locationid = $conn->real_escape_string($_POST["locationid"]);
	$rrule= $conn->real_escape_string($_POST["repeat"]);
	if (isset($_POST["allday"]))
		$allday = 1;
	else
		$allday = 0;

	if ($rrule ==""){
		$rrule = null;
	}

	//handle image upload ------------------
	if (isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]['error'] != UPLOAD_ERR_NO_FILE){
		$target_dir = "eventimages/2017/";
		$uploadOk = 1;;
		$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
		$uploadOk = 1;
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		// Check if image file is a actual image or fake image
		if(isset($_POST["submit"])) {
		    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		    if($check !== false) {
		        echo "File is an image - " . $check["mime"] . ".";
		        $uploadOk = 1;
		    } else {
		        echo "File is not an image.";
		        $uploadOk = 0;
		    }
		}

		// Check if file already exists
		if (file_exists($target_file)) {
		    echo "File already exists.";
		    $uploadOk = 0;
		} 

		 // Check file size, max 5mb
		if ($_FILES["fileToUpload"]["size"] > 5000000) {
		    echo "File is too large. Images must be 5mb or less.";
		    $uploadOk = 0;
		} 

		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
		    echo "Only JPG, JPEG, PNG & GIF files are allowed.";
		    $uploadOk = 0;
		} 

		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
		    echo "Sorry, your file was not uploaded. Please correct any errors and try again. ";
		// if everything is ok, try to upload file
		} else {
		    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
		        echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
		        $imageurl = $target_file;
		    } else {
		        echo "Sorry, there was an error uploading your file.";
		    }
		}
	}
	//end image upload------------

	//use common functions to update or submit event and show response. 
	include("functions.php");

	if ($id == '' or $clone == "Clone")
		$result = createEvent($title, $phone, $sd, $ed, $type, $description, $imageurl, $siteurl, $locationid, $rrule, $user, $allday);
	else 
		$result = updateEvent($id, $title, $phone, $sd, $ed, $type, $description, $imageurl, $siteurl, $locationid, $rrule, $user, $allday);
		
	if (strpos($result, 'Error') === false){
		echo "<div class='alert alert-success' role='alert'>".$result."
		<br>
		<a href='programsadmin.php'>Back to dashboard</a>
		<br>
		<a href='submitprogram.php'>Submit another Event</a>
		</div>";
	}
	else {
		echo "<div class='alert alert-danger' role='alert'>".$result."</div>";
	}

	?>
	<br>

	</div>

</body>
</html>