<?php
    require_once("./phpLibs/phpChart/conf.php");
    REQUIRE './SQLConnection.php';

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $graphType = $_GET['graphtype'];


    $sql = "SELECT DISTINCT 
        JourneyDetails.speed, JourneyDetails.RPM, JourneyDetails.time
        FROM  
            JourneyDetails
        WHERE
            JourneyDetails.journeyID = " . $_GET["journeyID"];

    $result = $conn->query($sql);

    $timeArray = array();
    $speedArray = array();    
    $rpmArray = array();

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            array_push($timeArray, $row["time"]);
            array_push($speedArray, $row["speed"]);
            array_push($rpmArray, $row["RPM"]);
        }
    } else {
    echo "0 results";
    }

    $conn->close();

    switch ($graphType){
        case "speed":
            $pc = new C_PhpChartX(array($timeArray, $speedArray),'chart');
            break;

        case "rpm":
            $pc = new C_PhpChartX(array($timeArray, $rpmArray),'chart');
            break;   
    }

    
    switch ($graphType){
        case "speed":
            $pc->set_title(array('text'=>'Speed vs Time'));
        break;

        case "rpm":
            $pc->set_title(array('text'=>'RPM vs Time'));
        break;
    }
    
    $pc->set_grid(array('background'=>'#fefbf3','borderWidth'=>2.5));
	$pc->set_animate(true);
    $pc->set_series_default(array('fill'=>true,'shadow'=>false,'showMarker'=>false));

    switch ($graphType){
        case "speed":
            $pc->set_axes(array(
                'xaxis'=>array('pad'=>1.0),
                'yaxis'=>array('min'=>0,'max'=> max($speedArray))
            ));
            break;

        case "rpm":
            $pc->set_axes(array(
                'xaxis'=>array('pad'=>1.0),
                'yaxis'=>array('min'=>0,'max'=> max($rpmArray))
            ));
            break;   
    }
    
    $pc->add_series(array('color'=>'rgba(68, 124, 147, 0.7)'));
    $pc->add_series(array('color'=>'rgba(150, 35, 90, 0.7)'));
    
    $pc->draw(560,400);

?>


<html>
    <head>
        <link rel="stylesheet" href="./style/index.css">
    </head>
    <body>
    </body>
</html>