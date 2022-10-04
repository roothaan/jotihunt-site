<?php

class JotihuntUtils {

    /**
     * confRijksDriehoekToGeo
     * <p>Converts "Rijksdriehoek Coordinaten" into GEO (dd.dd.ddd,dd.dd.ddd).</p>
     *
     * @param integer $x
     *            171822
     * @param integer $y
     *            475082
     * @return string GEO (format DD.ddddd,DD.ddddd), example 52.231028068552,5.6937618531185
     */
    static function convert($x, $y) {
        $x0 = 155000.000;
        $y0 = 463000.000;
        
        $f0 = 52.156160556;
        $l0 = 5.387638889;
        
        $a01 = 3236.0331637;
        $a20 = - 32.5915821;
        $a02 = - 0.2472814;
        $a21 = - 0.8501341;
        $a03 = - 0.0655238;
        $a22 = - 0.0171137;
        $a40 = 0.0052771;
        $a23 = - 0.0003859;
        $a41 = 0.0003314;
        $a04 = 0.0000371;
        $a42 = 0.0000143;
        $a24 = - 0.0000090;
        
        $b10 = 5261.3028966;
        $b11 = 105.9780241;
        $b12 = 2.4576469;
        $b30 = - 0.8192156;
        $b31 = - 0.0560092;
        $b13 = 0.0560089;
        $b32 = - 0.0025614;
        $b14 = 0.0012770;
        $b50 = 0.0002574;
        $b33 = - 0.0000973;
        $b51 = 0.0000293;
        $b15 = 0.0000291;
        
        $dx = ($x - $x0) * pow(10, - 5);
        $dy = ($y - $y0) * pow(10, - 5);
        
        $df = $a01 * $dy + $a20 * pow($dx, 2) + $a02 * pow($dy, 2) + $a21 * pow($dx, 2) * $dy + $a03 * pow($dy, 3);
        $df += $a40 * pow($dx, 4) + $a22 * pow($dx, 2) * pow($dy, 2) + $a04 * pow($dy, 4) + $a41 * pow($dx, 4) * $dy;
        $df += $a23 * pow($dx, 2) * pow($dy, 3) + $a42 * pow($dx, 4) * pow($dy, 2) + $a24 * pow($dx, 2) * pow($dy, 4);
        $f = $f0 + $df / 3600;
        
        $dl = $b10 * $dx + $b11 * $dx * $dy + $b30 * pow($dx, 3) + $b12 * $dx * pow($dy, 2) + $b31 * pow($dx, 3) * $dy;
        $dl += $b13 * $dx * pow($dy, 3) + $b50 * pow($dx, 5) + $b32 * pow($dx, 3) * pow($dy, 2) + $b14 * $dx * pow($dy, 4);
        $dl += $b51 * pow($dx, 5) * $dy + $b33 * pow($dx, 3) * pow($dy, 3) + $b15 * $dx * pow($dy, 5);
        $l = $l0 + $dl / 3600;
        
        return $f . "," . $l;
    }

    /**
     * <p>Checks if the user is logged in.<br />
     * If not, this redirects to login and ends script execution.</p>
     */
    static function requireLogin() {
        global $authMgr;
        if (! isset($authMgr) || ! $authMgr->isLoggedIn()) {
            header('Location: ' . WEBSITE_URL . 'login');
            die();
        }
    }
    
    static function getPhoneNumbersForUserId($userId) {
        global $driver;
        $first = true;
        
        $result = '';
        foreach ($driver->getPhonenumbersForUserId($userId) as $tel) {
            if (!$first) {
                $result .= ', ';
            }
            $result .= '<a href="tel:'.$tel['phonenumber'].'">'.$tel['phonenumber'].'</a>';
            $first = false;
        }
        return $result;
    }

    private static $noHeaderFooter = array(
        'beamer',
        'kaart',
        'delete_locatie',
        'kml',
        'deelgebieden-kml',
        'admin-subscriptions-kml'
    );

    private static $urlParts = array();
    
    public static function setUrlParts($urlToParse) {
        $_urlParts = parse_url($urlToParse);

        if (isset($_urlParts['path'])) {
            JotihuntUtils::$urlParts = explode('/', trim($_urlParts['path'], '/'));
        }
    }
    
    public static function getUrlPart($part) {
        if (isset(JotihuntUtils::$urlParts[$part])) {
            return urldecode(JotihuntUtils::$urlParts[$part]);
        }
        return null;
    }
    
    public static function hasHeaderOrFooter() {
        return !in_array(JotihuntUtils::getUrlPart(0), JotihuntUtils::$noHeaderFooter);
    }
}
?>