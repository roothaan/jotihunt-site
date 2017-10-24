<?php
require_once '../init.php';
JotihuntUtils::requireLogin();

/**
 * @param DOMElement $element
 * @param string     $team
 *
 * @return bool
 */
function getImages($element, $team)
{
    global $pictures;
    global $letters;
    global $teamLetters;
    
    $images = $element->getElementsByTagName('img');
    for ($i = 0; $i < $images->length; $i++) {
        $img = $images->item($i);
        $imgSrc = $img->getAttribute('src');
        if (preg_match('/^\/\//', $imgSrc)) {
            $imgSrc = 'http:' . $imgSrc;
        }
        
        if(extension_loaded('imagick')) {
            $imagick = new Imagick();
            $imagick->readImage($imgSrc);
            $imgHash = $imagick->getImageSignature();
        } else {
            $imgHash = md5_file($imgSrc);
        }
        
        if (!isset($pictures[$imgHash])) {
            $nextLetterPosition = count($pictures);
            if (!isset($letters[$nextLetterPosition])) {
                return false;
            }
            $pictures[$imgHash] = $letters[$nextLetterPosition];
        }
        
        $teamLetters[$team][] = $pictures[$imgHash];
    }
    return true;
}

$pictures = [];
$letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
$status = 'failure';
$hintNaam = '';
$teamLetters = [
    'alpha'   => [],
    'bravo'   => [],
    'charlie' => [],
    'delta'   => [],
    'echo'    => [],
    'foxtrot' => [],
];
$prefilledLetters = [
    'alpha'   => [
        'x' => '',
        'y' => '',
    ],
    'bravo'   => [
        'x' => '',
        'y' => '',
    ],
    'charlie' => [
        'x' => '',
        'y' => '',
    ],
    'delta'   => [
        'x' => '',
        'y' => '',
    ],
    'echo'    => [
        'x' => '',
        'y' => '',
    ],
    'foxtrot' => [
        'x' => '',
        'y' => '',
    ],
];
$hint = $driver->getLastBerichtByType('hint');
if ($hint instanceof Bericht && !empty($hint->getInhoud())) {
    $hintNaam = $hint->getTitel();
    // Loop through DIVs
    $dom = new DOMDocument();
    $dom->loadHTML($hint->getInhoud());
    $imgs = $dom->getElementsByTagName('img');
    if ($imgs->length > 9) {
        $divs = $dom->getElementsByTagName('div');
        $imageGrabHalted = false;
        for ($i = 0; $i < $divs->length; $i++) {
            $element = $divs->item($i);
            $class = $element->getAttribute('class');
            switch ($class) {
                case 'hintDeelgebied hintDeelgebied_Alpha':
                    $success = getImages($element, 'alpha');
                    break;
                case 'hintDeelgebied hintDeelgebied_Bravo':
                    $success = getImages($element, 'bravo');
                    break;
                case 'hintDeelgebied hintDeelgebied_Charlie':
                    $success = getImages($element, 'charlie');
                    break;
                case 'hintDeelgebied hintDeelgebied_Delta':
                    $success = getImages($element, 'delta');
                    break;
                case 'hintDeelgebied hintDeelgebied_Echo':
                    $success = getImages($element, 'echo');
                    break;
                case 'hintDeelgebied hintDeelgebied_Foxtrot':
                    $success = getImages($element, 'foxtrot');
                    break;
                default:
                    $success = true;
                    break;
            }
            
            // Stop if we don't have enough letters left
            if (!$success) {
                $imageGrabHalted = true;
                break;
            }
        }
    }
    
    if (!$imageGrabHalted && !empty($pictures)) {
        $status = 'success';
        foreach ($teamLetters as $team => $letters) {
            if (!empty($letters)) {
                $type = 'x';
                $i = 0;
                foreach ($letters as $letter) {
                    $prefilledLetters[$team][$type] .= $letter;
                    $i++;
                    if ($i == 5) {
                        $type = 'y';
                    }
                }
            }
        }
    }
}

die(json_encode([
    'status' => $status,
    'letters' => $prefilledLetters,
    'hintnaam' => $hintNaam,
]));