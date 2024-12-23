<?php

declare(strict_types=1);

namespace JTL\Customer;

use JTL\Helpers\Text;
use JTL\MagicCompatibilityTrait;
use JTL\Shop;

/**
 * Class CustomerField
 * @package JTL\Customer
 */
class CustomerField
{
    use MagicCompatibilityTrait;

    public const TYPE_TEXT   = 'text';
    public const TYPE_NUMBER = 'zahl';
    public const TYPE_SELECT = 'auswahl';
    public const TYPE_DATE   = 'datum';

    public const VALIDATE_OK           = 0;
    public const VALIDATE_EMPTY        = 1;
    public const VALIDATE_WRONG_FORMAT = 2;
    public const VALIDATE_WRONG_DATE   = 3;
    public const VALIDATE_NO_NUMBER    = 4;

    /**
     * @var int
     */
    private int $id = 0;

    /**
     * @var int
     */
    private int $langID = 0;

    /**
     * @var string
     */
    private string $label = '';

    /**
     * @var string
     */
    private string $name = '';

    /**
     * @var string
     */
    private string $type = self::TYPE_TEXT;

    /**
     * @var int
     */
    private int $order = 0;

    /**
     * @var bool
     */
    private bool $required = false;

    /**
     * @var bool
     */
    private bool $editable = true;

    /**
     * @var array<int, string>
     */
    private array $values = [];

    /**
     * @var array<string, string>
     */
    public static array $mapping = [
        'kKundenfeld'         => 'ID',
        'kSprache'            => 'LangID',
        'cName'               => 'Label',
        'cWawi'               => 'Name',
        'cTyp'                => 'Type',
        'nSort'               => 'Order',
        'nPflicht'            => 'Required',
        'nEditierbar'         => 'Editable',
        'oKundenfeldWert_arr' => 'Values',
    ];

    /**
     * CustomerFields constructor.
     *
     * @param object|null $record
     */
    public function __construct(?object $record = null)
    {
        $this->setRecord($record);
    }

    /**
     * @param int $id
     * @return self
     */
    public static function load(int $id): self
    {
        $instance = new self();
        $instance->setRecord(
            Shop::Container()->getDB()->getSingleObject(
                'SELECT tkundenfeld.kKundenfeld, tkundenfeld.kSprache, tkundenfeld.cName,
                       tkundenfeld.cWawi, tkundenfeld.cTyp, tkundenfeld.nSort,
                       tkundenfeld.nPflicht, tkundenfeld.nEditierbar
                    FROM tkundenfeld
                    WHERE tkundenfeld.kKundenfeld = :id',
                ['id' => $id]
            )
        )->loadValues();

        return $instance;
    }

    /**
     * @param string $name
     * @param int    $langID
     * @return self
     */
    public static function loadByName(string $name, int $langID): self
    {
        $instance = new self();
        $instance->setRecord(
            Shop::Container()->getDB()->getSingleObject(
                'SELECT tkundenfeld.kKundenfeld, tkundenfeld.kSprache, tkundenfeld.cName,
                       tkundenfeld.cWawi, tkundenfeld.cTyp, tkundenfeld.nSort,
                       tkundenfeld.nPflicht, tkundenfeld.nEditierbar
                    FROM tkundenfeld
                    WHERE tkundenfeld.cWawi = :name
                        AND tkundenfeld.kSprache = :langID',
                [
                    'name'   => $name,
                    'langID' => $langID,
                ]
            )
        )->loadValues();

        return $instance;
    }

    /**
     * @return self
     */
    protected function loadValues(): self
    {
        if ($this->getType() === self::TYPE_SELECT) {
            foreach (
                Shop::Container()->getDB()->getObjects(
                    'SELECT kKundenfeldWert, cWert
                    FROM tkundenfeldwert
                    WHERE kKundenfeld = :customerFieldID
                    ORDER BY nSort',
                    ['customerFieldID' => $this->getID()]
                ) as $customFieldValue
            ) {
                $this->values[$customFieldValue->kKundenfeldWert] = $customFieldValue->cWert;
            }
        } else {
            $this->values = [];
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getID(): int
    {
        return $this->id;
    }

    /**
     * @param int|string $id
     */
    public function setID(int|string $id): void
    {
        $this->id = (int)$id;
    }

    /**
     * @return int
     */
    public function getLangID(): int
    {
        return $this->langID;
    }

    /**
     * @param int|string $langID
     */
    public function setLangID(int|string $langID): void
    {
        $this->langID = (int)$langID;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int|string $order
     */
    public function setOrder(int|string $order): void
    {
        $this->order = (int)$order;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @return int
     */
    public function getRequired(): int
    {
        return $this->required ? 1 : 0;
    }

    /**
     * @param int|string|bool $required
     */
    public function setRequired(int|string|bool $required): void
    {
        $this->required = (bool)$required;
    }

    /**
     * @return bool
     */
    public function isEditable(): bool
    {
        return $this->editable;
    }

    /**
     * @return int
     */
    public function getEditable(): int
    {
        return $this->editable ? 1 : 0;
    }

    /**
     * @param int|string|bool $editable
     */
    public function setEditable(int|string|bool $editable): void
    {
        $this->editable = (bool)$editable;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param \stdClass|\stdClass[]|null $record
     * @return CustomerField
     */
    public function setRecord($record): self
    {
        if (!\is_object($record) && !\is_array($record)) {
            $this->setID(0);
            $this->setLangID(0);
            $this->setLabel('');
            $this->setName('');
            $this->setType(self::TYPE_TEXT);
            $this->setOrder(0);
            $this->setRequired(0);
            $this->setEditable(0);
            $this->values = [];

            return $this;
        }

        foreach ($record as $item => $value) {
            if (($mapped = self::getMapping($item)) !== null) {
                $method = 'set' . $mapped;

                $this->$method($value);
            }
        }

        if ($this->getType() === self::TYPE_SELECT) {
            $this->loadValues();
        }

        return $this;
    }

    /**
     * @param mixed $data
     * @return int
     */
    public function validate($data): int
    {
        if (($data === null || $data === '') && $this->isRequired()) {
            return self::VALIDATE_EMPTY;
        }
        if (!empty($data) && $this->getType() === self::TYPE_DATE) {
            // check for english date format
            $enDate = \DateTime::createFromFormat('Y-m-d', $data);

            return Text::checkDate($enDate === false ? $data : $enDate->format('d.m.Y'));
        }
        if (!empty($data) && !\is_numeric($data) && $this->getType() === self::TYPE_NUMBER) {
            return self::VALIDATE_NO_NUMBER;
        }
        if (!empty($data) && $this->getType() === self::TYPE_SELECT && !\in_array($data, $this->getValues(), true)) {
            return self::VALIDATE_EMPTY;
        }

        return self::VALIDATE_OK;
    }
}
