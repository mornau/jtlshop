<?php

/**
 * Change database version to semantic versioning
 *
 * @author msc
 * @created Thu, 23 Aug 2018 15:48:32 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180823154832
 */
class Migration20180823154832 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'msc';
    }

    public function getDescription(): string
    {
        return 'Change database version to semantic versioning';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE `tversion` CHANGE `nVersion` `nVersion` varchar(20) NOT NULL');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tversion` CHANGE `nVersion` `nVersion` int(10) DEFAULT NULL');
    }
}
