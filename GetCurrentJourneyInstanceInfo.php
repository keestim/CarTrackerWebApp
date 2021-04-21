<?php
    REQUIRE './SQLConnection.php';

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT 
            JourneyDetails.*
        FROM
        JourneyDetails
        INNER JOIN
            (SELECT 
            MAX(JourneyDetails.journeyID) as MostRecentJourneyID,
            MAX(JourneyDetails.time) as MaxJourneyTime
            FROM JourneyDetails) AS MostRecentJourney ON 
            MostRecentJourney.MostRecentJourneyID = JourneyDetails.journeyID
            AND MostRecentJourney.MaxJourneyTime = JourneyDetails.time";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            print(json_encode($row));
        }
    }
?>