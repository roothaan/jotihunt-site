function drawDeelgebieden(map, eventId) {
    let params = ''
    const cacheToken = sessionStorage.getItem('jotihunt.kmlCache')
    if (cacheToken) {
        params = '?cb=' + cacheToken
    }

    const kmlUrl = window.location.origin + '/deelgebieden-kml/' + eventId + params
    console.log("Using kmlUrl: " + kmlUrl);
    const kmlOptions = {
        suppressInfoWindows: false,
        preserveViewport: false,
        map: map
    };

    return new google.maps.KmlLayer(kmlUrl, kmlOptions);
}