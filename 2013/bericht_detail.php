<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');

JotihuntUtils::requireLogin();

$id = JotihuntUtils::getUrlPart(1);
if(null == $id) {
    die('Geef een id mee!');
}

$berichtcollection = $driver->getBerichtGeschiedenis($id);
if (sizeof($berichtcollection) === 0) {
    ?>Geen bericht gevonden<?php
} else {
    $i = 0;
    foreach ( $berichtcollection as $bericht ) {
        $i++;
        
        // Transform message
        $message = $bericht->getInhoud();
        // $message = str_replace("\n", '<br/>', $message);
        // $message = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $message);
        // $message = str_replace(" ", '&nbsp;', $message);
        ?>
    <div class="bericht <?= $bericht->getType() ?>">
        <div class="titel">
            <h1><?= $bericht->getTitel();?></h1>
        </div>
        <div class="type"><?= ucfirst($bericht->getType());?></div>
        <div class="datum"><?= strftime('%a, %d %b %H:%M', strtotime($bericht->getFormattedDatum()))?></div>
        <div class="inhoud"><?= $message; ?></div>
            <?php
            if ($bericht->getType() == 'opdracht') {
                ?>
                    <div class="einddatum">Deadline: <?= $bericht->getFormattedEindtijd() ?></div>
                    <?php
            } ?>
        </div>

        <?php
        if ($i == 1 && count($berichtcollection) > 1) { ?>
            <h2>Geschiedenis</h2>
            <?php
        }
    }
}
?>