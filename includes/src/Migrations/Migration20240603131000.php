<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Cron\Type;
use JTL\Update\IMigration;
use JTL\Update\Migration;
use stdClass;

/**
 * Class Migration20240603131000
 */
class Migration20240603131000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add redirect cleanup cronjob';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $cron            = new stdClass();
        $cron->name      = 'RedirectCleanup';
        $cron->jobType   = Type::REDIRECT_CLEANUP;
        $cron->frequency = 24;
        $cron->startDate = '2024-01-01 00:00:00';
        $cron->startTime = '00:00';
        $cron->nextStart = '2024-01-01 00:00:00';

        $cronID = $this->getDB()->insertRow('tcron', $cron);


        $jobQueue            = new stdClass();
        $jobQueue->cronID    = $cronID;
        $jobQueue->jobType   = Type::REDIRECT_CLEANUP;
        $jobQueue->isRunning = 0;
        $jobQueue->startTime = '2024-01-01 00:00:00';
        $jobQueue->lastStart = '2024-01-01 00:00:00';

        $this->getDB()->insertRow('tjobqueue', $jobQueue);
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM tjobqueue WHERE jobType = '" . Type::REDIRECT_CLEANUP . "'");
        $this->execute("DELETE FROM tcron WHERE jobType = '" . Type::REDIRECT_CLEANUP . "'");
    }
}
