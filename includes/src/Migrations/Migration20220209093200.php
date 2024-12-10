<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;
use JTL\Update\MigrationHelper;

/**
 * Class Migration20220209093200
 */
class Migration20220209093200 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'Make PLZ index unique, remove duplicate entries';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'DELETE t1
            FROM tplz AS t1
                JOIN tplz AS t2 ON
                    t1.cPLZ = t2.cPLZ AND
                    t1.cLandISO = t2.cLandISO AND
                    t1.cOrt = t2.cOrt AND
                    t1.kPLZ < t2.kPLZ'
        );
        MigrationHelper::createIndex('tplz', ['cLandISO', 'cPLZ', 'cOrt'], 'PLZ_ORT_UNIQUE', true);
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        MigrationHelper::createIndex('tplz', ['cLandISO', 'cPLZ', 'cOrt'], 'PLZ_ORT_UNIQUE');
    }
}
