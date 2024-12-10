<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210223120600
 */
class Migration20210223120600 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Separate shipping company';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'lieferadresse_abfragen_firma',
            'N',
            \CONF_KUNDEN,
            'Firma abfragen',
            'selectbox',
            325,
            (object)[
                'cBeschreibung' => 'Firma in Lieferadresse abfragen?',
                'inputOptions'  => [
                    'O' => 'Optional',
                    'Y' => 'Ja',
                    'N' => 'Nein',
                ],
            ]
        );
        $this->setConfig(
            'lieferadresse_abfragen_firmazusatz',
            'N',
            \CONF_KUNDEN,
            'Firmenzusatz abfragen',
            'selectbox',
            327,
            (object)[
                'cBeschreibung' => 'Firmenzusatz in Lieferadresse abfragen?',
                'inputOptions'  => [
                    'O' => 'Optional',
                    'Y' => 'Ja',
                    'N' => 'Nein',
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('lieferadresse_abfragen_firma');
        $this->removeConfig('lieferadresse_abfragen_firmazusatz');
    }
}
