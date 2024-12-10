<?php

/**
 * Add language variable one-off
 *
 * @author msc
 * @created Tue, 01 Aug 2017 13:10:13 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170801131013
 */
class Migration20170801131013 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'msc';
    }

    public function getDescription(): string
    {
        return 'Add language variable one-off';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'checkout', 'one-off', 'Einmalig enthalten');
        $this->setLocalization('eng', 'checkout', 'one-off', 'Included one-time');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('one-off');
    }
}
