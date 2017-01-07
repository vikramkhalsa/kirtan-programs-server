<html>
<head>
    <title>Bay Area Kirtan Programs</title>
<meta name="viewport" content="user-scalable=yes, width=device-width" />

<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
 
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<style type="text/css">

.programTitle {
  font-size: 1.4em;
}

.programSubtitle {
  font-weight: bold;
  font-size:1.1em;
}

body {
    background: #fff;
}

.cell{
    width:100%;
    padding: 10px;
    border-bottom: 1px solid gray;
    border-top: 1px solid gray;
    overflow:auto;

}

.cell:hover {
    background-color: #bcd4ec;
}

.navbar-default {
  background-color: #3f51b5;
  border-color: #303f9f;
}
.navbar-default .navbar-brand {
  color: white;
}
.navbar-default .navbar-brand:hover,
.navbar-default .navbar-brand:focus {
  color: #c5cae9;
}
.navbar-default .navbar-text {
  color: white;
}
.navbar-default .navbar-nav > li > a {
  color: white;
}
.navbar-default .navbar-nav > li > a:hover,
.navbar-default .navbar-nav > li > a:focus {
  color: #c5cae9;
}
.navbar-default .navbar-nav > .active > a,
.navbar-default .navbar-nav > .active > a:hover,
.navbar-default .navbar-nav > .active > a:focus {
  color: #c5cae9;
  background-color: #303f9f;
}
.navbar-default .navbar-nav > .open > a,
.navbar-default .navbar-nav > .open > a:hover,
.navbar-default .navbar-nav > .open > a:focus {
  color: #c5cae9;
  background-color: #303f9f;
}
.navbar-default .navbar-toggle {
  border-color: #303f9f;
}
.navbar-default .navbar-toggle:hover,
.navbar-default .navbar-toggle:focus {
  background-color: #303f9f;
}
.navbar-default .navbar-toggle .icon-bar {
  background-color: white;
}
.navbar-default .navbar-collapse,
.navbar-default .navbar-form {
  border-color: white;
}
.navbar-default .navbar-link {
  color: white;
}
.navbar-default .navbar-link:hover {
  color: #c5cae9;
}

@media (max-width: 767px) {
  .navbar-default .navbar-nav .open .dropdown-menu > li > a {
    color: #dfe9f1;
  }
  .navbar-default .navbar-nav .open .dropdown-menu > li > a:hover,
  .navbar-default .navbar-nav .open .dropdown-menu > li > a:focus {
    color: #c5cae9;
  }
  .navbar-default .navbar-nav .open .dropdown-menu > .active > a,
  .navbar-default .navbar-nav .open .dropdown-menu > .active > a:hover,
  .navbar-default .navbar-nav .open .dropdown-menu > .active > a:focus {
    color: #c5cae9;
    background-color: #303f9f;
  }

  .navbar-default .navbar-toggle{
    float:left;
    margin-left: 10px;
  }
}

</style>

</head>
<body>
  <nav class="navbar navbar-default">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Bay Area Kirtan Programs</a>
          </div>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <li class="active"><a href="programslisting.php">All Programs</a></li>
              <li><a href="http://www.isangat.org">iSangat.org</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>
      <div>

    <p>Welcome! Upcoming programs are listed below. 
        We hope to expand to include more Gurdwaras and locations soon. 
        Download the android app <a href="https://play.google.com/store/apps/details?id=com.vikramkhalsa.isangat"> here. </a>
        If you are hosting a Kirtan event and would like to submit it to this list, 
        please click <a href="submitprogram.php"> here. </a>
         You will be asked to log in or create a user account. </p>
</div>

<?php

// connect to the database
  include('config.php');
//programtbl.sd >= DATE(NOW()) AND
 $sql = "SELECT * FROM dharamkh_programs.programtbl WHERE programtbl.ed >= DATE(NOW()) AND  programtbl.approved=1 ORDER BY sd ASC";
    $result = mysqli_query($conn, $sql);

    $array = array();

    while($row=mysqli_fetch_assoc($result))
    {
      //echo $row;
        $array[] = $row;
       // echo ($row['content']);
    }


//echo $array;

 mysqli_close('$conn');

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