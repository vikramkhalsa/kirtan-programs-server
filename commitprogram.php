<!DOCTYPE html>
<html>
<body>



Welcome <br>

<?php 

echo "Title: ",$_POST["title"]; 
print "<br>";
echo "Subtitle: ",$_POST["subtitle"]; 
print "<br>";
echo "Address: ",$_POST["address"]; 
print "<br>";
echo "Phone Number: ",$_POST["phone"];
print "<br>"; 
echo "Start: ",$_POST["sd"]; 
print "<br>";
echo "End: ",$_POST["ed"]; 
print "<br>";
echo "Source: ",$_POST["source"]; 
print "<br>";
echo "Description: ",$_POST["description"];
print "<br>";

$id="";
if (isset($_POST['id']))
 { 
	echo $_POST['id'];
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
$subtitle = $conn->real_escape_string($_POST["subtitle"]);
$address = $conn->real_escape_string($_POST["address"]);
$phone = $conn->real_escape_string($_POST["phone"]);
$sd = $conn->real_escape_string($_POST["sd"]);
$ed = $conn->real_escape_string($_POST["ed"]);
$source = $conn->real_escape_string($_POST["source"]);
$description = $conn->real_escape_string($_POST["description"]);

$auth = $_POST["password"];



	if ($id == "" or ($clone=="Clone")){

	$sql = "INSERT INTO dharamkh_programs.programtbl (title, subtitle, address, phone, sd, ed, source, description)
	VALUES ('$title', '$subtitle', '$address', '$phone', '$sd','$ed', '$source', '$description')";
	if ($conn->query($sql) === TRUE) {
	    echo "\nNew record created successfully";
	} else {
	    echo "Error: " . $sql . "<br>" . $conn->error;
	}
$to = "vikramkhalsa@hotmail.com";
$subject = "New BayAreaKirtan Program Submitted";
$body =  sprintf("WJKK WJKF,\n\nThe following program has been submitted: \n\n Title: %s \n Subtitle: %s \n $address %s\n  Phone: %s\n Start: %s\n End: %s\n  Source: %s\n Description: %s \n\n To moderate, visit: http://vikramkhalsa.com/kirtanapp/programsadmin.php",$title, $subtitle, $address, $phone, $sd,$ed, $source, $description );

 if (mail($to, $subject, $body)) {
   echo("<p>Your submission has been sent for moderation.</p>");
  } else {
   echo("<p>Failed to send moderation request.</p>");
  }

} else {
	$sql = "UPDATE dharamkh_programs.programtbl SET title ='$title', subtitle ='$subtitle', address = '$address', phone ='$phone', sd = '$sd', ed ='$ed', source = '$source', description ='$description', approved = 1 WHERE id = '$id'";
		if ($conn->query($sql) === TRUE) {
	    echo "\nRecord updated successfully";
	} else {
	    echo "Error: " . $sql . "<br>" . $conn->error;
	}

}

//} else {
//	echo "Invalid Password.";
//}

?>
<br>




</body>
</html>