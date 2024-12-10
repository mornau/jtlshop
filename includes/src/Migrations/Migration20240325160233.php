<?php

/**
 * remove config kundenregistrierung_pruefen_ort and cityNotNumeric lang var
 *
 * @author dr
 * @created Mon, 25 Mar 2024 16:02:33 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration_20240325160233
 */
class Migration20240325160233 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'remove config kundenregistrierung_pruefen_ort';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->removeConfig('kundenregistrierung_pruefen_ort');
        $this->removeLocalization('cityNotNumeric');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setConfig(
            'kundenregistrierung_pruefen_ort',
            'N',
            \CONF_KUNDEN,
            'Stadt auf Zahl prüfen',
            'selectbox',
            191,
            (object)[
                'cBeschreibung' => 'Fehlermeldung ausgeben, wenn eingegebene Stadt eine Zahl enthält.',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein',
                ],
            ]
        );
        $this->setLocalization('ger', 'account data', 'cityNotNumeric', 'Der Ort darf keine Zahlen enthalten.');
        $this->setLocalization('eng', 'account data', 'cityNotNumeric', 'The city must not contain any numbers.');
    }
}
