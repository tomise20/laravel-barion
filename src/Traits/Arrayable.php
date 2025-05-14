<?php

declare(strict_types=1);

namespace Tomise\Barion\Traits;

use ReflectionClass;
use ReflectionProperty;
use Illuminate\Support\Str;

trait Arrayable {
    public function toArray(): array {
        $class = new ReflectionClass($this);
        $properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);

        $data = [];
        foreach ($properties as $property) {
            $value = $property->getValue($this);
            if ($value !== null) {
                if (is_object($value) && method_exists($value, 'toArray')) {
                    $data[$property->getName()] = $value->toArrayRecursive(); // Recursive call
                } elseif (is_array($value)) {
                    $data[$property->getName()] = array_map(function ($item) {
                        if (is_object($item) && method_exists($item, 'toArray')) {
                            return $item->toArrayRecursive();
                        }
                        return $item;
                    }, $value);
                } else {
                    $data[$property->getName()] = $value;
                }
            }
        }

        return $data;
    }

    public static function fromArray(array $data, bool $strict = false): static
    {
        $class = new ReflectionClass(static::class);
        $instance = $class->newInstanceWithoutConstructor();

        foreach ($data as $key => $value) {
            $propertyName = Str::camel($key);

            try {
                $property = $class->getProperty($propertyName);
                $property->setAccessible(true);

                $attributes = $property->getAttributes();

                if (!empty($attributes)) {
                    $attributeInstance = $attributes[0]->newInstance();

                    if (method_exists($attributeInstance, 'process')) {
                        $processedValue = $attributeInstance->process($value);
                        $property->setValue($instance, $processedValue);
                    }
                } else {
                    $property->setValue($instance, $value);
                }
            } catch (\ReflectionException $e) {
                if ($strict) {
                    throw new \Exception("Property '$propertyName' (from key '$key') is not defined in class '" . static::class . "'", 0, $e);
                }
            } catch (\ValueError $e) {
                if ($strict) {
                    throw new \Exception("Invalid value for property '$propertyName' (from key '$key') in class '" . static::class . "': " . $e->getMessage(), 0, $e);
                }
            }
        }

        return $instance;
    }
}