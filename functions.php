<?php 

//creates a new event
function createEvent($title, $phone, $sd, $ed, $type, $description, $imageurl, $siteurl, $locationid, $rrule, $user, $allday=0){
	
  // connect to the database
	include('config.php');
  if ($allday != 1)
	{
    $sd1 = strtotime($sd);
  	//$ed1 = null;
  	//if ($ed!=null and $ed!=""){
  	$ed1 = strtotime($ed);
  	//check if end date is before start date, don't allow
  	if ($sd1 > $ed1) {
  		return "Error: End time must be later than start time.";
  	}	
	}


	$sql = "INSERT INTO events_all.programtbl (title, locationid, phone, sd, ed, user, description, type, rrule, imageurl, siteurl, allday)
	VALUES ('$title', '$locationid','$phone', '$sd','$ed', '$user', '$description', '$type', '$rrule', '$imageurl', '$siteurl', $allday)";
	if ($conn->query($sql) === TRUE) 
	{

		$ret = "New event submitted successfully! ";

		$to = "vsk@sikh.events";
		$subject = "New BayAreaKirtan Program Submitted";
		$body =  sprintf("WJKK WJKF,\n\nThe following program has been submitted: 
			\n\n Title: %s \n Location: %s \n Phone: %s\n Start: %s\n End: %s\n  User: %s\n Description: %s \n\n 
			To moderate, visit: http://sikh.events/programsadmin.php",
			$title, $locationid, $phone, $sd,$ed, $user, $description );

		if (mail($to, $subject, $body)) 
		{
			return $ret. "\nYour submission has been sent for moderation.";
		} 
		else 
		{
			return $ret. "\nFailed to send moderation request.";
		}
	}
	else 
	{
		return "Error:". $conn->error;
	}

}

//updates an existing event
function updateEvent($id, $title, $phone, $sd, $ed, $type, $description, $imageurl, $siteurl, $locationid, $rrule, $user, $allday=0){

  // connect to the database
  include('config.php');

  $sd1 = strtotime($sd);
  //$ed1 = null;
  //if ($ed!=null and $ed!=""){
    $ed1 = strtotime($ed);
      //check if end date is before start date, don't allow
    if ($sd1 > $ed1) {
        return "Error: End time must be later than start time.";
    } 
  
  $sql = "UPDATE events_all.programtbl SET title ='$title', locationid='$locationid', phone ='$phone', 
    sd = '$sd', ed ='$ed', description ='$description', type ='$type', rrule='$rrule', imageurl='$imageurl', siteurl='$siteurl', allday=$allday WHERE id = '$id'";
    if ($conn->query($sql) === TRUE) {
      return "Event updated successfully!";
    } else {
      return "Error:". $sql . "<br>" . $conn->error;
    }
}


//deletes an event by id, returns error or success message
//does NOT validate permissions!
function deleteEvent($id){

	// connect to the database
	include('config.php');

	$sql = "DELETE FROM events_all.programtbl WHERE id = '$id'";

	if ($conn->query($sql) === TRUE) {
		if ($conn->affected_rows == 1)
			return "Event deleted successfully.";
		else if ($conn->affected_rows == 0)
			return "Error: Event not found!";
		else
			return "Error: Unknown server error.";
	} else {
		return "Error: " . $sql . "<br>" . $conn->error;
	}

}

//gets an event by its ID
function getEventByID($id){

  include('config.php');

  $sql = "SELECT programtbl.id, programtbl.sd, programtbl.ed, programtbl.title, programtbl.phone, programtbl.description,
  programtbl.type, programtbl.rrule, programtbl.approved, programtbl.user, programtbl.allday, programtbl.siteurl, programtbl.imageurl, programtbl.locationid,
  locationtbl.name AS subtitle, CONCAT(locationtbl.address,', ', locationtbl.city, ' ', locationtbl.state) as address
  FROM events_all.programtbl JOIN locationtbl on programtbl.locationid = locationtbl.locationid WHERE id = '$id'";

  $result = mysqli_query($conn, $sql);
  mysqli_close($conn);
  return $result;

  //needs to give error message if event not found
}

//returns list of all events or user-specific events if a user is specified
function getEvents($user = null){

	include('config.php');

	$sql = "SELECT programtbl.id, programtbl.sd, programtbl.ed, programtbl.title, programtbl.phone, programtbl.description,
	programtbl.type, programtbl.rrule, programtbl.approved, programtbl.user, programtbl.allday, programtbl.siteurl, programtbl.imageurl, programtbl.locationid,
	locationtbl.name AS subtitle, CONCAT(locationtbl.address,', ', locationtbl.city, ' ', locationtbl.state) as address
	FROM events_all.programtbl JOIN locationtbl on programtbl.locationid = locationtbl.locationid";

	if ($user != null){
		$sql = $sql." WHERE user='$user'";
	}

	$sql = $sql." ORDER BY sd DESC";
	$result = mysqli_query($conn, $sql);
	mysqli_close($conn);
	return $result;
}

//used to curl and get events from external sources
function get_data($url) {
  $ch = curl_init();
  $timeout = 5;
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}


//parses and generates recurring event instances
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
  //if it somehow got a negative duration, fix it.
  if (intval($duration) < 0){
  	$duration = "120";
  }
  //echo $duration;
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

  $byday = (isset($rrules['BYDAY']) && $rrules['BYDAY'] !== '')
  ? $rrules['BYDAY']
  : null;
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
                $tempevent['id'] = $event["id"];//."0".$ii;
                $tempevent['sd'] = $newsd->format('Y-m-d H:i:s'); 
                $tempevent['ed'] = $newed->format('Y-m-d H:i:s');
                $tempevent['repeats'] = $frequency;
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
              $tempevent['id'] = $event["id"];//."0".$ii;
              $tempevent['sd'] = $newsd->format('Y-m-d H:i:s'); 
              $tempevent['ed'] = $newed->format('Y-m-d H:i:s');
              $tempevent['repeats'] = $frequency;
              //echo $tempevent['sd'].' '.$tempevent['ed'];
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
          if ($byday != null){
            //$byday = "1SU";
            $weekno = intval($byday)-1;
            $dayofweek = substr($byday,-2);

            $weekdays = array('SU'=>'sunday','MO'=> 'monday','TU'=>'tuesday','WE'=>'wednesday','TH'=>'thursday','FR'=>'friday','SA'=>'saturday');
            $numwords = ['first','second','third','fourth','fifth'];


          $interval = 1;
          $newsd = $startdate;//->add(new DateInterval('P'.$interval.'D'));
          $tempd = clone $newsd;

          $newed = $tempd->add(new DateInterval('PT'.$duration.'M'));

          $ii = 0;
          while ($newed<=$until){
          //for($i=0;$i<$count;$i++){
            if($newed>=$currdate){
              $tempevent = $event;
              $tempevent['id'] = $event["id"];//."0".$ii;
              $tempevent['sd'] = $newsd->format('Y-m-d H:i:s'); 
              $tempevent['ed'] = $newed->format('Y-m-d H:i:s');
              $tempevent['repeats'] = $frequency;
              // echo $tempevent['sd'].' '.$tempevent['ed'];
              //echo '<br>';
              $events[] = $tempevent;
              $ii++;
            }

            //echo json_encode($tempevent);
            //echo $newsd->format('Y-m-d H:i');
            //echo '<br>';
            $newsd = $startdate->add(DateInterval::createFromDateString($numwords[$weekno]." ".$weekdays[$dayofweek]." of next month"));
            $tempd = clone $newsd;
            $newed = $tempd->add(new DateInterval('PT'.$duration.'M'));
          }
        }

          break;
  }
  return $events;
}


?>