<?php
require_once '../init.php';
$authMgr->requireSuperAdmin();
require_once BASE_DIR . 'header.php';
require_once BASE_DIR . 'includes/KmlHelper.class.php';

$kmlHelper = new KmlHelper();
$kmlHelper->parseDeelgebiedenKml();
$kmlHelper->parsePoiKml();

?>
<h1>KML import</h1>
<form action="/2013/admin/kml-import.php" method="POST" enctype="multipart/form-data">
    KML File: <input type="file" name="kml_file"><br />
    import: <input type="checkbox" name="import"><br />
    <input type="submit" value="Upload KML file">
</form>
