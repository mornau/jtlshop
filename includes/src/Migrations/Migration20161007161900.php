<?php

/**
 * change the column type of tuploadschemasprache.cBeschreibung to TEXT to hold longer descriptions
 *
 * @author dr
 * @created Fr, 07 Oct 2016 16:19:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20161007161900
 */
class Migration20161007161900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE tuploadschemasprache MODIFY cBeschreibung TEXT NOT NULL');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE tuploadschemasprache MODIFY cBeschreibung VARCHAR(45) NOT NULL');
    }
}
