<?php
session_start();

if ($_SESSION['usertype'] != "admin"){
   header("Location: " . "http://sikh.events/login.php");
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
   <script type="text/javascript">
$(document).ready(function () {


    $.getJSON('locations.json', function (data) {
  var locations = data;
 for (var key in locations)
{
  locNames.push(key);
}
</script>

<style>
table.db-table      { border-right:1px solid #ccc; border-bottom:1px solid #ccc; }
table.db-table th   { background:#eee; padding:5px; border-left:1px solid #ccc; border-top:1px solid #ccc; }
table.db-table td   { padding:5px; border-left:1px solid #ccc; border-top:1px solid #ccc; }
</style>
</head>
<body>
<?php
 include('header.html');
// connect to the database
    include('config.php');

 $sql = "SELECT * FROM events_all.programtbl ORDER BY sd DESC";
    $result = mysqli_query($conn, $sql);

    $array = array();
//<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>';

echo '<table cellpadding="0" cellspacing="0" class="db-table">';
echo '<tr><th>ID</th><th>SD</th><th>ED</th><th>Title</th><th>Subtitle<th>Address</th><th>Phone</th><th>Desc</th><th>source</th><th>Approved</th><th>Moderate</th></tr>';
    while($row=mysqli_fetch_assoc($result))
    {
    	echo '<tr>';
     foreach($row as $key=>$value) {
       // for ($i = 0; $i< mysql_num_fields($row); $i++)
       
			echo '<td>',$value,'</td>';
			}
            //echo '<td><a href="submitedit.php?id=' . $row['id'] . '">Approve</a></td>';
         // echo '<td>',$row["id"],'</td>';
 //echo "<td> <input type='button' value='Approve'/> </td>";
 //echo "<td><form action='submitedit.php' method='POST'><input type='hidden' name='approveID' value='".$row["id"]."'/><input type='submit' name='submit-btn' value='View/Update Details' /><form></td>";
     echo "<td>";
      if ($row["approved"]==0){
  echo "<form action='submitedit.php' method='POST'><input type='hidden' name='id' value='".$row["id"]."'/><input type='hidden' name='action' value='approve'/><input type='submit' name='submit-btn' value='Approve' /></form>";
}
else {
   echo "<form action='submitedit.php' method='POST'><input type='hidden' name='id' value='".$row["id"]."'/><input type='hidden' name='action' value='disprove'/><input type='submit' name='submit-btn' value='Disprove' /></form>";
 }
    echo "<form action='submitedit.php' method='POST'><input type='hidden' name='id' value='".$row["id"]."'/><input type='hidden' name='action' value='delete'/><input type='submit' name='submit-btn' value='Delete' /></form>";
   echo "<form action='submitprogram.php' method='POST'><input type='hidden' name='id' value='".$row["id"]."'/><input type='hidden' name='action' value='edit'/><input type='submit' name='submit-btn' value='Edit' /></form></td></tr>";

	//echo '</tr>';
    }


echo '</table><br />';

//echo json_encode($array); 

 mysqli_close($conn);

?>

</body>
</html>