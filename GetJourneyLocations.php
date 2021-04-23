<?php
    REQUIRE './SQLConnection.php';

    $journeyID = $_GET["journeyID"];

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    //gets all of the different locations that were visited for a specified journey
    //the journey is specified via a journeyID that is pass in via a GET variable in the URL
    $sql = "SELECT DISTINCT 
            JourneyDetails.longitude, JourneyDetails.latitude 
        FROM
            JourneyDetails
        WHERE
            JourneyDetails.journeyID = " . $journeyID;

    $result = $conn->query($sql);

    $outputArrayStr = "";

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            if ($outputArrayStr == ""){
                $outputArrayStr .= " [ "; 
            }
            else {
                $outputArrayStr .= ", ";
            }

            $outputArrayStr .= "[ " . $row["latitude"] . ", " . $row["longitude"] . "] ";
        }
    }

    echo $outputArrayStr . " ] ";
?>