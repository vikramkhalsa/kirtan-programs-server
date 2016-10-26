<html>
<head>
    <title>Bay Area Kirtan Programs</title>
<meta name="viewport" content="user-scalable=yes, width=device-width" />
<style type="text/css">

body {
    background: #fff;
    font-family: "Verdana";
    font-size: 1.0em;
}

.cell{
    width:100%;
    background-color: #EFEFFF;
    min-height: 100px;
    padding: 6px 11px;
    border-bottom: 1px solid #95bce2;
    vertical-align: top;
    height:auto;
}

.cell:hover {
    background-color: #bcd4ec;
}

</style>

</head>
<body>

<?php

// connect to the database
	include('config.php');
//programtbl.sd >= DATE(NOW()) AND
 $sql = "SELECT * FROM dharamkh_programs.programtbl WHERE  programtbl.approved=1 ORDER BY sd ASC";
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
        <div style="width:30%; float:left;">';
        echo $value["sd"];
        echo "<br>";
        echo $value["ed"];
        echo '</div> 
        <div style="width:70%; float:left;"> <strong>';
    	echo $value["title"];
        echo"</strong><br>";
        echo $value["subtitle"];
        echo"<br>";
        echo $value["address"];
        echo"<br>";
        echo $value["phone"];
        echo"<br>";
        echo '<a href="http://maps.google.com/?q='.$value["address"].'"><img src="http://isangat.org/map.png" border="0"></a><br>';
        echo '</div>
    </div>';
    
    }
?>


<div> 
    <div class="cell">
        <div style="width:30%; float:left;">
            Date
            <br>
            Time
            <br>
        </div> 
        <div style="width:70%; float:left;"> 
            Title <br>
            subtitle <br>
            Address <br>
            Phone number<br>
        </div>
    </div>
</div>

</body>
</html>