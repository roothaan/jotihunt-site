<?php
ini_set('memory_limit', '1024M');

if (!defined("opoiLoaded")) {
    die('Incorrect or unknown use of application');
}

JotihuntUtils::requireLogin();

//fetch vossen locaties voor placeholder
$deelgebieden = $driver->getAllDeelgebieden();
$deelgebieden_locs = [];
foreach ($deelgebieden as $deelgebied) {
    $vos = $driver->getVosIncludingLocations($deelgebied->getName());
    $vos_locations = [];
    if ($vos) {
        $vos_locations = $vos->getLocations();
    }
    if (count($vos_locations) > 0) {
        $vos_locatie = $vos_locations [0];
        $vos_x = (string)$vos_locatie->getX();
        $vos_y = (string)$vos_locatie->getY();
    } else {
        $vos_x = "00000";
        $vos_y = "00000";
    }
    if (strlen($vos_x) > 5) {
        $vos_x = substr($vos_x, 0, 5);
        $vos_y = substr($vos_y, 0, 5);
    }
    $deelgebieden_locs[$deelgebied->getId()] = [$vos_x, $vos_y];
}

$html = '';
if (!empty($_POST)) {
    define('MAX_ITERATIONS', 10);
    $availableNumbers = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
    $numbers = $letters = $probabilities = $words = $resultArray = [];
    $oldCoords = $_POST['numbers'] ?: [];
    $newCoords = $_POST['letters'] ?: [];
    
    // Read letters input
    foreach ($newCoords as $team => $coords) {
        $words[$team] = trim($coords['x']) . '-' . trim($coords['y']);
        foreach ($coords as $type => $chars) {
            $chars = trim($chars);
            if (empty($chars)) {
                $_POST['ignore'][$team] = 1;
            } else {
                for ($i = 0; $i < strlen($chars); $i++) {
                    $char = substr($chars, $i, 1);
                    $letters[$team][$type][$i] = $char;
                    if (!isset($probabilities[$char])) {
                        $probabilities[$char] = [1, 1, 1, 1, 1, 1, 1, 1, 1, 1];
                    }
                }
            }
        }
    }
    
    // Read old coords
    foreach ($oldCoords as $team => $coords) {
        foreach ($coords as $type => $chars) {
            $chars = trim($chars);
            for ($i = 0; $i < strlen($chars); $i++) {
                $char = substr($chars, $i, 1);
                $numbers[$team][$type][$i] = $char;
            }
        }
    }
    
    calculateProbabilities($probabilities, $letters, $numbers);
    
    // Sort probabilities
    $letterOrder = [];
    foreach ($probabilities as $letter => $array) {
        arsort($array);
        $probabilities[$letter] = $array;
        $letterOrder[$letter] = reset($array);
    }
    // Determine order of letters (most "solid" letters first)
    arsort($letterOrder);
    
    // Determine the best legenda's
    recursiveCalcBestPath(array_keys($letterOrder), $availableNumbers);
    
    // Calculate distance between new coords and old coords
    foreach ($resultArray as $key => $result) {
        $resultArray[$key] = calculateDistance($result, $oldCoords, $newCoords, $_POST['ignore']);
    }
    
    // Sort result array, best results first
    usort($resultArray, "cmpLegend");
    
    // Print results
    foreach ($resultArray as $legendArray) {
        if($legendArray['distance'] < 60) {
            // Groups wont walk over 10km in one hour
            $html .= $legendArray['distance'] . ' km - ' . printLegend($legendArray['legend']) . '<br />';
            $html .= 'coords: ' . printCoords($words, $legendArray['legend']) . '<br /><br />';
        }
    }
}

/**
 * Determine the best options and put them in global $resultArray
 *
 * @param     $availableLetters
 * @param     $availableNumbers
 * @param     $legend
 * @param int $currentPathScore
 */
function recursiveCalcBestPath($availableLetters, $availableNumbers, $legend = [], $currentPathScore = 0)
{
    global $probabilities;
    global $resultArray;
    
    $nextLetter = array_shift($availableLetters);
    
    $bestLocalPathScore = 0;
    
    foreach ($probabilities[$nextLetter] as $number => $probability) {
        if (isset($availableNumbers[$number])) {
            // Determine best local path score
            $bestLocalPathScore = max($probability, $bestLocalPathScore);
            
            // Ignore paths that are significantly worse than the best of this stage (i.e. < 33% score)
            if ($probability >= floor($bestLocalPathScore / 3)) {
                $availableNumbersCopy = $availableNumbers;
                unset($availableNumbersCopy[$number]);
                
                $newLegend = $legend;
                $newLegend[$nextLetter] = $number;
                $newScore = $currentPathScore + $probability;
                if (empty($availableLetters)) {
                    $resultArray[] = ['legend' => $newLegend, 'score' => $newScore];
                } else {
                    recursiveCalcBestPath($availableLetters, $availableNumbersCopy, $newLegend, $newScore);
                }
            }
        }
    }
}

function calculateDistance($legendArray, $oldCoords, $newCoords, $ignoreArray)
{
    $totalDistance = 0;
    foreach ($newCoords as $team => $coords) {
        if ($ignoreArray[$team] != '1') {
            $oldX = intval($oldCoords[$team]['x']);
            $oldY = intval($oldCoords[$team]['y']);
            $newX = intval(strtr($coords['x'], $legendArray['legend']));
            $newY = intval(strtr($coords['y'], $legendArray['legend']));
            $diffX = abs($oldX - $newX);
            $diffY = abs($oldY - $newY);
            $distance = intval(sqrt(pow($diffX, 2) + pow($diffY, 2)))/100;
            if($distance > 50){
                // Team walked over 50km in an hour?! Not very likely
                $distance *= 10000; // Multiply by 10000 to ensure it isn't listed
            }
            $totalDistance += $distance;
        }
    }
    $legendArray['distance'] = $totalDistance; // turn into KM
    return $legendArray;
}

/**
 * @param array $probabilities Array of probabilities (will be altered)
 * @param array $letters       Array of letters ("code")
 * @param array $numbers       Array of old coords
 */
function calculateProbabilities(&$probabilities, $letters, $numbers)
{
    $values = [
        5,
        1,
        1,
        0,
        0,
    ];
    
    // Compare old coords
    foreach ($numbers as $team => $coordSet) {
        foreach ($coordSet as $type => $coords) {
            foreach ($coords as $i => $coord) {
                if (!empty($letters[$team][$type][$i])) {
                    $probabilities[$letters[$team][$type][$i]][$coord] += $values[$i];
                }
            }
        }
    }
}

/**
 * Translate word using the legend
 *
 * @param $words
 * @param $legend
 *
 * @return string
 */
function printCoords($words, $legend)
{
    $html = '';
    foreach ($words as $key => $word) {
        $html .= strtoupper(substr($key, 0, 1)) . ": " . strtr($word, $legend) . ', ';
    }
    return $html;
}

/**
 * Compare two legends by their scores
 *
 * @param $a
 * @param $b
 *
 * @return int
 */
function cmpLegend($a, $b)
{
    if ($a['distance'] == $b['distance']) {
        return 0;
    }
    
    return ($a['distance'] < $b['distance']) ? -1 : 1;
}

/**
 * Print the legend
 *
 * @param $legend
 *
 * @return string
 */
function printLegend($legend)
{
    ksort($legend);
    $html = '';
    foreach ($legend as $letter => $number) {
        $html .= $letter . "=" . $number . ", ";
    }
    
    return substr($html, 0, -2);
}

?>
<script type="text/javascript">
    var fetchIsRunning = false;
    $(document).ready(function(){
        $('#fetchHintLetters').click(function(e){
            e.preventDefault();
            if(!fetchIsRunning) {
                $('#fetchHintLetters').prop('disabled', true).attr("value", "Bezig met ophalen ...");
                fetchIsRunning = true;
                $.ajax({
                    url: "<?= BASE_URL ?>ajax/hintletters.ajax.php",
                    type: "GET",
                    success: function (response) {
                        var json = $.parseJSON(response);
                        
                        if (json.status == "success") {
                            $('input[name="letters[alpha][x]"]').val(json.letters.alpha.x);
                            $('input[name="letters[alpha][y]"]').val(json.letters.alpha.y);
                            $('input[name="letters[bravo][x]"]').val(json.letters.bravo.x);
                            $('input[name="letters[bravo][y]"]').val(json.letters.bravo.y);
                            $('input[name="letters[charlie][x]"]').val(json.letters.charlie.x);
                            $('input[name="letters[charlie][y]"]').val(json.letters.charlie.y);
                            $('input[name="letters[delta][x]"]').val(json.letters.delta.x);
                            $('input[name="letters[delta][y]"]').val(json.letters.delta.y);
                            $('input[name="letters[echo][x]"]').val(json.letters.echo.x);
                            $('input[name="letters[echo][y]"]').val(json.letters.echo.y);
                            $('input[name="letters[foxtrot][x]"]').val(json.letters.foxtrot.x);
                            $('input[name="letters[foxtrot][y]"]').val(json.letters.foxtrot.y);
                        } else {
                            alert('Geen hint letters beschikbaar');
                        }
                    },
                    complete: function () {
                        fetchIsRunning = false;
                        $('#fetchHintLetters').prop('disabled', false).attr("value","Haal hint letters op");
                    }
                });
            }
        });
    });
</script>
<h1>Hints oplossen</h1>
Via dit formulier kun je hints op laten lossen aan de hand van vorige coördinaten. Dit werkt het beste bij hints met
tien plaatjes o.i.d., waarbij ieder plaatje overeenkomt met 1 cijfer. De vorige coördinaten worden gebruikt om te
kijken wat het meest aannemelijke cijfer is. Je kan ervoor kiezen om bepaalde coördinaten niet mee te laten nemen in
het berekenen van de kortste afstand, bijvoorbeeld omdat een groep net een verplaatsing heeft gehad.
<hr id="hrheader">
<form method="post">
    <h2>Oude coördinaten</h2>
    <table>
        <?php foreach ($deelgebieden as $deelgebied) { ?>
            <tr>
                <td><?= $deelgebied->getName() ?></td>
                <td><input type="text" name="numbers[<?= strtolower($deelgebied->getName()) ?>][x]"
                           value="<?= $deelgebieden_locs[$deelgebied->getId()][0] ?>"/></td>
                <td><input type="text" name="numbers[<?= strtolower($deelgebied->getName()) ?>][y]"
                           value="<?= $deelgebieden_locs[$deelgebied->getId()][1] ?>"/></td>
                <td><input type="checkbox" name="ignore[<?= strtolower($deelgebied->getName()) ?>]"
                           value="1" <?= $_POST['ignore'][strtolower($deelgebied->getName())] == 1 ? 'checked="checked"' : '' ?>/>
                    Negeer (groep is verplaatst of hint ontbreekt)
                </td>
            </tr>
        <?php } ?>
    </table>
    <hr id="hrheader">
    <h2>Hint letters</h2>
    <table>
        <?php foreach ($deelgebieden as $deelgebied) { ?>
            <tr>
                <td><?= $deelgebied->getName() ?></td>
                <td><input type="text" name="letters[<?= strtolower($deelgebied->getName()) ?>][x]"
                           value="<?= $_POST['letters'][strtolower($deelgebied->getName())]['x'] ?>"/>
                </td>
                <td><input type="text" name="letters[<?= strtolower($deelgebied->getName()) ?>][y]"
                           value="<?= $_POST['letters'][strtolower($deelgebied->getName())]['y'] ?>"/>
                </td>
            </tr>
        <?php } ?>
    </table>
    <input type="submit" id="fetchHintLetters" value="Haal hint letters op"/>
    <input type="submit" value="Bereken locaties"/>
</form>
<hr id="hrheader">
<h2>Resultaten</h2>
<?= empty($html) ? 'Er zijn nog geen resultaten beschikbaar' : $html ?>

<div class="clear"></div>