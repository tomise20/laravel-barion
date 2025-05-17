<?php
namespace Tomise\Barion\Attributes;

use Attribute;
use DateTime;

#[Attribute(Attribute::TARGET_PROPERTY)]
class MapToDateTime
{
    public function __construct(
        public string $format = 'Y-m-d H:i:s' 
    ) {}

    public function process(mixed $rawValue): ?DateTime
    {
        $dateTime = DateTime::createFromFormat($this->format, $rawValue);

        return $dateTime !== false ? $dateTime : null;
    }
}