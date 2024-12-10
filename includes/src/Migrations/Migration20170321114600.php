<?php

/**
 * Add news count config in news overview
 *
 * @author dr
 * @created Tue, 21 Mar 2017 11:46:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170321114600
 */
class Migration20170321114600 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'Add news count config in news overview';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'news_anzahl_uebersicht',
            '10',
            113,
            'Anzahl News in der Übersicht',
            'number',
            30,
            (object)[
                'cBeschreibung' =>
                    'Wieviele News sollen standardmäßig in der Newsübersicht angezeigt werden? 0 = standard'
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('news_anzahl_uebersicht');
    }
}
