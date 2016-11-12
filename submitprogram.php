<!DOCTYPE html>
<html>
<head>
    <script src="datetimepicker_css.js"></script>
    <meta name="viewport" content="user-scalable=yes, width=device-width" />
  </head>
<body>

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

if (is_numeric($_POST['id'])) 
{
$id = $_POST['id'];

echo $id;
// connect to the database
   include('config.php');

 $sql = "SELECT * FROM dharamkh_programs.programtbl WHERE id = '$id'";
 if ($result = mysqli_query($conn, $sql)){
  echo "success";
   $arr = $result->fetch_array();

 $title = $arr["title"];
 $subtitle = $arr["subtitle"];
 $address = $arr["address"];
 $phone = $arr["phone"];
 $sd = $arr["sd"];
 $ed = $arr["ed"];
 $source = $arr["source"];
 $description = $arr["description"];
}
}
?>

Welcome! Please submit a program by filling out the fields below. 
<br>

<form id="addprogram" action="commitprogram.php" method="post" >
  Title:<br>
  <input type="text" value="<?php echo $title; ?>" name="title"><br>
  Subtitle:<br>
  <input type="text" name="subtitle" value="<?php echo $subtitle; ?>"><br>
   Address:<br>
  <input type="text" name="address" value="<?php echo $address; ?>"><br>
   Phone Number:<br>
  <input type="text" name="phone" value="<?php echo  $phone; ?>"><br>
   
   Start Date:<br>
  <input type="text" name="sd" value="<?php echo $sd; ?>" id="sd1">
  <img src="images2/cal.gif" onclick="javascript:NewCssCal('sd1','yyyyMMdd','dropdown',true,'24')" style="cursor:pointer"/><br>

   EndDate:<br>  
  <input type="text" name="ed" value="<?php echo $ed; ?>" id="sd2">
  <img src="images2/cal.gif" onclick="javascript:NewCssCal('sd2','yyyyMMdd','dropdown',true,'24')" style="cursor:pointer"/><br>

  Source:<br>
  <input type="text" name="source" value="<?php echo  $source; ?>"><br>
  Description:<br>
  <input type="text" name="description" value="<?php echo $description; ?>"><br>
  <!--
   Password(You must have authorization to submit a program):<br>
  <input type="password" name="password" value=""><br>
     -->

     <input type='hidden' name='id' value="<?php echo $id; ?>"/>
  <input type="submit" value="Submit"> 
  <?php if (!is_null($id)){
  echo  '<input type="submit" name="clone" value="Clone">';
	}
   ?>

</form> 



</body>
</html>