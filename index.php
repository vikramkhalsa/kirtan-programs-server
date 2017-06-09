<?php
session_start();
?>
<html>
<head>
    <title>Sikh Events - Everything in one place!</title>
    <meta name="description" content="View kirtan programs, camps and other Sikh Events all over the Bay Area in one place!">
    <meta property="og:title" content="All Sikh Event in one app!" />
    <meta property="og:url" content="https://www.sikh.events" />
    <meta property="og:image" content="https://www.sikh.events/images/og_icon.jpg">

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

function onselectchange(){
    var e = $('#loc-region')[0];
    var regid = e.options[e.selectedIndex].value;
    if(regid == 0)
        window.location.href="index.php";
    else{
    window.location.href="index.php?region="+regid;
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
  
    <?php

    $filter = "";
    $regid = "";
    if (isset($_GET["type"])){
        $filter = "?type=".$_GET["type"];
    }
    else if (isset($_GET["source"])){
        $filter = "?source=".$_GET["source"];
    }
    else if (isset($_GET["region"])){
        $filter = "?region=".$_GET["region"];
        $regid = $_GET["region"];
    }





    $contents = file_get_contents('http://www.sikh.events/getprograms.php'.$filter);
    $array = json_decode($contents, true);

    $regions = file_get_contents('http://www.sikh.events/getlocations.php?regions=current'.$filter);
    $regions = json_decode($regions, true);

if (isset($_GET["source"]) && ($_GET["source"] == "isangat")){
        $array = $array["programs"];
}
 ?>
 <br>
<label for="loc-city">Show Regions </label>
            <select class="form-control" id="loc-region" onchange="onselectchange()">
                <option value="" selected>All Events</option>
              <?php foreach ($regions as $region) { ?>
              <option value="<?php echo $region['regionid'];?>"  <?php echo ($region["regionid"] == $regid) ? ' selected="selected"' : '';?>>      <?php echo $region['name'];?></option>
              <?php } ?>
            </select>

              </div>

 <div>

    

    <?php

    foreach($array as $value){
        echo '<div class="cell" id="'.$value['id'].'">
        <div class="left" style="width:30%; float:left; font-size:1.1em;  top: 50%; ">';
        $sdate = strtotime($value['sd']); // date('D M d Y H:i:s -0000', $sdate)
        $edate = strtotime($value['ed']);
        echo '<div class="sd" start="'.date('Ymd\THis', $sdate).'" end="'.date('Ymd\THis', $edate).'">'; //saving in this format for export to iCal
        echo date('D, M j', $sdate);
        if(date('d',$sdate) != date('d',$edate)){
             echo ' - '.date('D, M j', $edate);
        }
        echo "<br><br>";
        echo date('g:ia', $sdate).' to ';
        echo "<br>";
        echo date('g:ia', $edate);
        echo "</div>";
        $desc = str_replace("'", "\'", $value["description"]);
        if(!($desc == "")){
        echo '<br> <button class="infoBtn" onClick="showDescription(this)" val="'.$desc.'""><span class="glyphicon glyphicon-info-sign" aria-hidden="true" aria-label="description"></span></button>';
        }
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