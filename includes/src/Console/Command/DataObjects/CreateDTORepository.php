<?php

declare(strict_types=1);

namespace JTL\Console\Command\DataObjects;

use JTL\Abstracts\AbstractDBRepository;

class CreateDTORepository extends AbstractDBRepository
{
    /**
     * @inheritDoc
     */
    public function getTableName(): string
    {
        return '';
    }

    public function getTableDescription(string $tablename)
    {
        return $this->getDB()->getPDO()->query('DESCRIBE `' . $tablename . '`');
    }

    public function getAllTables(): mixed
    {
        return $this->getDB()->getPDO()->query('SHOW TABLES');
    }
}
