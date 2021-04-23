<?php
    REQUIRE './SQLConnection.php';

    #gets all of the SpeedingOccurances records from the database, that have the same journeyID as the journeyID get variable
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

        #loops through all speedingOccuranceID values, are renders a iFrame of the page SpeedingDetails.php, provide the relevant speedingOccuranceID value 
        while($row = $result->fetch_assoc()) {
            echo "<iframe src='./SpeedingDetails.php?speedingOccuranceID=" . $row["speedingOccuranceID"] . "'>View Info " . $row["speedingOccuranceID"] . "'></iframe>";
        }
    } else {
    echo "0 results";
    }

    $conn->close();
?>

    </body>
</html>