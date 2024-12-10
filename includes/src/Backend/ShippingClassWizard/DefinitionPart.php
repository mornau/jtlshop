<?php

declare(strict_types=1);

namespace JTL\Backend\ShippingClassWizard;

use InvalidArgumentException;
use JsonException;
use JsonSerializable;

/**
 * Class DefinitionPart
 * @package JTL\Backend\ShippingClassWizard
 */
final class DefinitionPart implements JsonSerializable
{
    /** @var int[] */
    private array $shippingClasses = [];

    /** @var string */
    private string $logic;

    /**
     * DefinitionPart constructor
     * @param string $logic
     */
    private function __construct(string $logic = CombineTypes::LOGIC_AND)
    {
        $this->setLogic($logic);
    }

    /**
     * @param string $jsonStr
     * @return self
     * @throws JsonException
     */
    public static function jsonDecode(string $jsonStr): self
    {
        $data = \json_decode($jsonStr, false, 64, \JSON_THROW_ON_ERROR);

        return (new self($data->logic ?? CombineTypes::LOGIC_AND))->setShippingClasses($data->shippingClasses ?? []);
    }

    /**
     * @param array $data
     * @return self
     */
    public static function createFromForm(array $data): self
    {
        return (new self($data['logic'] ?? CombineTypes::LOGIC_AND))->setShippingClasses($data['class'] ?? []);
    }

    /**
     * @param string $classIds
     * @return self
     */
    public static function createFromClassIds(string $classIds): self
    {
        return (new self(CombineTypes::LOGIC_AND))->setShippingClasses(\explode('-', $classIds));
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): object
    {
        return (object)[
            'logic'           => $this->getLogic(),
            'shippingClasses' => $this->getShippingClasses(),
        ];
    }

    /**
     * @return int[]
     */
    public function getShippingClasses(): array
    {
        return $this->shippingClasses;
    }

    /**
     * @param int[]|string[] $shippingClasses
     * @return self
     */
    public function setShippingClasses(array $shippingClasses): self
    {
        $this->shippingClasses = [];
        foreach ($shippingClasses as $shippingClass) {
            $this->addShippingClass((int)$shippingClass);
        }

        return $this;
    }

    /**
     * @param int $shippingClass
     * @return self
     */
    public function addShippingClass(int $shippingClass): self
    {
        if (!\in_array($shippingClass, $this->shippingClasses, true)) {
            $this->shippingClasses[] = $shippingClass;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getLogic(): string
    {
        return $this->logic;
    }

    /**
     * @param string $logic
     * @return self
     */
    public function setLogic(string $logic): self
    {
        if (!\in_array($logic, [CombineTypes::LOGIC_OR, CombineTypes::LOGIC_AND], true)) {
            throw new InvalidArgumentException(
                'logic must be "' . CombineTypes::LOGIC_OR . '" or "' . CombineTypes::LOGIC_AND . '"'
            );
        }
        $this->logic = $logic;

        return $this;
    }

    /**
     * @return bool
     */
    public function isLogicOr(): bool
    {
        return $this->getLogic() === CombineTypes::LOGIC_OR;
    }

    /**
     * @return bool
     */
    public function isLogicAnd(): bool
    {
        return $this->getLogic() === CombineTypes::LOGIC_AND;
    }
}