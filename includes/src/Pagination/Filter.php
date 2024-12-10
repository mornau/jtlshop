<?php

declare(strict_types=1);

namespace JTL\Pagination;

/**
 * Class Filter
 * @package JTL\Pagination
 */
class Filter
{
    /**
     * @var string
     */
    protected string $id = 'Filter';

    /**
     * @var FilterField[]
     */
    protected array $fields = [];

    /**
     * @var string
     */
    protected string $whereSQL = '';

    /**
     * @var string
     */
    protected mixed $action = '';

    /**
     * @var array<string, mixed>
     */
    protected array $sessionData = [];

    /**
     * Filter constructor.
     * Create a new empty filter object
     * @param string|null $id
     */
    public function __construct(?string $id = null)
    {
        if (\is_string($id)) {
            $this->id = $id;
        }

        $this->action = $_GET['action'] ?? '';
        $this->loadSessionStore();
    }

    /**
     * Add a text field to a filter object
     *
     * @param string|string[] $title - either title-string for this field or a pair of short title and long title
     * @param string|string[] $column - the column name to be compared
     * @param int             $testOp
     * @param int             $dataType
     * @param string|null     $id
     * @return FilterTextField
     */
    public function addTextfield(
        $title,
        $column,
        int $testOp = Operation::CUSTOM,
        int $dataType = DataType::TEXT,
        ?string $id = null
    ): FilterTextField {
        $field                                      = new FilterTextField(
            $this,
            $title,
            $column,
            $testOp,
            $dataType,
            $id
        );
        $this->fields[]                             = $field;
        $this->sessionData[$field->getID()]         = $field->getValue();
        $this->sessionData[$field->getID() . '_op'] = $field->getTestOp();

        return $field;
    }

    /**
     * Add a select field to a filter object. Options can be added with FilterSelectField->addSelectOption() to this
     * select field
     *
     * @param string[]|string $title - either title-string for this field or a pair of short title and long title
     * @param string          $column - the column name to be compared
     * @param string|int      $defaultOption
     * @param string|null     $id
     * @return FilterSelectField
     */
    public function addSelectfield(
        array|string $title,
        string $column,
        $defaultOption = 0,
        ?string $id = null
    ): FilterSelectField {
        $field                              = new FilterSelectField($this, $title, $column, $defaultOption, $id);
        $this->fields[]                     = $field;
        $this->sessionData[$field->getID()] = $field->getValue();

        return $field;
    }

    /**
     * Add a DateRange field to the filter object.
     *
     * @param string[]|string $title
     * @param string          $column
     * @param string          $defaultValue
     * @param string|null     $id
     * @return FilterDateRangeField
     */
    public function addDaterangefield(
        array|string $title,
        string $column,
        string $defaultValue = '',
        ?string $id = null
    ): FilterDateRangeField {
        $field                              = new FilterDateRangeField($this, $title, $column, $defaultValue, $id);
        $this->fields[]                     = $field;
        $this->sessionData[$field->getID()] = $field->getValue();

        return $field;
    }

    /**
     * Assemble filter object to be ready for use. Build WHERE clause.
     */
    public function assemble(): void
    {
        $this->whereSQL = \implode(
            ' AND ',
            \array_filter(
                \array_map(static function (FilterField $field) {
                    return $field->getWhereClause();
                }, $this->fields)
            )
        );
        $this->saveSessionStore();
    }

    /**
     * @return FilterField[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param int $i
     * @return FilterField|null
     */
    public function getField(int $i): ?FilterField
    {
        return $this->fields[$i];
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getWhereSQL(): string
    {
        return $this->whereSQL;
    }

    /**
     *
     */
    public function loadSessionStore(): void
    {
        $this->sessionData = $_SESSION['filter_' . $this->id] ?? [];
    }

    /**
     *
     */
    public function saveSessionStore(): void
    {
        $_SESSION['filter_' . $this->id] = $this->sessionData;
    }

    /**
     * @param string $field
     * @return bool
     */
    public function hasSessionField(string $field): bool
    {
        return isset($this->sessionData[$field]);
    }

    /**
     * @param string $field
     * @return mixed
     */
    public function getSessionField(string $field)
    {
        return $this->sessionData[$field];
    }

    /**
     * @return string
     */
    public function getID(): string
    {
        return $this->id;
    }
}
