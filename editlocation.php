<?php

//editlocation.php
//Vikram Singh
//10/27/2017

//This file provides a form for adding a new location, updating an existing location (if id is passed to it)
//or deleting a location.  This is linked to the edit button in locationsadmin page. Typically users should only be able to edit/delete their own locations, and admins can edit/delete all locations. This should be enforced at the API level, in addlocation.php.


session_start();

if ($_SESSION['user'] == null){
 header("Location:" . "login.php");
 exit();
}

?>

<html>

<head>

<meta name="viewport" content="user-scalable=yes, width=device-width" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<link href="navbar.css" rel="stylesheet">

<script type="text/javascript">

    // process the form
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
        'city'           : $('#loc-city').val(),
        'state'           : $('#loc-state').val(),
        'region'           : $('#loc-region').val(),
        'public'           : $('#loc-public').get(0).checked
      };

      if ($('#loc-id').val()){
        formData['locationid'] = $('#loc-id').val();
      }

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
                $('#location-message').html(data.message);
                $("#location").val($('#loc-name').val());
                $("#locationid").val(data.locationid);
                $("#address-info").removeClass("redfont");
                $("#address-info").html("Address: " + formData.address +', ' + formData.city + ' ' + formData.state);

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



      function deletelocation(){
        var formData = {};
        formData['submit'] = 'delete';
        formData['locationid'] = $('#loc-id').val();
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
             $('#location-message').html("Location deleted successfully!");
             $("#location").val($('#loc-name').val());
             $("#locationid").val(data.locationid);
             $("#address-info").removeClass("redfont");
             $("#address-info").html("Address: " + formData.address +', ' + formData.city + ' ' + formData.state);

          }else {
            
            $('#location-message').addClass("alert alert-danger");

            for (var error in data.errors){
              $('#location-message').append(data.errors[error] +"<br>");
            }

            $('#location-message').append(data.message);

          }

          // here we will handle errors and validation messages
        });

      }


  </script>

</head>

<body>

<?php 

  $contents = file_get_contents('http://www.sikh.events/getlocations.php'.$filter);

  $regions = json_decode($contents, true);


  $id = null;

  if (is_numeric($_GET['id'])) 
  {
    $id = $_GET['id'];
    //form with values from passed in id.
  	//echo $id;
  	// connect to the database
    include('config.php');
  	$sql = "SELECT * FROM events_all.locationtbl WHERE locationtbl.locationid = '$id'";

    

    if ($result = mysqli_query($conn, $sql)){
  	  //echo "success";
      $arr = $result->fetch_array();
      $name = htmlspecialchars($arr["name"]);
      $address = htmlspecialchars($arr["address"]);
      $city = htmlspecialchars($arr["city"]);
      $state = htmlspecialchars($arr["state"]);;
      $zip = htmlspecialchars($arr["zip"]);
      $regionid = htmlspecialchars($arr["region"]);
      $addedby = htmlspecialchars($arr["addedby"]);
      $privateowner = htmlspecialchars($arr["privateowner"]);

   	} else {
   		echo "FAIL!";
   	}

  }

?>


  <div id="location-panel" class="panel" style="background-color:#EEE; padding:10px;">
    <div class="row">
       <div class="col-sm-12">
        <h4>  <?php if ($id==null){echo 'Add New Location';}else {echo 'Update Location';} ?></h4> 
        <div id="location-message"> </div>

         <input type="hidden" id="loc-id" class="form-control" value="<?php echo $id; ?>"><br> 
        <label for="loc-name">Name: </label>
        <input type="text" id="loc-name" class="form-control" placeholder="Sri Guru Singh Sabha" value="<?php echo $name; ?>"><br> 

        <label for="address">Street Address: </label>
        <input type="text" id="loc-address" class="form-control" placeholder="123 Gurdwara Rd" value="<?php echo $address; ?>"><br>
        
        <label for="loc-city">City: </label>
        <input type="text" id="loc-city" class="form-control" placeholder="Begampura" value="<?php echo $city; ?>"><br>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-6">
        <label for="loc-city">State: </label>
        <input type="text" id="loc-state" class="form-control" maxlength=3 placeholder="CA" value="<?php echo $state; ?>"><br>
      </div>
      <div class="col-xs-6">
        <label for="loc-zip">Postal Code: </label>
        <input type="text" id="loc-zip" class="form-control" maxlength=10 placeholder="12345" pattern="[0-9]{5}" value="<?php echo $zip; ?>"><br>
      </div>
    </div>

    <div class="row">
     <div class="col-xs-6">
      <label for="loc-city">Region: </label>
     <!--  dynamically populate available regions from DB with region ids and names -->
        <select class="form-control" id="loc-region">
          <?php foreach ($regions as $region) { ?>
          <option value="<?php echo $region['regionid'];?>"  <?php    if ($region['regionid'] == $regionid){echo 'selected';}?> > <?php echo $region['name'];?></option>
          <?php } ?>
        </select>
      </div>
    </div>

    <!-- need to uncheck if its private! -->
    <input type="checkbox" id="loc-public"  <?php if ($privateowner == null) {echo 'checked';} ?> >
    Make this location available to other event creators (public)
    <br><br>
    <input type="button" id="submit-loc" class="btn btn-success" value="Save Location" onclick="savelocation()"/>

    <?php
      //if admin, show delete button
      if ($_SESSION['usertype'] == "admin"){
        echo '<input type="button" id="del-loc" class="btn btn-danger" value="Delete Location" onclick="deletelocation()"/>';
      }       
    ?>
    <!--      <input type="button" class="btn btn-default" onclick="hidelocpanel()" value="Cancel" /> -->
  </div>


</body>
</html>

