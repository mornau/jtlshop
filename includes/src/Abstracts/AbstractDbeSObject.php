<?php

declare(strict_types=1);

namespace JTL\Abstracts;

use JTL\DataObjects\DomainObjectInterface;
use JTL\Helpers\Typifier;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use stdClass;

/**
 * Class AbstractDomainObject
 * @package JTL\DataObjects
 */
abstract class AbstractDbeSObject implements DomainObjectInterface
{
    /**
     * AbstractDomainObject constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param array $xml
     * @return static
     * @throws ReflectionException
     */
    public static function initFromXML(array $xml): self
    {
        $keysAndTypes = self::getKeysAndValueTypes();
        $data         = [];
        foreach (\array_keys($keysAndTypes) as $key => $type) {
            $value      = $xml[$key] ?? null;
            $data[$key] = match ($type) {
                'int'    => Typifier::intify($value),
                'float'  => Typifier::floatify($value),
                'bool'   => Typifier::boolify($value),
                'array'  => Typifier::arrify($value),
                'object' => Typifier::objectify($value),
                default  => $value
            };
        }

        return new static(...$data);
    }

    /**
     * @inheritdoc
     */
    public function toArray(bool $deep = false, bool $serialize = true): array
    {
        $reflect = new ReflectionClass($this);
        if ($deep === true) {
            $properties = $reflect->getProperties();
        } else {
            $properties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        }
        $toArray = [];
        foreach ($properties as $property) {
            $propertyName  = $property->getName();
            $propertyValue = $property->getValue($this);
            if ($propertyName === 'modifiedKeys') {
                continue;
            }
            if ($serialize && (\is_array($propertyValue || \is_object($propertyValue)))) {
                $toArray[$propertyName] = \serialize($propertyValue);
            } else {
                $toArray[$propertyName] = $propertyValue;
            }
        }

        return $toArray;
    }

    /**
     * @inheritdoc
     */
    public function toObject(bool $deep = false): stdClass
    {
        return (object)$this->toArray($deep);
    }

    /**
     * @throws \JsonException
     */
    public function toJson(bool $deep = false, ?int $flags = null): string
    {
        return \json_encode($this->toArray($deep), $flags ?? \JSON_THROW_ON_ERROR);
    }

    /**
     * @inheritdoc
     */
    public function extract(): array
    {
        $reflect    = new ReflectionClass($this);
        $attributes = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        $extracted  = [];
        foreach ($attributes as $attribute) {
            $method = 'get' . \ucfirst($attribute->getName());
            if ($attribute->name !== 'modifiedKeys') {
                $extracted[$attribute->name] = $this->$method();
            }
        }

        return $extracted;
    }

    /**
     * @param class-string|null $domainObjectName
     * @return array<string, string>
     * @throws ReflectionException
     */
    public static function getKeysAndValueTypes(?string $domainObjectName = null): array
    {
        $result         = [];
        $reflectedClass = new ReflectionClass($domainObjectName ?? static::class);
        foreach ($reflectedClass->getProperties() as $property) {
            $type = $property->getType();
            if ($type !== null) {
                $result[$property->getName()] = $type->getName();
            }
        }

        return $result;
    }
}
