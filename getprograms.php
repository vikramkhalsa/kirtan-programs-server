<?php

// connect to the database
	include('config.php');

 $sql = "SELECT * FROM events_all.programtbl WHERE programtbl.ed >= DATE(NOW()) AND programtbl.approved=1 ORDER BY sd ASC";
    $result = mysqli_query($conn, $sql);

    $array = array();

    while($row=mysqli_fetch_assoc($result))
    {
    	//echo $row;
        $array[] = $row;
    }


echo json_encode($array); 

 mysqli_close($conn);

?>