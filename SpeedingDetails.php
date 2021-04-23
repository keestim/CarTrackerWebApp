<?php
    REQUIRE './SQLConnection.php';

    $speedingOccuranceID = $_GET["speedingOccuranceID"];

    //Get SpeedingOccurances data for a provided speedingOccuranceID
    $sql = "SELECT SpeedingOccurances.* 
    FROM 
        SpeedingOccurances  
    WHERE 
        SpeedingOccurances.speedingOccuranceID = " . $speedingOccuranceID;

    $result = $conn->query($sql);
            
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $speed = $row["speed"];
            $speedLimit = $row["speedLimit"];
            $RPM = $row["RPM"];
            $time = $row["time"];
            $longitude = $row["longitude"]; 
            $latitude = $row["latitude"]; 

        }
    } else {
    echo "0 results";
    }

    $conn->close();
?>

<!DOCTYPE html>
<html>
    <head>
        <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
        <script src="./scripts/jquery-3.6.0.min.js"></script>
        <link rel="stylesheet" href="./style/index.css">
        <script src="./scripts/mapsAPIFunctions.js"></script>

        <script>

        //maps api code taken and modified from: https://developers.google.com/maps/documentation/javascript/overview
        let map, infoWindow;

        //makes an AJAX request, getting speeding data from server script
        //once server script is returned
        function getLocationData()
        {
            var speedingOccuranceID = location.search.split('speedingOccuranceID=')[1];

            console.log(speedingOccuranceID);

            $.ajax({url: "./GetSpeedingLocation.php?speedingOccuranceID=" + speedingOccuranceID, success: function(result){
                console.log(result);

                var locationsArray = JSON.parse(result);
                
                //for all return locations from the server ajaz request
                //markers are placed on map, and map is centered on the location
                //server request should only ever return ONE location
                locationsArray.forEach(function(element){
                    addNewMarker(element[0], element[1]);   
                    centerMapAtLocation(element[0], element[1]);
                    }
                );
            }});
        }

        // Note: This example requires that you consent to location sharing when
        // prompted by your browser. If you see the error "The Geolocation service
        // failed.", it means you probably did not give permission for the browser to
        // locate you.

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
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

            //centers map on speeding occurance location and places a marker in the speeding position
            getLocationData();
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
        <div id="map"></div>

        <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBedQ99BuDCvtKbnHOX9haF-EtkpMJS8wk&callback=initMap&libraries=&v=weekly"
        async
        ></script>

        <div class="attrInfo">
            <h2>Speed: <?php echo $speed; ?> KPH</h2>
        </div>

        <div class="attrInfo">
            <h2>Speed Limit: <?php echo $speedLimit; ?> KPH</h2>
        </div>

        <div class="attrInfo">
            <h2>RPM: <?php echo $RPM; ?> RPM</h2>
        </div>

        <div class="attrInfo">
            <h2>Latitude: <?php echo $latitude; ?></h2>
        </div>

        <div class="attrInfo">
            <h2>Longitude: <?php echo $longitude; ?></h2>
        </div>
        
        <div class="attrInfo">
            <h2>Time: <?php echo $time; ?></h2>
        </div>
    </body>
</html>