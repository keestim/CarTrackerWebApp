<?php
    REQUIRE './SQLConnection.php';

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT DISTINCT 
        JourneyDetails.speed, JourneyDetails.RPM, JourneyDetails.time
        FROM  
            JourneyDetails
        WHERE
            JourneyDetails.journeyID = " . $_GET["journeyID"];

    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {

    }
    } else {
    echo "0 results";
    }


    $conn->close();
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="./style/index.css">

        <script src="./scripts/jquery-3.6.0.min.js"></script>
        <script src="./scripts/loadhtml.js"></script>


        <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>

        <link rel="stylesheet" href="./style/index.css">

        <script>
        function getLocationData(map)
        {
            var journeyID = location.search.split('journeyID=')[1];

            console.log(journeyID);

            $.ajax({url: "./GetJourneyLocations.php?journeyID=" + journeyID, success: function(result){
                var locationsArray = JSON.parse(result);
                
                locationsArray.forEach(
                    element => addNewMarker(map, element[0], element[1])   
                );
            }});
        }

        function addNewMarker(map, latitude, longitude)
        {
            new google.maps.Marker({
                position: { lat: parseFloat(latitude), lng: parseFloat(longitude) },
                map,
                title: "Start Location",
            });
        }

        // Note: This example requires that you consent to location sharing when
        // prompted by your browser. If you see the error "The Geolocation service
        // failed.", it means you probably did not give permission for the browser to
        // locate you.
        let map, infoWindow;

        function initMap() {
            const map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: -37.8072, lng: 145.154 },
            zoom: 14,
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

            getLocationData(map);
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

    <iframe src="./TestGraphs.php?journeyID=<?php echo $_GET['journeyID']; ?>&graphtype=speed"></iframe>
    <iframe src="./TestGraphs.php?journeyID=<?php echo $_GET['journeyID']; ?>&graphtype=rpm"></iframe>

    </body>
</html>