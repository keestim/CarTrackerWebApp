<?php
    REQUIRE './SQLConnection.php';

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $speedingOccuranceID = $_GET['speedingOccuranceID'];

    $sql = "SELECT 
            SpeedingOccurances.longitude,
            SpeedingOccurances.latitude
        FROM
            SpeedingOccurances
        WHERE
            SpeedingOccurances.speedingOccuranceID = " . $speedingOccuranceID . 
        " LIMIT 1 ";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            echo " [ [ " . $row["latitude"] . "," . $row["longitude"] . " ] ] ";
        }
    }
?>