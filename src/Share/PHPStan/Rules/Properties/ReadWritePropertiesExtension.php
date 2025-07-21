<?php

declare(strict_types=1);

namespace App\Share\PHPStan\Rules\Properties;

use PHPStan\Reflection\PropertyReflection;
use PHPStan\Rules\Properties\ReadWritePropertiesExtension as PHPStanReadWritePropertiesExtension;

class ReadWritePropertiesExtension implements PHPStanReadWritePropertiesExtension
{
    public function isAlwaysRead(PropertyReflection $property, string $propertyName): bool
    {
        // Check if this is a Doctrine entity property
        if ($this->isDoctrineEntityProperty($property, $propertyName)) {
            return false; // Doctrine entities can have properties written by ORM
        }

        return false;
    }

    public function isAlwaysWritten(PropertyReflection $property, string $propertyName): bool
    {
        // Check if this is a Doctrine entity property
        if ($this->isDoctrineEntityProperty($property, $propertyName)) {
            return false; // Doctrine entities can have properties written by ORM
        }

        return false;
    }

    public function isInitialized(PropertyReflection $property, string $propertyName): bool
    {
        // Check if this is a Doctrine entity property
        if ($this->isDoctrineEntityProperty($property, $propertyName)) {
            return true; // Doctrine entities are initialized by ORM
        }

        return false;
    }

    private function isDoctrineEntityProperty(PropertyReflection $property, string $propertyName): bool
    {
        $declaringClass = $property->getDeclaringClass();
        $reflection = $declaringClass->getNativeReflection();

        // Check if the class is a Doctrine entity
        $classAttributes = $reflection->getAttributes();
        $isDoctrineEntity = false;

        foreach ($classAttributes as $attribute) {
            $attributeName = $attribute->getName();
            if (str_contains($attributeName, 'Doctrine\\ORM\\Mapping\\Entity') ||
                str_contains($attributeName, 'Doctrine\\ORM\\Mapping\\Table')) {
                $isDoctrineEntity = true;

                break;
            }
        }

        if (!$isDoctrineEntity) {
            return false;
        }

        // Check if the property has ORM attributes
        $propertyReflection = $reflection->getProperty($propertyName);
        $propertyAttributes = $propertyReflection->getAttributes();

        foreach ($propertyAttributes as $attribute) {
            $attributeName = $attribute->getName();
            if (str_contains($attributeName, 'Doctrine\\ORM\\Mapping\\')) {
                return true;
            }
        }

        return false;
    }
}
