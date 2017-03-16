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

</head>
<body>
  <?php include('header.html'); ?>  
      <div>

    <p>Welcome! Upcoming programs are listed below. 
        We hope to expand to include more Gurdwaras and locations soon. 
        Download the android app <a href="https://play.google.com/store/apps/details?id=com.vikramkhalsa.isangat"> here. </a>
        If you are hosting a Kirtan event and would like to submit it to this list, 
        please click <a href="submitprogram.php"> Submit a program. </a>
         You will be asked to log in or create a user account. </p>
</div>


<iframe src="http://ekhalsa.com/programs.php" style="width:100%; height:100%"></iframe> 
</body>
</html>