<?php

session_start();

if ($_SESSION['user'] == null){
 header("Location:" . "login.php");
 exit();
}
$user = $_SESSION['user'];

// process.php

$errors         = array();      // array to hold validation errors
$data           = array();      // array to pass back data

// validate the variables ======================================================
// if any of these variables don't exist, add an error to our $errors array

if (empty($_POST['name']))
    $errors['name'] = 'Name is required.';

if (empty($_POST['address']))
    $errors['address'] = 'Address is required.';

if (empty($_POST['zip']))
    $errors['zip'] = 'Postal Code is required.';

if (empty($_POST['city']))
    $errors['city'] = 'City is required.';

if (empty($_POST['state']))
    $errors['state'] = 'State is required.';

if (empty($_POST['region']))
    $errors['state'] = 'Region is required.';

// return a response ===========================================================

    // if there are any errors in our errors array, return a success boolean of false
if ( ! empty($errors)) {

        // if there are items in our errors array, return those errors
    $data['success'] = false;
    $data['errors']  = $errors;
} else {

        // if there are no errors process our form, then return a message

        // DO ALL YOUR FORM PROCESSING HERE
        // THIS CAN BE WHATEVER YOU WANT TO DO (LOGIN, SAVE, UPDATE, WHATEVER)

    include('config.php');

//get values from POST and escape them
    $name = $conn->real_escape_string($_POST['name']);
    $address = $conn->real_escape_string($_POST['address']);
    $city = $conn->real_escape_string($_POST['city']);
    $state = $conn->real_escape_string($_POST['state']);
    $zip = $conn->real_escape_string($_POST['zip']);
    $regionid = $conn->real_escape_string($_POST['region']);

$public = $conn->real_escape_string($_POST['public']);;//check if not public, then owner is user, otherwise blank
$owner = null;
if($public == "false")
    $owner = $user;


//post values to database

$sql = "INSERT INTO events_all.locationtbl (name, address, city, state, zip, addedby, privateowner, region)
VALUES ('$name', '$address', '$city', '$state', '$zip', '$user', '$owner', '$regionid')";

if ($conn->query($sql) === TRUE) {
        // show a message of success and provide a true success variable
    $data['success'] = true;
    $data['message'] = 'Success!';
    $sql = "SELECT LAST_INSERT_ID();";
    $result = mysqli_query($conn, $sql);
    while($row=mysqli_fetch_assoc($result)){
        $data['locationid'] = $row["LAST_INSERT_ID()"];
    }



} else {
            // show a message of success and provide a true success variable
    $data['success'] = false;
    $data['message'] = "Error: " . $conn->error;
}
mysqli_close($conn);

}

    // return all our data to an AJAX call
echo json_encode($data);

