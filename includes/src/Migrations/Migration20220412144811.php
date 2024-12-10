<?php

/**
 * Add News Settings
 *
 * @author rf
 * @created Tue, 12 Apr 2022 14:48:11 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20220412144811
 */
class Migration20220412144811 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'rf';
    }

    public function getDescription(): string
    {
        return 'Add News Settings';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'news_kommentare_anzahl_antwort_kommentare_anzeigen',
            'Y',
            \CONF_NEWS,
            'Zeige Anzahl der Antworten',
            'selectbox',
            110,
            (object)[
                'cBeschreibung' => 'Zeige die Anzahl der Antworten, in Klammern, '
                    . 'neben der Kommentar-Anzahl an. Standard = Y',
                'inputOptions'  => [
                    'Y' => 'Anzeigen',
                    'N' => 'Ausblenden',
                ],
            ],
            true
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('news_kommentare_anzahl_antwort_kommentare_anzeigen');
    }
}
