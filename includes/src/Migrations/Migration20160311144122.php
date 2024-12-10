<?php

/**
 * livesearch language setting
 *
 * @author fp
 * @created Fri, 11 Mar 2016 14:41:22 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160311144122
 */
class Migration20160311144122 extends Migration implements IMigration
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
        $this->setLocalization('ger', 'global', 'noDataAvailable', 'Keine Daten verf&uuml;gbar!');
        $this->setLocalization('eng', 'global', 'noDataAvailable', 'No data available!');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM `tsprachwerte` WHERE `kSprachsektion` = 1 AND `cName` = 'noDataAvailable';");
    }
}
