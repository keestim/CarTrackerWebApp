<?php
REQUIRE './SQLConnection.php';
REQUIRE './APIKeys.php';

function getCoordinatesAddress($AzureKey, $Latitude, $Longitude)
{
    if ($Latitude != "" && $Longitude != "")
    {
        $url = "https://atlas.microsoft.com/search/address/reverse/json?subscription-key=" . $AzureKey . "&api-version=1.0&query=" . $Latitude . "," . $Longitude . "&returnSpeedLimit=true";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response_json = curl_exec($ch);
        curl_close($ch);
        $response=json_decode($response_json, true);

        return $response["addresses"][0]["address"]["freeformAddress"];
    }
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $sql = "SELECT DISTINCT 
        Journeys.*,
        COUNT(SpeedingOccurances.journeyID) as speedingCount, 
        FinalDetails.endLatitude,
        FinalDetails.endLongitude,
        FinalDetails.endTime
    FROM Journeys 
    LEFT OUTER JOIN SpeedingOccurances ON SpeedingOccurances.journeyID = Journeys.journeyID 
    LEFT OUTER JOIN
        (SELECT 
            JourneyDetails.journeyID, 
            JourneyDetails.latitude as endLatitude, 
            JourneyDetails.longitude as endLongitude,
            JourneyDetails.time as endTime 
        FROM 
            JourneyDetails
        INNER JOIN (
            SELECT 
                JourneyDetails.journeyID, MAX(JourneyDetails.time) as maxTime 
            FROM 
                JourneyDetails
            GROUP BY
                JourneyDetails.journeyID) AS MaxTimeSelection 
        ON 
            JourneyDetails.journeyID = MaxTimeSelection.journeyID AND 
            JourneyDetails.time = MaxTimeSelection.maxTime) AS FinalDetails ON FinalDetails.journeyID = Journeys.journeyID 
    GROUP BY Journeys.journeyID";  
?>

<!DOCTYPE html>
<html>
  <head>
    
  </head>
  <body>
    <table>
        <tr>
            <th>journeyID</th>
            <th>Start Latitude</th>
            <th>Start Longitude</th>
            <th>Start Address</th>
            <th>Start Time</th>
            <th>End Latitude</th>
            <th>End Longitude</th>
            <th>End Address</th>
            <th>End Time</th>
            <th>Speeding Count</th>
            <th>View Info</th>
        </tr>

    <?php 
      $result = $conn->query($sql);
  
      if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["journeyID"] . "</td>";
            echo "<td>" . $row["startLatitude"] . "</td>";
            echo "<td>" . $row["startLongitude"] . "</td>";
            echo "<td>" . getCoordinatesAddress($AzureKey, $row["startLatitude"], $row["startLongitude"]) . "</td>";
            echo "<td>" . $row["startTime"] . "</td>";
            echo "<td>" . $row["endLatitude"] . "</td>";
            echo "<td>" . $row["endLongitude"] . "</td>";
            echo "<td>" . getCoordinatesAddress($AzureKey, $row["endLatitude"], $row["endLongitude"]) . "</td>";
            echo "<td>" . $row["endTime"] . "</td>";
            echo "<td>" . $row["speedingCount"] . "</td>";
            echo "<td><a href='./JourneyDetails.php?journeyID=" . $row["journeyID"] ."'>View More Info</a></td>"; 
            echo "</tr>";
        }
      } else {
        echo "0 results";
      }
      
      
      $conn->close();
    ?>

    </table>
  </body>
</html>