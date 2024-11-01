<?php

namespace Tomise\Barion\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;

class AddressDto implements Arrayable {
    private string $country;
    private string $countryIso2;
    private string $city;
    private string $postalCode;
    private string $street;
    private ?string $street2 = null;
    private ?string $street3 = null;
    private ?string $fullName = null;

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

    public function getStreet2(): ?string
    {
        return $this->street2;
    }

    public function setStreet2(?string $street2): AddressDto
    {
        $this->street2 = $street2;

        return $this;
    }

    public function getStreet3(): ?string
    {
        return $this->street3;
    }

    public function setStreet3(?string $street3): AddressDto
    {
        $this->street3 = $street3;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): AddressDto
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function toArray(): array
    {
        $value = [
            'Country' => $this->country,
            'City' => $this->city,
            'Zip' => $this->postalCode,
            'Street' => $this->street,
        ];

        if($this->street2) {
            $value['Street2'] = $this->street2;
        }

        if($this->street3) {
            $value['Street3'] = $this->street3;
        }

        if($this->fullName) {
            $value['FullName'] = $this->fullName;
        }

        return $value;
    }
}