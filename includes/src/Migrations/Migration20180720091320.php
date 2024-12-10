<?php

/**
 * hierarchical_news
 *
 * @author mh
 * @created Fri, 20 Jul 2018 09:13:20 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180720091320
 */
class Migration20180720091320 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Hierarchical news';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE `tnewskategorie`
                ADD COLUMN `kParent` INT(10) NOT NULL DEFAULT 0'
        );
        $this->execute('ALTER TABLE `tnewskategorie` ADD INDEX `kParent` (kParent)');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tnewskategorie`DROP COLUMN `kParent`');
    }
}
