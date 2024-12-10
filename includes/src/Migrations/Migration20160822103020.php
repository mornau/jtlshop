<?php

/**
 * New index for customer prices
 *
 * @author root
 * @created Mon, 22 Aug 2016 10:30:20 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;
use JTL\Update\MigrationHelper;

/**
 * Class Migration20160822103020
 */
class Migration20160822103020 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        MigrationHelper::createIndex('tpreis', ['kKunde'], 'idx_tpreis_kKunde');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        MigrationHelper::dropIndex('tpreis', 'idx_tpreis_kKunde');
    }
}
