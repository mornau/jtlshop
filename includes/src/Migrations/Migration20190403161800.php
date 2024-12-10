<?php

/**
 * @author ms
 * @created Wed, 03 Apr 2019 16:18:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190403161800
 */
class Migration20190403161800 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Remove vcard';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $upMigration = new Migration20160713110643($this->db);
        $upMigration->down();
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $upMigration = new Migration20160713110643($this->db);
        $upMigration->up();
    }
}
