<?php

/**
 * @author fm
 * @created Fri, 27 Sep 2019 15:49:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190927154900
 */
class Migration20190927154900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add samesite cookie option';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'global_cookie_samesite',
            'S',
            \CONF_GLOBAL,
            'Samesite',
            'selectbox',
            1516,
            (object)[
                'cBeschreibung' => 'Samesite-Header für Cookies',
                'inputOptions'  => [
                    'S'      => 'Standard',
                    'N'      => 'Deaktiviert',
                    'Lax'    => 'Lax',
                    'Strict' => 'Strict',
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('global_cookie_samesite');
    }
}
