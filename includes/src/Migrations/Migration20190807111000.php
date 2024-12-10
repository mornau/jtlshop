<?php

/**
 * Remove varkombi preview options
 *
 * @author fm
 * @created Wed, 07 Aug 2019 11:10:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190807111000
 */
class Migration20190807111000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Remove varkombi preview options';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->removeConfig('artikeluebersicht_varikombi_anzahl');
        $this->removeConfig('artikeldetails_varikombi_anzahl');
        $this->removeConfig('artikeldetails_varikombi_vorschautext');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setConfig(
            'artikeluebersicht_varikombi_anzahl',
            '0',
            \CONF_ARTIKELUEBERSICHT,
            'Anzahl Vorschaubilder bei Variationskombis',
            'number',
            280,
            (object)[
                'cBeschreibung'     => 'Wieviele Vorschaubilder von Variationskombinationen sollen in der ' .
                    'Artikel&uuml;bersicht angezeigt werden? (0 = nicht anzeigen)',
                'nStandardAnzeigen' => 1
            ],
            true
        );
        $this->setConfig(
            'artikeldetails_varikombi_anzahl',
            '0',
            \CONF_ARTIKELDETAILS,
            'Anzahl Vorschaubilder bei Variationskombis',
            'number',
            470,
            (object)[
                'cBeschreibung'     => 'Wieviele Vorschaubilder von Variationskombinationen sollen in den ' .
                    'Artikeldetails angezeigt werden? (0 = nicht anzeigen)',
                'nStandardAnzeigen' => 1
            ],
            true
        );
        $this->setConfig(
            'artikeldetails_varikombi_vorschautext',
            'N',
            \CONF_ARTIKELDETAILS,
            'Beschriftung der Variantenvorschau',
            'selectbox',
            480,
            (object)[
                'cBeschreibung' => 'Was soll &uuml;ber der Variantenvorschau als Text angezeigt werden?',
                'inputOptions'  => [
                    'W' => '"Weitere Varianten"',
                    'S' => 'Variation mit kleinster Sortiernummer',
                ],
            ]
        );
    }
}
