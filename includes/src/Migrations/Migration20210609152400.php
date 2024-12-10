<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210609152400
 */
class Migration20210609152400 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Fix eng youtube consent name';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("UPDATE `tconsentlocalization` SET `name` = 'YouTube' WHERE `name`  = 'YoutTube'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
