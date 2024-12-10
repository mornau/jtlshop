<?php

declare(strict_types=1);

namespace JTL\Model;

use JTL\Plugin\Admin\InputType;

/**
 * Class InputConfig
 * @package JTL\Model
 */
class InputConfig
{
    /**
     * @var array<int, string>
     */
    public array $allowedValues = [];

    /**
     * @var string
     */
    public string $inputType = InputType::TEXT;

    /**
     * @var bool
     */
    public bool $modifyable = true;

    /**
     * @var bool
     */
    public bool $hidden = false;

    /**
     * @var bool
     */
    public bool $multiselect = false;

    /**
     * @return array<int, string>
     */
    public function getAllowedValues(): array
    {
        return $this->allowedValues;
    }

    /**
     * @param array<int, string> $allowedValues
     */
    public function setAllowedValues(array $allowedValues): void
    {
        $this->allowedValues = $allowedValues;
    }

    /**
     * @return string
     */
    public function getInputType(): string
    {
        return $this->inputType;
    }

    /**
     * @param string $inputType
     */
    public function setInputType(string $inputType): void
    {
        $this->inputType = $inputType;
    }

    /**
     * @return bool
     */
    public function isModifyable(): bool
    {
        return $this->modifyable;
    }

    /**
     * @param bool $modifyable
     */
    public function setModifyable(bool $modifyable): void
    {
        $this->modifyable = $modifyable;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     */
    public function setHidden(bool $hidden): void
    {
        $this->hidden = $hidden;
    }

    /**
     * @return bool
     */
    public function isMultiselect(): bool
    {
        return $this->multiselect;
    }

    /**
     * @param bool $multiselect
     */
    public function setMultiselect(bool $multiselect): void
    {
        $this->multiselect = $multiselect;
    }
}
