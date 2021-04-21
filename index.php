<?php
REQUIRE './SQLConnection.php';
// Check connection

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = 
  " SELECT 
      JourneyDetails.*, MaxJourneys.startLatitude, MaxJourneys.startLongitude
    FROM JourneyDetails 
    INNER JOIN (
      SELECT MAX(Journeys.journeyID) as journeyID, Journeys.startLatitude, Journeys.startLongitude FROM Journeys) as MaxJourneys 
    ON JourneyDetails.journeyID = MaxJourneys.journeyID ";

$sql = "SELECT MAX(Journeys.journeyID) as journeyID, Journeys.startLatitude, Journeys.startLongitude FROM Journeys";

$sql = "SELECT 
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
JourneyDetails.time = MaxTimeSelection.maxTime";

print($sql);

$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    echo print_r($row) . "<br/>";

    //$latitude = $row["startLatitude"];
    //$longitude = $row["startLongitude"];

    //echo "id: " . $row["journeyID"]. " | latitude : " . $row["latitude"]. " | longitude: " . $row["longitude"] . " | speed: " . $row["speed"] . "<br>";
  }
} else {
  echo "0 results";
}


$conn->close();

?>


<!DOCTYPE html>
<html>
  <head>
    <title>Geolocation</title>
    <link rel="stylesheet" href="./style/index.css">

    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>

    <script src="./scripts/jquery-3.6.0.min.js"></script>
    <script src="./scripts/loadhtml.js"></script>

    <script>
      // Note: This example requires that you consent to location sharing when
      // prompted by your browser. If you see the error "The Geolocation service
      // failed.", it means you probably did not give permission for the browser to
      // locate you.
      let map, infoWindow;

      function initMap() {
        const map = new google.maps.Map(document.getElementById("map"), {
          center: { lat: -37.8072, lng: 145.154 },
          zoom: 12,
        });

        new google.maps.Marker({
          position: { lat: -37.8072, lng: 145.154 },
          map,
          title: "Hello World!",
        });

        infoWindow = new google.maps.InfoWindow();
        const locationButton = document.createElement("button");
        locationButton.textContent = "Pan to Current Location";
        locationButton.classList.add("custom-map-control-button");
        map.controls[google.maps.ControlPosition.TOP_CENTER].push(
          locationButton
        );
        locationButton.addEventListener("click", () => {
          // Try HTML5 geolocation.
          if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
              (position) => {
                const pos = {
                  lat: position.coords.latitude,
                  lng: position.coords.longitude,
                };
                infoWindow.setPosition(pos);
                infoWindow.setContent("Location found.");
                infoWindow.open(map);
                map.setCenter(pos);
              },
              () => {
                handleLocationError(true, infoWindow, map.getCenter());
              }
            );
          } else {
            // Browser doesn't support Geolocation
            handleLocationError(false, infoWindow, map.getCenter());
          }
        });
      }

      function handleLocationError(browserHasGeolocation, infoWindow, pos) {
        infoWindow.setPosition(pos);
        infoWindow.setContent(
          browserHasGeolocation
            ? "Error: The Geolocation service failed."
            : "Error: Your browser doesn't support geolocation."
        );
        infoWindow.open(map);
      }
    </script>
  </head>
  <body>
    <div class="navbar" id="navbar">
    </div>

    <div id="map"></div>

    <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBedQ99BuDCvtKbnHOX9haF-EtkpMJS8wk&callback=initMap&libraries=&v=weekly"
      async
    ></script>
  </body>
</html>