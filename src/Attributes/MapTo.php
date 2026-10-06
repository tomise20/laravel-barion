<?php

declare(strict_types=1);

namespace Tomise\Barion\Attributes;

use Attribute;
use InvalidArgumentException;

/**
 * Maps a raw response value to an enum (unknown values become null), an object or a scalar type.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class MapTo
{
    public function __construct(public string $type) {}

    public function process(mixed $rawValue): mixed
    {
        if ($rawValue === null) {
            return null;
        }

        if (enum_exists($this->type)) {
            // A value Barion adds later must not break the whole response.
            return $this->type::tryFrom($rawValue);
        }

        if (class_exists($this->type)) {
            return match (true) {
                method_exists($this->type, 'createFromArray') => $this->type::createFromArray($rawValue),
                method_exists($this->type, 'fromArray') => $this->type::fromArray($rawValue),
                default => new $this->type($rawValue),
            };
        }

        if (in_array($this->type, ['int', 'float', 'string', 'bool'], true)) {
            settype($rawValue, $this->type);

            return $rawValue;
        }

        throw new InvalidArgumentException("Unsupported type: {$this->type}");
    }
}
