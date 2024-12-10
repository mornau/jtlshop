<?php

declare(strict_types=1);

namespace JTL\Services\JTL\Validation;

/**
 * Interface ObjectValidationResultInterface
 * @package JTL\Services\JTL\Validation
 */
interface SetValidationResultInterface
{
    /**
     * @param string                    $fieldName
     * @param ValidationResultInterface $valueValidationResult
     */
    public function setFieldResult(string $fieldName, ValidationResultInterface $valueValidationResult): void;

    /**
     * @param string $fieldName
     * @return ValidationResultInterface
     */
    public function getFieldResult(string $fieldName): ValidationResultInterface;

    /**
     * @return mixed[]|null
     */
    public function getSetAsArray(): ?array;

    /**
     * @return mixed[]
     */
    public function getSetAsArrayInsecure(): array;

    /**
     * @return \stdClass|null
     */
    public function getSetAsObject(): ?\stdClass;

    /**
     * @return \stdClass
     */
    public function getSetAsObjectInsecure(): \stdClass;

    /**
     * @return bool
     */
    public function isValid(): bool;
}
