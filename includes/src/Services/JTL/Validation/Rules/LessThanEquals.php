<?php

declare(strict_types=1);

namespace JTL\Services\JTL\Validation\Rules;

use JTL\Services\JTL\Validation\RuleInterface;
use JTL\Services\JTL\Validation\RuleResult;

/**
 * Class LessThanEquals
 * @package JTL\Services\JTL\Validation\Rules
 */
class LessThanEquals implements RuleInterface
{
    /**
     * LessThan constructor.
     * @param mixed $value
     */
    public function __construct(protected $value)
    {
    }

    /**
     * @inheritdoc
     */
    public function validate($value): RuleResult
    {
        return $value <= $this->value
            ? new RuleResult(true, '', $value)
            : new RuleResult(false, 'value too high', $value);
    }
}
