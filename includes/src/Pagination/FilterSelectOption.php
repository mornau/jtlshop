<?php

declare(strict_types=1);

namespace JTL\Pagination;

/**
 * Class FilterSelectOption
 * @package JTL\Pagination
 */
class FilterSelectOption
{
    /**
     * @var string
     */
    protected string $title = '';

    /**
     * @var string|int
     */
    protected string|int $value = '';

    /**
     * @var int
     */
    protected int $testOp = Operation::CUSTOM;

    /**
     * FilterSelectOption constructor.
     *
     * @param string     $title
     * @param int|string $value
     * @param int        $testOp
     */
    public function __construct(string $title, int|string $value, int $testOp)
    {
        $this->title  = $title;
        $this->value  = $value;
        $this->testOp = $testOp;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string|int
     */
    public function getValue(): int|string
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getTestOp(): int
    {
        return $this->testOp;
    }
}
