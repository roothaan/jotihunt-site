<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');

JotihuntUtils::requireLogin();

if (! isset($_GET ['id']) || empty($_GET ['id'])) {
    die('Geef een id mee!');
}

?>
<style>
div.bericht {
	background: #CCCCCC;
	border: 0;
	margin: 5px;
	padding: 5px;
	position: relative;
}

div.bericht.nieuws {
	border-left: 10px solid orange;
}

div.bericht.opdracht {
	border-left: 10px solid #4c7303;
}

div.bericht.hint {
	border-left: 10px solid #024d52;
}

div.inhoud {
	margin-bottom: 20px;
}

div.einddatum {
	background: #990000;
	color: white;
	text-align: center;
	padding: 5px;
	position: absolute;
	bottom: 0;
	left: 0;
	width: 970px;
}
</style>
<?php

$berichtcollection = $driver->getBerichtGeschiedenis($_GET ['id']);
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
        <div class="datum"><?= strftime('%a, %d %b %H:%M', strtotime($bericht->getDatum()))?></div>
        <div class="inhoud"><?= $message; ?></div>
            <?php
            if ($bericht->getType() == 'opdracht') {
                ?>
                    <div class="einddatum">Deadline: <?= ucfirst(strftime('%a, %d %b %H:%M', strtotime($bericht->getEindtijd())))?></div>
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