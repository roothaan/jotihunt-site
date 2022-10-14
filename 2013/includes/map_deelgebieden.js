function drawDeelgebieden(map, eventId) {
    const cacheToken = sessionStorage.getItem('jotihunt.kmlCache')
    if (!cacheToken) {
        sessionStorage.setItem('jotihunt.kmlCache', Math.random().toString(36).substr(2))
    }
    const kmlUrl = window.location.origin + '/deelgebieden-kml/' + eventId + '?cb=' + sessionStorage.getItem('jotihunt.kmlCache');
    console.log("Using kmlUrl: " + kmlUrl);
    const kmlOptions = {
        suppressInfoWindows: false,
        preserveViewport: false,
        map: map
    };

    return new google.maps.KmlLayer(kmlUrl, kmlOptions);
}