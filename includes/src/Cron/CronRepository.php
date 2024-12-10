<?php

declare(strict_types=1);

namespace JTL\Cron;

use JTL\Abstracts\AbstractDBRepository;

/**
 * Class CronRepository
 * @package JTL\Cron
 */
class CronRepository extends AbstractDBRepository
{
    /**
     * @inheritdoc
     */
    public function getTableName(): string
    {
        return 'tcron';
    }

    /**
     * @inheritdoc
     */
    public function getKeyName(): string
    {
        return 'cronID';
    }

    /**
     * @param int[]    $ids
     * @param string[] $exclude
     * @return bool
     */
    public function deleteCron(array $ids, array $exclude): bool
    {
        $affected = $this->getDB()->getAffectedRows(
            'DELETE FROM ' . $this->getTableName() . ' WHERE cronID IN (:ids) AND jobType NOT IN (:jobTypes)',
            [
                'jobTypes' => \implode(',', $exclude),
                'ids'      => \implode(',', $ids)
            ]
        );

        return $affected > 0;
    }
}
