<?php
//usersadmin.php
//Vikram Singh
//11/15/2017
//Provides a centralized dashboard to view all users
//Admin only
//TBA: Delete/manage users?


session_start();

if ($_SESSION['user'] == null){
 header("Location:" . "login.php");
 exit();
}

?>



<html>

<head>

<meta name="viewport" content="user-scalable=yes, width=device-width" />
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<link href="navbar.css" rel="stylesheet">

<style>
table.db-table      { border-right:1px solid #ccc; border-bottom:1px solid #ccc; font-size:1.1em;}
table.db-table th   { background:#eee; padding:5px; border-left:1px solid #ccc; border-top:1px solid #ccc; }
table.db-table td   { padding:5px; border-left:1px solid #ccc; border-top:1px solid #ccc; }
</style>

</head>


<body>

<?php include('header.html'); ?>

<div style="padding:10px;">
    Welcome! Here you can view all the users and their information.  <br>

    <br>

<?php 

	include('config.php');
  
  if ($_SESSION['usertype'] != "admin"){
         echo "<h4> You do not have permissions to view this page.  </h4>";
  return;
  }


	$sql = "SELECT * FROM events_all.usertbl";

	//Show all locations for admin, otherwise only show locations that this user has created. (For now)
	//may want to show locations to 'managers' in the future, not just creators.  

	$result = mysqli_query($conn, $sql);

	$array = array();
	WHILE ($row= mysqli_fetch_assoc($result)){
		$array[] = $row;
	}

	mysqli_close($conn);

  if (count($array) < 1){
    echo "<h4> No users available.  </h4>";
    return;
  }

	//output in table format

	echo '<table cellpadding="0" cellspacing="0" class="db-table">';
	echo '<tr> 
  		<th>Name</th>
  		<th>Email</th>
  		</tr>';

  	foreach ($array as $user){
  		echo '<tr>';
  		echo '<td>'.$user['username'].'</td>
  			  <td>'.$user['email'].'</td>
  			  </tr>';
  	}

	echo '</table>';


?>

</div>

</body>

</html>