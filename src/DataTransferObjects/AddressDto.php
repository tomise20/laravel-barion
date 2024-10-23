<?php

namespace Tomise\Barion\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;

class AddressDto implements Arrayable {
    private string $country;
    private string $countryIso2;
    private string $city;
    private string $postalCode;
    private string $street;

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): AddressDto
    {
        $this->country = $country;

        return $this;
    }

    public function getCountryIso2(): string
    {
        return $this->countryIso2;
    }

    public function setCountryIso2(string $iso2): AddressDto
    {
        $this->countryIso2 = $iso2;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): AddressDto
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): AddressDto
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street): AddressDto
    {
        $this->street = $street;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'Country' => $this->country,
            'City' => $this->city,
            'Zip' => $this->postalCode,
            'Street' => $this->street
        ];
    }
}