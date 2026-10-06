<?php

declare(strict_types=1);

namespace Tomise\Barion\Traits;

use BadMethodCallException;
use Illuminate\Support\Str;

/**
 * Fluent setters for the public properties: setUnitPrice(100) sets $unitPrice and returns the object.
 */
trait HasSetter
{
    public function __call(string $method, array $arguments): static
    {
        if (str_starts_with($method, 'set') && count($arguments) === 1) {
            $property = Str::camel(substr($method, 3));

            if (property_exists($this, $property)) {
                $this->{$property} = $arguments[0];

                return $this;
            }
        }

        throw new BadMethodCallException(sprintf('Method %s::%s does not exist.', static::class, $method));
    }
}
