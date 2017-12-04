<?php 
//submitedit.php
//Vikram Singh
// 12016
//internal endpoint for admin dashboard page, handles actions like approve, disprove, delete events. 

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

		//only admin can approve/disprove
		if ($_SESSION['usertype'] == "admin")
		{

			if ($action == "approve"){
				$sql = "UPDATE events_all.programtbl SET approved='1' WHERE id = '$id'";
			}
			elseif ($action == "disprove"){
				$sql = "UPDATE events_all.programtbl SET approved='0' WHERE id = '$id'";
			}
		}

		if ($action == "delete"){ 
			$sql = "DELETE FROM events_all.programtbl WHERE id = '$id'";

			//admin can delete anything, other users can only delete event if its theirs
			if ($_SESSION['usertype'] != "admin"){
				$sql = $sql . " AND user = '$user'";
			}
		}	
		

		if ($conn->query($sql) === TRUE) {
	   // echo "New record created successfully";
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}

		if ($action == "approve"){
			$sql = "SELECT programtbl.user, programtbl.title, programtbl.sd, programtbl.ed, programtbl.description, usertbl.email FROM events_all.programtbl  JOIN usertbl on programtbl.user = usertbl.username where programtbl.id ='$id'";
			
				//get values of this event
				$result = mysqli_query($conn, $sql);
				$name = "";
				$email = "";
				$title="";
				$sd="";
				$ed = "";
				$desc = "";

				while($row=mysqli_fetch_assoc($result)){
					$name = $row["user"];
					$email = $row["email"];
					$title = $row["title"];
					$sd = $row["sd"];
					$ed = $row["ed"];
					$desc = $row["description"];
				}
				//send email to user who submitted it;
				$subject = "Your event has been approved!";
				$body =  sprintf("Waheguru Ji Ka Khalsa, Waheguru Ji Ki Fateh,
					\n\nCongratulations, the following event has been approved: 
					\n\n Title: %s \n Start: %s\n End: %s\n  Description: %s 
					\n\n You can now share the event using this link: http://sikh.events/eventdetails.php?id=%s
					\n To Edit, Clone, or Delete, please visit: http://sikh.events/programsadmin.php",
					$title, $sd,$ed, $desc, $id);

				mail($email, $subject, $body, "From: vsk@sikh.events");
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