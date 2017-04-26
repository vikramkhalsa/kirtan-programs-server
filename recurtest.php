<?php



// connect to the database
    include('config.php');


    /* gets the data from a URL */


    $sql = "SELECT * FROM events_all.programtbl WHERE programtbl.ed >= DATE(NOW())";  
$sql = $sql." AND programtbl.approved=1 "; 
$sql = $sql." ORDER BY sd ASC";
    $result = mysqli_query($conn, $sql);
    $array = array();
    $event = null;

    while($row=mysqli_fetch_assoc($result))
    {
        $array[] = $row;
        echo $row["rrule"];
        echo '<br>';
        if ($row['rrule']!=null){
          echo $row['title'];
             echo '<br>';
          $event = $row;
        }
    }





$rulestr = $event['rrule'];//"FREQ=DAILY";//;COUNT=5;INTERVAL=3";

$startdate = new DateTime($event['sd']);
$enddate = new DateTime($event['ed']);

  // Recurring event, parse RRULE and add appropriate duplicate events
                $rrules = array();
                $rruleStrings = explode(';', $rulestr);
                foreach ($rruleStrings as $s) {
                    list($k, $v) = explode('=', $s);
                    $rrules[$k] = $v;
                }

     // Get frequency
                $frequency = $rrules['FREQ'];            

  $startTimestamp = "";
    $endTimestamp="";
     $duration="";

     // Get Interval
                $interval = (isset($rrules['INTERVAL']) && $rrules['INTERVAL'] !== '')
                    ? $rrules['INTERVAL']
                    : 1;
                    
                    //get Count
                   $count = (isset($rrules['COUNT']) && $rrules['COUNT'] !== '')
                    ? $rrules['COUNT']
                    : 1;
 // Decide how often to add events and do so
                switch ($frequency) {
                    case 'DAILY':
                      $newsd = $startdate->add(new DateInterval('P'.$interval.'D'));
                    while ($newsd<$enddate){
                    	//for($i=0;$i<$count;$i++){
                    	$tempevent = $event;
                      $tempevent['sd'] = $newsd->format('Y-m-d H:i');
                      echo json_encode($tempevent);
                    		//echo $newsd->format('Y-m-d H:i');
                    		echo '<br>';
                        $newsd = $startdate->add(new DateInterval('P'.$interval.'D'));
                    	} 
                    break;
               }

?>