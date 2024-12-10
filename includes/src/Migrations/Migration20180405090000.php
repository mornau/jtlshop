<?php

/**
 * remove-shopinfo-menu-point
 *
 * @author ms
 * @created Thu, 05 Apr 2018 09:00:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180405090000
 */
class Migration20180405090000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Remove maintenance hint setting';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->removeConfig('wartungsmodus_hinweis');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setConfig(
            'wartungsmodus_hinweis',
            'Dieser Shop befindet sich im Wartungsmodus.',
            \CONF_GLOBAL,
            'Wartungsmodus Hinweis',
            'text',
            1020,
            (object)[
                'cBeschreibung' => 'Dieser Hinweis wird Besuchern angezeigt, wenn der Shop im Wartungsmodus ist. ' .
                    'Achtung: Im Evo-Template steuern Sie diesen Text über die Sprachvariable maintenanceModeActive.',
            ]
        );
    }
}
