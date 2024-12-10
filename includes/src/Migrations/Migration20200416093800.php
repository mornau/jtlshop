<?php

declare(strict_types=1);

namespace JTL\Migrations;

use DateTime;
use JTL\Update\IMigration;
use JTL\Update\Migration;
use stdClass;

/**
 * Class Migration20200416093800
 */
class Migration20200416093800 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add license cron';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $ins            = new stdClass();
        $ins->frequency = 4;
        $ins->jobType   = 'licensecheck';
        $ins->name      = 'licensecheck';
        $ins->startTime = (new DateTime())->format('H:i:s');
        $ins->startDate = (new DateTime())->format('Y-m-d H:i:s');
        $this->getDB()->insert('tcron', $ins);
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM tcron WHERE jobType = 'licensecheck'");
    }
}
