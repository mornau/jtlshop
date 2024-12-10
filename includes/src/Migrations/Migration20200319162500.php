<?php

/**
 * Remove cron type tpl
 *
 * @author fm
 * @created Thu, 19 Mar 2020 16:25:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200319162500
 */
class Migration20200319162500 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Remove cron type tpl';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $useCron = $this->fetchOne(
            "SELECT ttemplate.name, ttemplateeinstellungen.cWert
                FROM ttemplateeinstellungen
                JOIN ttemplate USING (cTemplate)
                WHERE ttemplateeinstellungen.cName = 'use_cron';"
        );
        $this->setConfig(
            'cron_type',
            ($useCron->cWert ?? 'N') === 'N' ? 'N' : 's2s',
            \CONF_CRON,
            'Pseudo-Cron Methode',
            'selectbox',
            1,
            (object)[
                'cBeschreibung' => 'Welche Methode soll verwendet werden?',
                'inputOptions'  => [
                    'N'   => 'keine',
                    's2s' => 'Curl Server-to-Server',
                ],
            ],
            true
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
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
    }
}
