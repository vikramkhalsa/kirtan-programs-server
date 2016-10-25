<?php include("/home4/dharamkh/public_html/vsk/kirtanapp/password_protect.php"); ?>

<style>
table.db-table      { border-right:1px solid #ccc; border-bottom:1px solid #ccc; }
table.db-table th   { background:#eee; padding:5px; border-left:1px solid #ccc; border-top:1px solid #ccc; }
table.db-table td   { padding:5px; border-left:1px solid #ccc; border-top:1px solid #ccc; }
</style>

<?php

// connect to the database
    include('config.php');

 $sql = "SELECT * FROM dharamkh_programs.programtbl";
    $result = mysqli_query($conn, $sql);

    $array = array();
echo ' <head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
</head> ';
echo '<h3>Bay Area Kirtan Programs</h1> </br>';
echo "<a href='submitprogram.html'>Add new program </a></br>";
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
  echo "<td><form action='submitedit.php' method='POST'><input type='hidden' name='id' value='".$row["id"]."'/><input type='hidden' name='action' value='approve'/><input type='submit' name='submit-btn' value='Approve' /></form>";
   echo "<form action='submitedit.php' method='POST'><input type='hidden' name='id' value='".$row["id"]."'/><input type='hidden' name='action' value='disprove'/><input type='submit' name='submit-btn' value='Disprove' /></form>";
    echo "<form action='submitedit.php' method='POST'><input type='hidden' name='id' value='".$row["id"]."'/><input type='hidden' name='action' value='delete'/><input type='submit' name='submit-btn' value='Delete' /></form>";
   echo "<form action='submitprogram.php' method='POST'><input type='hidden' name='id' value='".$row["id"]."'/><input type='hidden' name='action' value='edit'/><input type='submit' name='submit-btn' value='Edit' /></form></td></tr>";

	//echo '</tr>';
    }


echo '</table><br />';

//echo json_encode($array); 

 mysqli_close('$conn');

?>