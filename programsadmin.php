<?php
//programsadmin.php
//Vikram Singh
//11/1/2017
//Provides a centralized dashboard for viewing all events a user has submitted as well as being able to edit and delete them.
//Admins have access to all events
//also provides links to other functionality such as logging out, resetting password, and managing locations. 

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
        
        .visible-lg{
          display:none;
        }

        @media screen and (min-width: 400px) {
        
          .visible-lg{
            display:initial;
          }

        }



      </style>
    </head>
    <body>

     <?php include('header.html');?>

    	<div style="padding:10px;">
      Welcome! Here you can view the past events you have added and modify them.  <br>
      If you click edit you can also clone the event to create a new one just like it with some of the values changed. <br><br>

      <form action='submitedit.php' method='POST'>
      	<input type='hidden' name='action' value='logout'/>
      	<input type='submit' name='submit-btn' value='Log Out' class='btn btn-default' />
      </form>


      <!--Add links to create new event, log out, reset password here. 
      and description of what they can do.  -->
      <a href="resetpassword.php"> Reset Password </a><br>
      <a href="locationsadmin.php"> Manage My Locations </a><br><br>

 	<?php 
	// connect to the database
    include('config.php');

    include('functions.php');
    
    if ($_SESSION['usertype'] == "admin")
    {
      $result = getEvents();
    }
    else {
      $result = getEvents($_SESSION['user']);
    }


    $array = array();
    //<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>';

    echo '<table cellpadding="0" cellspacing="0" class="db-table">';
    echo '<tr><th>ID</th><th>Start/End</th><th>Title</th><th>Location</th><th class="visible-lg">Address</th><th class="visible-lg">Phone</th><th class="visible-lg">Description</th><th>Type</th>';
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
      { 
        echo '<br><a href="editlocation.php?id='.$row['locationid'].'">Edit</a>';
      }
      echo '</td><td class="visible-lg">'.$row["address"].'</td><td class="visible-lg">'.$row["phone"].
      '</td><td class="visible-lg">'.substr($row["description"],0,200).'...</td><td>'.$row["type"].'</td>';
      if ($_SESSION['usertype'] == "admin"){
        echo '<td>'.$row["user"]."</td>";
      }

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
      else {
        if ($row["approved"] == 1){
           echo "<span>Status:<br><em>Approved</em></span>";
        }
        else {
          echo "<span>Status:<br><em>Pending</em></span>";
        }
      }

      echo "<form action='submitprogram.php' method='POST'><input type='hidden' name='id' value='".$row["id"]."'/><input type='hidden' name='action' value='edit'/><input type='submit' name='submit-btn' value='Edit' class='btn btn-default'/></form>";
      echo "<form action='submitedit.php' method='POST'><input type='hidden' name='id' value='".$row["id"]."'/><input type='hidden' name='action' value='delete'/><input type='submit' name='submit-btn' value='Delete' class='btn btn-danger' /></form></td></tr>";
    }


    echo '</table><br />';

    //echo json_encode($array); 

    mysqli_close($conn);

  ?>
</div>

</body>
</html>