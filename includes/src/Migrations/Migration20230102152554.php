<?php

/**
 * Set min max values to float type.
 *
 * @author fp
 * @created Mon, 02 Jan 2023 15:25:54 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20230102152554
 */
class Migration20230102152554 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Set min max values to float type.';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->db->update(
            'tplugineinstellungenconf',
            ['cName', 'cInputTyp'],
            ['Mindestbestellwert', 'zahl'],
            (object)[
                'cInputTyp' => 'kommazahl'
            ]
        );
        $this->db->update(
            'tplugineinstellungenconf',
            ['cName', 'cInputTyp'],
            ['Maximaler Bestellwert', 'zahl'],
            (object)[
                'cInputTyp' => 'kommazahl'
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->db->update(
            'tplugineinstellungenconf',
            ['cName', 'cInputTyp'],
            ['Mindestbestellwert', 'kommazahl'],
            (object)[
                'cInputTyp' => 'zahl'
            ]
        );
        $this->db->update(
            'tplugineinstellungenconf',
            ['cName', 'cInputTyp'],
            ['Maximaler Bestellwert', 'kommazahl'],
            (object)[
                'cInputTyp' => 'zahl'
            ]
        );
    }
}
