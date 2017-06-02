<?php 
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
		if (isset($_POST['id']))
		{ 
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

		$clone = $_POST["clone"];
		$title = $conn->real_escape_string($_POST["title"]);
//$subtitle = $conn->real_escape_string($_POST["subtitle"]);
//$address = $conn->real_escape_string($_POST["address"]);
		$phone = $conn->real_escape_string($_POST["phone"]);
		$sd = $conn->real_escape_string($_POST["sd"]);
//$sd = date ("Y-m-d H:i:s", strtotime($sd));
		$ed = $conn->real_escape_string($_POST["ed"]);
//$ed = date ("Y-m-d H:i:s", strtotime($sd));
		$type = $conn->real_escape_string($_POST["type"]);
//$zip = $conn->real_escape_string($_POST["zip"]);
//$source = $conn->real_escape_string($_POST["source"]);
		$description = $conn->real_escape_string($_POST["description"]);
		$locationid = $conn->real_escape_string($_POST["locationid"]);
		$rrule= $conn->real_escape_string($_POST["repeat"]);
		if ($rrule ==""){
			$rrule = null;
		}


		if ($id == "" or ($clone=="Clone")){
//need to check if values are blank, validate form data in submit program page??
			$sql = "INSERT INTO events_all.programtbl (title, locationid, phone, sd, ed, user, description, type, rrule)
			VALUES ('$title', '$locationid','$phone', '$sd','$ed', '$user', '$description', '$type', '$rrule')";
			if ($conn->query($sql) === TRUE) 
			{
				echo "<div class='alert alert-success' role='alert'>New event submitted successfully! <br>";
				
				$to = "vsk@sikh.events";
				$subject = "New BayAreaKirtan Program Submitted";
				$body =  sprintf("WJKK WJKF,\n\nThe following program has been submitted: 
					\n\n Title: %s \n Subtitle: %s \n Phone: %s\n Start: %s\n End: %s\n  User: %s\n Description: %s \n\n 
					To moderate, visit: http://sikh.events/programsadmin.php",
					$title, $subtitle, $phone, $sd,$ed, $user, $description );

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
			sd = '$sd', ed ='$ed', description ='$description', type ='$type', rrule='$rrule' WHERE id = '$id'";
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