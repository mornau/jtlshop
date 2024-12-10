<?php

/**
 * Rename options for setting 192
 *
 * @author fp
 * @created Thu, 28 Sep 2017 16:24:40 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170928162440
 */
class Migration20170928162440 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Rename options for setting 192';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "UPDATE teinstellungenconfwerte
                SET cName = 'Automatischer Wechsel zu https'
                WHERE kEinstellungenConf = 192 AND cWert = 'P'"
        );
        $this->execute(
            "UPDATE teinstellungenconfwerte
                SET cName = 'Kein automatischer Wechsel'
                WHERE kEinstellungenConf = 192 AND cWert = 'N'"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "UPDATE teinstellungenconfwerte
                SET cName = 'Permanentes SSL mit eigenem Zertifikat'
                WHERE kEinstellungenConf = 192 AND cWert = 'P'"
        );
        $this->execute(
            "UPDATE teinstellungenconfwerte
                SET cName = 'SSL-Verschl&uuml;sselung deaktivieren'
                WHERE kEinstellungenConf = 192 AND cWert = 'N'"
        );
    }
}
