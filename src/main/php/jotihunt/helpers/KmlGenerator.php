<?php

class KmlGenerator {
    private function getStyles(): string {
        $lineStyle = '<Style id="line">';
        $lineStyle .= '<LineStyle>';
        $lineStyle .= '<color>7f0000B2</color>';
        $lineStyle .= '<width>3</width>';
        $lineStyle .= '</LineStyle>';
        $lineStyle .= '<PolyStyle>';
        $lineStyle .= '<color>7f00ff00</color>';
        $lineStyle .= '</PolyStyle>';
        $lineStyle .= '</Style>';

        $iconStyle = '<Style id="icon">';
        $iconStyle .= '<IconStyle>';
        $iconStyle .= '<Icon>';
        $iconStyle .= '<href>https://maps.gstatic.com/mapfiles/ms2/micons/yellow-dot.png</href>';
        $iconStyle .= '</Icon>';
        $iconStyle .= '</IconStyle></Style>';

        return $lineStyle . $iconStyle;
    }

    function printXmlHeader(): void {
        header( 'Content-Type: text/xml' );
    }

    function getHeader(string $name): string {
        $base = '<?xml version="1.0" encoding="UTF-8"?>';
        $base .= '<kml xmlns="https://www.opengis.net/kml/2.2">';
        $base .= '<Document>';
        $base .= $this->getStyles();
        $base .= "<name>$name</name>";

        return $base;
    }

    function addDescription( string $description ): string {
        return "<description>$description</description>";
    }

    function addPlacemark( string $name, string $description, string $lat, string $long ): string {
        $placemark = '<Placemark>';
        $placemark .= "<name>" . htmlspecialchars( $name ) . "</name>";
        $placemark .= "<description>" . htmlspecialchars( $description ) . "</description>";
        $placemark .= "<styleUrl>#icon</styleUrl>";
        $placemark .= "<Point><coordinates>$long,$lat</coordinates></Point>";
        $placemark .= '</Placemark>';

        return $placemark;
    }

    function getFooter(): string {
        return '</Document></kml>';
    }
}