function drawDeelgebieden(map, eventId, domainname) {

    var kmlUrl =  location.protocol + domainname + 'kml/deelgebieden.php?event_id=' + eventId
    var kmlOptions = {
      suppressInfoWindows: false,
      preserveViewport: false,
      map: map
    };
    
    return new google.maps.KmlLayer(kmlUrl, kmlOptions);
}