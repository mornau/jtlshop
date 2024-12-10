<?php

/**
 * Add isbn and hazard config
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180312123000
 */
class Migration20180313133000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Add ISBN and ADR hazard config';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'isbn_display',
            'N',
            \CONF_ARTIKELDETAILS,
            'Artikel ISBN anzeigen',
            'selectbox',
            499,
            (object)[
                'cBeschreibung' => 'Soll die ISBN (International Standard Book Number) angezeigt werden?',
                'inputOptions'  => [
                    'N'  => 'Nein',
                    'D'  => 'Ja, in den Artikeldetails',
                    'L'  => 'Ja, in Listen',
                    'DL' => 'Ja, in den Details und den Listen'
                ]
            ]
        );
        $this->setConfig(
            'adr_hazard_display',
            'N',
            \CONF_ARTIKELDETAILS,
            'Gefahrentafel im Artikel anzeigen',
            'selectbox',
            499,
            (object)[
                'cBeschreibung' => 'Soll die europäische Gefahrentafel angezeigt werden ' .
                    '(sofern in Wawi UN-Nummer und Gefahrnummer gepflegt sind)?',
                'inputOptions'  => [
                    'N'  => 'Nein',
                    'D'  => 'Ja, in den Artikeldetails',
                    'L'  => 'Ja, in Listen',
                    'DL' => 'Ja, in den Details und den Listen'
                ]
            ]
        );

        $this->setLocalization('ger', 'global', 'isbn', 'ISBN');
        $this->setLocalization('eng', 'global', 'isbn', 'ISBN');

        $this->setLocalization('ger', 'global', 'adrHazardSign', 'Gefahrentafel');
        $this->setLocalization('eng', 'global', 'adrHazardSign', 'ADR European hazard sign');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('isbn_display');
        $this->removeConfig('adr_hazard_display');

        $this->removeLocalization('isbn');
        $this->removeLocalization('adrHazardSign');
    }
}
