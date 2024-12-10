<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210217124900
 */
class Migration20210217124900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add show comparelist setting';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'vergleichsliste_anzeigen',
            'Y',
            \CONF_VERGLEICHSLISTE,
            'Vergleichliste nutzen',
            'selectbox',
            105,
            (object)[
                'cBeschreibung' => 'Vergleichliste nutzen?',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein'
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('vergleichsliste_anzeigen');
    }
}
