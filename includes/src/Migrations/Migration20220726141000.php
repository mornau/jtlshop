<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20220726141000
 */
class Migration20220726141000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Next start for cron jobs';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE `tcron` ADD COLUMN `nextStart` DATETIME NULL DEFAULT NULL AFTER `lastStart`');
        $this->execute(
            'UPDATE tcron
                SET nextStart = DATE_ADD(
                    COALESCE(DATE_ADD(
                        DATE(tcron.lastStart),
                        INTERVAL tcron.frequency * GREATEST(CEIL(
                            TIME_TO_SEC(
                                TIMEDIFF(TIME(tcron.lastStart), tcron.startTime)
                            ) / tcron.frequency / 3600
                        ), 0) HOUR
                    ), DATE(tcron.startDate)),
                    INTERVAL TIME_TO_SEC(tcron.startTime) SECOND
                )'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tcron` DROP COLUMN `nextStart`');
    }
}
