<?php 

session_start();

 // if there are any errors, display them
 if ($error != '')
 {
 echo '<div style="padding:4px; border:1px solid red; color:red;">'.$error.'</div>';
 }

 if ($_SESSION['user'] == null){
   header("Location:" . "login.php");
   exit();
}
$user = $_SESSION['user'];

  // check if the form has been submitted. If it has, process the form and save it to the database
 if (isset($_POST['id']))
 { 
	//echo $_POST['id'];
 // confirm that the 'id' value is a valid integer before getting the form data
 	if (is_numeric($_POST['id']))
 	{
 // get form data, making sure it is valid
 	$id = $_POST['id'];
	 //echo $id;
 //$firstname = mysql_real_escape_string(htmlspecialchars($_POST['firstname']));
 //$lastname = mysql_real_escape_string(htmlspecialchars($_POST['lastname']));
 

// connect to the database
	include('config.php');

	$action = $_POST["action"];


if ($_SESSION['usertype'] == "admin")
    {

	if ($action == "approve"){
	$sql = "UPDATE events_all.programtbl SET approved='1' WHERE id = '$id'";
	}
	elseif ($action == "disprove"){
			$sql = "UPDATE events_all.programtbl SET approved='0' WHERE id = '$id'";
	}
	elseif ($action == "saveloc"){
	$sql = "SELECT subtitle, address, phone, zip FROM events_all.programtbl where id='$id'";
	//now insert into location table
	$result = mysqli_query($conn, $sql);

	    while($row=mysqli_fetch_assoc($result)){
	    	$name = $row["subtitle"];
	    	$addr = $row["address"];
	    	$phone = $row["phone"];
	    	$zip = $row["zip"];
	    	//echo $name.$addr.$phone;//.$zip;
			$sql = "INSERT INTO events_all.locationtbl (name, address, phone, zip)
			VALUES ('$name','$addr','$phone','$zip')";
	    }
	}
}
if ($action == "delete"){
	$sql = "DELETE FROM events_all.programtbl WHERE id = '$id'";
}	
	

if ($conn->query($sql) === TRUE) {
	   // echo "New record created successfully";
	} else {
	    echo "Error: " . $sql . "<br>" . $conn->error;
	}
	 mysqli_close($conn);
 // once saved, redirect back to the view page
 header("Location: programsadmin.php"); 
}
}
else {
if (isset($_POST['action'])){
	$action = $_POST["action"];
	if ($action =="logout"){
$_SESSION = array(); 
session_destroy();
setcookie(session_name(),'',1);

header("Location:" . "login.php");
   exit();
	}
}else{

	echo 'error';
}
}

 ?>