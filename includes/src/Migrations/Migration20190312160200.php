<?php

/**
 * add cron config
 *
 * @author fm
 * @created Tue, 12 Mar 2019 16:02:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190312160200
 */
class Migration20190312160200 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add cron config';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'cron_type',
            'N',
            \CONF_CRON,
            'Pseudo-Cron Methode',
            'selectbox',
            1,
            (object)[
                'cBeschreibung' => 'Welche Methode soll verwendet werden?',
                'inputOptions'  => [
                    'N'   => 'keine',
                    'tpl' => 'Template-gesteuert',
                    's2s' => 'Curl Server-to-Server',
                ],
            ],
            true
        );
        $this->setConfig(
            'cron_freq',
            '10',
            \CONF_CRON,
            'Server-to-Server jeden X-ten Aufruf starten',
            'number',
            2,
            (object)[
                'cBeschreibung'     => 'Starte bei jedem x-ten Seitenaufruf den Pseudo-Cron',
                'nStandardAnzeigen' => 0
            ],
            true
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('cron_type');
        $this->removeConfig('cron_freq');
    }
}
