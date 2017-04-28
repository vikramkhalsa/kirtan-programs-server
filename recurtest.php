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

        if ($row['rrule']!=null){
          //echo $row['title'];
          array_push($array,getRecurEvents($row));
        }else {

         $array[] = $row;
       }
    }

echo json_encode($array);
function getRecurEvents($event){

$events = array();

$rulestr = $event['rrule'];//"FREQ=DAILY";//;COUNT=5;INTERVAL=3";
//$rulestr = "FREQ=DAILY;DURATION=60";//;COUNT=5;INTERVAL=3";
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


 //get duration
   $duration = (isset($rrules['DURATION']) && $rrules['DURATION'] !== '')
                    ? $rrules['DURATION']
                    : "120";
//$duration = explode(':',$duration);
//$durHrs = $duration[0];
//$durMins = $duration[1];

     // Get Interval
      $interval = (isset($rrules['INTERVAL']) && $rrules['INTERVAL'] !== '')
                    ? $rrules['INTERVAL']
                    : 1;
 //get Count
   $count = (isset($rrules['COUNT']) && $rrules['COUNT'] !== '')
                    ? $rrules['COUNT']
                    : 1;

$until=$enddate;
$currdate = new DateTime(null,(new DateTimeZone("America/Los_Angeles")));
              //  if (isset($rrules['UNTIL'])) {
                    // Get Until
                //    $until = new DateTime($rrules['UNTIL']);    
                  //  }           
 // Decide how often to add events and do so
                switch ($frequency) {
                    case 'DAILY':
                      $newsd = $startdate;//->add(new DateInterval('P'.$interval.'D'));
                      $tempd = clone $newsd;
                       $newed = $tempd->add(new DateInterval('PT'.$duration.'M'));
                    while ($newed<=$until){
                      //for($i=0;$i<$count;$i++){
                      if($newed>=$currdate){
                      $tempevent = $event;
                      $tempevent['sd'] = $newsd->format('Y-m-d H:i'); 
                      $tempevent['ed'] = $newed->format('Y-m-d H:i');
                     // echo $tempevent['sd'].' '.$tempevent['ed'];
                      //echo '<br>';
                      $events[] = $tempevent;
                    }

                      //echo json_encode($tempevent);
                        //echo $newsd->format('Y-m-d H:i');
                        //echo '<br>';
                        $newsd = $startdate->add(new DateInterval('P'.$interval.'D'));
                              $tempd = clone $newsd;
                         $newed = $tempd->add(new DateInterval('PT'.$duration.'M'));
                      } 
                    break;
               }
               return $events;
}



?>