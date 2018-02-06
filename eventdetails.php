<!-- 
eventdetails.php
Vikram Singh
10/30/2017
This file displays details about a single event. It can be used to share a url to the specific event -->

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
	<script defer src="https://use.fontawesome.com/releases/v5.0.4/js/all.js"></script>


	<style>
	h4 {
		padding-top: 15px;
	}


	</style>

<?php

$header1 = '<meta name="description" content="'.str_replace('<br>', "\r", $array["description"]).'">
    <meta property="og:title" content="'.$array["title"].'" />
    <meta property="og:url" content="'.'https://www.sikh.events/eventdetails.php?id='.$id.'" />
    <meta property="og:image" content="https://www.sikh.events/images/og_icon.jpg">';

$header2 = "</head>\n<body>";
$header3 = '<div class="container"> <div class="panel panel-default"><div class="panel-body">';


if(isset($_GET['id'])){
$id = $_GET['id'];

include ("functions.php");
$result = getEventByID($id);

if ($result->num_rows < 1){
	echo $header1;
	echo $header2;
	include('header.html');
	echo $header3;
	echo "<h3>Sorry, we were unable to find this event.</h3>";
}
else {

	$array = $result->fetch_array();

	$header1 = '<meta name="description" content="'.str_replace('<br>', "\r", $array["description"]).'">
    <meta property="og:title" content="'.$array["title"].'" />
    <meta property="og:url" content="'.'https://www.sikh.events/eventdetails.php?id='.$id.'" />
    <meta property="og:image" content="https://www.sikh.events/images/og_icon.jpg">';

	echo $header1;
	echo $header2;
	include('header.html');
	echo $header3;

	echo "<h2>".$array["title"]."</h2>"; 
	echo "<h4> <span class='far fa-calendar-alt'></span>&nbsp";
	$sdate = strtotime($array['sd']); 
	$edate = strtotime($array['ed']);
	echo date('l, F jS', $sdate);
	//only show end date if it differs from start date. 
	if(date('d',$sdate) != date('d',$edate)){
	     echo ' - '.date('l, F jS', $edate);
	}

	echo "</h4>";
	//only show times if not all day
	if ($array["allday"]!= 1)
		echo "<h4><span class='far fa-clock'></span>&nbsp". date('g:ia', $sdate).' to ' . date('g:ia', $edate) . "</h4>";
	else 
		echo "<h4> All Day Event </h4>";
	if (isset($array['repeats'])){
		echo "<h5><em> Repeats ".ucfirst(strtolower($array['repeats']))."</em></h5>";
	}
	echo "<h4 style='margin-bottom:0px;'> <span class='fas fa-map-marker'></span>&nbsp", $array['subtitle'], "</h4>";
	echo  "<em style='padding-left: 20px;'>",$array['address'],"</em>";
	// echo "Type: ",$array["type"];
	// print "<br>";
	echo '<p style="padding-top:15px"> <span class="fas fa-info-circle"></span> '.$array["description"].'</p>';
	if(isset($array["phone"]) && $array["phone"]!== "")
		echo "<h4> <span class='fas fa-phone'></span>&nbsp",$array["phone"],"</h4>";
    
    if ($array["siteurl"]){
        $siteurl = $array["siteurl"];
        if(strpos($siteurl, "http://") !== false && strpos($siteurl, "https://") !== false){ }
        else { $siteurl = "http://".$siteurl; }
        echo '<p> <span class="fas fa-link"></span>&nbsp <a href="'.$siteurl.'">'.$array["siteurl"].'</a></p>';
    }

	if ($array["imageurl"]){
		echo '<img class="img-responsive" src="'.$array["imageurl"].'"/>';
		echo "<br>";
	}

	print "<br>";

	}
}
else {
	echo $header1;
	echo $header2;
	include('header.html');
	echo $header3;	
	//add some message here or redirect user?
}
//add suggested events from same region or something here?

?>

See this event and others in the Sikh Events Mobile App! <br>
<a href="https://play.google.com/store/apps/details?id=com.vikramkhalsa.isangat"> <img src="images/googleplay.png" style="width:150px; padding:5px"></a>
<a href="https://itunes.apple.com/us/app/sikh-events/id1220078093?mt=8"> <img src="images/appstore.png" style="width:150px; padding:5px"></a>

</div>
</div>
</div>
 </body>
</html>