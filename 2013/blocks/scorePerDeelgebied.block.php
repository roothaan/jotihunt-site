<?php
if (! defined('BASE_DIR')) {
    echo 'This file only works as include!';
    return;
}
require_once BASE_DIR . 'init.php';

$allHunts = $driver->getAllHunts();

$huntCounter = array ();
foreach ( $allHunts as $hunt ) {
    $deelGebied = $hunt->getDeelgebied();
    if (! array_key_exists($deelGebied, $huntCounter)) {
        $huntCounter [$deelGebied] = 0;
    }
    $huntCounter [$deelGebied] = $huntCounter [$deelGebied] + 1;
}
ksort($huntCounter);

echo '<h2>Statistieken</h2><div><table>';
foreach ( $huntCounter as $deelgebied => $counter ) {
    echo "<tr><td style=\"width: 200px;font-weight: bold;\">" . $deelgebied . "</td><td>" . $counter . "</td></tr>";
}
echo '</table></div>';
?>