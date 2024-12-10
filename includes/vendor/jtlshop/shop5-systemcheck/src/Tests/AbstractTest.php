<?php

declare(strict_types=1);

namespace Systemcheck\Tests;

use JsonSerializable;

/**
 * Class AbstractTest
 * @package Systemcheck\Tests
 */
abstract class AbstractTest implements JsonSerializable
{
    public const RESULT_OK = 0;

    public const RESULT_FAILED = 1;

    public const RESULT_UNKNOWN = 2;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string
     */
    protected string $currentState = '';

    /**
     * @var int
     */
    protected int $result = self::RESULT_FAILED;

    /**
     * @var string
     */
    protected string $requiredState;

    /**
     * @var string
     */
    protected string $description = '';

    /**
     * @var bool
     */
    protected bool $isRecommended = false;

    /**
     * @var bool
     */
    protected bool $isOptional = false;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRequiredState(): string
    {
        return $this->requiredState;
    }

    /**
     * @return string
     */
    public function getCurrentState(): string
    {
        return $this->currentState;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return bool
     */
    public function getIsOptional(): bool
    {
        return $this->isOptional;
    }

    /**
     * getIsRecommended
     * @return bool
     */
    public function getIsRecommended(): bool
    {
        return $this->isRecommended;
    }

    /**
     * @return string|false
     */
    public function getIsReplaceableBy(): bool|string
    {
        return \property_exists($this, 'isReplaceableBy')
            ? $this->isReplaceableBy
            : false;
    }

    /**
     * @return bool|null
     */
    public function getRunStandAlone(): ?bool
    {
        return \property_exists($this, 'runStandAlone')
            ? $this->runStandAlone
            : null; // do not change to 'false'! we need three states here!
    }

    /**
     * @return self::RESULT_FAILED|self::RESULT_OK|self::RESULT_UNKNOWN
     */
    public function getResult(): int
    {
        return $this->result;
    }

    /**
     * @param bool $result
     */
    public function setResult(bool $result): void
    {
        $this->result = $result === true ? self::RESULT_OK : self::RESULT_FAILED;
    }

    /**
     * @return bool
     */
    abstract public function execute(): bool;

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return \get_object_vars($this);
    }
}
