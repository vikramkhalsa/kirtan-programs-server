<?php 
 // if there are any errors, display them
 if ($error != '')
 {
 echo '<div style="padding:4px; border:1px solid red; color:red;">'.$error.'</div>';
 }


  // check if the form has been submitted. If it has, process the form and save it to the database
 if (isset($_POST['id']))
 { 
	echo $_POST['id'];
 // confirm that the 'id' value is a valid integer before getting the form data
 	if (is_numeric($_POST['id']))
 	{
 // get form data, making sure it is valid
 	$id = $_POST['id'];
	 echo $id;
 //$firstname = mysql_real_escape_string(htmlspecialchars($_POST['firstname']));
 //$lastname = mysql_real_escape_string(htmlspecialchars($_POST['lastname']));
 
 // check that firstname/lastname fields are both filled in
 //if ($firstname == '' || $lastname == '')
 //{
 // generate error message
 //$error = 'ERROR: Please fill in all required fields!';
 
 //error, display form
 //renderForm($id, $firstname, $lastname, $error);
 //}
 //else
 //{
 // save the data to the database

// connect to the database
	include('config.php');

	$action = $_POST["action"];

	if ($action == "approve"){
	$sql = "UPDATE programs_all.programtbl SET approved='1' WHERE id = '$id'";
	}
	elseif ($action == "disprove"){
			$sql = "UPDATE programs_all.programtbl SET approved='0' WHERE id = '$id'";
	}
	elseif ($action == "delete"){
	$sql = "DELETE FROM programs_all.programtbl WHERE id = '$id'";
	}

	if ($conn->query($sql) === TRUE) {
	    echo "New record created successfully";
	} else {
	    echo "Error: " . $sql . "<br>" . $conn->error;
	}
	 mysqli_close($conn);
 // once saved, redirect back to the view page
 header("Location: programsadmin.php"); 
}
}
else {
	echo 'error';
}

 ?>