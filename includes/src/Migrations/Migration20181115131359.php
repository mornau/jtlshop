<?php

/**
 * Change setting for restriction to only delivery countries
 *
 * @author fp
 * @created Thu, 15 Nov 2018 13:13:59 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20181115131359
 */
class Migration20181115131359 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Change setting for restriction to only delivery countries';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "UPDATE teinstellungenconf
                SET cBeschreibung = 'Damit gibt es bei der Lieferadresse nur Länder zur Auswahl, "
            . "für die min. eine Versandart definiert ist.'
                WHERE cWertName = 'kundenregistrierung_nur_lieferlaender'"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "UPDATE teinstellungenconf
                SET cBeschreibung = 'Damit gibt es bei der Rechnungsadresse und Lieferadresse nur Länder"
            . " zur Auswahl, für die min. eine Versandart definiert ist.'
                WHERE cWertName = 'kundenregistrierung_nur_lieferlaender'"
        );
    }
}
