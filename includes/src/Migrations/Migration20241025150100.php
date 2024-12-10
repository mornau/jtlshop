<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

class Migration20241025150100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Update license check cron interval';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        // set start date to now to handle applied hotfixes UPDATE `tcron` SET `startDate`='2100-08-17 10:20:22' [...]
        $this->execute('
            UPDATE tcron 
                SET frequency = 24, startDate = NOW(), startTime = TIME(NOW())
                WHERE jobType = \'licensecheck\'');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('UPDATE tcron SET frequency = 4 WHERE jobType = \'licensecheck\'');
    }
}
