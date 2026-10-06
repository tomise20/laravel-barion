<?php

declare(strict_types=1);

namespace Tomise\Barion\Utils;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use Throwable;

/**
 * Fills an object from a Barion response. Barion uses PascalCase keys with upper case acronyms
 * (e.g. "POSTransactionId"), so the keys are matched to the properties case-insensitively.
 */
class TransformHelper
{
    public static function transformArray(object $instance, array $rawResponse, bool $strict = false): object
    {
        $keys = [];

        foreach ($rawResponse as $key => $value) {
            $keys[strtolower((string) $key)] = $key;
        }

        foreach ((new ReflectionClass($instance))->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $key = $keys[strtolower($property->getName())] ?? null;

            if ($key === null) {
                continue;
            }

            try {
                $property->setValue($instance, self::castValue($property, $rawResponse[$key]));
            } catch (Throwable $exception) {
                if ($strict) {
                    throw new InvalidArgumentException(sprintf('Invalid value for %s::$%s: %s', $instance::class, $property->getName(), $exception->getMessage()), 0, $exception);
                }
            }
        }

        return $instance;
    }

    private static function castValue(ReflectionProperty $property, mixed $value): mixed
    {
        foreach ($property->getAttributes() as $attribute) {
            $mapper = $attribute->newInstance();

            if (method_exists($mapper, 'process')) {
                return $value === null ? null : $mapper->process($value);
            }
        }

        // Barion sends whole numbers for amounts (e.g. 1500), which a float property accepts only as a float.
        $type = $property->getType();

        if ($type instanceof ReflectionNamedType && $type->getName() === 'float' && is_int($value)) {
            return (float) $value;
        }

        return $value;
    }
}
