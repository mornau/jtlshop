<?php

declare(strict_types=1);

namespace JTL\Update;

use JTL\DB\DbInterface;
use stdClass;

/**
 * Trait MigrationTrait
 * @package JTL\Update
 */
trait MigrationTrait
{
    /**
     * @var DbInterface
     */
    protected DbInterface $db;

    /**
     * executes query and returns misc data
     *
     * @param string $query - Statement to be executed
     * @param int    $return - what should be returned.
     * @return array<mixed>|stdClass[]|stdClass|\PDOStatement|int|bool
     * @deprecated since 5.4.0
     */
    protected function exec(string $query, int $return)
    {
        return $this->getDB()->query($query, $return);
    }

    /**
     * @return DbInterface
     */
    public function getDB(): DbInterface
    {
        return $this->db;
    }

    /**
     * @param DbInterface $db
     */
    public function setDB(DbInterface $db): void
    {
        $this->db = $db;
    }

    /**
     * @param string $query
     * @return int
     */
    public function execute(string $query): int
    {
        return $this->getDB()->getAffectedRows($query);
    }

    /**
     * @param string $query
     * @return stdClass|null
     */
    public function fetchOne(string $query): ?stdClass
    {
        return $this->getDB()->getSingleObject($query);
    }

    /**
     * @param string $query
     * @return stdClass[]
     */
    public function fetchAll(string $query): array
    {
        return $this->getDB()->getObjects($query);
    }

    /**
     * @param string $query
     * @return array<int, array<mixed>>
     */
    public function fetchArray(string $query): array
    {
        return $this->getDB()->getArrays($query);
    }
}
