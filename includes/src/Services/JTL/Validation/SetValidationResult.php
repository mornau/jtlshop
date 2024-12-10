<?php

declare(strict_types=1);

namespace JTL\Services\JTL\Validation;

use function Functional\none;

/**
 * Class SetValidationResult
 * @package JTL\Services\JTL\Validation
 */
class SetValidationResult implements SetValidationResultInterface
{
    /**
     * @var ValidationResultInterface[]
     */
    protected array $fieldResults = [];

    /**
     * ObjectValidationResult constructor.
     * @param mixed[] $set
     */
    public function __construct(protected array $set)
    {
    }

    /**
     * @inheritdoc
     */
    public function setFieldResult(string $fieldName, ValidationResultInterface $valueValidationResult): void
    {
        $this->fieldResults[$fieldName] = $valueValidationResult;
    }

    /**
     * @inheritdoc
     */
    public function getFieldResult(string $fieldName): ValidationResultInterface
    {
        return $this->fieldResults[$fieldName];
    }

    /**
     * @inheritdoc
     */
    public function getSetAsArray(): ?array
    {
        return $this->isValid() ? $this->set : null;
    }

    /**
     * @inheritdoc
     */
    public function getSetAsArrayInsecure(): array
    {
        return $this->set;
    }

    /**
     * @inheritdoc
     */
    public function getSetAsObject(): ?\stdClass
    {
        return $this->isValid() ? (object)$this->set : null;
    }

    /**
     * @inheritdoc
     */
    public function getSetAsObjectInsecure(): \stdClass
    {
        return (object)$this->set;
    }

    /**
     * @inheritdoc
     */
    public function isValid(): bool
    {
        return none($this->fieldResults, static function (ValidationResultInterface $fieldResult): bool {
            return !$fieldResult->isValid();
        });
    }
}
