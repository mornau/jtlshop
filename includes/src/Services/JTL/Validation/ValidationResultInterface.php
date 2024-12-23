<?php

declare(strict_types=1);

namespace JTL\Services\JTL\Validation;

/**
 * Interface ValidationResultInterface
 * @package JTL\Services\JTL\Validation
 */
interface ValidationResultInterface extends ValueCarrierInterface
{
    /**
     * @param RuleResultInterface $ruleResult
     * @return void
     */
    public function addRuleResult(RuleResultInterface $ruleResult): void;

    /**
     * @return RuleResultInterface[]
     */
    public function getRuleResults(): array;

    /**
     * @return bool
     */
    public function isValid(): bool;
}
