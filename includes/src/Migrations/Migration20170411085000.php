<?php

/**
 * add lang key choose filter
 *
 * @author ms
 * @created Tue, 11 Apr 2017 08:50:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170411085000
 */
class Migration20170411085000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Add lang key select filter';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'global', 'selectFilter', 'Beliebig');
        $this->setLocalization('eng', 'global', 'selectFilter', 'Any');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('selectFilter');
    }
}
