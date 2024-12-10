<?php

/**
 * Remove image scale setting
 *
 * @author mh
 * @created Thu, 30 Apr 2020 12:30:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200430113000
 */
class Migration20200430113000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Remove image scale setting';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->removeConfig('bilder_skalieren');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setConfig(
            'bilder_skalieren',
            'N',
            \CONF_BILDER,
            'Bilder hochskalieren?',
            'selectbox',
            580,
            (object)[
                'cBeschreibung' => 'Zu kleine Bilder werden automatisch hochskaliert',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein'
                ]
            ]
        );
    }
}
