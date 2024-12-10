<?php

/**
 * add new link types
 *
 * @author ms
 * @created Wed, 08 Jun 2016 11:29:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160608112900
 */
class Migration20160608112900 extends Migration implements IMigration
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
        $this->execute('ALTER TABLE `tlink` DROP PRIMARY KEY, ADD PRIMARY KEY (`kLink`, `kLinkgruppe`);');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tlink` DROP PRIMARY KEY, ADD PRIMARY KEY (`kLink`);');
    }
}
