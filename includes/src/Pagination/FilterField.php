<?php

declare(strict_types=1);

namespace JTL\Pagination;

use JTL\Helpers\Text;

/**
 * Class FilterField
 * @package JTL\Pagination
 */
abstract class FilterField
{
    /**
     * @var string
     */
    protected string $title = '';

    /**
     * @var string
     */
    protected string $titleLong = '';

    /**
     * @var string|string[]
     */
    protected string|array $column = '';

    /**
     * @var mixed|string
     */
    protected $value = '';

    /**
     * @var string
     */
    protected string $id = '';

    /**
     * FilterField constructor.
     *
     * @param Filter          $filter
     * @param string          $type
     * @param string[]|string $title - either title-string for this field or a pair of short title and long title
     * @param string|string[] $column
     * @param string          $defaultValue
     * @param string|null     $id
     */
    public function __construct(
        protected Filter $filter,
        protected string $type,
        array|string $title,
        $column,
        $defaultValue = '',
        ?string $id = null
    ) {
        $this->title     = \is_array($title) ? $title[0] : $title;
        $this->titleLong = \is_array($title) ? $title[1] : '';
        $this->column    = $column;
        $this->id        = $id ?? \preg_replace('/\W+/', '', $this->title);
        $this->value     = $this->initValue($filter, $defaultValue);
    }

    private function initValue(Filter $filter, int|string $defaultValue): string
    {
        if ($filter->getAction() === $filter->getID() . '_filter') {
            $value = $_GET[$filter->getID() . '_' . $this->id];
        } elseif ($filter->getAction() === $filter->getID() . '_resetfilter') {
            $value = $defaultValue;
        } elseif ($filter->hasSessionField($this->id)) {
            $value = $filter->getSessionField($this->id);
        } else {
            $value = $defaultValue;
        }

        return Text::filterXSS($value);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getTitleLong(): string
    {
        return $this->titleLong;
    }

    /**
     * @return string
     */
    public function getID(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return void
     */
    public function setID(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    abstract public function getWhereClause(): ?string;
}
