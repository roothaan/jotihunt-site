<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');

JotihuntUtils::requireLogin();

// Find Event
$event = $driver->getEventById($authMgr->getMyEventId());

// Then grab all Deelgebieden
$_allDeelgebieden = $driver->getAllDeelgebiedenForEvent($event->getId());

$allDeelgebieden = array();

// Then all Coordinates for that Deelgebied
foreach ($_allDeelgebieden as $deelgebied) {
    $coordinates = $driver->getAllCoordinatesForDeelgebied($deelgebied->getId());
    $kml[$deelgebied->getName()] = $coordinates;
    $allDeelgebieden[$deelgebied->getName()] = $deelgebied;
}

?>
    "use strict";

    function Point(x, y) {
        this.x = x;
        this.y = y;
    }

    function Region(points) {
        this.points = points || [];
        this.length = points.length;
    }

    Region.prototype.area = function () {
        var area = 0,
            i,
            j,
            point1,
            point2;

        for (i = 0, j = this.length - 1; i < this.length; j=i,i++) {
            point1 = this.points[i];
            point2 = this.points[j];
            area += point1.x * point2.y;
            area -= point1.y * point2.x;
        }
        area /= 2;

        return area;
    };

    Region.prototype.centroid = function () {
        var x = 0,
            y = 0,
            i,
            j,
            f,
            point1,
            point2;

        for (i = 0, j = this.length - 1; i < this.length; j=i,i++) {
            point1 = this.points[i];
            point2 = this.points[j];
            f = point1.x * point2.y - point2.x * point1.y;
            x += (point1.x + point2.x) * f;
            y += (point1.y + point2.y) * f;
        }

        f = this.area() * 6;

        return new Point(x / f, y / f);
    };
var areaCenters = {};
<?php
foreach ($kml as $deelgebiedName => $coordinates) {
?>
(function () {
    "use strict";
    var polygon = [
        <?php foreach ($coordinates as $coordinate) { ?>
        {"x": <?= $coordinate->getLongitude() ?>, "y": <?= $coordinate->getLatitude() ?>},
        <?php } ?>
    ],
    region = new Region(polygon);
    areaCenters[<?= json_encode($deelgebiedName) ?>] = region.centroid();
}());
<?php } ?>