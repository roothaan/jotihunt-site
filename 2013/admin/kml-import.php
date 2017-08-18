<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');
$authMgr->requireSuperAdmin();
require_once BASE_DIR . 'includes/KmlHelper.class.php';

$kmlHelper = new KmlHelper();
$kmlHelper->parseDeelgebiedenKml();
$kmlHelper->parsePoiKml();

?>
<h1>KML import</h1>
<form action="<?= WEBSITE_URL ?>suadmin-kml-import" method="POST" enctype="multipart/form-data">
    KML File: <input type="file" name="kml_file"><br />
    import: <input type="checkbox" name="import"><br />
    <input type="submit" value="Upload KML file">
</form>
