<?php

//source_parser.php
//Vikram Singh
//11/17/2017
//Pulls and parses events from ekhalsa.com and converts them into a format for sikh events site and apps to digest.
//Should be renamed


$doc = new DOMDocument();
$html = file_get_contents('http://ekhalsa.com/m');
@$doc->loadHTML($html);

$classname = "box";

$pattern = "/.:..../";
$pattern2 = "/[A-Z][^a-z]*[A-Z]/";
$pattern3 ="/\S.*,.*\S/";

$starttime;
$endtime;
$title;
$date;

$a = new DOMXPath($doc);
$boxes= $a->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");

echo '[';

for ($i = 1; $i < $boxes->length; $i++) {
	$item = $boxes->item($i);
	$input_str = $item->firstChild->nodeValue;
    //echo $input_str;
    //$result[] = $input_str;
    if (preg_match_all($pattern, $input_str,$matches_out)){
    	//print_r($matches_out[0]);
        $starttime =  $matches_out[0][0];
        $endtime =  $matches_out[0][1];
    }
    if (preg_match_all($pattern2, $input_str,$matches_out1)){
    	//print_r($matches_out1[0]);
        $title = $matches_out1[0][0];
    }
     if (preg_match_all($pattern3, $input_str,$matches_out3)){
    	//print_r($matches_out3[0]);
        $date = $matches_out3[0][0];
        $date = str_replace(chr(194).chr(160), " ", $date);
        $date = trim($date);
    }
   // echo "<br>";
    $subtitle = $item->childNodes[1]->nodeValue;
    //echo $subtitle;
    //echo $item->childNodes[2]->nodeValue;
    // echo "<br>";
    $address = $item->childNodes[3]->nodeValue . $item->childNodes[5]->nodeValue;
    $address = trim($address);
   // echo $address;
   // echo "<br>";
    $phone = substr($item->childNodes[7]->nodeValue,0,16);
    $phone = trim($phone);
    //echo $phone;
    //echo "<br>";
    $starttime = $date." ".$starttime;
    $endtime = $date." ".$endtime;
    $sd1 = date("Y-m-d H:i:00",strtotime($starttime));//date_parse($starttime);
    $ed1 = date("Y-m-d H:i:00",strtotime($endtime));
    //$sd1["year"]=2017;
    //$sd2 = $sd1["year"].' '.$sd1["month"]." ".$sd1["day"];
    $array = ['sd'=> $sd1, 'ed'=>$ed1,'title'=>$title,
    'subtitle'=>$subtitle,'address'=>$address,'phone'=>$phone, 'date'=>$date, 'id'=>$i, 'source'=>'ekhalsa.com'];

    echo json_encode($array);

    if ($i < $boxes->length-1)
        echo ',';
}

echo ']';
//echo "<pre>";
//print_r($result);
exit();

?>
