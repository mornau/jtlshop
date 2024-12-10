<?php

/**
 * Create index for tartikel.kStueckliste
 *
 * @author fp
 * @created Tue, 16 May 2017 15:30:34 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;
use JTL\Update\MigrationHelper;

/**
 * Class Migration20170516153034
 */
class Migration20170516153034 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    /** @lang text */
    public function getDescription(): string
    {
        return 'Create index for tartikel.kStueckliste';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        MigrationHelper::createIndex('tartikel', ['kStueckliste'], 'idx_tartikel_kStueckliste');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        MigrationHelper::dropIndex('tartikel', 'idx_tartikel_kStueckliste');
    }
}
