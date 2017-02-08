<?php 
if ($error != '')
{
echo '<div style="padding:4px; border:1px solid red; color:red;">'.$error.'</div>';
}

session_start();

if ($_SESSION['user'] == null){
   header("Location:" . "login.php");
   exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <script src="datetimepicker_css.js"></script>
    <meta name="viewport" content="user-scalable=yes, width=device-width" />
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
 <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
 <link href="navbar.css" rel="stylesheet">
 <script type="text/javascript">
  var locations = null;
  var locNames = [];
$(document).ready(function () {


    $.getJSON('locations.json', function (data) {
  locations = data;
  var select = document.createElement("select");
//var locations = json.parse(loc);
 for (var key in locations)
{
  locNames.push(key);
  //if (!locations.hasOwnProperty(key)) {
        //The current property is not a direct property of p
       // continue;
   // }
    //var option = document.createElement("option");
   // option.value = locations[key];
  //option.text = locations[key];
 //select.appendChild(option);
}
//var element = document.getElementById("a");
//element.appendChild(select);
       });

$( "#location" ).on( "autocompleteselect", function( event, ui ) {
  var key = ui.item.label;
  $("#address").val(locations[key].Address);
} );

    $("#location").autocomplete({
      source: locNames
    });
    });






</script>

  </head>
<body>
<?php 
include('header.html');


if (is_numeric($_POST['id'])) 
{
$id = $_POST['id'];

echo $id;
// connect to the database
   include('config.php');

 $sql = "SELECT * FROM events_all.programtbl WHERE id = '$id'";
 if ($result = mysqli_query($conn, $sql)){
  echo "success";
   $arr = $result->fetch_array();

 $title = $arr["title"];
 $subtitle = $arr["subtitle"];
 $address = $arr["address"];
 $phone = $arr["phone"];
 $sd = $arr["sd"];
 $ed = $arr["ed"];
 //$source = $arr["source"];
 $description = $arr["description"];
}
}
?>
<div style="padding:10px">
Welcome! Please submit a program by filling out the fields below. 
<br>

<form id="addprogram" action="commitprogram.php" method="post" >
  Title:<br>
  <div class="row">
    <div class="col-sm-6">
  <input type="text" value="<?php echo $title; ?>" name="title" class="form-control"><br>
  <label for="location">Location: </label><br>
  <input id="location" name="subtitle" value="<?php echo $subtitle; ?>" class="form-control"><br>
   Address:<br>
  <input type="text" id="address" name="address" value="<?php echo $address; ?>" class="form-control"><br>
   Phone Number:<br>
  <input type="text" name="phone" value="<?php echo  $phone; ?>" class="form-control"><br>
   
   Start Date:<br>
  <input type="text" name="sd" value="<?php echo $sd; ?>" id="sd1" class="form-control">
  <img src="images2/cal.gif" onclick="javascript:NewCssCal('sd1','yyyyMMdd','dropdown',true,'24')" style="cursor:pointer"/><br>

   EndDate:<br>  
  <input type="text" name="ed" value="<?php echo $ed; ?>" id="sd2" class="form-control">
  <img src="images2/cal.gif" onclick="javascript:NewCssCal('sd2','yyyyMMdd','dropdown',true,'24')" style="cursor:pointer"/><br>

 <!--  Source:<br>
  <input type="text" name="source" value="<?php echo  $source; ?>"><br> -->
  Description:<br>
  <textarea name="description" class="form-control"><?php echo $description; ?></textarea>
  <br>
  <!--
   Password(You must have authorization to submit a program):<br>
  <input type="password" name="password" value=""><br>
     -->
</div>
</div>
     <input type='hidden' name='id' value="<?php echo $id; ?>"/>
     <br>
  <button name="submit" value="Submit" class="btn btn-default">Submit</button>
  <?php if (!is_null($id)){
  echo  '<button name="clone" value="Clone" class="btn btn-default">Clone</button>';
	}
   ?>

</form> 

</div>

</body>
</html>