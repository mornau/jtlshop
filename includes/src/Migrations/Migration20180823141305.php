<?php

/**
 * remove_unused_shopversion_template_property
 *
 * @author msc
 * @created Thu, 23 Aug 2018 14:13:05 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180823141305
 */
class Migration20180823141305 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'msc';
    }

    public function getDescription(): string
    {
        return "Remove unused template property 'shopversion'";
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->dropColumn('ttemplate', 'shopversion');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->getDB()->query('ALTER TABLE `ttemplate` ADD `shopversion` int(11) DEFAULT NULL AFTER `version`');
    }
}
