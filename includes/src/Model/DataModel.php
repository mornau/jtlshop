<?php

declare(strict_types=1);

namespace JTL\Model;

use Exception;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Iterator;
use JTL\DB\DbInterface;
use JTL\Shop;
use stdClass;

use function Functional\select;

/**
 * Class DataModel
 * @package JTL\Model
 */
abstract class DataModel implements DataModelInterface, Iterator
{
    use IteratorTrait;

    /**
     * @var array<string, string|int>
     * Stores the property values
     */
    protected array $members = [];

    /**
     * @var callable[]
     * List of setting handlers
     */
    protected array $setters = [];

    /**
     * @var callable[]
     * List of getting handlers
     */
    protected array $getters = [];

    /**
     * @var array<string, array<string, string>>
     */
    protected static array $nameMapping = [];

    /**
     * true when loaded from database
     * @var bool
     */
    protected bool $loaded = false;

    /**
     * @var DbInterface|null
     */
    private ?DbInterface $db = null;

    /**
     * @inheritdoc
     */
    public function getDB(): DbInterface
    {
        if ($this->db === null) {
            $this->db = Shop::Container()->getDB();
        }

        return $this->db;
    }

    /**
     * @inheritdoc
     */
    public function setDB(DbInterface $db): void
    {
        $this->db = $db;
    }

    /**
     * @inheritdoc
     */
    public function wasLoaded(): void
    {
        $this->loaded = true;
    }

    /**
     * @inheritdoc
     */
    public function getWasLoaded(): bool
    {
        return $this->loaded;
    }

    /**
     * @inheritdoc
     */
    public function setWasLoaded(bool $loaded): void
    {
        $this->loaded = $loaded;
    }

    /**
     * DataModel constructor.
     * @param DbInterface|null $db
     */
    public function __construct(DbInterface $db = null)
    {
        $this->prepare($db);
        $this->fabricate();
    }

    /**
     * @return string[]
     */
    public function __sleep(): array
    {
        return select(\array_keys(\get_object_vars($this)), static function (string $e): bool {
            return $e !== 'getters' && $e !== 'db' && $e !== 'setters';
        });
    }

    public function __wakeup(): void
    {
        $this->onRegisterHandlers();
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        $attribute = \lcfirst(\substr($name, 3));
        if (\array_key_exists($attribute, $this->members)) {
            if (\str_starts_with($name, 'get')) {
                return $this->$attribute;
            }
            if (\str_starts_with($name, 'set')) {
                $this->$attribute = $arguments[0];

                return null;
            }
        }
        throw new InvalidArgumentException('Call to undefined method ' . $name);
    }

    /**
     * @param string $name - name of the property
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->getAttribValue($name);
    }

    /**
     * @param string $name - name of the property
     * @param mixed  $value - value of the poperty
     * @return void
     */
    public function __set(string $name, mixed $value): void
    {
        $this->setAttribValue($name, $value);
    }

    /**
     * @param string $name - name of the property
     * @return bool
     */
    public function __isset(string $name): bool
    {
        $attributes = $this->getAttributes();

        return (\array_key_exists($name, $attributes) && $this->getAttribValue($name) !== null)
            || ($this->hasMapping($name) && $this->getAttribValue($this->getMapping($name)) !== null);
    }

    /**
     * check if an attribute exists - mapped or unmapped
     *
     * @param object $attributes
     * @param string $attribName
     * @return string|null
     */
    private function checkAttribute(object $attributes, $attribName): ?string
    {
        if (\property_exists($attributes, $attribName)) {
            return $attribName;
        }
        $attribName = $this->getMapping($attribName);
        if (\property_exists($attributes, $attribName)) {
            return $attribName;
        }

        return null;
    }

    /**
     * @param DbInterface|null $db
     */
    public function prepare(DbInterface $db = null): void
    {
        $this->db           = $db;
        $this->iteratorKeys = \array_keys($this->getAttributes());
        $this->onRegisterHandlers();
    }

    /**
     * @param DbInterface|null $db
     * @return static
     */
    public static function newInstance(DbInterface $db = null): self
    {
        return new static($db);
    }

    /**
     * @param int $option
     * @return $this
     * @throws Exception
     */
    protected function createNew(int $option = self::NONE): self
    {
        $pkValue = $this->getDB()->insert($this->getTableName(), $this->getSqlObject(true));

        if (!empty($pkValue)) {
            if (!empty($this->getKeyName())) {
                $key = $this->getKey();
                if (empty($key)) {
                    $this->setKey($pkValue);
                }
            }
        } elseif ($option === self::ON_EXISTS_UPDATE) {
            $this->save();
        } elseif ($option !== self::ON_INSERT_IGNORE) {
            throw new Exception(__METHOD__ . ': SQL error', self::ERR_DATABASE);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public static function create($attributes, DbInterface $db, $option = self::NONE)
    {
        $instance = static::newInstance($db);
        $instance->fill($attributes);
        $instance->createNew($option);
        $instance->updateChildModels();

        return $instance;
    }

    /**
     * @inheritdoc
     */
    public function init(array $attributes, $option = self::NONE): DataModelInterface
    {
        try {
            $this->fill($attributes);
            $record = $this->getDB()->select($this->getTableName(), $this->getKeyName(true), $this->getKey());
        } catch (Exception $e) {
            if ($e->getCode() === self::ERR_NO_PRIMARY_KEY) {
                $attribs = $this->getAttributes();
                $conds   = [];
                foreach ($attributes as $key => $value) {
                    $conds[$attribs[$this->getMapping($key)]->name] = $value;
                }
                $record = $this->getDB()->select($this->getTableName(), \array_keys($conds), \array_values($conds));
            } else {
                throw $e;
            }
        }

        if ($record === null) {
            if ($option === self::ON_NOTEXISTS_FAIL) {
                throw new Exception(__METHOD__ . ': No Data Found', self::ERR_NOT_FOUND);
            }
            return $option === self::ON_NOTEXISTS_NEW ? $this->createNew($option) : $this;
        }
        $this->loaded = true;

        return $this->fill($record);
    }

    /**
     * @inheritdoc
     */
    public static function load($attributes, DbInterface $db, $option = self::ON_NOTEXISTS_NEW)
    {
        return static::newInstance($db)->init((array)$attributes, $option);
    }

    /**
     * @inheritdoc
     */
    public static function loadByAttributes($attributes, DbInterface $db, $option = self::ON_NOTEXISTS_FAIL)
    {
        $instance = static::newInstance($db);
        $attribs  = $instance->getAttributes();
        $conds    = [];
        foreach ($attributes as $key => $value) {
            $attribute = $attribs[$key] ?? null;
            if ($attribute === null) {
                continue;
            }
            $mapped = $attribute->name;
            if ($mapped !== null && !self::isChildModel($attribute->dataType)) {
                $conds[$mapped] = $value;
            }
        }
        try {
            $record = $db->select($instance->getTableName(), \array_keys($conds), \array_values($conds));
        } catch (Exception $e) {
            if ($e->getCode() === self::ERR_NO_PRIMARY_KEY) {
                $attribs = $instance->getAttributes();
                $conds   = [];
                foreach ($attributes as $key => $value) {
                    $conds[$attribs[$instance->getMapping($key)]->name] = $value;
                }

                $record = $db->select($instance->getTableName(), \array_keys($conds), \array_values($conds));
            } else {
                throw $e;
            }
        }
        if (isset($record)) {
            $instance->loaded = true;
        } else {
            switch ($option) {
                case self::ON_NOTEXISTS_NEW:
                    $instance->fill($attributes);

                    return $instance;
                case self::ON_NOTEXISTS_CREATE:
                    return static::create($attributes, $db);
                default:
                    throw new Exception(__METHOD__ . ': No Data Found', self::ERR_NOT_FOUND);
            }
        }
        $instance->fill($record);

        return $instance;
    }

    /**
     * @inheritdoc
     */
    public static function loadAll(DbInterface $db, $key, $value): Collection
    {
        $instance = static::newInstance($db);

        return \collect($db->selectAll($instance->getTableName(), $key, $value))
            ->map(static function ($value) use ($db) {
                $i = new static($db);
                $i->fill($value);
                $i->setWasLoaded(true);

                return $i;
            });
    }

    /**
     * Cast given value to value of given type
     *
     * @param mixed  $value - the value to cast for
     * @param string $type - the type to cast to, supported types are:
     *      - bool,boolean: will be cast to bool
     *      - int,tinyint,smallint,mediumint,integer,bigint,decimal,dec: will be cast to int
     *      - float,double: will be cast to float
     *      - string,date,time,year,datetime,timestamp,char,varchar,tinytext,text,mediumtext,enum: will be cast to
     *     string
     * @return mixed
     * @throws Exception - throws an exception with ERR_INVALID_PARAM if type is not a supported datatype
     */
    protected static function cast($value, $type, bool $nullable = false)
    {
        if ($nullable === true && ($value === null || $value === '_DBNULL_')) {
            return null;
        }
        $result = null;
        switch (self::getType($type)) {
            case 'bool':
                $result = (bool)$value;
                break;
            case 'int':
                $result = (int)$value;
                break;
            case 'float':
                if (\is_numeric($value)) {
                    $result = (float)$value;
                }
                break;
            case 'string':
                if (\is_scalar($value)) {
                    $result = (string)$value;
                }
                break;
            case 'model':
            case 'object':
                return $value;
            case 'yesno':
                if (\in_array($value, ['Y', 'N'], true)) {
                    $result = $value;
                } elseif (\is_numeric($value) || \is_bool($value)) {
                    $result = (bool)$value === true ? 'Y' : 'N';
                } elseif ($value === 'true') {
                    $result = 'Y';
                } elseif ($value === 'false') {
                    $result = 'N';
                }
                break;

            default:
                throw new Exception(__METHOD__ . ': unsupported data type(' . $type . ')', self::ERR_INVALID_PARAM);
        }

        return $result;
    }

    /**
     * @param string $type
     * @return mixed
     */
    private static function getType(string $type)
    {
        if ($type === 'object') {
            return $type;
        }
        if (self::isChildModel($type)) {
            return 'model';
        }
        $typeMap = [
            'bool|boolean',
            'int|tinyint|smallint|mediumint|integer|bigint|decimal|dec',
            'float|double',
            'yesno',
            'string|date|time|year|datetime|timestamp|char|varchar|tinytext|text|mediumtext|enum',
        ];
        $type    = \strtolower($type);

        return \array_reduce($typeMap, static function ($carry, $item) use ($type) {
            if (!isset($carry) && \preg_match('/' . $item . '/', $type)) {
                $carry = \explode('|', $item, 2)[0];
            }

            return $carry;
        });
    }

    /**
     *
     */
    protected function onInstanciation(): void
    {
    }

    /**
     * This method can be overridden to register handlers for getter and/or setter or to add iteratorKeys
     */
    protected function onRegisterHandlers(): void
    {
    }

    /**
     *
     */
    protected function onBeforeInsert(): void
    {
    }

    /**
     *
     */
    protected function onBeforeUpdate(): void
    {
    }

    /**
     * Set a getter method for an attribute of this model and return model itself.
     * A good place to use this function is {@link onRegisterHandlers}.
     *
     * @param string   $attribName - name of the attribute the handler fired for
     * @param callable $getter - mixed function (mixed $value, mixed $default)
     *      - the specified callable gets two parameters - the internal value and a default value
     *      - the type of this parameters correspond to the type of the linked attribute
     *      - the handler can return a value of the type the public property will be have
     *      - for example: a getter for a datetime-field gets two string parameters and returns a DateTime-Object
     * @return static
     */
    protected function registerGetter($attribName, callable $getter): self
    {
        if (\is_callable($getter)) {
            $this->getters[$this->getMapping($attribName)] = $getter;
        }

        return $this;
    }

    /**
     * Set a setter method for an attribute of this model and return model itself.
     * A good place to use this function is {@link onRegisterHandlers}.
     *
     * @param string   $attribName - name of the attribute the handler fired for
     * @param callable $setter - mixed function (mixed $value)
     *      - the specified callable gets one parameter - the public value
     *      - the type of this value is the same as the return type of the corresponding getter handler
     *      - the handler should return a value of the type of the linked attribute
     *      - for example: a setter for a datetime-field gets a DateTime-Object as parameter and returns a string
     * @return static
     */
    protected function registerSetter($attribName, callable $setter): self
    {
        if (\is_callable($setter)) {
            $this->setters[$this->getMapping($attribName)] = $setter;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function fill($attributes): DataModelInterface
    {
        if (\is_array($attributes)) {
            $attributes = (object)$attributes;
        }
        foreach ($this->getAttributes() as $attribute) {
            $attribName = $attribute->name;
            if (($name = $this->checkAttribute($attributes, $attribName)) !== null) {
                $this->setAttribValue($name, $attributes->$name);
            } elseif ($attribute->foreignKey !== null && \class_exists($attribute->dataType)) {
                $key       = $attribute->foreignKey;
                $className = $attribute->dataType;
                /** @var DataModelInterface $className */
                $value = $className::loadAll($this->db, $attribute->foreignKeyChild ?? $key, $this->$key);
                if (isset($this->setters[$attribName])) {
                    $value = \call_user_func($this->setters[$attribName], $value, $this);
                }
                $this->members[$attribName] = $value;
            } else {
                $this->setAttribValue($attribName, $attribute->default);
            }
        }
        $this->onInstanciation();

        return $this;
    }

    /**
     * Produces an empty set of attribute values and return the model itself.
     * This will use the defined database attributes to fill property values with defaults.
     *
     * @return static
     */
    protected function fabricate(): self
    {
        foreach ($this->getAttributes() as $attribute) {
            $this->setAttribValue($attribute->name, $attribute->default);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function save(array $partial = null, bool $updateChildModels = true): bool
    {
        $noPrimaryKey = false;
        $keyValue     = null;
        $keyName      = null;
        $members      = $this->getSqlObject();
        $allKeyNames  = [];
        try {
            $allKeyNames = $this->getAllKeyNames(true);
            $keyName     = $this->getKeyName(true);
            $keyValue    = $this->getKey();
            if (\count($allKeyNames) === 1 && empty($members->$keyName)) {
                unset($members->$keyName);
            }
        } catch (Exception $e) {
            if ($e->getCode() === self::ERR_NO_PRIMARY_KEY) {
                $noPrimaryKey = true;
            } else {
                throw $e;
            }
        }
        $members = $this->getMembersToSave($members, $partial);
        if (!$this->loaded || $noPrimaryKey || $keyValue === null || $keyValue === 0) {
            $pkValue = $this->getDB()->insert($this->getTableName(), $members);
            if ((empty($keyValue) || $noPrimaryKey) && !empty($pkValue)) {
                try {
                    $this->setKey($pkValue);
                } catch (Exception) {
                }
                if ($updateChildModels) {
                    $this->updateChildModels();
                }

                return true;
            }
            if ($updateChildModels) {
                $this->updateChildModels();
            }

            return false;
        }
        // hack to allow updating tables like "tkategoriesprache" where no single primary key is present
        if (\count($allKeyNames) > 1) {
            $keyValue = [];
            $keyName  = [];
            foreach ($allKeyNames as $name) {
                $keyName[]  = $name;
                $keyValue[] = (int)$this->getAttribValue($name);
            }
        }
        $res = $this->getDB()->update($this->getTableName(), $keyName, $keyValue, $members) >= 0;
        if ($updateChildModels) {
            $this->updateChildModels();
        }

        return $res;
    }

    /**
     * @param stdClass      $members
     * @param string[]|null $partial
     * @return stdClass
     */
    protected function getMembersToSave(stdClass $members, array $partial = null): stdClass
    {
        if (\is_array($partial) && \count($partial)) {
            foreach ($this->getAttributes() as $attributeName => $attribute) {
                if (!\in_array($attributeName, $partial, true) && !\in_array($attribute->name, $partial, true)) {
                    $memberName = $attribute->name;
                    unset($members->$memberName);
                }
            }
        }
        $definedMembers = \array_keys(\get_object_vars($members));
        foreach ($this->getAttributes() as $attributeName => $attribute) {
            if ($attribute->foreignKey !== null || $attribute->foreignKeyChild !== null) {
                continue;
            }
            $mapping = $attribute->name;
            if (
                $attribute->nullable === true
                && \in_array($mapping, $definedMembers, true)
                && $members->$mapping === null
            ) {
                $members->$mapping = '_DBNULL_';
            }
        }

        return $members;
    }

    /**
     * @inheritdoc
     */
    public function delete(): bool
    {
        try {
            $this->deleteChildModels();
            $result = $this->getDB()->delete($this->getTableName(), $this->getKeyName(true), $this->getKey()) > 0;
        } catch (Exception $e) {
            if ($e->getCode() === self::ERR_NO_PRIMARY_KEY) {
                $keys   = [];
                $values = [];
                foreach ($this->rawArray() as $key => $value) {
                    if ($value !== null) {
                        $keys[]   = $key . '=?';
                        $values[] = $value;
                    }
                }
                $pdo = $this->getDB()->getPDO();
                $s   = $pdo->prepare('DELETE FROM ' . $this->getTableName() . ' WHERE ' . \implode(' AND ', $keys));
                $s->execute($values);
                $result = $s->rowCount();
            } else {
                throw $e;
            }
        }

        return $result === true || $result > 0;
    }

    /**
     * @inheritdoc
     */
    public function reload()
    {
        $record = $this->getDB()->select($this->getTableName(), $this->getKeyName(true), $this->getKey());

        if (!isset($record)) {
            throw new Exception(__METHOD__ . ': No Data Found', self::ERR_NOT_FOUND);
        }
        $this->setWasLoaded(true);
        $this->fill($record);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMapping($attribName): string
    {
        $tableName = $this->getTableName();
        if (!isset(static::$nameMapping[$tableName][$attribName])) {
            foreach ($this->getAttributes() as $name => $attribute) {
                if ($attribute->name === $attribName) {
                    static::$nameMapping[$tableName][$attribName] = $name;
                    break;
                }
            }
        }

        return static::$nameMapping[$tableName][$attribName] ?? $attribName;
    }

    /**
     * @param string $attribName
     * @return bool
     */
    private function hasMapping(string $attribName): bool
    {
        $tableName = $this->getTableName();
        if (!isset(static::$nameMapping[$tableName][$attribName])) {
            foreach ($this->getAttributes() as $name => $attribute) {
                if ($attribute->name === $attribName) {
                    static::$nameMapping[$tableName][$attribName] = $name;
                    break;
                }
            }
        }

        return isset(static::$nameMapping[$tableName][$attribName]);
    }

    /**
     * @param string $type
     * @return bool
     */
    private static function isChildModel(string $type): bool
    {
        return \class_exists($type) && \is_subclass_of($type, self::class);
    }

    /**
     * @inheritdoc
     */
    public function getKey(): int
    {
        return (int)$this->getAttribValue($this->getKeyName());
    }

    /**
     * @inheritdoc
     */
    public function setKey($value)
    {
        $this->setAttribValue($this->getKeyName(), $value);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getKeyName(bool $realName = false): string
    {
        $keyName = null;
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute->isPrimaryKey) {
                $keyName = $attribute->name;
                break;
            }
        }

        if (!isset($keyName)) {
            throw new Exception(__METHOD__ . ': no primary key1 exists', self::ERR_NO_PRIMARY_KEY);
        }

        return $realName ? $keyName : $this->getMapping($keyName);
    }

    /**
     * @inheritdoc
     */
    public function getAllKeyNames(bool $realName = false): array
    {
        $keyNames = [];
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute->isPrimaryKey) {
                $keyNames[] = $attribute->name;
            }
        }
        if (\count($keyNames) === 0) {
            throw new Exception(__METHOD__ . ': no primary key', self::ERR_NO_PRIMARY_KEY);
        }

        return $realName ? $keyNames : \array_map($this->getMapping(...), $keyNames);
    }

    /**
     * @inheritdoc
     */
    public function getAttribValue($attribName, $default = null)
    {
        $attribName = $this->getMapping($attribName);
        if (!\array_key_exists($attribName, $this->members)) {
            if (\array_key_exists($attribName, $this->getAttributes())) {
                return $default;
            }
            throw new Exception(__METHOD__ . ': invalid attribute(' . $attribName . ')', self::ERR_INVALID_PARAM);
        }

        if (isset($this->getters[$attribName])) {
            return \call_user_func($this->getters[$attribName], $this->members[$attribName], $default);
        }

        return $this->members[$attribName];
    }

    /**
     * @inheritdoc
     */
    public function setAttribValue($attribName, $value)
    {
        $attribName = $this->getMapping($attribName);
        $attributes = $this->getAttributes();
        if (!\array_key_exists($attribName, $attributes)) {
            throw new Exception(__METHOD__ . ': invalid attribute(' . $attribName . ')', self::ERR_INVALID_PARAM);
        }
        if (isset($this->setters[$attribName])) {
            $this->members[$attribName] = self::cast(
                \call_user_func($this->setters[$attribName], $value, $this),
                $attributes[$attribName]->dataType,
                $attributes[$attribName]->nullable
            );
        } else {
            $this->members[$attribName] = self::cast(
                $value,
                $attributes[$attribName]->dataType,
                $attributes[$attribName]->nullable
            );
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function rawJSON(int $options = 0, bool $iterated = false)
    {
        return \json_encode($this->rawObject($iterated), $options);
    }

    /**
     * @inheritdoc
     */
    public function rawArray(bool $iterated = false): array
    {
        $result = [];
        if ($iterated) {
            /** @var string $member */
            foreach ($this as $member => $value) {
                if (\is_a($value, Collection::class)) {
                    $value = $value->map(static function (DataModelInterface $e): array {
                        return $e->rawArray(true);
                    })->toArray();
                } elseif ($value instanceof DataModelInterface) {
                    $value = $value->rawArray(true);
                }
                $result[$member] = $value;
            }
        } else {
            /** @var string $name */
            foreach ($this->getAttributes() as $name => $attribute) {
                $result[$attribute->name] = $this->members[$name];
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function rawObject(bool $iterated = false): stdClass
    {
        $result = new stdClass();
        if ($iterated) {
            foreach ($this as $member => $value) {
                $result->$member = $value;
            }
        } else {
            foreach ($this->getAttributes() as $name => $attribute) {
                $member          = $attribute->name;
                $result->$member = $this->members[$name];
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getSqlObject(bool $noPrimary = false): stdClass
    {
        $result = new stdClass();
        foreach ($this->getAttributes() as $name => $attr) {
            if (
                $attr->foreignKey !== null
                || $attr->foreignKeyChild !== null
                || $attr->dynamic === true
                || ($noPrimary === true && $attr->isPrimaryKey === true && $this->members[$name] === null)
            ) {
                // do not add child relations to sql statement
                continue;
            }
            $member          = $attr->name;
            $result->$member = $this->members[$name];
            if ($result->$member === null) {
                $result->$member = '_DBNULL_';
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function replicate(array $except = null)
    {
        $members = $this->rawObject();
        if (\is_array($except) && \count($except)) {
            foreach ($this->getAttributes() as $attributeName => $attribute) {
                if (\in_array($attributeName, $except, true) || \in_array($attribute->name, $except, true)) {
                    $memberName = $attribute->name;
                    unset($members->$memberName);
                }
            }
        }

        return static::newInstance($this->db)->init((array)$members);
    }

    /**
     * @return $this
     */
    protected function updateChildModels(): self
    {
        foreach ($this->getChildModels() as $childModel) {
            if (!\is_a($childModel, Collection::class)) {
                continue;
            }
            $childModel->each(function (DataModelInterface $model): void {
                $class = \get_class($model);
                foreach ($this->getKeyUpdates($class) as $k => $v) {
                    $model->$k = $v;
                }
                $model->setDB($this->getDB());
                $model->save();
            });
        }

        return $this;
    }

    /**
     * update foreign key constraints for child models after creating parent model
     *
     * @param string $className
     * @return array<string, string>
     */
    protected function getKeyUpdates(string $className): array
    {
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute->getDataType() === $className && ($key = $attribute->getForeignKey()) !== null) {
                $foreignKey = $attribute->getForeignKeyChild() ?? $key;

                return [$foreignKey => $this->$key];
            }
        }

        return [];
    }

    /**
     * @return $this
     */
    protected function deleteChildModels(): self
    {
        foreach ($this->getChildModels() as $childModel) {
            if (\is_a($childModel, Collection::class)) {
                $childModel->each(function (DataModelInterface $model): void {
                    $model->setDB($this->getDB());
                    $model->delete();
                });
            } elseif ($childModel instanceof DataModelInterface) {
                $childModel->setDB($this->getDB());
                $childModel->delete();
            }
        }

        return $this;
    }

    /**
     * @return DataModelInterface[]
     */
    public function getChildModels(): array
    {
        $result = [];
        foreach ($this->getAttributes() as $name => $attribute) {
            if ($attribute->foreignKey !== null || $attribute->foreignKeyChild !== null) {
                $result[] = $this->members[$name];
            }
        }

        return $result;
    }
}
