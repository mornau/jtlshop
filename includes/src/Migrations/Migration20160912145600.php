<?php

/**
 * Rename row in tkampagne and tkampagnedef to not blow up the table-width in statistics overview in backend
 *
 * @author dr
 * @created Mo, 12 Sep 2016 14:56:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160912145600
 */
class Migration20160912145600 extends Migration implements IMigration
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
        $this->execute(
            "UPDATE tkampagne
                SET cName = 'Verfügbarkeits-Benachrichtigungen' WHERE kKampagne = 1"
        );
        $this->execute(
            "UPDATE tkampagnedef
                SET cName = 'Verfügbarkeits-Anfrage' WHERE kKampagneDef = 6"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "UPDATE tkampagne
                SET cName = 'Verfügbarkeitsbenachrichtigungen' WHERE kKampagne = 1"
        );
        $this->execute(
            "UPDATE tkampagnedef
                SET cName = 'Verfügbarkeitsanfrage' WHERE kKampagneDef = 6"
        );
    }
}
