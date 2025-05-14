<?php

declare(strict_types=1);

namespace Tomise\Barion\Attributes;

use Attribute;
use InvalidArgumentException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class MapTo
{
    public function __construct(public string $type) {}

    public function process(mixed $rawValue): mixed
    {
        if (is_null($rawValue)) {
            return null;
        }
        
        if (enum_exists($this->type)) {
            if (method_exists($this->type, 'tryFrom')) {
                $enumValue = $this->type::tryFrom($rawValue);
                if ($enumValue === null) {
                    throw new InvalidArgumentException("Invalid value '{$rawValue}' for enum '{$this->type}'.");
                }
                return $enumValue;
            }
            throw new InvalidArgumentException("The enum type {$this->type} does not support tryFrom.");
        }

        if (class_exists($this->type)) {
            if (method_exists($this->type, 'createFromArray')) {
                return $this->type::createFromArray($rawValue);
            }
            return new $this->type($rawValue);
        }

        if (in_array($this->type, ['int', 'float', 'string', 'bool'], true)) {
            settype($rawValue, $this->type);
            return $rawValue;
        }

        throw new InvalidArgumentException("Unsupported type: {$this->type}");
    }
}