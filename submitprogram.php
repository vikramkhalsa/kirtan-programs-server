<?php 
session_start();
if ($error != '')
{
echo '<div style="padding:4px; border:1px solid red; color:red;">'.$error.'</div>';
}



if ($_SESSION['user'] == null){
   header("Location:" . "login.php");
   exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Submit a Program</title>
 <script src="datetimepicker_css.js"></script>
    <meta name="viewport" content="user-scalable=yes, width=device-width" />
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
 <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
 <link href="navbar.css" rel="stylesheet">
 <script src="jquery-ui-timepicker-addon.js"></script>
 <link href="jquery-ui-timepicker-addon.css" rel="stylesheet">

 <script type="text/javascript">
  var locations = null;
 // var locNames = [];

    <?php 

    include('config.php');
     $sql = "SELECT * FROM events_all.locationtbl";   
     $result = mysqli_query($conn, $sql);
    $array = array();
    $names = array();
    while($row=mysqli_fetch_assoc($result))
    {
      if($row['privateowner']== null){
        $array[$row["name"]] = $row;
        $names[] = $row["name"];
      }
    }
print "var data ='".json_encode($array)."';\n";
print "var locNames = ".json_encode($names).";\n";
 mysqli_close($conn);
    ?>

function setTimes(prefix){

  var sdate = $("#" + prefix + "d").val();
var stime = $("#"+prefix + "t").val();
var sm = $("#s" + prefix +"m").val();
//handle midnight and noon still!!! use val for 12 =0
var hrs = parseInt(stime.substring(0,2));

if (sm=="PM"){
  if (hrs < 12)
    stime = (hrs + 12) + stime.substring(2,5);
}else {
if (hrs >=12)
    stime = (hrs -12) + stime.substring(2,5);
}

var date = sdate + " " + stime;
$("#" + prefix + "dfull").val(date);

}


$(document).ready(function () {

$("#sd1").datetimepicker({
  controlType: 'select',
  timeFormat: 'hh:mm tt',
  dateFormat: 'yy-mm-dd',
  stepMinute: 15,
  oneLine: true,
  altField: "#sd",
  altFieldTimeOnly: false,
  altFormat: "yy-mm-dd",
  altTimeFormat: "HH:mm",
});


$("#ed1").datetimepicker({
  controlType: 'select',
  timeFormat: 'hh:mm tt',
  dateFormat: 'yy-mm-dd',
  stepMinute: 15,
  oneLine: true,
  altField: "#ed",
  altFieldTimeOnly: false,
  altFormat: "yy-mm-dd",
  altTimeFormat: "HH:mm",
});

$("#ed2").datepicker({
  dateFormat: 'yy-mm-dd'
});


$('#repeat').on('change', function(){
if ($(this).is(":checked")){
  $('#recurrence-panel').show();
}
else{
   $('#recurrence-panel').hide();
}
});

$("#sd1").on("change", function(){
  if($('#ed1').val()){
  }
  else{
     var time = $(this).datetimepicker('getDate');
     time.setHours(time.getHours() + 2);
     $("#ed1").datetimepicker('setDate', time);
    //$('#ed1').val($(this).val());
  }
  
});

  var select = document.createElement("select");
  locations = JSON.parse(data);

$( "#location" ).on( "autocompleteselect", function( event, ui ) {
  var key = ui.item.label;
  $("#address").val(locations[key].address +', ' + locations[key].city + ' ' + locations[key].state);
  $("#zip").val(locations[key].zip);
  $("#phone").val(locations[key].phone);
} );

    $("#location").autocomplete({
      source: locNames
    });

$("[required]").before("<span style='color:red'>*</span>");


    });

function submitForm(){
  //VALIDATE FORM

var input = document.getElementById("#title");

//check/fix  dates and don't submit if they are invalid!~

//style invalid controls only after submitting
 var form= $(this).closest('#addprogram');
var ips =  form.find('[required]');
 ips.addClass('notvalid');


var repeat = $('#repeat').is(":checked");
if(repeat){
  var tim = $('#ed1').datetimepicker('getDate')- $('#sd1').datetimepicker('getDate');
  var mins = tim/(1000*60);
  //var interval = $('#interval').val();
  var freq = $('#freq').val();
  $('#repeat').val("FREQ="+freq+";DURATION="+mins);
  var newed = $('#ed2').val();
  var oldet =$('#ed').val().slice(10)
  $('#ed').val(newed+ oldet);
}
else {
  $('#repeat').val("");
}
}


function convertDates(){
var form = document.getElementById("addProgram");
var sd= document.getElementById("sd1");
var ed = document.getElementById("ed1");  
var sdate = new Date(sd.value).toISOString();
var edate = new Date(ed.value).toISOString();
sd.value = sdate;
ed.value = edate;
form.submit();
}



</script>

<style>

.notvalid{
 box-shadow: 0 0 3px 1px red }

  
/*input[required]:invalid:focus { box-shadow: 0 0 3px 1px red }*/

.notvalid:valid{
box-shadow: 0 0 3px 1px green }
}

</style>

  </head>
<body>
<?php 
include('header.html');

$showpanel = 'style="display: none"';
if (is_numeric($_POST['id'])) 
{
$id = $_POST['id'];

//echo $id;
// connect to the database
   include('config.php');

 $sql = "SELECT * FROM events_all.programtbl WHERE id = '$id'";
 if ($result = mysqli_query($conn, $sql)){
 // echo "success";
   $arr = $result->fetch_array();

 $title = htmlspecialchars($arr["title"]);
 $subtitle = htmlspecialchars($arr["subtitle"]);
 $address = htmlspecialchars($arr["address"]);
 $phone = htmlspecialchars($arr["phone"]);
 $sd = htmlspecialchars($arr["sd"]);
 $sd1 = date_format(new DateTime($sd),'Y-m-d h:i a');
 $ed = htmlspecialchars($arr["ed"]);
 $ed1 = date_format(new DateTime($ed),'Y-m-d h:i a');
  $ed2 = date_format(new DateTime($ed),'Y-m-d');
 $type = htmlspecialchars($arr["type"]);
 $zip = htmlspecialchars($arr["zip"]);
 //$source = $arr["source"];
 $description = $arr["description"];
$recurr = ($arr["rrule"] != null && $arr["rrule"] !== '')
                    ? true
                    : false;

 $repeat =  $recurr ? "checked" : "";
 if ($recurr)
  $showpanel = "";
$rrule = htmlspecialchars($arr['rrule']);
if ($recurr){
  $rrules = array();
$rruleStrings = explode(';', $rrule);
foreach ($rruleStrings as $s) {
         list($k, $v) = explode('=', $s);
           $rrules[$k] = $v;
         }

          //get duration
   $duration = (isset($rrules['DURATION']) && $rrules['DURATION'] !== '')
                    ? $rrules['DURATION']
                    : "120";
  $freq = $rrules["FREQ"];
 //echo "DURATION";
 echo $duration;

 //update end date 
  $ed3 = new DateTime($sd);
  $ed3->add(new DateInterval('PT'.$duration.'M'));
   $ed1 = date_format($ed3,'Y-m-d h:i a');
echo $ed1;
}

}
}
?>
<div style="padding:10px">
Welcome! Please submit a program by filling out the fields below. 
<br><br>

<form id="addprogram" action="commitprogram.php" method="post" class="form-group" onsubmit="submitForm()">
  <div class="row">
    <div class="col-sm-6">
  <label for="title">Title: </label>
  <input type="text" id="title" value="<?php echo $title; ?>" name="title" class="form-control" required placeholder="Kirtan Divaan"><br>
  
  <label for="location">Location: </label>
  <input id="location" name="subtitle" value="<?php echo $subtitle; ?>" class="form-control" required placeholder="San Jose Gurdwara Sahib"><br>

  <label for="address">Address: </label>
  <input type="text" id="address" name="address" value="<?php echo $address; ?>" class="form-control"><br>
  
  <label for="zip">Zip Code: </label>
  <input type="text" id="zip" name="zip" value="<?php echo $zip; ?>" class="form-control" required maxlength=10 placeholder="12345" pattern="[0-9]{5}"><br>
  
  <label for="phone">Phone Number:</label>
  <input type="tel" name="phone" id="phone" value="<?php echo  $phone; ?>" class="form-control" maxlength=16 placeholder="1234567890"><br>  



<input type='hidden' name='sd' id='sd' value="<?php echo $sd; ?>">
<input type='hidden' name='ed' id='ed' value = "<?php echo $ed; ?>">
   <label for="sd1">Start Date and Time:</label>
  <input type="text" name="sd1" value="<?php echo $sd1; ?>" id="sd1" class="form-control" placeholder="yyyy-mm-dd hh:mm pm"
  pattern="^[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}\s[a-z]{2}" required>
<br>

  <label for="ed1">End Date and Time:</label>
  <input type="text" name="ed1" value="<?php echo $ed1; ?>" id="ed1" class="form-control" placeholder="yyyy-mm-dd hh:mm am" 
    pattern="^[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}\s[a-z]{2}">
<br>

   <label for="repeat">Repeat </label>
<input type="checkbox" id="repeat" name="repeat" value="" <?php echo $repeat; ?> >
<br>
 <div <?php echo $showpanel; ?>  id="recurrence-panel">

    <div class="row">
     <div class="col-xs-6">
          Repeat

  <?php $freqOpts= array("DAILY","WEEKLY"); ?>
  <select class="form-control" id="freq">
  <?php foreach ($freqOpts as $value) { ?>
  <option value="<?php echo $value;?>" <?php echo ($value== $freq) ? ' selected="selected"' : '';?>><?php echo ucfirst($value);?></option>
<?php } ?>
  </select>
       </div>

<!-- <div class="col-xs-4">
      Every # of days
  <input type="text" id="interval" class="form-control" value="1" maxlength="3">
     </div> -->


     <div class="col-xs-6">
      Until
  <input type="text" id="ed2" class="form-control" value="<?php echo $ed2; ?>">
     </div>
  </div>
</div>

 <br>
<label for="type">Event Type:</label>


<?php $plan = array("kirtan","katha","fundraiser","discussion","samaagam","camp", "other"); ?>

<select name="type" class="form-control">
<?php foreach ($plan as $value) { ?>
  <option value="<?php echo $value;?>" <?php echo ($value== $type) ? ' selected="selected"' : '';?>><?php echo ucfirst($value);?></option>
<?php } ?>


<!-- <option name=one value=kirtan> Kirtan </option>
<option name=two value=katha> Katha </option>
<option name=three value=fundraiser> Fundraiser </option>
<option name=three value=discussion> Discussion </option>
<option name=three value=samaagam> Samaagam </option>
<option name=three value=other> Other </option> -->

</select></br>

 <!--  Source:<br>
  <input type="text" name="source" value="<?php echo  $source; ?>"><br> -->
  <label for="description">Description:</label>
  <textarea name="description" class="form-control"><?php echo $description; ?></textarea>
  <br>
</div>
</div>
     <input type='hidden' name='id' value="<?php echo $id; ?>"/>
     <br>
  <button id="submit" type="submit" class="btn btn-primary">Submit</button>
  <?php if (!is_null($id)){
  echo  '<button name="clone" value="Clone" class="btn btn-default">Clone</button>';
  }
   ?>

</form> 

</div>

</body>


</html>