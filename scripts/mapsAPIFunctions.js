//moves the center of the map to the provided coordinates
function centerMapAtLocation(lat, lng)
{
  const center = new google.maps.LatLng(lat, lng);
  map.panTo(center);
}

//adds marker to google maps API view
function addMarker(latitude, longitude, titleMsg = ""){
  return new google.maps.Marker({
    position: { lat: parseFloat(latitude), lng: parseFloat(longitude) },
    map,
    title: titleMsg,
  });
}

//moves the center of the map to the provided coordinates
//and adds a marker
function moveToLocation(lat, lng){
    centerMapAtLocation(lat, lng);
    addMarker(lat, lng);
  }