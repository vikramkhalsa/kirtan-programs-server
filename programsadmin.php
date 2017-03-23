<?php
session_start();

//if ($_SESSION['usertype'] != "admin"){
 //  header("Location: " . "http://sikh.events/login.php");
  // exit();
//}

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
table.db-table      { border-right:1px solid #ccc; border-bottom:1px solid #ccc; font-size:1.1em;}
table.db-table th   { background:#eee; padding:5px; border-left:1px solid #ccc; border-top:1px solid #ccc; }
table.db-table td   { padding:5px; border-left:1px solid #ccc; border-top:1px solid #ccc; }
</style>
</head>
<body>
<?php
 include('header.html');

//Add links to create new event, log out, reset password here. 
 //and description of what they can do. 

 echo "Welcome! Here you can view the past events you have added and modify them.  <br>
 If you click edit you can also clone the event to create a new one just like it with some of the values changed. <br><br>";

   echo "<form action='submitedit.php' method='POST'><input type='hidden' name='action' value='logout'/><input type='submit' name='submit-btn' value='Log Out' class='btn btn-default' /></form><br>";

// connect to the database
    include('config.php');

 $sql = "SELECT * FROM events_all.programtbl";

if ($_SESSION['usertype'] != "admin"){
  $user =$_SESSION['user'];
$sql = $sql." WHERE user='$user'";
}
 $sql = $sql." ORDER BY sd DESC";
    $result = mysqli_query($conn, $sql);

    $array = array();
//<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>';

echo '<table cellpadding="0" cellspacing="0" class="db-table">';
echo '<tr><th>ID</th><th>Start/End</th><th>Title</th><th>Location<th>Address</th><th>Zip</th><th>Phone</th><th>Description</th><th>type</th>';
if ($_SESSION['usertype'] == "admin"){
    echo '<th>User</th>';
  }
echo '<th>Moderate</th></tr>';
    while($row=mysqli_fetch_assoc($result))
    {
    	echo '<tr>';
     //foreach($row as $key=>$value) {
       // for ($i = 0; $i< mysql_num_fields($row); $i++)
      echo '<td>'.$row["id"].'</td><td>'.$row["sd"].'<br>'.$row["ed"].'</td><td>'.$row["title"].'</td><td>'.$row["subtitle"];
      if ($_SESSION['usertype'] == "admin")
        { echo "<form action='submitedit.php' method='POST'><input type='hidden' name='id' value='".$row["id"]."'/></br>
          <input type='hidden' name='action' value='saveloc'/><input type='submit' name='submit-btn' value='Save' class='btn btn-default'/></form>";
        }
      echo '</td><td>'.$row["address"].'</td><td>'. $row["zip"].'</td><td>'.$row["phone"].
      '</td><td>'.$row["description"].'</td><td>'.$row["type"].'</td>';
    if ($_SESSION['usertype'] == "admin"){
          echo '<td>'.$row["user"]."</td>";
        }
			
			
            //echo '<td><a href="submitedit.php?id=' . $row['id'] . '">Approve</a></td>';
         // echo '<td>',$row["id"],'</td>';
 //echo "<td> <input type='button' value='Approve'/> </td>";
 //echo "<td><form action='submitedit.php' method='POST'><input type='hidden' name='approveID' value='".$row["id"]."'/><input type='submit' name='submit-btn' value='View/Update Details' /><form></td>";
     echo "<td>";

if ($_SESSION['usertype'] == "admin")
    {

      if ($row["approved"]==0){
        echo "<form action='submitedit.php' method='POST'><input type='hidden' name='id' value='".$row["id"]."'/><input type='hidden' name='action' value='approve'/><input type='submit' name='submit-btn' value='Approve' class='btn btn-success'/></form>";
      }
      else {
        echo "<form action='submitedit.php' method='POST'><input type='hidden' name='id' value='".$row["id"]."'/><input type='hidden' name='action' value='disprove'/><input type='submit' name='submit-btn' value='Disprove' class='btn btn-warning' /></form>";
      }
    }
       echo "<form action='submitprogram.php' method='POST'><input type='hidden' name='id' value='".$row["id"]."'/><input type='hidden' name='action' value='edit'/><input type='submit' name='submit-btn' value='Edit' class='btn btn-default'/></form>";
   echo "<form action='submitedit.php' method='POST'><input type='hidden' name='id' value='".$row["id"]."'/><input type='hidden' name='action' value='delete'/><input type='submit' name='submit-btn' value='Delete' class='btn btn-danger' /></form></td></tr>";
    }


echo '</table><br />';

//echo json_encode($array); 

 mysqli_close($conn);

?>

</body>
</html>