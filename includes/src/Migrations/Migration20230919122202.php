<?php

/**
 * adds setting for shelf-life expiration date
 *
 * @author ms
 * @created Tue, 19 Sep 2023 12:22:02 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20230919122202
 */
class Migration20230919122202 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'adds setting for shelf-life expiration date';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'show_shelf_life_expiration_date',
            'Y',
            \CONF_ARTIKELDETAILS,
            'Mindesthaltbarkeitsdatum (MHD) anzeigen',
            'selectbox',
            498,
            (object)[
                'cBeschreibung' => 'Hier legen Sie fest, ob Kunden das Mindesthaltbarkeitsdatum '
                    . 'von Artikeln im Onlineshop sehen können oder nicht. '
                    . 'Dies betrifft alle Stellen, an denen das MHD standardmäßig angezeigt wird.',
                'inputOptions'  => [
                    'N' => 'Nein',
                    'Y' => 'Ja'
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('show_shelf_life_expiration_date');
    }
}
