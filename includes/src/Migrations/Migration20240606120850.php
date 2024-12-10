<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20240606120850
 */
class Migration20240606120850 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'tnt';
    }

    public function getDescription(): string
    {
        return 'Remove setting global_versandklasse_anzeigen';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->removeConfig('global_versandklasse_anzeigen');
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function down(): void
    {
        $this->setConfig(
            configName: 'global_versandklasse_anzeigen',
            configValue: 'Y',
            configSectionID: \CONF_GLOBAL,
            externalName: 'Versandklasse im Versandtext anzeigen',
            inputType: 'selectbox',
            sort: 260,
            additionalProperties: (object)[
                'cBeschreibung' => 'Soll bei jedem Artikel die Versandklasse mit im Versandtext angezeigt werden,'
                    . ' falls die Versandklasse nicht Standard ist?',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein'
                ],
            ]
        );
    }
}
