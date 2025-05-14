<?php

declare(strict_types=1);

namespace Tomise\Barion\Utils;

use ReflectionClass;

class TransformHelper
{
    public static function transformArray(mixed $instance, array $rawResponse): mixed
    {
        $reflectionClass = new ReflectionClass($instance);

        foreach ($reflectionClass->getProperties() as $property) {
            $propertyName = $property->getName();
            $arrayKey = ucfirst($propertyName);

            if (isset($rawResponse[$arrayKey])) {
                $attributes = $property->getAttributes();

                if (!empty($attributes)) {
                    $attributeInstance = $attributes[0]->newInstance();

                    if (method_exists($attributeInstance, 'process')) {
                        $processedValue = $attributeInstance->process($rawResponse[$arrayKey]);
                        $property->setValue($instance, $processedValue);
                    }
                } else {
                    $property->setValue($instance, $rawResponse[$arrayKey]);
                }
            }
        }

        return $instance;
    }
}