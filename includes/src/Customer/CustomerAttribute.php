<?php

declare(strict_types=1);

namespace JTL\Customer;

use JTL\MagicCompatibilityTrait;
use JTL\Shop;
use stdClass;

/**
 * Class CustomerAttribute
 * @package JTL\Customer
 */
class CustomerAttribute
{
    use MagicCompatibilityTrait;

    /**
     * @var int
     */
    private int $id = 0;

    /**
     * @var int
     */
    private int $customerID = 0;

    /**
     * @var int
     */
    private int $customerFieldID = 0;

    /**
     * @var string
     */
    private string $label = '';

    /**
     * @var string
     */
    private string $name = '';

    /**
     * @var string|null
     */
    private ?string $value = '';

    /**
     * @var int
     */
    private int $order = 0;

    /**
     * @var bool
     */
    private bool $editable = true;

    /**
     * @var array<string, string>
     */
    public static array $mapping = [
        'kKundenAttribut' => 'ID',
        'kKunde'          => 'CustomerID',
        'kKundenfeld'     => 'CustomerFieldID',
        'cName'           => 'Label',
        'cWawi'           => 'Name',
        'cWert'           => 'Value',
        'nSort'           => 'Order',
        'nEditierbar'     => 'Editable',
    ];

    /**
     * CustomerAttribute constructor.
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
                'SELECT tkundenattribut.kKundenAttribut, tkundenattribut.kKunde, tkundenattribut.kKundenfeld,
                       tkundenfeld.cName, tkundenfeld.cWawi, tkundenattribut.cWert, tkundenfeld.nSort,
                       IF(COALESCE(tkundenattribut.cWert, \'\') = \'\', 1, tkundenfeld.nEditierbar) nEditierbar
                    FROM tkundenattribut
                    INNER JOIN tkundenfeld ON tkundenfeld.kKundenfeld = tkundenattribut.kKundenfeld
                    WHERE tkundenattribut.kKundenAttribut = :id',
                ['id' => $id]
            )
        );

        return $instance;
    }

    /**
     * @return self
     */
    public function save(): self
    {
        $record        = $this->getRecord();
        $record->cName = $record->cWawi;
        unset(
            $record->cWawi,
            $record->nSort,
            $record->nEditierbar
        );

        if ($record->kKundenAttribut === 0) {
            unset($record->kKundenAttribut);
        }
        $res = Shop::Container()->getDB()->upsert(
            'tkundenattribut',
            $record,
            ['kKundenAttribut', 'kKunde', 'kKundenfeld']
        );

        if ($res > 0) {
            $this->setID($res);
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
     * @param int|string|null $id
     */
    public function setID(int|string|null $id): void
    {
        $this->id = (int)($id ?? 0);
    }

    /**
     * @return int
     */
    public function getCustomerID(): int
    {
        return $this->customerID;
    }

    /**
     * @param int|string|null $customerID
     */
    public function setCustomerID(int|string|null $customerID): void
    {
        $this->customerID = (int)($customerID ?? 0);
    }

    /**
     * @return int
     */
    public function getCustomerFieldID(): int
    {
        return $this->customerFieldID;
    }

    /**
     * @param int|string $customerFieldID
     */
    public function setCustomerFieldID(int|string $customerFieldID): void
    {
        $this->customerFieldID = (int)$customerFieldID;
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
    public function getValue(): string
    {
        return $this->value ?? '';
    }

    /**
     * @param string|null $value
     */
    public function setValue(?string $value): void
    {
        $this->value = $value;
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
     * @param bool|int|string $editable
     */
    public function setEditable(bool|int|string $editable): void
    {
        $this->editable = (bool)$editable;
    }

    /**
     * @param stdClass|stdClass[]|null $record
     * @return CustomerAttribute
     */
    public function setRecord($record): self
    {
        if (!\is_object($record) && !\is_array($record)) {
            $this->setID(0);
            $this->setCustomerFieldID(0);
            $this->setCustomerID(0);
            $this->setLabel('');
            $this->setName('');
            $this->setValue('');
            $this->setOrder(0);
            $this->setEditable(true);

            return $this;
        }
        foreach ($record as $item => $value) {
            if (($mapped = self::getMapping($item)) !== null) {
                $method = 'set' . $mapped;

                $this->$method($value);
            }
        }

        return $this;
    }

    /**
     * @return stdClass
     */
    public function getRecord(): stdClass
    {
        $result = new stdClass();

        foreach (self::$mapping as $item => $mapped) {
            $method        = 'get' . $mapped;
            $result->$item = $this->$method();
        }

        return $result;
    }
}
