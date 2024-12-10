<?php

/**
 * Create index for tkategorie.nLevel
 *
 * @author fp
 * @created Thu, 20 Apr 2017 09:49:22 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;
use JTL\Update\MigrationHelper;

/**
 * Class Migration20170420094922
 */
class Migration20170420094922 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    /** @lang text */
    public function getDescription(): string
    {
        return 'Create index for tkategorie.nLevel';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        MigrationHelper::createIndex('tkategorie', ['nLevel'], 'idx_tkategorie_nLevel');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        MigrationHelper::dropIndex('tkategorie', 'idx_tkategorie_nLevel');
    }
}
