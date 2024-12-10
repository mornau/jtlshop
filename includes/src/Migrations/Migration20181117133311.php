<?php

/**
 * create store table
 *
 * @author aj
 * @created Mon, 17 Nov 2018 13:33:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20181117133311
 */
class Migration20181117133311 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'aj';
    }

    public function getDescription(): string
    {
        return 'Add plugin store id';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE tplugin
                ADD COLUMN cStoreID varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER cPluginID'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->dropColumn('tplugin', 'cStoreID');
    }
}
