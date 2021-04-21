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
      let map, infoWindow, lastKnownDetails, lastStartDetails, lastPositionMarker;

      function centerMapAtLocation(lat, lng)
      {
        const center = new google.maps.LatLng(lat, lng);
        map.panTo(center);
      }

      function moveToLocation(lat, lng){
        centerMapAtLocation(lat, lng);
        addMarker(lat, lng);
      }

      function addMarker(latitude, longitude, titleMsg = ""){
        new google.maps.Marker({
          position: { lat: parseFloat(latitude), lng: parseFloat(longitude) },
          map,
          title: titleMsg,
        });
      }

      function getCurrentJourneyInfo(){
        $.ajax({url: "./GetCurrentJourney.php", success: function(result){
          lastStartDetails = JSON.parse(result);
          moveToLocation(lastStartDetails["startLatitude"], lastStartDetails["startLongitude"]);
        }});
      }

      function updateLastKnownPosition()
      {
        setInterval(
          function(){ 
            getLastKnownDetails(); 

            if (lastPositionMarker != undefined)
            {
              lastPositionMarker.setMap(null);
            }

            if (lastKnownDetails != undefined)
            {
              console.log("Adding new marked!")
              lastPositionMarker = addMarker(lastKnownDetails["latitude"], lastKnownDetails["longitude"]);
            }

          }, 2000);
      }

      function getLastKnownDetails()
      {
        $.ajax({url: "./GetCurrentJourneyInstanceInfo.php", success: function(result){
          lastKnownDetails = JSON.parse(result);
          console.log(lastKnownDetails);
        }});
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

        var journeyJSON = getCurrentJourneyInfo();
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

    <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBedQ99BuDCvtKbnHOX9haF-EtkpMJS8wk&callback=initMap&libraries=&v=weekly"
      async
    ></script>

    <div>
        <h2>Start Location</h2>
        <p>Latitude: </p>
        <p>Longitude: </p>
        <p>Time: </p>
        <p>Physical Address: </p>
    </div>    

    <div>
        <h2>Current Location</h2>
        <p>Speed: 60 KPH</p>
        <p>RPM: 2500 RPM</p>
        <p>Latitude: </p>
        <p>Longitude: </p>
        <p>Time: </p>
        <p>Physical Address: </p>
    </div>    

    <p><em>Potentially add something about speeding?</em></p>

  </body>
</html>