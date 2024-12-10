<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210527115400
 */
class Migration20210527115400 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Remove unused vari image setting';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->removeConfig('artikeldetails_variationskombikind_bildvorschau');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setConfig(
            'artikeldetails_variationskombikind_bildvorschau',
            'N',
            \CONF_ARTIKELDETAILS,
            'Bildervorschau von Variationskombikinder anzeigen',
            'selectbox',
            499,
            (object)[
                'cBeschreibung' => 'Soll in der Artikel?bersicht die Vorschaubilder von Variationskombikinder (falls '
                    . 'vorhanden) angezeigt werden?',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein'
                ]
            ]
        );
    }
}
