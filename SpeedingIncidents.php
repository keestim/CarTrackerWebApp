<?php
    REQUIRE './SQLConnection.php';

    $journeyID = $_GET["journeyID"];

    $sql = "SELECT SpeedingOccurances.* 
        FROM 
            SpeedingOccurances  
        WHERE 
            SpeedingOccurances.journeyID = " . $journeyID;
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="./style/index.css">

        <script src="./scripts/jquery-3.6.0.min.js"></script>
        <script src="./scripts/loadhtml.js"></script>
    </head>
    <body>
        <div class="navbar" id="navbar">
        </div>

        <h1>Speeding Incidents for Journey <?php echo $_GET["journeyID"]; ?></h1>

<?php
    $result = $conn->query($sql);
        
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            //print_r($row);
            //echo "<br/>";
            echo "<iframe src='./SpeedingDetails.php?speedingOccuranceID=" . $row["speedingOccuranceID"] . "'>View Info " . $row["speedingOccuranceID"] . "'></iframe>";

            //echo "<a href='./SpeedingDetails.php?speedingOccuranceID=" . $row["speedingOccuranceID"] . "'>View Info " . $row["speedingOccuranceID"] . "</a>";
            //echo "<br/>";
        }
    } else {
    echo "0 results";
    }

    $conn->close();
?>

    </body>
</html>