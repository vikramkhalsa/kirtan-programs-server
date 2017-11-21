<?php
// $doc = new DOMDocument();
// $html = file_get_contents('http://samagams.org/');
// //echo $html;

// @$doc->loadHTML($html);

//  $a = new DOMXPath($doc);

//  $cells = $a->query("//tr/td/p");
//  //echo $cells;
//  echo $cells->length;
//  echo '<br>';
//   for ($i = 0; $i < $cells->length; $i++) {
//   	$item = $cells[$i];
//   	echo $i;
//   	echo '<br>';
// $input_str = $item->nodeValue;
//   echo $input_str;

//   }

// require 'class.iCalReader.php';

// $ical = new ical('Events.ics');
// $arr2 = [];
// //print_r($ical->events());

// foreach($ical->events() as $ar){
// 	//print_r($ar);
// 	echo $ar['SUMMARY'];
// 	echo $ar['LOCATION'];
// 	ECHO '<br>';
// }


$doc = new domdocument();
$html = file_get_contents('https://www.akj.org/programs.php');
//echo $html;
echo '[';
@$doc->loadhtml($html);

 $a = new domxpath($doc);

$classname='prog-div-rep';
 $cells = $a->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
 //echo $cells;
 //echo $cells->length;
 //echo '<br>';

 $arr = [];
  for ($i = 0; $i < $cells->length; $i++) {
  	$items = $cells[$i]->getElementsByTagName('div');
  	$item = $items[0];
  	$contact = $items[1];
 $nodes = $item->getElementsByTagName('h4');
 $ar = [];
    foreach ($nodes as $node) {
    
//print_r($node);
//echo'<br>';
    	$class = $node->getAttribute('class');
    	//echo $class;
    	$val = $node->nodeValue;
    	//echo $val;
    	if ($ar[$class]!= null){
    	$ar[$class.'b'] = $val;
    	}else {
    		    	$ar[$class] = $val;
    	}
    	if ($class=='prog-name'){
    		$ar['siteurl'] = "www.akj.org/".$node->firstChild->getAttribute('href');
    	}


    	// if ($class=='prog-name'){
    	// echo 'Name: '.$node->nodeValue;
    	// }

      //echo $node->hasAttributes(). "\n";
    }

$jar = [];

 $nums = $contact->getElementsByTagName('h4');
  foreach ($nums as $num) {
    	$class = $num->getAttribute('class');
    	if ($class == "prog-mob-info"){
    		$val = $num->nodeValue;
    		if ($val != null && trim($val)!=='')
    			$jar['phone'] = $val;

    	}
    }




$date = explode("To",$ar['prog-date']);
$sd = $date[0];
$ed= $date[1];

$times = explode("to",$ar['prog-time']);

 $starttime = $sd." ".$times[0];

 if($ed!= null)
    $endtime = $ed." ".$times[1];
  else
     $endtime = $sd." ".$times[1];
   
    $sd1 = date("Y-m-d H:i:00",strtotime($starttime));//date_parse($starttime);
    $ed1 = date("Y-m-d H:i:00",strtotime($endtime));
//echo '<br>';
//echo $sd1;
//echo $ed1;
$jar['title'] = $ar['prog-name'];
$jar['subtitle'] = $ar['prog-lc'];
$jar['address'] = $ar['prog-st'];
$jar['sd'] = $sd1;
$jar['ed'] = $ed1;
$jar['id']=99999+$i;
$jar['siteurl'] = $ar['siteurl'];
$jar['description'] = "See details on original website.";
 //$arr[] = $jar;

echo json_encode($jar);
   if ($i < $cells->length-1)
  		echo ',';

  }
  //print_r($arr);


    

echo ']';
  ?>


