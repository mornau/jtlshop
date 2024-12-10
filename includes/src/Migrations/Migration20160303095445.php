<?php

/**
 * landing page statistics
 *
 * @author ms
 * @created Thu, 03 Mar 2016 09:54:45 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160303095445
 */
class Migration20160303095445 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "INSERT INTO `tadminrecht` (`cRecht`, `cBeschreibung`, `kAdminrechtemodul`)
                VALUES ('STATS_LANDINGPAGES_VIEW', 'Einstiegsseiten', '10');"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM `tadminrecht` WHERE `cRecht` = 'STATS_LANDINGPAGES_VIEW';");
    }
}
