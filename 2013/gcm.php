<?php
require_once 'init.php';
$authMgr->requireAdmin();
require_once BASE_DIR . 'header.php';

require_once CLASS_DIR . 'jotihunt/Gcm.class.php';

if (isset($_POST ['message'])) {
    $allGcmIds = DataStore::getSiteDriver()->getAllActiveGcms();
    $payload = array (
            'notification' => $_POST ['message'] 
    );
    $gcmSender = new GcmSender();
    $gcmSender->setReceiverIds($allGcmIds);
    $gcmSender->setPayload($payload);
    $result = $gcmSender->send();
    
    echo '<pre>GCM Result: ' . $result . '</pre>';
}

?>
<script type="text/javascript">
$(document).ready(function() {
    $("#gcm").dataTable( {
        "bPaginate": false,
	    "aoColumns": [
            {sName: "id" },
            {sName: "gcmid" },
            {sName: "riderid" },
            {sName: "ridername" },
            {sName: "locations" },
            {sName: "enabled" },
            {sName: "time" }
        ]
    })
    .makeEditable({
	    sAddURL:    "<?= BASE_URL ?>ajax/gcm.ajax.php",
        sUpdateURL: "<?= BASE_URL ?>ajax/gcm.ajax.php",
        sDeleteURL: "<?= BASE_URL ?>ajax/gcm.ajax.php"
    });
});
</script>
<?php
$allGcms = $driver->getAllGcms();
if ($authMgr->isSuperAdmin()) {
    $allGcms = $driver->getAllGcmsSU();
}
?>
<button id="btnDeleteRow">Verwijder GCM</button>
<button id="btnAddNewRow">Add</button>
<div class="add_delete_toolbar"></div>

<table id="gcm">
    <thead>
        <tr>
            <th>ID</th>
            <th>GCM ID</th>
            <th>Rider ID</th>
            <th>Rider Naam</th>
            <th>Locaties</th>
            <th>Enabled</th>
            <th>time</th>
        </tr>
    </thead>
    <tbody>
<?php
foreach ( $allGcms as $gcm ) {
    $rider = $driver->getRider($gcm->getRiderId());
    echo '<tr id="' . $gcm->getId() . '">';
    echo '<td class="read_only">' . $gcm->getId() . '</td>';
    if (strlen($gcm->getGcmId()) > 40) {
        echo '<td class="read_only"><code>' . substr($gcm->getGcmId(), 0, 40) . '... (' . strlen($gcm->getGcmId()) . ')</code></td>';
    } else {
        echo '<td class="read_only"><code>' . $gcm->getGcmId() . '</code></td>';
    }
    echo '<td>' . $gcm->getRiderId() . '</td>';
    if (null != $rider) {
        echo '<td class="read_only">' . $rider->getUser()->getDisplayName() . '</td>';
        $locations = $driver->getRiderLocation($rider->getId(), $gcm->getGcmId());
        echo '<td class="read_only">' . count($locations) . '</td>';
    } else {
        echo '<td class="read_only"><em>Onbekend</em></td>';
        echo '<td class="read_only"><em>0</em></td>';
    }
    echo '<td>' . ($gcm->getEnabled() ? '1' : '0') . '</td>';
    echo '<td class="read_only">' . $gcm->getTime() . '</td>';
    echo '</tr>';
}
?>
</tbody>
</table>

<form id="formAddNewRow" action="#" title="Add new record">
    <input type="hidden" name="id" id="id" rel="0" />
    <label for="name">GCM ID</label>
    <input type="text" name="gcmid" id="gcmid" class="required" rel="1" /><br />
    
    <label for="name">Rider ID</label>
    <input type="text" name="riderid" id="riderid" rel="2" /><br />
    
    <input type="hidden" name="ridername" id="ridername" rel="3" />
    <input type="hidden" name="locations" id="locations" rel="4" />
    
    <label for="name">Enabled</label><input type="text" name="enabled" id="enabled" rel="5" value="t" />
    <input type="hidden" name="time" id="time" rel="6" />
</form>

<div>
    <br />Verstuur algemeen bericht:
    <form id="formSendNotification" action="<?= WEBSITE_URL ?>admin-gcm" method="POST">
        <input type="text" name="message" /><br /> <input type="submit" value="Verstuur bericht" />
    </form>
</div>

<?php
require_once BASE_DIR . 'footer.php';
?>