<!DOCTYPE html>
<html>
<head>
<title>Event Details</title>
    <meta name="viewport" content="user-scalable=yes, width=device-width" />
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
 <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
 <link href="navbar.css" rel="stylesheet">

</head>
<body>

<?php

include('header.html');

if(isset($_GET['id'])){
$id = $_GET['id'];

include('config.php');


    $sql = "SELECT * FROM events_all.programtbl WHERE programtbl.id = $id";  
    $result = mysqli_query($conn, $sql);
    $array = array();
    $event = null;

    while($row=mysqli_fetch_assoc($result))
    {

        if ($row['rrule']!=null){
          //echo $row['title'];
          $array = array_merge($array,getRecurEvents($row));
        }else {

         $array[] = $row;
       }
    }

$array = $array[0];

echo "Title: ",$array["title"]; 
print "<br>";
echo "Subtitle: ",$array["subtitle"]; 
print "<br>";
echo "Address: ",$array["address"]; 
print "<br>";
echo "Phone Number: ",$array["phone"];
print "<br>"; 
echo "Start: ",$array["sd"]; 
print "<br>";
echo "End: ",$array["ed"]; 
print "<br>";
echo "Type: ",$array["type"];
print "<br>";
echo "Zip Code: ",$array["zip"];
print "<br>";
//echo "Source: ",$array["source"]; 
//print "<br>";
echo "Description: ",$array["description"];
print "<br><br>";
}

//^^^ OR add to API page and use that to get the program?

?>


 </body>
</html>