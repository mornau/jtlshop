<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20211130154000
 */
class Migration20211130154000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add hide partlist setting';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'bestellvorgang_partlist',
            'Y',
            \CONF_KAUFABWICKLUNG,
            'Stücklistenkomponenten anzeigen',
            'selectbox',
            505,
            (object)[
                'cBeschreibung' => 'Sollen die Stücklistenkomponenten in Warenkorb und Checkout angezeigt werden?',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein',
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('bestellvorgang_partlist');
    }
}
