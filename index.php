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

<?php

// connect to the database
  include('config.php');
//programtbl.sd >= DATE(NOW()) AND
 $sql = "SELECT * FROM events_all.programtbl WHERE programtbl.ed >= DATE(NOW()) AND  programtbl.approved=1 ORDER BY sd ASC";
    $result = mysqli_query($conn, $sql);

    $array = array();

    while($row=mysqli_fetch_assoc($result))
    {
      //echo $row;
        $array[] = $row;
       // echo ($row['content']);
    }


//echo $array;

 mysqli_close($conn);

echo "<div>"; 
    

    foreach($array as $value){
        echo '<div class="cell">
        <div style="width:30%; float:left; font-size:1.1em;  top: 50%; ">';
        $sdate = strtotime($value['sd']);
        echo date('D, M j', $sdate);
        echo "<br><br>";
        echo date('g:ia', $sdate).' to ';
        $edate = strtotime($value['ed']);
        echo "<br>";
        echo date('g:ia', $edate);
        echo '</div> 
        <div style="width:70%; float:left;">
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


</body>
</html>