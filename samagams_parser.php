<?php

//samagams_parser.php
//Vikram Singh
//11/17/2017
//pulls and parses events from samagams.org saved to a csv excel file. 
//waiting for them to expose an API or better website which is easier to parse. 

$ar = [];
$row = 1;

echo '[';

//csv file contains table of data from samagams.org
//parse csv file by line
if (($handle = fopen("samagams.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
       // $num = count($data);
        //echo "<p> $num fields in line $row: <br /></p>\n";
        $row++;
        if ($data[0] != null && trim($data[0])!==''){

        	try{

                if ($row>2)// add comma after first item (for building json array)
                  echo ',';
                $prog = [];

                $sd = "";
                $ed = "";
                $test = $date = explode("-",$data[0]);
                //parse dates
                if (strlen($test[1])>3){
        	       //Mar 27-April 3 case
                   $date = explode("-",$data[0]);
                   $sd = $date[0]." 2017";
                   $ed = $date[1]." 2017";
                }
                else {
            		//Mar 25-27 case
                    $date = explode(" ",$data[0]);
                    $month = $date[0];
                    $days = explode('-',$date[1]);
                    $sd = $month.' '.$days[0]." 2017";
                    $ed = $month.' '.$days[1]." 2017";
                }

                $loc = explode(',',$data[1])[0];
                $prog['sd'] = date("Y-m-d H:i:00", strtotime($sd));
                $prog['ed'] = date("Y-m-d H:i:00", strtotime($ed));
                $prog['subtitle']=$data[1];
                $prog['address']= "";
                $prog['phone']=$data[3];
                $prog['title'] = $loc." Samagam";
                $prog['description'] ="Sevadar: ".$data[2].'<br>'.'Nearest Airport: '.$data[4];
                echo json_encode($prog);  
            } 
            catch (Exception $error){
            //if something goes wrong, just skip this row
            }

        // for debug
        // echo "Start: ".date("Y-m-d H:i:00", strtotime($sd));
        // echo '<br>';
        // echo "End: ".date("Y-m-d H:i:00", strtotime($ed));
        // echo '<br>';
        // echo "Locations: ".$data[1];
        //   echo '<br>';
        // echo "Sevadar: ".$data[2];
        //   echo '<br>';
        // echo "Phone #: ".$data[3];
        //   echo '<br>';
        // echo "Nearest Airport: ".$data[4];
        //   echo '<br>';
        // echo "Additional Info: ".$data[5];
        //   echo '<br>';
        //   echo '<br>';

        }

    }
    fclose($handle);

    echo ']';
}

?>