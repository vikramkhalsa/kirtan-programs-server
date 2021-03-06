<?php
//submitprogram.php
//Vikram Singh
//11-15-2017
//This file provides the main form for users to submit new events. It is also used to edit and clone existing events. 
//There is a lot of complex js code to handle the date times and recursion. it also has a 'sub'form embedded into it for adding new locations inline.  Ideally that should be shared between the new location add form page (TBD).
//Added ability to import event details from facebook using the event url and fb graph API.


 
session_start();
if ($error != '')
{
  echo '<div style="padding:4px; border:1px solid red; color:red;">'.$error.'</div>';
}


if ($_SESSION['user'] == null){
  $_SESSION['enter_url'] = "submitprogram.php";
  header("Location:" . "login.php");
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Submit a Program</title>
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
//get all locations from the database and put them into a javascript object for autocomplete control
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

//what does this do? I think it sets  start date and time into the format that we want for php/mysql?
//converts 12 hr time to 24 hour time...
function setTimes(prefix){

  var sdate = $("#" + prefix + "d").val();
  var stime = $("#"+prefix + "t").val();
  var sm = $("#s" + prefix +"m").val();
  //handle midnight and noon still!!! use val for 12 =0
  var hrs = parseInt(stime.substring(0,2));

  if (sm =="PM"){
    if (hrs < 12)
      stime = (hrs + 12) + stime.substring(2,5);
  }else {
    if (hrs >=12)
      stime = (hrs -12) + stime.substring(2,5);
  }

  var date = sdate + " " + stime;
  $("#" + prefix + "dfull").val(date);

}

    $(document).on('change', ':file', function() {
        var input = $(this),
        //numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        $('#imageurl').val(label);
    });



$(document).ready(function () {

	$("#fbPanel-link").on('click',function(){
		$("#fbimport-panel").toggle();
	});

  //initalize datetime controls. 

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

  //show/hide note about all day times. Better way to do this would be hiding the time values completely. 
  $('#allday').on('change', function(){
    if ($(this).is(":checked")){
      $('#alldaynote').css('visibility', 'visible');
    }
    else{
      $('#alldaynote').css('visibility', 'hidden');
   }
 });

  //show/hide recurrence panel if repeat box is checked
  $('#repeat').on('change', function(){
    if ($(this).is(":checked")){
      $('#recurrence-panel').show(400);
    }
    else{
     $('#recurrence-panel').hide(400);
   }
 });

  //show additional controls if repeat frequency is monthly
  $('#freq').on('change', function(){
    if ($(this).val() =="MONTHLY"){
      $('#monthrow').show(200);
    }
    else{
     $('#monthrow').hide(200);
   }
 });

  //if the location they selected is not from list of known locations, prompt user to add new or select one
  $("#location").blur(function() {

    if (locNames.indexOf($("#location").val()) < 0){
      $("#address-info").addClass("redfont");
      $("#address-info").html("Please select an existing location or add a new location");
    }
  });

  var update_end = 0; 
  //if end date has not been set, set it to start date + 2 hours when start date is being set
  //just for convenient user experience. 
  $("#sd1").on("change", function(){
    if(update_end<=2){
     var time = $(this).datetimepicker('getDate');
     time.setHours(time.getHours() + 2);
     $("#ed1").datetimepicker('setDate', time);
     update_end +=1;
    //$('#ed1').val($(this).val());
    }
  }); 


  //set up autocomplete location select box and change handlers
  var select = document.createElement("select");
  locations = JSON.parse(data);

  $( "#location" ).on( "autocompleteselect", function( event, ui ) {
      var key = ui.item.label;
      $("#address-info").removeClass("redfont");
      $("#address-info").html("Address: " + locations[key].address +', ' + locations[key].city + ' ' + locations[key].state);
      $("#locationid").val(locations[key].locationid);
    //locations[key].locationid;  
    //$("#address").val(locations[key].address +', ' + locations[key].city + ' ' + locations[key].state);
    //$("#zip").val(locations[key].zip);
    $("#phone").val(locations[key].phone);
  });

  $("#location").autocomplete({
    source: locNames
  });

//$("[required]").before("<span style='color:red'>*</span>");

}); //end doc.ready functions


function submitForm(){
  
  if(!$("#locationid").val()){
    $("#address-info").addClass("redfont");
    $("#address-info").html("Please select an existing location or add a new location");
    return false;
  }
  //VALIDATE FORM

  var input = document.getElementById("#title");

  //check/fix  dates and don't submit if they are invalid!~

  //style invalid controls only after submitting
  var form= $(this).closest('#addprogram');
  var ips =  form.find('[required]');
  ips.addClass('notvalid');


  //handle reccuring event setup
  var repeat = $('#repeat').is(":checked");
  if(repeat){
    var tim = $('#ed1').datetimepicker('getDate')- $('#sd1').datetimepicker('getDate');
    var mins = tim/(1000*60);
    //var interval = $('#interval').val();
    var freq = $('#freq').val();
   
  var rep = "FREQ="+freq+";DURATION="+mins;
  //for monthly recurrence, get day of week and number
  if (freq=="MONTHLY"){
    const weekno = $('#weekno').val();
    const day = $('#weekday').val();
  rep = rep+";BYDAY="+weekno+day;

  }

  $('#repeat').val(rep);
    var newed = $('#ed2').val();
    var oldet =$('#ed').val().slice(10)
    $('#ed').val(newed+ oldet);
  }
  else {
    $('#repeat').val("");
  }

}


//not used. Was used to get dates in correct format from date controls
// function convertDates(){
// var form = document.getElementById("addProgram");
// var sd= document.getElementById("sd1");
// var ed = document.getElementById("ed1");  
// var sdate = new Date(sd.value).toISOString();
// var edate = new Date(ed.value).toISOString();
// sd.value = sdate;
// ed.value = edate;
// form.submit();
// }

//show Add New Location panel
function showlocpanel(){
 $('#location-panel').show(400);
}

//hide Add New Location panel
function hidelocpanel(){
 $('#location-panel').hide(400);
}


 // process the add location form
function savelocation() {

//this needs to be done in a better way
 // if(!$('#loc-name').get(0).checkValidity()){
 //  return;
 // }
 //  if(!$('#loc-address').get(0).checkValidity()){
 //  return;
 // }
 //  if(!$('#loc-zip').get(0).checkValidity()){
 //  return;
 // }
 //  if(!$('#loc-city').get(0).checkValidity()){
 //  return;
 // }
 //  if(!$('#loc-state').get(0).checkValidity()){
 //  return;
 // }

  // get the form data
  // there are many ways to get this data using jQuery (you can use the class or id also)
  var formData = {
    'name'          : $('#loc-name').val(),
    'address'       : $('#loc-address').val(),
    'zip'           : $('#loc-zip').val(),
    'city'          : $('#loc-city').val(),
    'state'         : $('#loc-state').val(),
    'region'        : $('#loc-region').val(),
    'public'        : $('#loc-public').get(0).checked
  };

  // process the form
  $.ajax({
      type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
      url         : 'addlocation.php', // the url where we want to POST
      data        : formData, // our data object
      dataType    : 'json', // what type of data do we expect back from the server
      encode          : true
    })
    // using the done promise callback
    .done(function(data) {
      // log data to the console so we can see
      //console.log(data); 
      $('#location-message').html('');
      $('#location-message').removeClass();

      if(data.success){

        $('#location-message').addClass("alert alert-success");
        $('#location-message').html("Location added successfully!");
        $("#location").val($('#loc-name').val());
        $("#locationid").val(data.locationid);
        $("#address-info").removeClass("redfont");
        $("#address-info").html("Address: " + formData.address +', ' + formData.city + ' ' + formData.state);
        $("#location-panel").delay(2000).fadeOut(500);
        //add id to hidden field
      }else {
        
        $('#location-message').addClass("alert alert-danger");

        for (var error in data.errors){
          $('#location-message').append(data.errors[error] +"<br>");
        }

        $('#location-message').append(data.message);
      }   

    // here we will handle errors and validation messages
    });

    // stop the form from submitting the normal way and refreshing the page
}

</script>

<style>

.notvalid{
box-shadow: 0 0 3px 1px red; 
}

/*input[required]:invalid:focus { box-shadow: 0 0 3px 1px red }*/

.notvalid:valid{
box-shadow: 0 0 3px 1px green; 
}

.redfont {
color:red;
}

.require::after{
content :"*";
color:red;
}

</style>

</head>
<body>
  <?php 
  include('header.html');

  $showpanel = 'style="display: none"';
  $showmonth = 'style="display: none"';
  $importerror = "";
  if (is_numeric($_POST['id'])) 
  {
    $id = $_POST['id'];

    //echo $id;
    // connect to the database
    include('config.php');
    include('functions.php');
    $result = getEventByID($id);

    if ($result){
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
      //$zip = htmlspecialchars($arr["zip"]);
     $locationid = htmlspecialchars($arr["locationid"]);
      //$source = $arr["source"];
     $description = $arr["description"];
     $imageurl = $arr["imageurl"];
     $siteurl = $arr["siteurl"];
     $recurr = ($arr["rrule"] != null && $arr["rrule"] !== '') ? true : false;
     $allday = (!empty($arr["allday"]) && $arr["allday"] ==1) ? "checked": ""; 
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
     $duration = (isset($rrules['DURATION']) && $rrules['DURATION'] !== '')     ? $rrules['DURATION']     : "120";
     //if it somehow got a negative duration, fix it.
     if (intval($duration) < 0){
        $duration = "120";
      }
     $freq = $rrules["FREQ"];

      $weekno = null;
      $dayofweek = null;

     if (isset($rrules['BYDAY']) && $rrules['BYDAY']!= null){

        $byday = $rrules['BYDAY'];
        $weekno = intval($byday);
        $dayofweek = substr($byday,-2);
        $showmonth = '';
     }

    //echo "DURATION";
     //echo $duration;

 //update end date 
     $ed3 = new DateTime($sd);
     $ed3->add(new DateInterval('PT'.$duration.'M'));
     $ed1 = date_format($ed3,'Y-m-d h:i a');
     //echo $ed1;
   }

 }
}
else if (isset($_GET['fburl'])){
  $fbid = $_GET['fburl'];
  
	$events_pos = strpos($fbid, "events/"); //get position of 'events/' in url
	if ($events_pos === False) {
		$importerror =
			"<div class='row'>
				<div class='col-xs-6'>
					<div class='alert alert-danger'>
						Invalid url, please ensure url is correct and try again.
					</div>
				</div>
			</div>";
} else {
	$fbid = substr($fbid,$events_pos+7); //trim off everthing left of events/, leaving {id}/blahblahblah

	//keep going until there are no more numbers. apparently the link won't always end with /
	$end_pos = 0;
	while (is_numeric($fbid[$end_pos])){
		$end_pos++;
	}

	//$end_pos = strpos($fbid, "/");
	$fbid = substr($fbid,0,$end_pos);

	$url = "https://graph.facebook.com/v2.11/".$fbid."?fields=name,description,cover,start_time,end_time,place,event_times&access_token=".$token;

 include('functions.php'); 

	$json = get_data($url);
	$event = json_decode($json, true);

	if ($event["error"]){
		$importerror = 
			"<div class='row'>
				<div class='col-xs-6'>
					<div class='alert alert-danger'>
						There was an error trying to import, please ensure url is correct and try again.
					</div>
			  	</div>
			</div>";
	}

	$description = $event['description'];
	$title = $event['name'];
  $locname = $event['place']['name'];
  $subtitle = "";

  $address = "Please try searching for '".$event['place']['name']."' above manually. If it does not appear, click 'Add New' and add this location.";

  //search through known locations to find this place
  foreach($names as $locc){
    if (strpos($locc, $locname)===false){
      //do nothing
    }
    else {//if found, update location, id, and address
      $subtitle = $locc;
      $address = $array[$locc]['address'].', '.$array[$locc]['city'].' '.$array[$locc]['state'];
      $locationid = $array[$locc]['locationid'];
      $phone = $array[$locc]['phone'];
    }
  }


	$sd = $event['start_time'];
	$sd = date_format(new DateTime($sd),'Y-m-d H:i');
	$sd1 = date_format(new DateTime($sd),'Y-m-d h:i a');
	$edt = $event['end_time'];
	if ($edt!= null){
		 $ed = date_format(new DateTime($edt),'Y-m-d H:i');
	     $ed1 = date_format(new DateTime($edt),'Y-m-d h:i a');
	     $ed2 = date_format(new DateTime($edt),'Y-m-d');
	 }
	$imageurl= $event["cover"]["source"];
	$siteurl = "https://www.facebook.com/events/".$fbid;

  }

}

$contents = file_get_contents('http://www.sikh.events/getlocations.php'.$filter);

$regions = json_decode($contents, true);

?>
<div class="container">
  Welcome! Please submit a program by filling out the fields below. 
  <br>
<strong><a href="#" id="fbPanel-link" >Import from Facebook</a><sup>NEW!</sup></strong> <br><br>

<?php  echo $importerror;   ?>

<div id="fbimport-panel"  style="display:none;">
  	<div class="row">
  		<div class="col-sm-6">
  			<form  action="submitprogram.php" method="get" class="form-group" style="background-color:#EEE; padding:10px;">
  			Paste the Facebook Event URL below and click "Import". Please verify imported data and complete missing fields before submitting. <br><br>
  			<div class="input-group">
  			<input name="fburl" type="text" class="form-control" required placeholder="https://www.facebook.com/events/12345" />
  	   		<span class="input-group-btn">
  				<input name="submit" type="submit" value="Import" class="btn btn-primary"/>
 			</span>
 			  </form>
  			</div>
  		</div>
  	</div>

</div>
  <form id="addprogram" action="commitprogram.php" method="post" class="form-group" onsubmit="return submitForm()" enctype="multipart/form-data" autocomplete="on">
    <div class="row">
      <div class="col-sm-6">
        <label for="title" class="require">Title: </label>
        <input type="text" id="title" value="<?php echo $title; ?>" name="title" class="form-control" required placeholder="Kirtan Divaan"><br>


        <label for="location" class="require">Location: </label> 
        <div class="input-group">
          <input type="text" class="form-control" id="location" name="subtitle" value="<?php echo $subtitle; ?>" placeholder="San Jose Gurdwara Sahib" required style="z-index: 1;">
          <span class="input-group-btn">
            <input type="button"  class="btn btn-default" value="Add New" onclick="showlocpanel()" style="z-index: 1;"/>
          </span>
        </div>

        <input type='hidden' name='locationid' id='locationid' value="<?php echo $locationid; ?>">

        <span id="address-info">
          <?php echo "Address: ".$address; ?>
        </span>

        <br><br>
        <div id="location-panel" class="panel" style="background-color:#EEE; padding:10px; display:none">
          <div class="row">
           <div class="col-sm-12">
            <h4>Add New Location</h4> 
            <div id="location-message"> </div>
            <label for="loc-name">Name: </label>
            <input type="text" id="loc-name" class="form-control" placeholder="Sri Guru Singh Sabha"><br> 

            <label for="address">Street Address: </label>
            <input type="text" id="loc-address" class="form-control" placeholder="123 Gurdwara Rd"><br>
            
            <label for="loc-city">City: </label>
            <input type="text" id="loc-city" class="form-control" placeholder="Begampura"><br>
          </div>
        </div>

        <div class="row">
         <div class="col-xs-6">
          <label for="loc-city">State: </label>
          <input type="text" id="loc-state" class="form-control" maxlength=3 placeholder="CA"><br>
        </div>
        <div class="col-xs-6">
          <label for="loc-zip">Postal Code: </label>
          <input type="text" id="loc-zip" class="form-control" maxlength=10 placeholder="12345" pattern="[0-9]{5}"><br>
        </div>
      </div>
        <div class="row">
         <div class="col-xs-6">
          <label for="loc-city">Region: </label>
         <!--  dynamically populate available regions from DB with region ids and names -->
            <select class="form-control" id="loc-region">
              <?php foreach ($regions as $region) { ?>
              <option value="<?php echo $region['regionid'];?>"> <?php echo $region['name'];?></option>
              <?php } ?>
            </select>
          </div>
      </div>

      <input type="checkbox" id="loc-public" checked>
      Make this location available to other event creators
      <br><br>
      <input type="button" id="submit-loc" class="btn btn-success" value="Save Location" onclick="savelocation()"/>
      <input type="button" class="btn btn-default" onclick="hidelocpanel()" value="Cancel" />
    </div>

 <!--  <label for="address">Address: </label>
  <input type="text" id="address" name="address" value="<?php echo $address; ?>" class="form-control"><br>
  
  <label for="zip">Zip Code: </label>
  <input type="text" id="zip" name="zip" value="<?php echo $zip; ?>" class="form-control" required maxlength=10 placeholder="12345" pattern="[0-9]{5}"><br>
-->
<label for="phone">Phone Number:</label>
<input type="tel" name="phone" id="phone" value="<?php echo  $phone; ?>" class="form-control" maxlength=16 placeholder="1234567890"><br>  


<label for="allday">All Day Event </label>
<input type="checkbox" id="allday" name="allday" value="" <?php echo $allday; ?> >
<span id="alldaynote" style="visibility:hidden"><em>Start and End Times will be ignored.</em></span>
<br>
<input type='hidden' name='sd' id='sd' value="<?php echo $sd; ?>">
<input type='hidden' name='ed' id='ed' value = "<?php echo $ed; ?>">
<label for="sd1" class="require">Start Date and Time: </label>
<input type="text" name="sd1" value="<?php echo $sd1; ?>" id="sd1" class="form-control" placeholder="yyyy-mm-dd hh:mm pm"
pattern="^[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}\s[a-z]{2}" required>
<br>

<label for="ed1" class="require">End Date and Time: </label>
<input type="text" name="ed1" value="<?php echo $ed1; ?>" id="ed1" class="form-control" placeholder="yyyy-mm-dd hh:mm am" 
pattern="^[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}\s[a-z]{2}" required>
<br>

<label for="repeat">Repeat </label>
<input type="checkbox" id="repeat" name="repeat" value="" <?php echo $repeat; ?> >
<br>
<div <?php echo $showpanel; ?>  id="recurrence-panel">

  <div class="row">
   <div class="col-xs-6">
    Repeat

    <?php $freqOpts= array("DAILY","WEEKLY", "MONTHLY"); ?>
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

<div class="row" id="monthrow" <?php echo $showmonth; ?> >

   <div class="col-xs-6">Every
<?php $weeknos = array("First", "Second","Third","Fourth","Fifth"); 
    $weekcount = 0;?>
    <select class='form-control' id="weekno">
 <?php foreach ($weeknos as $value) {
       $weekcount +=1; 
  ?>
      <option value="<?php echo $weekcount;?>" <?php echo ($weekcount == $weekno) ? ' selected="selected"' : '';?>><?php echo ucfirst($value);?></option>

      <?php } ?>
    </select>
     </div>
    <div class="col-xs-6">
Day of week
    <?php $weekdays = array('SU'=>'sunday','MO'=> 'monday','TU'=>'tuesday','WE'=>'wednesday','TH'=>'thursday','FR'=>'friday','SA'=>'saturday'); ?>
    <select class='form-control' id="weekday">
 <?php foreach ($weekdays as $value=>$day) { ?>
      <option value="<?php echo $value;?>" <?php echo ($value== $dayofweek) ? ' selected="selected"' : '';?>><?php echo ucfirst($day);?></option>
      <?php } ?>
    </select>


   </div>
  </div>
</div>

<br>
<label for="type">Event Type:</label>


<?php $types = array("kirtan","katha","camp","discussion","samaagam","seva","fundraiser","other"); ?>

<select name="type" class="form-control">
  <?php foreach ($types as $value) { ?>
  <option value="<?php echo $value;?>" <?php echo ($value== $type) ? ' selected="selected"' : '';?>><?php echo ucfirst($value);?></option>
  <?php } ?>
</select></br>

<!-- <option name=one value=kirtan> Kirtan </option>
<option name=two value=katha> Katha </option>
<option name=three value=fundraiser> Fundraiser </option>
<option name=three value=discussion> Discussion </option>
<option name=three value=samaagam> Samaagam </option>
<option name=three value=other> Other </option> -->



 <!--  Source:<br>
 <input type="text" name="source" value="<?php echo  $source; ?>"><br> -->
 <label for="description">Description:</label>
 <textarea name="description" class="form-control"><?php echo $description; ?></textarea>
 <br>

<label for="poster">Upload event poster or paste image url</label>
<!-- <input type="text" name="imageurl" id="imageurl" value="<?php echo  $imageurl; ?>" class="form-control">
<br> -->
<!-- <input type="file" name="fileToUpload" id="fileToUpload" class="form-control"> -->

<div class="input-group">
  <input type="text" name="imageurl" id="imageurl" value="<?php echo  $imageurl; ?>" class="form-control" style="z-index: 1;">
  <span class="input-group-btn style="z-index: 1;">
    <label class="btn btn-default btn-file">
      Browse <input type="file" style="display: none;" name="fileToUpload" id="fileToUpload">
    </label>
  </span>
</div>
<br>

<label for="poster">Website Url:</label>
<input type="text" name="siteurl" id="siteurl" value="<?php echo  $siteurl; ?>" class="form-control">
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