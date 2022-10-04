<?php

class Deelnemer {
    private string $name;
    private string $accomodation;
    private string $street;
    private int $housenumber;
    private ?string $housenumber_addition;
    private string $postcode;
    private string $city;
    private string $lat;
    private string $long;

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Deelnemer
     */
    public function setName( string $name ): Deelnemer {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccomodation(): string {
        return $this->accomodation;
    }

    /**
     * @param string $accomodation
     *
     * @return Deelnemer
     */
    public function setAccomodation( string $accomodation ): Deelnemer {
        $this->accomodation = $accomodation;

        return $this;
    }

    /**
     * @return string
     */
    public function getStreet(): string {
        return $this->street;
    }

    /**
     * @param string $street
     *
     * @return Deelnemer
     */
    public function setStreet( string $street ): Deelnemer {
        $this->street = $street;

        return $this;
    }

    /**
     * @return int
     */
    public function getHousenumber(): int {
        return $this->housenumber;
    }

    /**
     * @param int $housenumber
     *
     * @return Deelnemer
     */
    public function setHousenumber( int $housenumber ): Deelnemer {
        $this->housenumber = $housenumber;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getHousenumberAddition(): ?string {
        return $this->housenumber_addition;
    }

    /**
     * @param ?string $housenumber_addition
     *
     * @return Deelnemer
     */
    public function setHousenumberAddition( ?string $housenumber_addition ): Deelnemer {
        $this->housenumber_addition = $housenumber_addition;

        return $this;
    }

    /**
     * @return string
     */
    public function getPostcode(): string {
        return $this->postcode;
    }

    /**
     * @param string $postcode
     *
     * @return Deelnemer
     */
    public function setPostcode( string $postcode ): Deelnemer {
        $this->postcode = $postcode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCity(): string {
        return $this->city;
    }

    /**
     * @param string $city
     *
     * @return Deelnemer
     */
    public function setCity( string $city ): Deelnemer {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getLat(): string {
        return $this->lat;
    }

    /**
     * @param string $lat
     *
     * @return Deelnemer
     */
    public function setLat( string $lat ): Deelnemer {
        $this->lat = $lat;

        return $this;
    }

    /**
     * @return string
     */
    public function getLong(): string {
        return $this->long;
    }

    /**
     * @param string $long
     *
     * @return Deelnemer
     */
    public function setLong( string $long ): Deelnemer {
        $this->long = $long;

        return $this;
    }


}
