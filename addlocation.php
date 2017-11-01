<?php

//addlocation.php
//Vikram Singh
//10/27/2017

//this file is responsible for making calls for adding new locations, updating existing locations, or deleting a location
//this typically occurs through a js ajax call to this page (like from submitprogram.php OR editlocation.php)
//it should also pass back an error message in case there are any exceptions or confirmation of success.

session_start();

if ($_SESSION['user'] == null){
 header("Location:" . "login.php");
 exit();
}
$user = $_SESSION['user'];
$locationid = null;


$errors         = array();      // array to hold validation errors
$data           = array();      // array to pass back data
include('config.php');


// validate the variables ======================================================
// if any of these variables don't exist, add an error to our $errors array

 if ($_POST['submit'] == 'delete'){

 		//check for admin
	 	if ($_SESSION['usertype'] != "admin"){
	 		
	 		$data['success'] = false;
   			$data['message']  = "Admin privelidges required to delete locations.";
   			echo json_encode($data);
   			return;

    	}

    	//make sure an id was passed in
    	if (!isset($_POST['locationid'])){
    		$data['success'] = false;
   			$data['message']  = "Location id is required.";
   			echo json_encode($data);
   			return;
    	}

    	//check for owner?
 		//check if row exists? 
 		//TBD: fix- says successful update/delete even when it doesn't exist!


		$id = $conn->real_escape_string($_POST['locationid']);

 		$sql = "DELETE FROM events_all.locationtbl WHERE locationid = '$id'";

 		if ($conn->query($sql) === TRUE) {
	    
		    // show a message of success and provide a true success variable
		    $data['success'] = true;
		    $data['message'] = 'Location Deleted Successfully!';

		} else {
		    // show a message of failure and provide an error message
		    $data['success'] = false;
		    $data['message'] = "Error: " . $conn->error;
		}

		mysqli_close($conn);
		echo json_encode($data);
	   	return;

 } else {

	if (empty($_POST['name']))
	    $errors['name'] = 'Name is required.';

	if (empty($_POST['address']))
	    $errors['address'] = 'Address is required.';

	if (empty($_POST['zip']))
	    $errors['zip'] = 'Postal Code is required.';

	if (empty($_POST['city']))
	    $errors['city'] = 'City is required.';

	if (empty($_POST['state']))
	    $errors['state'] = 'State is required.';

	if (empty($_POST['region']))
	    $errors['region'] = 'Region is required.';

	// return a response ===========================================================

	// if there are any errors in our errors array, return a success boolean of false
	if (!empty($errors)) {

	    // if there are items in our errors array, return those errors
	    $data['success'] = false;
	    $data['errors']  = $errors;
	    echo json_encode($data);
	   	return;

	}

	// if there are no errors process our form, then return a message

	// DO ALL YOUR FORM PROCESSING HERE
	// THIS CAN BE WHATEVER YOU WANT TO DO (LOGIN, SAVE, UPDATE, WHATEVER)

	//get values from POST and escape them
	$name = $conn->real_escape_string($_POST['name']);
	$address = $conn->real_escape_string($_POST['address']);
	$city = $conn->real_escape_string($_POST['city']);
	$state = $conn->real_escape_string($_POST['state']);
	$zip = $conn->real_escape_string($_POST['zip']);
	$regionid = $conn->real_escape_string($_POST['region']);

	$public = $conn->real_escape_string($_POST['public']);//check if not public, then owner is user, otherwise blank
	$owner = null;
	if($public == "false")
		$owner = $user;

	//post values to database
	//Add new
	$sql = "INSERT INTO events_all.locationtbl (name, address, city, state, zip, addedby, privateowner, region)
	VALUES ('$name', '$address', '$city', '$state', '$zip', '$user', '$owner', '$regionid')";

	//if there is a location id, then just update the existing record instead of adding new
	if ( isset($_POST['locationid'])  && is_numeric($_POST['locationid']) ){
		
		//TBD add manager support

		$id = $_POST['locationid'];

		$sql = "SELECT * FROM events_all.locationtbl WHERE locationtbl.locationid = '$id'";

	    if ($result = mysqli_query($conn, $sql)){

			if ($result->num_rows < 1){
				$data['success'] = false;
				$data['message'] = "Error: " . "Location not found.";
				echo json_encode($data);
				return;
			}

			$arr = $result->fetch_array();

			$addedby = htmlspecialchars($arr["addedby"]);
			$privateowner = htmlspecialchars($arr["privateowner"]);

			if ($addedby != $user AND $_SESSION['usertype'] != "admin"){
				$data['success'] = false;
				$data['message'] = "Error: " . "You don't have permission to edit this location.";
				echo json_encode($data);
				return;
			}

			//set private owner to creator
			if($public == "false")
				$owner = $addedby;
			else 
				$owner = null;

			$sql = "UPDATE events_all.locationtbl SET name = '$name', address = '$address', city = '$city', state = '$state', zip = '$zip', privateowner = '$owner', region = '$regionid' WHERE locationid = '$id'";

	   	} else {

		    $data['success'] = false;
		    $data['message'] = "Error: " . $conn->error;
		    echo json_encode($data);
		   	return;
	   	}

	 }


	if ($conn->query($sql) === TRUE) {
	    
	    // show a message of success and provide a true success variable
	    $data['success'] = true;
	    $data['message'] = 'Location Added/Updated Successfully!';
	    $sql = "SELECT LAST_INSERT_ID();";
	    $result = mysqli_query($conn, $sql);

	    while($row=mysqli_fetch_assoc($result)){
	        $data['locationid'] = $row["LAST_INSERT_ID()"];
	    }

	} else {
	    // show a message of failure and provide an error message
	    $data['success'] = false;
	    $data['message'] = "Error: " . $conn->error;
	}

	mysqli_close($conn);
	// return all our data to an AJAX call
	echo json_encode($data);

}


