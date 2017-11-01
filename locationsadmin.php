<?php
//locationsadmin.php
//Vikram Singh
//10/26/2017
//Provides a centralized dashboard to view and manage all locations in the system (for admins) or 
//all locations that the current user has added. 
//Shows links to the editlocations.php page with the appropriate location id, for editing or deletion. 
//eventually need to show pages to managers, regardless of who created it. 


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
    Welcome! Here you can view the locations you have added and modify them.  <br>

    <a href="editlocation.php">Add New Location</a><br><br>


<?php 

	include('config.php');
	$regions = "all";

	if(isset($_GET['regions'])){
	  $regions = $_GET['regions'];
	}
	//get all locations

	$sql = "SELECT * FROM events_all.locationtbl";

	//Show all locations for admin, otherwise only show locations that this user has created. (For now)
	//may want to show locations to 'managers' in the future, not just creators.  
  	if ($_SESSION['usertype'] != "admin"){
        $user =$_SESSION['user'];
        $sql = $sql." WHERE addedby ='$user'";
    }

	$result = mysqli_query($conn, $sql);

	$array = array();
	WHILE ($row= mysqli_fetch_assoc($result)){
		$array[] = $row;
	}

	mysqli_close($conn);

  if (count($array) < 1){
    echo "<h4> No locations available. Please add a location first using the link above or try submitting a new event. The location you are looking for may already exist. </h4>";
    return;
  }

	//output in table format

	echo '<table cellpadding="0" cellspacing="0" class="db-table">';
	echo '<tr> 
  		<th>Name</th>
  		<th>Address</th>
  		<th>City</th>
  		<th>State</th>
  		<th>Zip</th>
  		<th>Region</th>
  		<th>Added By</th>
  		<th>Private Owner</th>
  		<th>Manage</th>

  		</tr>';

  	foreach ($array as $loc){
  		echo '<tr>';
  		echo '<td>'.$loc['name'].'</td>
  			  <td>'.$loc['address'].'</td>
  			  <td>'.$loc['city'].'</td>
  			  <td>'.$loc['state'].'</td>
  			  <td>'.$loc['zip'].'</td>
  			  <td>'.$loc['region'].'</td>
  			  <td>'.$loc['addedby'].'</td>
  			  <td>'.$loc['privateowner'].'</td>
  			  <th><a href="editlocation.php?id='.$loc['locationid'].'">Edit</a></th>
  			  </tr>';
  	}

	echo '</table>';

	//echo json_encode($array); 

?>

</div>

</body>

</html>