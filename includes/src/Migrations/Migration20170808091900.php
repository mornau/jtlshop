<?php

/**
 * changes matrix option names in configuration
 *
 * @author ms
 * @created Tue, 08 Aug 2017 09:19:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170808091900
 */
class Migration20170808091900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Change matrix option names in configuration';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "UPDATE teinstellungenconfwerte
                SET cName = 'Hochformat (nur bis zu 2 Variationen möglich)'
                WHERE kEinstellungenConf = 1330 AND cWert = 'H'"
        );
        $this->execute(
            "UPDATE teinstellungenconfwerte
                SET cName = 'Querformat (nur bis zu 2 Variationen möglich)'
                WHERE kEinstellungenConf = 1330 AND cWert = 'Q'"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "UPDATE teinstellungenconfwerte
                SET cName = 'Hochformat (nur bei 1 Variation möglich)'
                WHERE kEinstellungenConf = 1330 AND cWert = 'H'"
        );
        $this->execute(
            "UPDATE teinstellungenconfwerte
                SET cName = 'Querformat (nur bei 1 Variation möglich)'
                WHERE kEinstellungenConf = 1330 AND cWert = 'Q'"
        );
    }
}
