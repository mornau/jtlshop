<?php

/**
 * remove_google_analytics
 *
 * @author mh
 * @created Thu, 20 Dec 2018 10:42:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20181220104200
 */
class Migration20181220104200 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Remove Google Analytics';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->removeConfig('global_google_analytics_id');
        $this->removeConfig('global_google_ecommerce');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setConfig(
            'global_google_analytics_id',
            '',
            \CONF_GLOBAL,
            'Google Analytics ID',
            'text',
            520,
            (object)[
                'cBeschreibung' => 'Falls Sie einen Google Analytics Account haben, ' .
                    'tragen Sie hier Ihre ID ein (z.B. UA-xxxxxxx-x)'
            ]
        );
        $this->setConfig(
            'global_google_ecommerce',
            0,
            \CONF_GLOBAL,
            'Google Analytics eCommerce Erweiterung nutzen',
            'selectbox',
            520,
            (object)[
                'cBeschreibung' => 'M&ouml;chten Sie, dass Google alle Ihre Verk&auml;ufe trackt?',
                'inputOptions'  => [
                    0 => 'Nein',
                    1 => 'Ja'
                ]
            ]
        );
    }
}
