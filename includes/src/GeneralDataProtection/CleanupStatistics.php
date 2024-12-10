<?php

declare(strict_types=1);

namespace JTL\GeneralDataProtection;

use JTL\DB\ReturnType;

/**
 * Class CleanupStatistics
 * @package JTL\GeneralDataProtection
 *
 * Delete old statistics.
 * (interval former "interval_clear_statistics" = 365 days)
 *
 * names of the tables, we manipulate:
 *
 * `consent_statistics`
 */
class CleanupStatistics extends Method implements MethodInterface
{
    private array $methodName = [
        'consentStatistics'
    ];

    /**
     * max repetitions of this task
     *
     * @var int
     */
    public int $taskRepetitions = 0;

    /**
     * runs all anonymize methods
     *
     * @return void
     */
    public function execute(): void
    {
        $workLimitStart = $this->workLimit;
        foreach ($this->methodName as $method) {
            if ($this->workLimit === 0) {
                $this->isFinished = false;
                return;
            }
            $affected        = $this->$method();
            $this->workLimit -= $affected; // reduce $workLimit locallly for the next method
            $this->workSum   += $affected; // summarize complete work
        }
        $this->isFinished = ($this->workSum < $workLimitStart);
    }

    /**
     * delete consent statistics
     * older than given interval
     *
     * @return int
     */
    private function consentStatistics(): int
    {
        return $this->db->queryPrepared(
            'DELETE FROM consent_statistics
                WHERE eventDate <= :dateLimit
                ORDER BY eventDate
                LIMIT :workLimit',
            [
                'dateLimit' => $this->dateLimit,
                'workLimit' => $this->workLimit
            ],
            ReturnType::AFFECTED_ROWS
        );
    }
}
