<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210223095300
 */
class Migration20210223095300 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add cart total weight setting';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'warenkorb_gesamtgewicht_anzeigen',
            'N',
            \CONF_KAUFABWICKLUNG,
            'Gesamtgewicht alle Artikel auf Warenkorb-Seite anzeigen.',
            'selectbox',
            265,
            (object)[
                'cBeschreibung' => 'Gesamtgewicht alle Artikel auf Warenkorb-Seite anzeigen.',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein',
                ],
            ]
        );

        $this->setLocalization(
            'ger',
            'basket',
            'cartTotalWeight',
            'Das Gesamtgewicht aller Artikel im Warenkorb beträgt %s kg.'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'cartTotalWeight',
            'The total weight of all items in the basket is %s kg.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('warenkorb_gesamtgewicht_anzeigen');

        $this->removeLocalization('cartTotalWeight', 'basket');
    }
}
