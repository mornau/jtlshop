<?php

/**
 * adds child item bulk price setting
 *
 * @author ms
 * @created Fri, 24 Jan 2020 11:34:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200124113400
 */
class Migration20200124113400 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Add child item bulk price setting';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'general_child_item_bulk_pricing',
            'N',
            \CONF_KAUFABWICKLUNG,
            'Variationsübergreifende Staffelpreise',
            'selectbox',
            280,
            (object)[
                'cBeschreibung' => 'Für Staffelpreisgrenzen im Warenkorb zählen alle Kindartikel einer VarKombi.',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein'
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('general_child_item_bulk_pricing');
    }
}
