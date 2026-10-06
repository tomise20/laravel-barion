<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

use Tomise\Barion\Traits\Arrayable;
use Tomise\Barion\Traits\HasSetter;

/**
 * A billing or shipping address (Barion: ShippingAddressModel / BillingAddressModel).
 *
 * @method self setCountry(string $country) two letter ISO code, e.g. "HU"
 * @method self setRegion(string $region)
 * @method self setCity(string $city)
 * @method self setZip(string $zip)
 * @method self setStreet(string $street)
 * @method self setStreet2(string $street2)
 * @method self setStreet3(string $street3)
 * @method self setFullName(string $fullName)
 */
class AddressDto
{
    use Arrayable, HasSetter;

    public string $country;
    public ?string $region = null;
    public string $city;
    public string $zip;
    public string $street;
    public ?string $street2 = null;
    public ?string $street3 = null;
    public ?string $fullName = null;

    /**
     * @deprecated Barion calls it Zip.
     */
    public function setPostalCode(string $postalCode): self
    {
        $this->zip = $postalCode;

        return $this;
    }

    /**
     * @deprecated Barion's Country is the two letter ISO code.
     */
    public function setCountryIso2(string $countryIso2): self
    {
        $this->country = strtoupper($countryIso2);

        return $this;
    }
}
