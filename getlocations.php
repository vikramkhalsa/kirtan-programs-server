<?php

// connect to the database
	include('config.php');

 $sql = "SELECT DISTINCT subtitle FROM events_all.programtbl WHERE programtbl.ed >= DATE(NOW()) AND programtbl.approved=1";
    $result = mysqli_query($conn, $sql);

    $array = array();
    WHILE ($row= mysqli_fetch_array($result, MYSQLI_NUM)){
	$array[] = $row[0];
    }

   // while($row=mysqli_fetch_assoc($result))
   // {
    	//echo $row;
   //     $array[] = $row;
   // }

echo json_encode($array); 

 mysqli_close($conn);

?>