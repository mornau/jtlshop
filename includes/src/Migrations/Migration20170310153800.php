<?php

/**
 * Remove option for partial https encryption
 *
 * @author fm
 * @created Fri, 10 Mar 2017 15:38:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170310153800
 */
class Migration20170310153800 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Remove partial https encryption option';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "UPDATE teinstellungen 
                SET cWert = 'P' 
                WHERE kEinstellungenSektion = 1 
                AND cName = 'kaufabwicklung_ssl_nutzen'
                AND cWert = 'Z'"
        );
        $this->execute(
            "DELETE 
                FROM teinstellungenconfwerte 
                WHERE kEinstellungenConf = 192 
                AND cWert = 'Z'"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "INSERT INTO 
                teinstellungenconfwerte (`kEinstellungenConf`, `cName`, `cWert`, `nSort`)
                VALUES (192, 'Teilverschlüsselung und automatischer Wechsel zwischen http und https', 'Z', 3)"
        );
    }
}
