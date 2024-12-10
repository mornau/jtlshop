<?php

/**
 * add setting "review reminder bound to newsletter"
 *
 * @author cr
 * @created Wed, 30 Jan 2019 13:08:22 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190130130822
 */
class Migration20190130130822 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'cr';
    }

    public function getDescription(): string
    {
        return 'Add setting "review reminder bound to newsletter"';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "UPDATE teinstellungenconfwerte w
            JOIN teinstellungenconf c
                ON c.kEinstellungenConf = w.kEinstellungenConf
                AND c.cWertName = 'bewertungserinnerung_nutzen'
                AND w.cWert = 'N'
            SET w.nSort = 2"
        );
        $this->execute(
            "UPDATE teinstellungenconfwerte w
            JOIN teinstellungenconf c
                ON c.kEinstellungenConf = w.kEinstellungenConf
                AND c.cWertName = 'bewertungserinnerung_nutzen'
                AND w.cWert = 'Y'
            SET w.nSort = 3"
        );
        $this->execute(
            "INSERT INTO teinstellungenconfwerte(
                kEinstellungenConf,
                cName,
                cWert,
                nSort)
            VALUES(
                (SELECT kEinstellungenConf FROM teinstellungenconf WHERE cWertName = 'bewertungserinnerung_nutzen'),
                'An Newslettereinwilligung koppeln',
                'B',
                1)"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "DELETE w FROM teinstellungenconfwerte w JOIN teinstellungenconf c
            WHERE w.kEinstellungenConf = c.kEinstellungenConf
                AND c.cWertName = 'bewertungserinnerung_nutzen'
                AND w.cWert = 'B'"
        );
        $this->execute(
            "UPDATE teinstellungenconfwerte w
            JOIN teinstellungenconf c
                ON c.kEinstellungenConf = w.kEinstellungenConf
                AND c.cWertName = 'bewertungserinnerung_nutzen'
                AND w.cWert = 'N'
            SET w.nSort = 2"
        );
        $this->execute(
            "UPDATE teinstellungenconfwerte w
            JOIN teinstellungenconf c
                ON c.kEinstellungenConf = w.kEinstellungenConf
                AND c.cWertName = 'bewertungserinnerung_nutzen'
                AND w.cWert = 'Y'
            SET w.nSort = 1"
        );
    }
}
