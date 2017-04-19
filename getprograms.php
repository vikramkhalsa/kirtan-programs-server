<?php


if(isset($_GET['source'])){
$src = $_GET['source'];

//if all sources requested, append programs from isangat as well
if ($src =="isangat"){
$returned_content = get_data('http://www.isangat.org/json.php');
echo $returned_content;
}

if ($src =="ekhalsa"){
$returned_content = get_data('http://www.sikh.events/source_parser.php');
echo $returned_content;
}
}
else {


// connect to the database
    include('config.php');


    /* gets the data from a URL */


    $sql = "SELECT * FROM events_all.programtbl WHERE programtbl.ed >= DATE(NOW())";  

if (isset($_GET['location']))
{
   $loc =  $conn->real_escape_string($_GET['location']);
   $sql = $sql." AND programtbl.subtitle LIKE '{$loc}%' "; 
}
if (isset($_GET['type']))
{
   $type =  $conn->real_escape_string($_GET['type']);
   $sql = $sql." AND programtbl.type LIKE '{$type}%' "; 
}
if(!isset($_GET["status"])){ //"secret" api to allow getting all programs for debugging

$sql = $sql." AND programtbl.approved=1 "; 
}


$sql = $sql." ORDER BY sd ASC";


    $result = mysqli_query($conn, $sql);

    $array = array();

    while($row=mysqli_fetch_assoc($result))
    {
    	//echo $row;
    	//$row["sd"] = date("c",strtotime($row["sd"]));
    	//$row["ed"] = date("c",strtotime($row["ed"]));
        $array[] = $row;
    }


echo json_encode($array); 

 mysqli_close($conn);

}
//check if programs from specific source are requested



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

?>