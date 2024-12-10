<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20220609113400
 */
class Migration20220609113400 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Routing options';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'configgroup_' . \CONF_GLOBAL . '_routing',
            'Routing',
            \CONF_GLOBAL,
            'Routing',
            null,
            800,
            (object)['cConf' => 'N']
        );
        $this->setConfig(
            'routing_scheme',
            'F',
            \CONF_GLOBAL,
            'Routing-Schema',
            'selectbox',
            801,
            (object)[
                'cBeschreibung' => '',
                'inputOptions'  => [
                    'F'  => 'Flach (Standard)',
                    'L'  => 'Mit Locale',
                    'P'  => 'Mit Präfix',
                    'LP' => 'Mit Locale und Präfix',
                ]
            ]
        );
        $this->setConfig(
            'routing_default_language',
            'F',
            \CONF_GLOBAL,
            'Routing-Schema für Standardsprache',
            'selectbox',
            802,
            (object)[
                'cBeschreibung' => '',
                'inputOptions'  => [
                    'F'  => 'Flach (Standard)',
                    'L'  => 'Mit Locale',
                    'P'  => 'Mit Präfix',
                    'LP' => 'Mit Locale und Präfix',
                ]
            ]
        );
        $this->setConfig(
            'routing_duplicates',
            'F',
            \CONF_GLOBAL,
            'Behandlung von Nicht-Standardrouten',
            'selectbox',
            803,
            (object)[
                'cBeschreibung' => '',
                'inputOptions'  => [
                    'R' => '301-Redirect auf Standardroute',
                    'I' => 'Belassen',
                    'F' => '404-Fehler',
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('configgroup_' . \CONF_GLOBAL . '_routing');
        $this->removeConfig('routing_scheme');
        $this->removeConfig('routing_default_language');
        $this->removeConfig('routing_duplicates');
    }
}
