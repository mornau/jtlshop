<?php

/**
 * adding language variable for download-order-date
 *
 * @author cr
 * @created Mon, 10 Feb 2020 12:30:41 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200210123041
 */
class Migration20200210123041 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'cr';
    }

    public function getDescription(): string
    {
        return 'Add lang var download order date';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'global', 'downloadOrderDate', 'Bestellt am');
        $this->setLocalization('eng', 'global', 'downloadOrderDate', 'Ordered on');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('downloadOrderDate');
    }
}
