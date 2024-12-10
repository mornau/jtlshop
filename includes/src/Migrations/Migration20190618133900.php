<?php

/**
 * @author fm
 * @created Tue, 18 June 2019 13:39:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190618133900
 */
class Migration20190618133900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Remove option to allow news comments for unregistered users';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->removeConfig('news_kommentare_eingeloggt');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setConfig(
            'news_kommentare_eingeloggt',
            'N',
            \CONF_NEWS,
            'Einloggen um Kommentare zu schreiben',
            'selectbox',
            70,
            (object)[
                'cBeschreibung' => 'Muss man als Besucher eingeloggt sein um einen Newskommentar zu schreiben ' .
                    'oder dürfen es alle Besucher?',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein',
                ],
            ]
        );
    }
}
