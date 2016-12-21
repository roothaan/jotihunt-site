<?php
if(!defined("opoiLoaded")) die("Scouting Putten, het lukt jullie niet om ons te hacken!");

JotihuntUtils::requireLogin();

function chooseEvent($eventId) {
    global $authMgr, $driver;
    $currentSession = $authMgr->getSessionInformation();
    $newSession = new Session(
                    $currentSession->getSessionId(), 
                    $currentSession->getUserId(), 
                    $currentSession->getOrganisationId(), 
                    $eventId);
    $driver->updateSession($newSession);
    $sessionUpdated = true;
    // Redirect if we came from the homepage
    if (isset($_REQUEST['redirect']) && $_REQUEST['redirect'] == 1) {
        header('Location: '.WEBSITE_URL);
        die();
    }

}
$sessionUpdated = false;

// If POST, update Session
if (isset($_POST['event_id'])) {
    chooseEvent($_POST['event_id']);
}

// Get all events for this user
$events = $driver->getMyEvents();

$event_id = $authMgr->getMyEventId();

// If there is only 1 event, pick that one
if (sizeof($events) == 1 && !isset($event_id)) {
    chooseEvent($events[0]->getId());
}

require_once BASE_DIR . 'header.php';


echo '<h1>Welkom op de Jotihunt site!</h1>';
if (sizeof($events) == 0) { ?>
    <h2>Waarschuwing</h2>
    <p>Leuk dat je er bent. Helaas is er voor jouw groep geen evenement aangemeld.</p>
    <p>Als je denkt dat dit niet juist is, stuur een e-mail naar de organisator.</p>
<?php
} else {
    if ($sessionUpdated) { ?>
        <h2>Je wijziging is opgeslagen!</h2>
    <?php } ?>
    <p>Je bent op deze pagina geland omdat we nog niet weten aan welk evenement je mee doet.</p>
    <p>Hieronder kan je kiezen welke evenement op jou van toepassing is.</p>
    
    <?php
    echo '<form action="/events" method="POST">
            <select name="event_id">';
    // Display them
    foreach ($events as $event) {
        $selected = $event_id === $event->getId() ? ' selected="selected"' : '';
        echo '<option value="'.$event->getId().'"'.$selected.'>'.$event->getName(). ($selected ? ' (*geselecteerd)' : '') . '</option>';
    }
    $redirect_value = isset($_GET['redirect']) && $_GET['redirect'] == 1 ? 1 : 0;
    echo '
        </select>
        <input type="hidden" name="redirect" value="'.$redirect_value.'" />
        <input type="submit" value="Choose" />';
}
?>