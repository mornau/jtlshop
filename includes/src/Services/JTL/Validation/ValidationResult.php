<?php

declare(strict_types=1);

namespace JTL\Services\JTL\Validation;

use function Functional\none;

/**
 * Class ValidationResult
 * @package JTL\Services\JTL\Validation
 */
class ValidationResult implements ValidationResultInterface
{
    /**
     * @var RuleResultInterface[]
     */
    protected array $ruleResults = [];

    protected mixed $value;

    /**
     * ValidationResult constructor.
     * @param mixed $unfilteredValue
     */
    public function __construct(protected mixed $unfilteredValue)
    {
    }

    /**
     * @inheritdoc
     */
    public function addRuleResult(RuleResultInterface $ruleResult): void
    {
        $this->ruleResults[] = $ruleResult;
    }

    /**
     * @inheritdoc
     */
    public function getRuleResults(): array
    {
        return $this->ruleResults;
    }

    /**
     * @inheritdoc
     */
    public function isValid(): bool
    {
        return none($this->ruleResults, static function (RuleResultInterface $item): bool {
            return !$item->isValid();
        });
    }

    /**
     * @inheritdoc
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function getValue($default = null)
    {
        return $this->isValid() ? $this->value : $default;
    }

    /**
     * @inheritdoc
     */
    public function getValueInsecure()
    {
        return $this->unfilteredValue;
    }
}
