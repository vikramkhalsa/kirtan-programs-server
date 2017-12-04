<?php
//getlocations.php
//Vikram Singh
//8/18/2017
//Provides an API endpoint to return all the regions in the DB for dynamically listing in mobile apps
//optionally filter to only give current regions (which have active events in them)

// connect to the database
include('config.php');
$regions = "all";

if(isset($_GET['regions'])){
  $regions = $_GET['regions'];
}

$sql = "SELECT DISTINCT regiontbl.name, regiontbl.regionid FROM events_all.regiontbl";

if($regions=="current"){

$sql = "SELECT DISTINCT regiontbl.name, regiontbl.regionid FROM events_all.programtbl 
 JOIN locationtbl on programtbl.locationid = locationtbl.locationid
 JOIN  regiontbl on locationtbl.region = regiontbl.regionid
 WHERE programtbl.ed >= DATE(NOW()) AND programtbl.approved=1";
}

 //$sql = "SELECT DISTINCT subtitle FROM events_all.programtbl WHERE programtbl.ed >= DATE(NOW()) AND programtbl.approved=1";
$result = mysqli_query($conn, $sql);

$array = array();
WHILE ($row= mysqli_fetch_assoc($result)){
	$array[] = $row;
}

   // while($row=mysqli_fetch_assoc($result))
   // {
    	//echo $row;
   //     $array[] = $row;
   // }

echo json_encode($array); 

mysqli_close($conn);

?>