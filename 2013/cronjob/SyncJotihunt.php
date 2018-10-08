<?php
require_once '../init.php';

require_once CLASS_DIR . 'jotihunt/Gcm.class.php';
require_once CLASS_DIR . 'jotihunt/GcmSender.class.php';

$debug = isset($_GET['debug']) ? '1' === $_GET['debug'] : false;

// enable user error handling
$libxml_internal_errors_enabled = libxml_use_internal_errors(true);

if (isset($_GET['sessionId'])) {
    $sessionId = $_GET['sessionId'];
    $authMgr->setSessionId($sessionId);
}

$authMgr->requireAdmin();

function localizeImagesInText($text) {
    global $driver;
    
    // Check of er images in staan
    if(strpos($text,"<img")) {
        $dom = new DOMDocument();
        // Example in case duplicate IDs make it into the same document (bug from a previous Jotihunt)
        //$text = str_replace('id="m_506514002950288963gmail-m_3742687155103668368AppleMailSignature"', '', $text);
        $dom->loadHTML($text);
        $images = $dom->getElementsByTagName('img');
        foreach ($images as $image) {
            $imageUrl = $image->getAttribute('src');

            // Removed "rawurlencode" from the pathinfo part, since it seems to be encoded already now
            $imageFile = file_get_contents(pathinfo($imageUrl, PATHINFO_DIRNAME)
                        .'/'.
                        pathinfo($imageUrl, PATHINFO_BASENAME));
            $imageSha = sha1($imageFile);

            if(!$image = $driver->imageGetBySha($imageSha)) {
                $image = new Image();
                $image->setData($imageFile);
                $image->setSha1($imageSha);
                
                $filename = basename($imageUrl);
                $image->setName(pathinfo($filename, PATHINFO_FILENAME));
                $image->setExtension(pathinfo($filename, PATHINFO_EXTENSION));
                
                $image->setFileSize(strlen($imageFile));
                $image->setLastModified(time());
                
                $image->setId($driver->addImage($image));
            }

            $text = str_replace($imageUrl, "__WEBSITE_URL__images/".$image->getId(), $text);
        }
    }
    return $text;
}

function print_debug_pre($msg) {
    global $debug;
    if ($debug) {
        echo '<pre>';
        print_r($msg);
        echo '</pre>';
    }
}

function print_debug($message) {
    global $debug;
    if ($debug) {
        echo $message;
    }
}

$jotihuntinformatie = new JotihuntInformatieRest();
$jotihuntinformatie->setDebug($debug);

print_debug('<h1>Nieuws</h1>');
$nieuwscollection = $jotihuntinformatie->updateNieuws();

foreach ( $nieuwscollection as $nieuwsitem ) {
    if($debug) {
        print_debug('<h2>Nieuws bericht</h2>');
        print_debug_pre($nieuwsitem);
        continue;
    }
    $nieuwsitem->setInhoud(localizeImagesInText($nieuwsitem->getInhoud()));
    $driver->addBericht($nieuwsitem);
}

print_debug('<h1>Opdrachten</h1>');
$opdrachtcollection = $jotihuntinformatie->updateOpdrachten();
foreach ( $opdrachtcollection as $opdracht ) {
    if($debug) {
        print_debug_pre($opdracht);
        continue;
    }
    $opdracht->setInhoud(localizeImagesInText($opdracht->getInhoud()));
    $driver->addBericht($opdracht);
}

print_debug('<h1>Hints</h1>');
$hintcollection = $jotihuntinformatie->updateHints();
foreach ( $hintcollection as $hint ) {
    if($debug) {
        print_debug_pre($hint);
        continue;
    }
    $hint->setInhoud(localizeImagesInText($hint->getInhoud()));
    $driver->addBericht($hint);
}

print_debug('<h1>Vossen Statussen</h1>');
$berichtcollection = $driver->getBerichtCollection();

$vossenstatuscollection = $jotihuntinformatie->getVossenStatusen();
$allGcmIds = $driver->getAllActiveGcms();
// $vossenstatuscollection == Warning: Invalid argument supplied for foreach() in /app/2013/cronjob/SyncJotihunt.php on line 114
foreach ( $vossenstatuscollection as $vossenstatus ) {
    $oudeteam = $driver->getVosXYByDeelgebied($vossenstatus->getDeelgebied());
    if (!$oudeteam) {
        print_debug('<div>Could not find Team for ' . $vossenstatus->getDeelgebied() . '</div>');
        continue;
    }
    
    print_debug('<h2>$oudeteam</h2>');
    print_debug_pre($oudeteam);
    if ($oudeteam) {
        print_debug($oudeteam->getStatus());
    }

    print_debug('<h2>$vossenstatus</h2>');
    print_debug_pre($vossenstatus);
    if ($vossenstatus) {
        print_debug($vossenstatus->getStatus());
    }

    if ($oudeteam->getStatus() != $vossenstatus->getStatus()) {
        $payload = array (
                'status' => $vossenstatus->getStatus(),
                'teamName' => $vossenstatus->getName() 
        );
        
        /* Verstuurt status update naar Google Message Service */
        $gcmSender = new GcmSender();
        $gcmSender->setReceiverIds($allGcmIds);
        $gcmSender->setPayload($payload);
        if ($debug) {
            print_debug('<h1>GCM integration</h1>');
            print_debug('Would sent below messages to ' . count($allGcmIds) . ' GCM IDs');
            print_debug_pre($payload);
            continue;
        }
        $result = $gcmSender->send();
    }
    
    $oudeteam->setStatus($vossenstatus->getStatus());
    $oudeteam->setName($vossenstatus->getName());
    
    if ($debug) {
        print_debug('<h1>Updating team</h1>');
        print_debug_pre($oudeteam);
        continue;
    }
    $driver->updateTeam($oudeteam);
}

$scorecollection = $jotihuntinformatie->getScorelijst();

$changed = false;
if(!empty($scorecollection) && count($scorecollection) > 0) {
    foreach ( $scorecollection as $score ) {
        $huidigeScore = $driver->getScoreByGroep($score->getGroep());
        if ($score->getPlaats() != $huidigeScore->getPlaats() || $score->getHunts() != $huidigeScore->getHunts()) {
            $changed = true;
        }
    }
}

print_debug('<h1>Scorelijst</h1>');
if ($changed) {
    foreach ( $scorecollection as $score ) {
    if ($debug) {
        print_debug('<h2>Updating score</h2>');
        print_debug_pre($score);
        continue;
    }
        $driver->addScore($score);
    }
    print_debug('Scores updated.');
}

foreach (libxml_get_errors() as $error) {
    // handle errors here
    echo '<pre>';
    echo 'Errors enabled:';
    print_r($libxml_internal_errors_enabled);
    var_dump($error);
    echo '</pre>';
}

libxml_clear_errors();

print_debug('<hr />Synchronization finished.');