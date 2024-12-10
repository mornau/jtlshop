<?php

/**
 * used flag for new customer coupons
 *
 * @author ms
 * @created Fri, 19 Aug 2016 12:53:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160819125300
 */
class Migration20160819125300 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("ALTER TABLE `tkuponneukunde` ADD COLUMN `cVerwendet` VARCHAR(1) NOT NULL DEFAULT 'N';");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->dropColumn('tkuponneukunde', 'cVerwendet');
    }
}
