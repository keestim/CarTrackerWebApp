<!DOCTYPE html>
<html>
  <head>
    <title>Geolocation</title>
    <link rel="stylesheet" href="./style/index.css">

    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>

    <script src="./scripts/jquery-3.6.0.min.js"></script>
    <script src="./scripts/loadhtml.js"></script>
    <script src="./scripts/mapsAPIFunctions.js"></script>

    <script>
      //maps api code taken and modified from: https://developers.google.com/maps/documentation/javascript/overview

      // Note: This example requires that you consent to location sharing when
      // prompted by your browser. If you see the error "The Geolocation service
      // failed.", it means you probably did not give permission for the browser to
      // locate you.
      let map, infoWindow;
      var lastKnownDetails, lastStartDetails, lastPositionMarker, lastStartPositionMarker;

      //makes an ajax request to the server to get the start position of the last record journey from the system
      function getLastJourneyStartDetails()
      {

        $.ajax({url: "./GetCurrentJourney.php", success: function(result){
          window.lastStartDetails = JSON.parse(result);
        }});
      }

      //updates Google Maps API Map and HTML according to updated start position
      function setLastJourneyStartDetails()
      {
        if (lastStartPositionMarker != undefined)
        {
          //position is only updated IF the position changes
          if (lastStartPositionMarker.getPosition().lat() != window.lastStartDetails["startLatitude"] && 
            lastStartPositionMarker.getPosition().lng() != window.lastStartDetails["startLongitude"])
          {
            lastStartPositionMarker.setMap(null);
          }
        }

        if (window.lastStartDetails == undefined)
        {
          return;
        }

        //if the variable which holds the start position marker is null/undefined, then a new marker will be created
        if (lastStartPositionMarker == undefined)
        {
          lastStartPositionMarker = addMarker(window.lastStartDetails["startLatitude"], window.lastStartDetails["startLongitude"]);
          moveToLocation(lastStartDetails["startLatitude"], lastStartDetails["startLongitude"]);
        }

        //updates the HTML with the new data
        $("#start_latitude").text(window.lastStartDetails["startLatitude"]);
        $("#start_longitude").text(window.lastStartDetails["startLongitude"]);
        $("#start_time").text(window.lastStartDetails["startTime"]);

        //makes ajax request, to get the physical address of these new coordinates
        $.ajax({url: "./GetCoordinateAPIAddress.php?latitude=" + window.lastStartDetails["startLatitude"] + "&longitude=" + window.lastStartDetails["startLongitude"], success: function(result){
          $("#start_address").text(result);
        }});
      }

      //makes an ajax request to the server to get the last recorded position from the system
      function getLastKnownDetails()
      {
        $.ajax({url: "./GetCurrentJourneyInstanceInfo.php", success: function(result){
          window.lastKnownDetails = JSON.parse(result);
        }});
      }

      //updates Google Maps API Map and HTML according to updated vehicle position
      function setLastKnownPosition()
      {
        if (lastPositionMarker != undefined)
        {
          if (lastPositionMarker.getPosition().lat() != window.lastKnownDetails["latitude"] && 
            lastPositionMarker.getPosition().lng() != window.lastKnownDetails["longitude"])
          {
            lastPositionMarker.setMap(null);
	          lastPositionMarker = undefined;
          }
        }

        if (window.lastKnownDetails == undefined)
        {
          return;
        }

        if (lastPositionMarker == undefined)
        {
          lastPositionMarker = addMarker(window.lastKnownDetails["latitude"], window.lastKnownDetails["longitude"]);
        }

        $("#current_latitude").text(window.lastKnownDetails["latitude"]);
        $("#current_longitude").text(window.lastKnownDetails["longitude"]);
        $("#current_rpm").text(window.lastKnownDetails["RPM"]);
        $("#current_speed").text(window.lastKnownDetails["speed"]);
        $("#current_time").text(window.lastKnownDetails["time"]);

        $.ajax({url: "./GetCoordinateAPIAddress.php?latitude=" + window.lastKnownDetails["latitude"] + "&longitude=" + window.lastKnownDetails["longitude"], success: function(result){
          $("#current_address").text(result);
        }});
      }

      //updates the last known position for the journey
      function updateLastKnownPosition()
      {
        setInterval(
          function(){ 
            getLastKnownDetails(); 
            setLastKnownPosition();
          }, 2000);
      }

      //updates the start positions of the journey
      function updateLastJourneyStartDetails()
      {
        setInterval(
          function(){ 
            getLastJourneyStartDetails(); 
            setLastJourneyStartDetails();
          }, 2000);
      }

      function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
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

        //call functions, which will constantly update the current and start journey positions every 2 seconds
        updateLastJourneyStartDetails();
        updateLastKnownPosition();
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

    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBedQ99BuDCvtKbnHOX9haF-EtkpMJS8wk&callback=initMap&libraries=&v=weekly"
      async
    ></script>

    <div class="journeyData">
        <h2>Last Recored Start Location</h2>
        <p>Latitude: <span id="start_latitude"></span></p>
        <p>Longitude: <span id="start_longitude"></span></p>
        <p>Time: <span id="start_time"></span></p>
        <p>Physical Address: <span id="start_address"></span></p>
    </div>    

    <div class="journeyData">
        <h2>Last Recorded Location</h2>
        <p>Speed: <span id="current_speed"></span> KPH</p>
        <p>RPM: <span id="current_rpm"></span> RPM</p>
        <p>Latitude: <span id="current_latitude"></span></p>
        <p>Longitude: <span id="current_longitude"></span></p>
        <p>Time: <span id="current_time"></span></p>
        <p>Physical Address: <span id="current_address"></span></p>
    </div>    

  </body>
</html>