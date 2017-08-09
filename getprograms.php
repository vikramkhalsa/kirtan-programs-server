<?php
//check source filter
if(isset($_GET['source'])){
  $src = $_GET['source'];

  if ($src =="isangat"){ //return only isangat programs
    $returned_content = get_data('http://www.isangat.org/json2.php');
        echo $output = str_replace(array("\r\n", "\r"), "", $returned_content);
  }

  if ($src =="ekhalsa"){ //return only ekhalsa programs
    $returned_content = get_data('http://www.sikh.events/source_parser.php');
    echo $returned_content;
  }
}
else { //get all sikh.events programs


// connect to the database
  include('config.php');

  //get specific fields and address from joined location tables so as to return in the format mobile apps expect
  $sql = "SELECT programtbl.id, programtbl.sd, programtbl.ed, programtbl.title, programtbl.phone, programtbl.description, programtbl.type, programtbl.rrule, programtbl.imageurl, programtbl.siteurl, locationtbl.name AS subtitle, CONCAT(locationtbl.address,', ', locationtbl.city, ' ', locationtbl.state) as address FROM events_all.programtbl JOIN locationtbl on programtbl.locationid = locationtbl.locationid WHERE programtbl.ed >= DATE(NOW() - INTERVAL 1 WEEK)";
// $sql = "SELECT * FROM events_all.programtbl WHERE programtbl.ed >= DATE(NOW())"; 

//check region filter
 if (isset($_GET['region']))
 {
   $region =  $conn->real_escape_string($_GET['region']);
   $sql = $sql."  AND locationtbl.region = {$region} "; 
 }

//check filter by name
  if (isset($_GET['location']))
  {
   $loc =  $conn->real_escape_string($_GET['location']);
   $sql = $sql." AND locationtbl.name LIKE '{$loc}' "; 
 }

 //check filter by type
 if (isset($_GET['type']))
 {
   $type =  $conn->real_escape_string($_GET['type']);
   $sql = $sql." AND programtbl.type LIKE '{$type}%' "; 
 }

 //filter by status to allow showing unapproved programs.
if(!isset($_GET["status"])){ //"secret" api to allow getting all programs for debugging
$sql = $sql." AND programtbl.approved=1 "; 
}

//check id filter
 if (isset($_GET['id']))
 {
   $id =  $conn->real_escape_string($_GET['id']);
   $sql = $sql."  AND programtbl.id = {$id} "; 
 }


$sql = $sql." ORDER BY programtbl.sd ASC";
//echo $sql;
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$array = array();
$event = null;
while($row=mysqli_fetch_assoc($result))
{

  if ($row['rrule']!=null){
    //if event has an rrule, parse rule, generate recurring instances and add to array of events
    $array = array_merge($array,getRecurEvents($row));
  }else {

   $array[] = $row;
 }
}



//sorty events again by date, because of recurring instances
usort($array, 'date_compare');

header('Content-Type: application/json; charset=utf-8');
if(isset($_GET['alexa']))
{


$array2 = array();
$dt = gmdate("Y-m-d\TH:i:s\Z");
foreach($array as $row){
  $row1 = array();
  $row1["uid"] =  $row["id"];
    $row1["updateDate"] = $dt;//"2017-07-30T00:00:00.0Z";//$row['sd'];
  $row1["titleText"] =  $row["title"];
  $row1["mainText"] = $row["title"].' on '.date('l, M j \a\t g:ia', strtotime($row['sd']));//$row["description"];
  $row1["redirectionUrl"] = "http://www.sikh.events/eventdetails.php?id=".$row['id'];
  $array2[] = $row1;
}
echo $output = json_encode($array2);

}else {
$output = json_encode($array);
//replace newlines with html tags so they show up in popup views
echo $output1 = str_replace('\r\n', "<br>", $output);
//echo $output;
}

}

//compares string dates to order events by date
function date_compare($a, $b)
{
  $t1 = strtotime($a['sd']);
  $t2 = strtotime($b['sd']);
  return ($t1 -$t2);
} 

//used to curl and get events from external sources
function get_data($url) {
  $ch = curl_init();
  $timeout = 5;
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}



//parses and generates recurring events 
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

          $ii = 0;
          while ($newed<=$until){
          //for($i=0;$i<$count;$i++){
            if($newed>=$currdate){
              $tempevent = $event;
              $tempevent['id'] = $event["id"]."0".$ii;
              $tempevent['sd'] = $newsd->format('Y-m-d H:i:s'); 
              $tempevent['ed'] = $newed->format('Y-m-d H:i:s');
         // echo $tempevent['sd'].' '.$tempevent['ed'];
              $events[] = $tempevent;
              $ii++;
            }

          //echo json_encode($tempevent);
            //echo $newsd->format('Y-m-d H:i');
            //echo '<br>';
            $newsd = $startdate->add(new DateInterval('P'.$interval.'D'));
            $tempd = clone $newsd;
            $newed = $tempd->add(new DateInterval('PT'.$duration.'M'));
          } 
          break;
    case 'WEEKLY':
        $interval = 7; //if weekly, just do same thing as daily but add 7 days
        $newsd = $startdate;//->add(new DateInterval('P'.$interval.'D'));
        $tempd = clone $newsd;
        $newed = $tempd->add(new DateInterval('PT'.$duration.'M'));

        $ii = 0;
        while ($newed<=$until){
        //for($i=0;$i<$count;$i++){
          if($newed>=$currdate){
            $tempevent = $event;
            $tempevent['id'] = $event["id"]."0".$ii;
            $tempevent['sd'] = $newsd->format('Y-m-d H:i:s'); 
            $tempevent['ed'] = $newed->format('Y-m-d H:i:s');
       // echo $tempevent['sd'].' '.$tempevent['ed'];
        //echo '<br>';
            $events[] = $tempevent;
            $ii++;
          }

          //echo json_encode($tempevent);
          //echo $newsd->format('Y-m-d H:i');
          //echo '<br>';
          $newsd = $startdate->add(new DateInterval('P'.$interval.'D'));
          $tempd = clone $newsd;
          $newed = $tempd->add(new DateInterval('PT'.$duration.'M'));
        } 
        break;
    case 'MONTHLY'://handle by weekday case, also need to handle for weekly.. hmm
        break;
      }
      return $events;
    }



?>