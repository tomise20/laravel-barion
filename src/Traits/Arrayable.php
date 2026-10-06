<?php

declare(strict_types=1);

namespace Tomise\Barion\Traits;

use BackedEnum;
use DateTimeInterface;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionProperty;
use Tomise\Barion\Utils\Amount;
use Tomise\Barion\Utils\TransformHelper;

/**
 * Converts the public properties to the PascalCase array Barion expects (e.g. unitPrice => UnitPrice), and back.
 * Unset (uninitialized) and null properties are left out.
 */
trait Arrayable
{
    public function toArray(): array
    {
        $data = [];

        foreach ((new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isStatic() || ! $property->isInitialized($this)) {
                continue;
            }

            $value = $property->getValue($this);

            if ($value !== null) {
                $data[ucfirst($property->getName())] = self::serializeValue($value);
            }
        }

        return $data;
    }

    public static function fromArray(array $data, bool $strict = false): static
    {
        $instance = (new ReflectionClass(static::class))->newInstanceWithoutConstructor();

        return TransformHelper::transformArray($instance, $data, $strict);
    }

    private static function serializeValue(mixed $value): mixed
    {
        return match (true) {
            $value instanceof BackedEnum => $value->value,
            $value instanceof DateTimeInterface => $value->format(DATE_ATOM),
            $value instanceof Collection => $value->map(fn (mixed $item): mixed => self::serializeValue($item))->values()->all(),
            is_object($value) && method_exists($value, 'toArray') => $value->toArray(),
            is_array($value) => array_map(fn (mixed $item): mixed => self::serializeValue($item), $value),
            is_float($value) => Amount::normalize($value),
            default => $value,
        };
    }
}
