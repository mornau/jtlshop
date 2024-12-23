<?php

declare(strict_types=1);

namespace JTL\Plugin\Data;

use JTL\License\Struct\ExsLicense;

/**
 * Class License
 * @package JTL\Plugin\Data
 */
class License
{
    /**
     * @var string|null
     */
    private ?string $key = null;

    /**
     * @var string|null
     */
    private ?string $className = null;

    /**
     * @var string|null
     */
    private ?string $class = null;

    /**
     * @var ExsLicense|null
     */
    private ?ExsLicense $exsLicense = null;

    /**
     * @return bool
     */
    public function hasLicenseCheck(): bool
    {
        return !empty($this->class) && !empty($this->className);
    }

    /**
     * @return bool
     */
    public function hasLicense(): bool
    {
        return $this->hasLicenseCheck() && !empty($this->key);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param string $className
     */
    public function setClassName(string $className): void
    {
        $this->className = $className;
    }

    /**
     * @return class-string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass(string $class): void
    {
        $this->class = $class;
    }

    /**
     * @return ExsLicense|null
     */
    public function getExsLicense(): ?ExsLicense
    {
        return $this->exsLicense;
    }

    /**
     * @param ExsLicense|null $exsLicense
     */
    public function setExsLicense(?ExsLicense $exsLicense): void
    {
        $this->exsLicense = $exsLicense;
    }
}
