function drawDeelgebieden(map, eventId) {
    var kmlUrl =  window.location.origin + '/deelgebieden-kml/' + eventId
    console.log("Using kmlUrl: " + kmlUrl);
    var kmlOptions = {
      suppressInfoWindows: false,
      preserveViewport: false,
      map: map
    };
    
    return new google.maps.KmlLayer(kmlUrl, kmlOptions);
}