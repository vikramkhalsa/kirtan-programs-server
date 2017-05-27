<?php
session_start();
?>
<html>
<head>
    <title>Bay Area Kirtan Programs</title>
<meta name="viewport" content="user-scalable=yes, width=device-width" />

<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
 
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

 <link href="navbar.css" rel="stylesheet">

<script type="text/javascript">
function showDescription(el){
     var val = el.getAttribute("val");
    $('#myModal').find(".modal-body").html(val);
    $('#myModal').modal();
}

function downloadiCal(id){
    var cell = $('#'+id);
    var title = cell.find('.programTitle').html();
    var addr = cell.find('a').html();
    var start = cell.find('.sd').attr("start");
    var desc = cell.find('.infoBtn').attr("val");
    var end = cell.find('.sd').attr("end");
  
    var filedata =""; 
var header = "BEGIN:VCALENDAR\nVERSION:2.0\nMETHOD:PUBLISH\nPRODID:-//sikh.events//NONSGML DDay.iCal 1.0//EN\nBEGIN:VEVENT\n";
var sd = "DTSTART:" + start + "\n";
var ed = "DTEND:" + end + "\n";
var loc = "LOCATION:" +addr + "\n";
var sum = "SUMMARY:"+title + "\n";
var uid = "UID:" + 123 + "\n";
var des = "DESCRIPTION:" + desc + "\n"; 
var footer = "END:VEVENT\nEND:VCALENDAR";

filedata = header + sd + ed + loc + sum + uid + des +footer;

save("sikhevent.ics", filedata);
//window.open( "data:text/calendar;charset=utf8," + escape( filedata ) , "sikhevent.ics");

}

function save(filename, data) {
    var blob = new Blob([data], {type: 'text/calendar'});
    if(window.navigator.msSaveOrOpenBlob) {
        window.navigator.msSaveBlob(blob, filename);
    }
    else{
        var elem = window.document.createElement('a');
        elem.href = window.URL.createObjectURL(blob);
        elem.download = filename;        
        document.body.appendChild(elem);
        elem.click();        
        document.body.removeChild(elem);
    }
}
</script>

</head>
<body>
  <?php include('header.html'); ?>  
      <div style="padding:10px;">

    <p>Welcome! Upcoming programs are listed below. 
        We hope to expand to include more Gurdwaras and locations soon. 
        If you are hosting a Kirtan event and would like to submit it to this list, 
        please click <a href="submitprogram.php"> Submit a program. </a>
         You will be asked to log in or create a user account. </p>
         Mobile apps are available for iOS and Android. <br>
         <a href="https://play.google.com/store/apps/details?id=com.vikramkhalsa.isangat"> <img src="images/googleplay.png" style="width:150px; padding:5px"></a>
         <a href="https://itunes.apple.com/us/app/sikh-events/id1220078093?mt=8"> <img src="images/appstore.png" style="width:150px; padding:5px"></a>
</div>
<?php

$filter = "";
if (isset($_GET["type"])){
$filter = "?type=".$_GET["type"];
}

$contents = file_get_contents('http://www.sikh.events/getprograms.php'.$filter);


$array = json_decode($contents, true);


echo "<div>"; 
    

    foreach($array as $value){
        echo '<div class="cell" id="'.$value['id'].'">
        <div class="left" style="width:30%; float:left; font-size:1.1em;  top: 50%; ">';
        $sdate = strtotime($value['sd']); // date('D M d Y H:i:s -0000', $sdate)
        $edate = strtotime($value['ed']);
        echo '<div class="sd" start="'.date('Ymd\THis', $sdate).'" end="'.date('Ymd\THis', $edate).'">'; //saving in this format for export to iCal
        echo date('D, M j', $sdate);
        echo "<br><br>";
        echo date('g:ia', $sdate).' to ';
        echo "<br>";
        echo date('g:ia', $edate);
        $desc = str_replace("'", "\'", $value["description"]);
        echo "</div>";
        echo '<br> <button class="infoBtn" onClick="showDescription(this)" val="'.$desc.'""><span class="glyphicon glyphicon-info-sign" aria-hidden="true" aria-label="description"></span></button>';
        echo '<button class="infoBtn" onclick="downloadiCal('.$value['id'].')"><span class="glyphicon glyphicon-calendar" aria-hidden="true" aria-label="Export to Calendar"></span></button>';
        echo '</div> 
        <div class="right" style="width:70%; float:left;">
        <div class="programTitle">';
        echo $value["title"];
        echo'</div><br><div class="programSubtitle">';
        echo $value["subtitle"];
        echo'</div><br> <a href="http://maps.google.com/?q='.$value["address"].'">';
        echo $value["address"];
        echo"</a><br>";
        echo $value["phone"];
        echo"<br>";
        //echo '<a class="" href="http://maps.google.com/?q='.$value["address"].'"><img src="http://isangat.org/map.png" border="0"></a><br>';
        echo '</div>
    </div>';
    
    }
?>

<div id="myModal" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
        <div class="modal-body">
      </div>
    </div>
  </div>
</div>

</body>
</html>