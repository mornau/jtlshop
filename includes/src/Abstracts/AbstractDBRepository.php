<?php

declare(strict_types=1);

namespace JTL\Abstracts;

use JTL\DataObjects\DomainObjectInterface;
use JTL\DB\DbInterface;
use JTL\Interfaces\RepositoryInterface;
use JTL\Shop;
use stdClass;

/**
 * Class AbstractRepository
 * @package JTL\Abstracts
 */
abstract class AbstractDBRepository implements RepositoryInterface
{
    protected const UPDATE_OR_UPSERT_FAILED = -1;

    protected const DELETE_FAILED = -1;

    protected readonly DbInterface $db;

    public function __construct(?DbInterface $db = null)
    {
        $this->db = $db ?? Shop::Container()->getDB();
    }

    protected function getDB(): DbInterface
    {
        return $this->db;
    }

    /**
     * @inheritdoc
     */
    abstract public function getTableName(): string;

    /**
     * @inheritdoc
     */
    public function getKeyName(): string
    {
        return 'id';
    }

    /**
     * @inheritdoc
     */
    public function getKeyValue(DomainObjectInterface $domainObject): ?int
    {
        return $domainObject->${$this->getKeyName()} ?? null;
    }

    /**
     * @param int $id
     * @return stdClass|null
     */
    public function get(int $id): ?stdClass
    {
        return $this->db->select($this->getTableName(), $this->getKeyName(), $id);
    }

    /**
     * @param array<string, int|float|string> $filters
     * @return stdClass|null
     */
    public function filter(array $filters): ?stdClass
    {
        if (empty($filters)) {
            return null;
        }

        return $this->db->select($this->getTableName(), \array_keys($filters), \array_values($filters));
    }

    /**
     * @inheritdoc
     */
    public function getList(array $filters = []): array
    {
        return $this->db->selectAll(
            $this->getTableName(),
            \array_keys($filters),
            \array_values($filters)
        );
    }

    /**
     * @inheritdoc
     */
    public function getCount(array $filters = []): int
    {
        $columnName = $this->getKeyName();
        $where      = '';
        if (!empty($filters)) {
            $where = ' WHERE '
                . \implode(
                    ' AND ',
                    \array_map(
                        static fn($key, $value) => $key . ' = ' . $value,
                        \array_keys($filters),
                        \array_values($filters)
                    )
                );
        }

        return $this->db->getSingleInt(
            'SELECT COUNT(:column) as cnt FROM ' . $this->getTableName() . $where,
            'cnt',
            ['column' => $columnName]
        );
    }

    /**
     * @inheritdoc
     */
    public function delete(int $id): bool
    {
        $delete = $this->db->deleteRow(
            $this->getTableName(),
            $this->getKeyName(),
            $id
        );

        return $delete !== self::DELETE_FAILED;
    }

    /**
     * @inheritdoc
     */
    public function insert(DomainObjectInterface $domainObject): int
    {
        if (isset($domainObject->modifiedKeys) && \count($domainObject->modifiedKeys) > 0) {
            throw new \InvalidArgumentException(
                'DomainObject has been modified. The last modified keys are '
                . \print_r($domainObject->modifiedKeys, true) . '. The DomainObject looks like this: '
                . \print_r($domainObject->toArray(true), true)
            );
        }

        $obj = $domainObject->toObject();
        foreach ($obj as &$value) {
            if ($value === null) {
                $value = '_DBNULL_';
            }
        }

        return $this->db->insertRow($this->getTableName(), $obj);
    }

    /**
     * @inheritdoc
     */
    public function update(DomainObjectInterface $domainObject): bool
    {
        if (isset($domainObject->modifiedKeys) && \count($domainObject->modifiedKeys) > 0) {
            throw new \InvalidArgumentException(
                'DomainObject has been modified. The modified keys are '
                . \print_r($domainObject->modifiedKeys, true) . '. The DomainObject looks like this: '
                . \print_r($domainObject->toArray(true), true)
            );
        }
        $update = $this->db->updateRow(
            $this->getTableName(),
            $this->getKeyName(),
            $this->getKeyValue($domainObject),
            $domainObject->toObject()
        );

        return $update !== self::UPDATE_OR_UPSERT_FAILED;
    }

    /**
     * @param int[]|numeric-string[] $values
     * @return int[]
     */
    final protected function ensureIntValuesInArray(array $values): array
    {
        return \array_map('\intval', $values);
    }
}
