<?php

/**
 * Plugin bootstrap flag
 *
 * @author aj
 * @created Mon, 13 Jun 2016 15:51:56 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160613155156
 */
class Migration20160613155156 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'aj';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("ALTER TABLE `tplugin` ADD `bBootstrap` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->dropColumn('tplugin', 'bBootstrap');
    }
}
