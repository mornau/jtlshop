<?php

declare(strict_types=1);

namespace JTL\Services\JTL\Validation\Rules;

use JTL\Services\JTL\Validation\RuleInterface;
use JTL\Services\JTL\Validation\RuleResult;

/**
 * Class Type
 * @package JTL\Services\JTL\Validation\Rules
 *
 * Validates that the value is of the specified type
 *
 * No transform
 */
class Type implements RuleInterface
{
    /**
     * Type constructor.
     * @param string $expected
     */
    public function __construct(protected string $expected)
    {
    }

    /**
     * @inheritdoc
     */
    public function validate($value): RuleResult
    {
        return $this->expected === \gettype($value)
            ? new RuleResult(true, '', $value)
            : new RuleResult(false, 'invalid type', $value);
    }
}
