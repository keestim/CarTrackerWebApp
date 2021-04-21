<?php
    REQUIRE './SQLConnection.php';

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT 
            Journeys.*
        FROM
            Journeys
        INNER JOIN
            (SELECT 
            MAX(Journeys.JourneyID) as MostRecentJourneyID
            FROM Journeys) AS MostRecentJourney ON 
            MostRecentJourney.MostRecentJourneyID = Journeys.journeyID";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            print(json_encode($row));
        }
    }
?>