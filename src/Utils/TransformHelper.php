<?php

declare(strict_types=1);

namespace Tomise\Barion\Utils;

use DateTime;
use Illuminate\Support\Collection;
use ReflectionClass;

class TransformHelper {
    public static function transformArray(mixed $instance, array $rawResponse): mixed
    {
        $reflectionClass = new ReflectionClass($instance);

        foreach ($reflectionClass->getProperties() as $property) {
            $propertyName = $property->getName();
            $arrayKey = ucfirst($propertyName);

            if (isset($rawResponse[$arrayKey])) {
                $propertyType = $property?->getType()?->getName();

                if (class_exists($propertyType) && method_exists($propertyType, 'createFromArray')) {
                    $property->setValue($instance, $propertyType::createFromArray($rawResponse[$arrayKey]));
                } elseif($propertyType === 'Collection') {
                    $collectionClass = $property->getType()->getName();
                    $collectionItems = array_map(function ($item) use ($collectionClass) {
                    
                        return $collectionClass::createFromArray($item);
                    }, $rawResponse[$arrayKey]);
                
                    $property->setValue($instance, new Collection($collectionItems));
                } elseif(is_object($property) && method_exists($propertyType, 'tryFrom')) {
                    $property->setValue($instance, $propertyType::tryFrom($rawResponse[$arrayKey]));
                } elseif (is_string($rawResponse[$arrayKey]) && DateTime::createFromFormat('Y-m-d H:i:s', $rawResponse[$arrayKey]) !== false) {
                    $property->setValue($instance, new DateTime($rawResponse[$arrayKey]));
                } else {
                    $property->setValue($instance, $rawResponse[$arrayKey]);
                }
            }
        
        }

        return $instance;
    }
}