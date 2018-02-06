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


    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#ebaa2d">
    <meta name="theme-color" content="#466eb4">

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script
              src="http://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
              integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
              crossorigin="anonymous"></script>

    <link href="navbar.css" rel="stylesheet">

    <script type="text/javascript">


        $(document).ready(function(){

         $('#sourceSelect').on('change', function(evt, params) {
            var e = $('#sourceSelect')[0];
            var source = e.options[e.selectedIndex].value;
            if(source =="")
                window.location.href="index2.php";
            else 
                window.location.href="index2.php?source="+source;

        });


        $('#regSelect').on('change', function(evt, params) {
            var e = $('#regSelect')[0];
            var reg = e.options[e.selectedIndex].value;
            if(reg =="all")
                 filters.region = "";
            else 
                filters.region = reg;
            filter();
        });


        $('#locSelect').on('change', function(evt, params) {
            var e = $('#locSelect')[0];
            var loc = e.options[e.selectedIndex].value;
            if(loc =="all")
                 filters.location = "";
            else 
                filters.location = loc;
            filter();
        });


        $('#typeSelect').on('change', function(evt, params) {

            var e = $('#typeSelect')[0];
            var type = e.options[e.selectedIndex].value;
            if(type =="all")
                 filters.type = "";
            else 
                filters.type = type;
            filter();

        });

        $('#dateSelect').on('change', function(evt, params) {
            var e = $('#dateSelect')[0];
            var date = e.options[e.selectedIndex].value;
            filters.date = date;
            filter();
        });


     });

    var td = new Date();
    var noEvents = false;

    var filters = {
        type : "",
        location : "",
        region: "",
        date: "0"
    };

    function filter(){
        noEvents = true;
        var i = 0;
        var j = 0;
        events.forEach(function(event) {
        var show = true;
        //only  show event if it matches ALL filters
        //so need to check each filter one by one;
        //if type is in matching set
        //if location is in matching set
        //only then show
        //for now assume only 1 type

        //use .some to see if any colors match

        if (filters.region){
            if(event.region.indexOf(filters.region) < 0){
                show = false;
            }
        }


        if ( filters.location){
            if (event.subtitle.indexOf(filters.location) < 0) {
                show = false;
            }
        }


        if ( filters.type){
            if (event.type.indexOf(filters.type) < 0) {
                show = false;
            }
        }

        if (filters.date){

            var ed = new Date(event.ed);
            var days = Date.daysBetween(ed,td);

            if (days > filters.date){ 
                show = false;
            }

        }
        if ((show == true) && (noEvents == true))
            noEvents = false;

        //TODO get element once and reuse instead of looking up every time

        if (show && $('#'+ event.id).css('display') == 'none'){
            $('#'+ event.id).delay(i).show("drop");
            i = i+50;
        }
        else if(!show && $('#'+ event.id).css('display') != 'none')
        {
            $('#'+ event.id).delay(j).hide("drop");
            j = j+50;
        }

        });

        //if no events are showing, display a message to the user
        if (noEvents){
            $('#noEvents').show();
        }
        else {
            $('#noEvents').hide();
        }

    }


    Date.daysBetween = function( date1, date2 ) {
      //Get 1 day in milliseconds
      var one_day=1000*60*60*24;

      // Convert both dates to milliseconds
      var date1_ms = date1.getTime();
      var date2_ms = date2.getTime();

      // Calculate the difference in milliseconds
      var difference_ms = date2_ms - date1_ms;

      // Convert back to days and return
      return Math.round(difference_ms/one_day); 
    }

    function showDescription(el){
       var val = el.getAttribute("val");
       $('#myModal').find(".modal-body").html(val);
       $('#myModal').modal();
    }

function showImage(el){
    var val = el.getAttribute("val");
    var img = $('<img id="dynamic" class="img-responsive" style="display:block; margin:auto;">'); //Equivalent: $(document.createElement('img'))
    img.attr('src', val);
    $('#myModal').find(".modal-body").html(img);
    $('#myModal').modal();
    }

   function downloadiCal(id){
        var cell = $('#'+id);
        var title = cell.find('.programTitle').find('a').html();
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
  <div class="container">

    <p>Welcome! Upcoming programs are listed below. 
        We hope to expand to include more Gurdwaras and locations soon. 
        If you are hosting a Kirtan event and would like to submit it to this list, 
        please click <a href="submitprogram.php"> Submit a program. </a>
        You will be asked to log in or create a user account. </p>
        Mobile apps are available for iOS and Android. <br>
        <a href="https://play.google.com/store/apps/details?id=com.vikramkhalsa.isangat"> <img src="images/googleplay.png" style="width:150px; padding:5px"></a>
        <a href="https://itunes.apple.com/us/app/sikh-events/id1220078093?mt=8"> <img src="images/appstore.png" style="width:150px; padding:5px"></a>
        <a href="https://www.amazon.com/VSK-Sikh-Events/dp/B07541QG26/ref=sr_1_1?s=mobile-apps&ie=UTF8&qid=1506234517&sr=1-1&keywords=sikh+events"> <img src="images/amazon.png" style="height:53px; padding:5px"></a>
  
    <?php


    //
    $filter = "?past=60";
    $regid = "";
    $past = false;
    $disable_filters = false;
    if (isset($_GET["type"])){
        $filter .= "&type=".$_GET["type"];
    }
    else if (isset($_GET["source"])){
        $filter .= "&source=".$_GET["source"];
        $source = $_GET["source"];
        $disable_filters = true;
    }
    else if (isset($_GET["region"])){
        $filter .= "&region=".$_GET["region"];
        $regid = $_GET["region"];
    }
    //else if (isset($_GET["past"])){
        //$past = true;
    //}

    $contents = file_get_contents('https://www.sikh.events/getprograms.php'.$filter);
    $array = json_decode($contents, true);

    echo '<script>
    var events = '.$contents.';
    </script>';

    $regions = file_get_contents('https://www.sikh.events/getlocations.php?regions=current');
    $regions = json_decode($regions, true);

    $locations = file_get_contents('https://www.sikh.events/getlocations.php?locations=current');
    $locations = json_decode($locations, true);

     ?>
     <br>
<!--     <div style="margin: 0 15px">

        <?php 
        if ($past){
           echo '<a href="index.php"><button class="btn btn-default">Hide past events</button></a>';
        }
        else {
           echo '<a href="index.php?past=60"><button class="btn btn-default">Show past events</button></a>';
        }
        ?>
       
    </div> -->
<div>


<select  id="sourceSelect" data-placeholder="Select a source" class="form-control">
    <option value="">Sikh.Events</option>
    <option value="akjorg" <?php echo ($source == "akjorg") ? ' selected="selected"' : '';?>   >akj.org</option>
    <option value="samagams" <?php echo ($source == "samagams") ? ' selected="selected"' : '';?> >samagams.org</option>
    <option value="ekhalsa" <?php echo ($source == "ekhalsa") ? ' selected="selected"' : '';?> >ekhalsa.com</option>
</select>


<?php 
if ($disable_filters){
    echo "<!--";
}
    ?>

<div class = "row">
    <div class="col-sm-3">
        <select  id="regSelect" data-placeholder="Select Region" class="form-control">
        <option value="all">All Regions</option>
         <?php foreach ($regions as $reg) { ?>
             <option value= "<?php echo $reg['regionid']?>"> <?php echo $reg['name']?>   </option>

              <?php } ?>
        </select>
    </div>

   <div class="col-sm-3">
    <select  id="typeSelect" data-placeholder="Select Event Type" class="form-control">
    <option value="all">All Types</option>
        <option>kirtan</option>
         <option>camp</option>
         <option>samaagam</option>
         <option>discussion</option>
         <option>seva</option>
         <option>other</option>
    </select>
    </div>

   <div class="col-sm-3">
    <select  id="locSelect" data-placeholder="Select Event Location" class="form-control">
    <option value="all">All Locations</option>

         <?php foreach ($locations as $loc) { ?>
         <option> <?php echo $loc["name"]?></option>

          <?php } ?>
    </select>
    </div>

   <div class="col-sm-3">
    <select id="dateSelect" data-placeholder="Select Date Range" class="form-control">
        <option value="0"> >Today</option>
        <option value="30"> >Past 30 days</option>
        <option value="60"> >Past 60 days</option>
    </select>
    </div>

</div>


<div id="noEvents" style="display:none;">
    <h4>No events match your filter selection. Please remove or change some filters.</h4>
</div>


<?php
if ($disable_filters){
       echo "-->";
}


if (!is_array($array)){
    echo "<h3>We're sorry, there was an error processing your request. </h3>";
}
else {

    $td = strtotime("now");

    foreach($array as $value){
        $hide="";
        if (!$disable_filters){
            $edate = strtotime($value['ed']);
            if ($edate < $td){
                $hide='style="display:none"';
            }
        }
        echo '<div class="row">';
        echo '<div class="cell" id="'.$value['id'].'" '.$hide.'">
        <div class="col-xs-3 col-sm-2">';
       // <div class="left" stye="width:30%; float:left; font-size:1.1em;  top: 50%; ">';
        $sdate = strtotime($value['sd']); // date('D M d Y H:i:s -0000', $sdate)


        echo '<div class="sd" start="'.date('Ymd\THis', $sdate).'" end="'.date('Ymd\THis', $edate).'">'; //saving in this format for export to iCal
        echo date('D, M j', $sdate);
        //only show end date if different from start date
        if(date('d',$sdate) != date('d',$edate)){
             echo ' - '.date('D, M j', $edate);
        }
        echo "<br><br>";
        if ($value["allday"]!=1){
            echo date('g:ia', $sdate).' to ';
            echo "<br>";
            echo date('g:ia', $edate);
        }
        echo "</div>";
        $desc = str_replace("'", "\'", $value["description"]);
        $desc = str_replace('"','&quot;',$value["description"]);

        if(!($desc == "")){

        echo '<br> <button class="infoBtn" onClick="showDescription(this)" val="'.$desc.'""><span class="glyphicon glyphicon-info-sign" aria-hidden="true" aria-label="description"></span></button>';

        }
        echo '<button class="infoBtn" onclick="downloadiCal('.$value['id'].')"><span class="glyphicon glyphicon-calendar" aria-hidden="true" aria-label="Export to Calendar"></span></button>';
        echo '</div> 
        <div class="col-xs-9 col-sm-7">
            <div class="programTitle">';
            echo '<a href="eventdetails.php?id='.$value['id'].'">' . $value["title"] . '</a>';
            echo'</div><div class="programSubtitle">';
            echo $value["subtitle"];
            echo'</div><a href="http://maps.google.com/?q='.$value["address"].'">';
            echo $value["address"];
            echo"</a><div class='phone'>";
            echo $value["phone"];
            echo"</div>";

            if ($value["imageurl"]){
                echo '<div class="visible-xs" onclick="showImage(this)" val="'.$value["imageurl"].'">View Poster</div>';
               // echo '<a  href="'.$value["imageurl"].'">View Poster</a>';
                echo "<br>";
            }

            if ($value["siteurl"]){
                $siteurl = $value["siteurl"];
                //if it doesn't contain http OR https, add https
                if((strpos($siteurl, "http://") === false) && (strpos($siteurl, "https://") === false))
                {
                      $siteurl = "https://".$siteurl; 
                }
                echo '<a  href="'.$siteurl.'">'.$value["siteurl"].'</a>';
                echo "<br>";
            }

        //echo '<a class="" href="http://maps.google.com/?q='.$value["address"].'"><img src="http://isangat.org/map.png" border="0"></a><br>';
            echo '</div>';
        //</div>';
        echo '<div class="col-sm-3 visible-sm visible-md visible-lg">  <img class="img-thumbnail" src="'.$value["imageurl"].'" style="max-height:170px" onclick="showImage(this)" val="'.$value["imageurl"].'"/></div></div>';
        echo '</div>'; //end row
    }
}

?>

<div id="myModal" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>



</body>
</html>