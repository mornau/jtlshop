<?php

/**
 * Add customer-fields nSort
 *
 * @author cr
 * @created Thu, 23 Nov 2017 10:08:24 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20171123100824
 */
class Migration20171123100824 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'cr';
    }

    public function getDescription(): string
    {
        return 'Add customer-fields nSort';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE `tkundenfeldwert`
            ADD COLUMN `nSort` int(10) unsigned NOT NULL AFTER `cWert`'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'ALTER TABLE `tkundenfeldwert`
            DROP COLUMN `nSort`;'
        );
    }
}
