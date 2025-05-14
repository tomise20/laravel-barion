<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

use Tomise\Barion\Traits\Arrayable;
use Tomise\Barion\Traits\HasSetter;


/**
 * @method self setCountry(string $country)
 * @method self setCountryIso2(string $countryIso2)
 * @method self setCity(string $city)
 * @method self setPostalCode(string $postalCode)
 * @method self setStreet(string $street)
 * @method self setStreet2(string $street2)
 * @method self setStreet3(string $street3)
 * @method self setFullName(string $fullName)
 */
class AddressDto {


    use Arrayable, HasSetter;

    public string $country;
    public string $countryIso2;
    public string $city;
    public string $postalCode;
    public string $street;
    public ?string $street2 = null;
    public ?string $street3 = null;
    public ?string $fullName = null;
}
