<?php

declare(strict_types=1);

namespace JTL\Pagination;

use JTL\Shop;

/**
 * Class FilterTextField
 * @package JTL\Pagination
 */
class FilterTextField extends FilterField
{
    /**
     * @var int{Operation::*}
     */
    protected int $testOp = Operation::CUSTOM;

    /**
     * @var int{DataType::*}
     */
    protected int $dataType = DataType::TEXT;

    /**
     * @var bool
     */
    protected bool $customTestOp = true;

    /**
     * FilterTextField constructor.
     *
     * @param Filter          $filter
     * @param string|string[] $title - either title-string for this field or a pair of short title and long title
     * @param string|string[] $column - column/field or array of them to be searched disjunctively (OR)
     * @param int             $testOp
     * @param int             $dataType
     * @param string|null     $id
     */
    public function __construct(
        Filter $filter,
        $title,
        $column,
        int $testOp = Operation::CUSTOM,
        int $dataType = DataType::TEXT,
        ?string $id = null
    ) {
        parent::__construct($filter, 'text', $title, $column, '', $id);

        $this->testOp       = $testOp;
        $this->dataType     = $dataType;
        $this->customTestOp = $this->testOp === Operation::CUSTOM;

        if ($this->customTestOp) {
            $this->setCustomTestOp($filter);
        }
    }

    private function setCustomTestOp(Filter $filter): void
    {
        if ($filter->getAction() === $filter->getID() . '_filter') {
            $this->testOp = (int)$_GET[$filter->getID() . '_' . $this->id . '_op'];
        } elseif ($filter->getAction() === $filter->getID() . '_resetfilter') {
            $this->testOp = 1;
        } elseif ($filter->hasSessionField($this->id . '_op')) {
            $this->testOp = (int)$filter->getSessionField($this->id . '_op');
        } else {
            $this->testOp = 1;
        }
    }

    /**
     * @return string|null
     */
    public function getWhereClause(): ?string
    {
        if (
            $this->value !== ''
            || ($this->dataType === DataType::TEXT
                && ($this->testOp === Operation::EQUALS || $this->testOp === Operation::NOT_EQUAL))
        ) {
            $value   = Shop::Container()->getDB()->escape($this->value);
            $columns = \is_array($this->column)
                ? $this->column
                : [$this->column];
            $or      = [];
            foreach ($columns as $column) {
                $cond = match ($this->testOp) {
                    Operation::CONTAINS           => $column . " LIKE '%" . $value . "%'",
                    Operation::BEGINS_WITH        => $column . " LIKE '" . $value . "%'",
                    Operation::ENDS_WITH          => $column . " LIKE '%" . $value . "'",
                    Operation::EQUALS             => $column . " = '" . $value . "'",
                    Operation::LOWER_THAN         => $column . " < '" . $value . "'",
                    Operation::GREATER_THAN       => $column . " > '" . $value . "'",
                    Operation::LOWER_THAN_EQUAL   => $column . " <= '" . $value . "'",
                    Operation::GREATER_THAN_EQUAL => $column . " >= '" . $value . "'",
                    Operation::NOT_EQUAL          => $column . " != '" . $value . "'",
                    default                       => null,
                };
                if ($cond !== null) {
                    $or[] = $cond;
                }
            }

            return '(' . \implode(' OR ', $or) . ')';
        }

        return null;
    }

    /**
     * @return int
     */
    public function getTestOp(): int
    {
        return $this->testOp;
    }

    /**
     * @return int
     */
    public function getDataType(): int
    {
        return $this->dataType;
    }

    /**
     * @return bool
     */
    public function isCustomTestOp(): bool
    {
        return $this->customTestOp;
    }
}
