<?php
REQUIRE './SQLConnection.php';
REQUIRE './APIKeys.php';

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
    <link rel="stylesheet" href="./style/index.css">

    <script src="./scripts/jquery-3.6.0.min.js"></script>
    <script src="./scripts/loadhtml.js"></script>

    <script>
        $(document).ready(function (e)
        {
            $('.CoordinatesAddress').each(function(i, obj) {
                console.log(obj);

                var coordinatesString = obj.innerHTML;
                var coordinatesArray = coordinatesString.split(",");

                $.ajax({url: "./GetCoordinateAPIAddress.php?latitude=" + coordinatesArray[0] + "&longitude=" + coordinatesArray[1], success: function(result){
                    obj.innerHTML = result;
                }});

            });
        });

    </script>

  </head>
  <body>
    <div class="navbar" id="navbar">
    </div>

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
            echo "<tr id= " . $row["journeyID"] . ">";
            echo "<td>" . $row["journeyID"] . "</td>";
            echo "<td>" . $row["startLatitude"] . "</td>";
            echo "<td>" . $row["startLongitude"] . "</td>";
            echo "<td class='CoordinatesAddress'>" . $row["startLatitude"] . "," . $row["startLongitude"] . "</td>";
            echo "<td>" . $row["startTime"] . "</td>";
            echo "<td>" . $row["endLatitude"] . "</td>";
            echo "<td>" . $row["endLongitude"] . "</td>";
            echo "<td class='CoordinatesAddress'>" . $row["endLatitude"] . "," . $row["endLongitude"] . "</td>";
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