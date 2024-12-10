<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20240610091030
 */
class Migration20240610091030 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'tnt';
    }

    public function getDescription(): string
    {
        return 'Add setting for HAN display';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'han_display',
            'always',
            \CONF_ARTIKELDETAILS,
            'HAN anzeigen',
            'selectbox',
            499,
            (object)[
                'cBeschreibung' => 'Hier legen Sie fest, ob die HAN angezeigt wird.',
                'inputOptions'  => [
                    'N'       => 'Nein',
                    'details' => 'Ja, auf der Artikeldetailseite',
                    'lists'   => 'Ja, in Listen',
                    'always'  => 'Ja, auf der Artikeldetailseite und in den Listen',
                ]
            ]
        );
        $this->setLocalization(
            'ger',
            'global',
            'han',
            'HAN'
        );
        $this->setLocalization(
            'eng',
            'global',
            'han',
            'HAN'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('han_display');
        $this->removeLocalization(
            'han',
            'global',
        );
    }
}
