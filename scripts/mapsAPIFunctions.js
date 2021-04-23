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
  return new google.maps.Marker({
    position: { lat: parseFloat(latitude), lng: parseFloat(longitude) },
    map,
    title: titleMsg,
  });
}