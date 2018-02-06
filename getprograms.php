<?php
//getprograms.php
//Vikram Singh
//10/30/2017
//This is the main entry point for the 'API'. It will take various query parameters and run the actual MYSQL query, and then return the events from the databse in JSON format for the website and mobile apps to use. 
//This will also call the various custom parsers or files to get events from external sources.
//It can filter events based on region, type, location, status, or id. It can also return events or events which haven't been approved (for debugging) 

//include common functions
include('functions.php');


//check source filter
if(isset($_GET['source'])){
  $src = $_GET['source'];

  if ($src =="isangat"){ //return only isangat programs
    //$returned_content = get_data('http://www.isangat.org/json2.php');
      // echo $output = str_replace(array("\r\n", "\r"), "", $returned_content);
     echo '{"programs":[{
        "id":"1",
        "sd": "2018-02-02 00:00:00",
        "ed":"2018-02-02 00:00:00",
        "title":"Please visit www.isangat.org for these programs",
        "subtitle":"Programs from isangat.org are no longer supported in this app.",
        "siteurl":"www.isangat.org",
        "address":"",
        "description":"This website is no longer supported by Sikh Events. Please visit the website directly to view programs."

      }]}';  
  }
   if ($src =="isangat2"){ //return only isangat programs
    // $returned_content = get_data('http://www.isangat.org/json3.php');
    //     echo $output = str_replace(array("\r\n", "\r"), "", $returned_content);
     echo '[{
        "id":"1",
        "sd": "2018-02-02 00:00:00",
        "ed":"2018-02-02 00:00:00",
        "title":"Please visit www.isangat.org for these programs",
        "subtitle":"Programs from isangat.org are no longer supported in this app.",
        "siteurl":"www.isangat.org",
        "address":"",
        "description":"This website is no longer supported by Sikh Events. Please visit the website directly to view programs."

      }]';  
  }

  if ($src =="ekhalsa"){ //return only ekhalsa programs
    $returned_content = get_data('http://www.sikh.events/source_parser.php');
    echo $returned_content;
  }
  if ($src =="akjorg"){ //return only akj.org programs
    $returned_content = get_data('http://www.sikh.events/akj_parser.php');
    echo $returned_content;
  }
   if ($src =="samagams"){ //return only samagams.org programs
    $returned_content = get_data('http://www.sikh.events/samagams_parser.php');
    echo $returned_content;
  }
}
else { //get all sikh.events programs


  // connect to the database
  include('config.php');

  //get specific fields and address from joined location tables so as to return in the format mobile apps expect
  $sql = "SELECT programtbl.id, programtbl.sd, programtbl.ed, programtbl.title, programtbl.phone, programtbl.description, programtbl.type, programtbl.rrule, programtbl.imageurl, programtbl.siteurl, programtbl.allday, locationtbl.name AS subtitle, CONCAT(locationtbl.address,', ', locationtbl.city, ' ', locationtbl.state) as address FROM events_all.programtbl JOIN locationtbl on programtbl.locationid = locationtbl.locationid WHERE ";

// $sql = "SELECT * FROM events_all.programtbl WHERE programtbl.ed >= DATE(NOW())"; 
  // See past events DATE_SUB(NOW(), INTERVAL 60 DAY)

 //check past events filter
 if (isset($_GET['past']))
 {
   $pastdays =  $conn->real_escape_string($_GET['past']);
   $sql = $sql."programtbl.ed >=  DATE_SUB(NOW(), INTERVAL ".$pastdays." DAY) "; 
 }else if(isset($_GET['id'])){
  $sql = $sql."programtbl.id > 0"; //just put this here so the WHERE clause doesn't break the query. 
  //TBD figure out the proper way to do this.
 }
 else {
   $sql = $sql."programtbl.ed >= DATE(NOW())";

 }
   
//check region filter
 if (isset($_GET['region']))
 {
   $region =  $conn->real_escape_string($_GET['region']);
   $sql = $sql."  AND locationtbl.region = {$region} "; 
 }

//check filter by location name
  if (isset($_GET['location']))
  {
   $loc =  $conn->real_escape_string($_GET['location']);
   $sql = $sql." AND locationtbl.name LIKE '{$loc}' "; 
 }

  if (isset($_GET['locationid']))
  {
   $locid =  $conn->real_escape_string($_GET['locationid']);
   $sql = $sql." AND locationtbl.locationid = {$locid} "; 
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
  
}

}

else 
{
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


?>