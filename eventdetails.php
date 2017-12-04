<!-- 
eventdetails.php
Vikram Singh
10/30/2017
This file displays details about a single event. It can be used to share a url for the specific event -->

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


<?php

include('header.html');

echo '<meta name="description" content="'.str_replace('<br>', "\r", $array["description"]).'">
    <meta property="og:title" content="'.$array["title"].'" />
    <meta property="og:url" content="'.'https://www.sikh.events/eventdetails.php?id='.$id.'" />
    <meta property="og:image" content="https://www.sikh.events/images/og_icon.jpg">';

echo"
</head>
<body>";
echo '<div class="panel panel-default" style="width:90%; margin:10px"> <div class="panel-body">';


if(isset($_GET['id'])){
$id = $_GET['id'];
$contents = file_get_contents('http://www.sikh.events/getprograms.php?id='.$id);
$array = json_decode($contents, true);

if ($array == null OR count($array) <1){
	echo "<h3>Sorry, we were unable to find this event.</h3>";
}
else {

	$array = $array[0]; 

	echo "<h2>".$array["title"]."</h2>"; 
	echo "<h3>";
	$sdate = strtotime($array['sd']); 
	$edate = strtotime($array['ed']);
	echo date('l, F jS', $sdate);

	if(date('d',$sdate) != date('d',$edate)){
	     echo ' - '.date('l, F jS', $edate);
	}

	echo "</h3>";
	echo "<h4>". date('g:ia', $sdate).' to ' . date('g:ia', $edate) . "</h4>";
	if (isset($array['repeats'])){
		echo "<h5><em> Repeats ".ucfirst(strtolower($array['repeats']))."</em></h5>";
	}
	echo "<h3>", $array['subtitle'], "</h3>";
	echo  "<h4>",$array['address'],"</h4>";
	// echo "Type: ",$array["type"];
	// print "<br>";
	echo '<h4>'.$array["description"].'</h4>';
	echo "<h4>Phone Number: ",$array["phone"],"</h4>";
	    if ($array["siteurl"]){
	        $siteurl = $array["siteurl"];
	         if(strpos($siteurl, "http://") !== false && strpos($siteurl, "https://") !== false){ }
	        else { $siteurl = "http://".$siteurl; }
	        echo '<h4>Website: <a  href="'.$siteurl.'">'.$array["siteurl"].'</a></h4>';
	    }

	     if ($array["imageurl"]){
	        echo '<img class="img-responsive" src="'.$array["imageurl"].'"/>';
	        echo "<br>";
	    }

	print "<br>";

	}
}
//add suggested events from same region or something here?

?>

See this event and others in the Sikh Events Mobile App! <br>
<a href="https://play.google.com/store/apps/details?id=com.vikramkhalsa.isangat"> <img src="images/googleplay.png" style="width:150px; padding:5px"></a>
<a href="https://itunes.apple.com/us/app/sikh-events/id1220078093?mt=8"> <img src="images/appstore.png" style="width:150px; padding:5px"></a>

</div>
</div>
 </body>
</html>