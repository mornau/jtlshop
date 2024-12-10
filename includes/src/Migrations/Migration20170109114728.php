<?php

/**
 * Enable article fulltext search
 *
 * @author fp
 * @created Mon, 09 Jan 2017 11:47:28 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170109114728
 */
class Migration20170109114728 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Enable article fulltext search';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'suche_fulltext',
            'N',
            \CONF_ARTIKELUEBERSICHT,
            'Volltextsuche verwenden',
            'selectbox',
            105,
            (object)[
                'cBeschreibung' => 'F&uuml;r die Volltextsuche werden spezielle Indizes angelegt. ' .
                    'Dies muss von der verwendeten Datenbankversion unterst&uuml;tzt werden.',
                'inputOptions'  => [
                    'N' => 'Standardsuche verwenden',
                    'Y' => 'Volltextsuche verwenden',
                ],
            ]
        );

        $this->setConfig(
            'suche_min_zeichen',
            '4',
            \CONF_ARTIKELUEBERSICHT,
            'Mindestzeichenanzahl des Suchausdrucks',
            'number',
            180,
            (object)[
                'cBeschreibung' => 'Unter dieser Zeichenanzahlgrenze wird die Suche nicht ausgef&uuml;hrt. ' .
                    '(Bei Verwendung der Volltextsuche sollte dieser Wert an den Datenbankparameter ' .
                    'ft_min_word_len angepasst werden.)',
            ],
            true
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setConfig(
            'suche_min_zeichen',
            '4',
            \CONF_ARTIKELUEBERSICHT,
            'Mindestzeichenanzahl des Suchausdrucks',
            'number',
            180,
            (object)['cBeschreibung' => 'Unter dieser Zeichenanzahlgrenze wird die Suche nicht ausgef&uuml;hrt',],
            true
        );
        $this->removeConfig('suche_fulltext');
    }
}
