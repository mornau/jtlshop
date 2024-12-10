<?php

/**
 * adds setting for GTIN display
 *
 * @author ms
 * @created Fri, 14 Feb 2020 10:00:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200214100000
 */
class Migration20200214100000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Add setting for GTIN display';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'gtin_display',
            'always',
            \CONF_ARTIKELDETAILS,
            'GTIN anzeigen',
            'selectbox',
            499,
            (object)[
                'cBeschreibung' => 'Hier legen Sie fest, ob die GTIN angezeigt wird.',
                'inputOptions'  => [
                    'N'       => 'Nein',
                    'details' => 'Ja, auf der Artikeldetailseite',
                    'lists'   => 'Ja, in Listen',
                    'always'  => 'Ja, auf der Artikeldetailseite und in den Listen',
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('gtin_display');
    }
}
