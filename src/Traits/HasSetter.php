<?php

declare(strict_types=1);

namespace Tomise\Barion\Traits;

use ReflectionClass;
use ReflectionProperty;
use Illuminate\Support\Str;

trait HasSetter {
    public static function bootHasSetters()
    {
        $class = new ReflectionClass(static::class);
        $properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $setterName = 'set' . Str::studly($propertyName);

            if (!method_exists(static::class, $setterName)) {
                static::macro($setterName, function ($value) use ($propertyName) {
                    $this->$propertyName = $value;
                    return $this; // For method chaining
                });
            }
        }
    }
}