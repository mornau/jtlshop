<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210630091300
 */
class Migration20210630091300 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Ust setting no percent';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "INSERT INTO `teinstellungenconfwerte` (`kEinstellungenConf`,`cName`,`cWert`,`nSort`)
                VALUES (
                (SELECT `kEinstellungenConf` FROM `teinstellungenconf` WHERE `cWertName` = 'global_ust_auszeichnung'),
                'Automatik: Inkl. / Exkl. USt. (ohne konkreten Steuersatz)',
                'autoNoVat',
                3)"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "DELETE FROM `teinstellungenconfwerte`
                WHERE `kEinstellungenConf` =
                  (SELECT `kEinstellungenConf` FROM `teinstellungenconf` WHERE `cWertName` = 'global_ust_auszeichnung')
                  AND `cWert` = 'autoNoVat'"
        );
    }
}
