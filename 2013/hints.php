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
    if (strlen($vos_x) > 4) {
        $vos_x = substr($vos_x, 0, 4);
        $vos_y = substr($vos_y, 0, 4);
    }
    $deelgebieden_locs[$deelgebied->getId()] = [$vos_x, $vos_y];
}

$html = '';
$error = '';
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
            } elseif(strlen($chars) != 4) {
                $error .= 'Hint letters niet compleet. Controleer of ze allemaal 4 tekens bevatten.<br />';
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
    
    if(empty($error)) {
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
        $resultArrayWithCalculatedDistance = [];
        foreach ($resultArray as $key => $result) {
            $result = calculateDistance($result, $oldCoords, $newCoords, (isset($_POST['ignore']) ? $_POST['ignore'] : []));
            if(!empty($result)) {
                $resultArrayWithCalculatedDistance[$key] = $result;
            }
        }
        
        // Sort result array, best results first
        usort($resultArrayWithCalculatedDistance, "cmpLegend");
        
        // Print results
        foreach ($resultArrayWithCalculatedDistance as $legendArray) {
            // Groups wont walk over 10km in one hour
            if ($legendArray['distance'] < 200) {
                $html .= printLegendAndCoords($words, $legendArray);
            } else {
                $t = 1;
            }
        }
        
        if(!empty($html)){
            $html = '<table class="resultTable" cellspacing="0" cellpadding="0">'.$html.'</table>';
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
        if (empty($ignoreArray[$team]) || $ignoreArray[$team] != '1') {
            $oldX = isset($oldCoords[$team]['x']) ? intval($oldCoords[$team]['x']) : 0;
            $oldY = isset($oldCoords[$team]['y']) ? intval($oldCoords[$team]['y']) : 0;
            $newX = intval(strtr($coords['x'], $legendArray['legend']));
            $newY = intval(strtr($coords['y'], $legendArray['legend']));
            if($newX > 1200 && $newX < 2600 && $newY > 4100 && $newY < 5100) {
                $diffX = abs($oldX - $newX);
                $diffY = abs($oldY - $newY);
                $distance = intval(sqrt(pow($diffX, 2) + pow($diffY, 2))) / 100;
                if ($distance > 5) {
                    // Team walked over 50km in an hour?! Not very likely
                    return [];
                }
            } else {
                // Coordinates are outside Gelderland
                return [];
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
 * @param $words
 * @param $legendArray
 *
 * @return string
 */
function printLegendAndCoords($words, $legendArray)
{
    $html = '<tr>';
    $html .= '<td>'.$legendArray['distance'].' km</td>';
    $html .= '<td>'.printLegend($legendArray['legend']).'</td>';
    $html .= '<td>'.printCoords($words, $legendArray['legend']).'</td>';
    $html .= '</tr>';
    return $html;
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
        $coord = strtr($word, $legend);
        $x = explode('-', $coord)[0].'0';
        $y = explode('-', $coord)[1].'0';
        $html .= '<a class="openMap" href="/2013/fullscreen_map.php?team='.ucfirst($key).'&marker_x='.$x.'&marker_y='.$y.'">';
        $html .= strtoupper(substr($key, 0, 1)) . ":" . $x.'-'.$y;
        $html .= '</a>';
        $html .= ', ';
    }
    return substr($html, 0, -2);
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
        $html .= $letter . " = " . $number . ", ";
    }
    
    return substr($html, 0, -2);
}

?>
<style>
    .openMap {
        color: black;
        text-decoration: none;
    }
    .openMap:hover {
        color: inherit;
    }
</style>
<script type="text/javascript">
    var fetchIsRunning = false;
    $(document).ready(function(){
        $('.openMap').click(function(e){
            e.preventDefault();
            window.open(
                $(this).attr('href'),
                null,
                "height=500,width=650,status=no,toolbar=no,menubar=no,location=no"
            );
        });
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
<style>
    table.resultTable td {
        padding: 5px;
    }
    table.resultTable tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,0.2);
    }
</style>
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
                           value="1" <?= isset($_POST['ignore'][strtolower($deelgebied->getName())]) && $_POST['ignore'][strtolower($deelgebied->getName())] == 1 ? 'checked="checked"' : '' ?>/>
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
                           value="<?= isset($_POST['letters'][strtolower($deelgebied->getName())]['x']) ? $_POST['letters'][strtolower($deelgebied->getName())]['x'] : "" ?>"/>
                </td>
                <td><input type="text" name="letters[<?= strtolower($deelgebied->getName()) ?>][y]"
                           value="<?= isset($_POST['letters'][strtolower($deelgebied->getName())]['y']) ? $_POST['letters'][strtolower($deelgebied->getName())]['y'] : "" ?>"/>
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