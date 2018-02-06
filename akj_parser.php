<?php
//akj_parser.php
//Vikram Singh
//11/17/2017
//Pulls and parses events from the website www.akj.org, 
//caches to a local file if last check was over a day ago, otherwise just reads from the cache file. 
//Can't get detailed description or schedules because you have to follow the link one more level. 
//TBA - follow links and get detailed descriptions. 


//check if cache file exists

if (file_exists("akjcache.json")){
//if cache file is recent, just load from it

  $modtime  = filemtime("akjcache.json");

  //check if mod time is within last 24 hrs
  if (time() - $modtime < 24 * 60 * 60){
    // echo time();
    // echo '<br>';
    // echo $modtime;

    $myfile = fopen("akjcache.json", "r");
    //assume data in file is valid!! or find some way to check.. 
    $data = fread($myfile, 99999999);

    echo $data;
    fclose($myfile);
    return;
  }

}

//else parse from akj again and update file. 

$doc = new domdocument();
$html = file_get_contents('https://www.akj.org/programs.php');
//echo $html;
$result =  '[';
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
  $datetime =$items[1];
	$contact = $items[2];
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
//loop through second div of nodes which contains date time.
//looks like they changed html structure of page which broke things. 
//fixed on 1/5/18 - VS
$nodes = $datetime->getElementsByTagName('h4');
foreach ($nodes as $node) {
    
  $class = $node->getAttribute('class');
  $val = $node->nodeValue;
  if ($ar[$class]!= null){
  $ar[$class.'b'] = $val;
  }else {
          $ar[$class] = $val;
  }
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
//var_dump($ar);
//echo $times[0];
//echo $times[1];

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
$jar['source'] = "akj.org";
 //$arr[] = $jar;

$result = $result.json_encode($jar);
if ($i < $cells->length-1)
	$result = $result.',';

}
//print_r($arr);   

$result = $result. ']';

echo $result;

$myfile = fopen("akjcache.json", "w");
fwrite($myfile,$result);
fclose($myfile);


?>


