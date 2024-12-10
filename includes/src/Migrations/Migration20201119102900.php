<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20201119102900
 */
class Migration20201119102900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Fix warenkorbpers_nutzen setting';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->getDB()->update(
            'teinstellungenconf',
            'cWertName',
            'warenkorbpers_nutzen',
            (object)[
                'kEinstellungenSektion' => \CONF_KAUFABWICKLUNG,
                'nSort'                 => 275,
                'nModul'                => 0
            ]
        );
        $this->getDB()->update(
            'teinstellungen',
            'cName',
            'warenkorbpers_nutzen',
            (object)['kEinstellungenSektion' => \CONF_KAUFABWICKLUNG]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->getDB()->update(
            'teinstellungenconf',
            'cWertName',
            'warenkorbpers_nutzen',
            (object)[
                'kEinstellungenSektion' => \CONF_GLOBAL,
                'nSort'                 => 810,
                'nModul'                => 1
            ]
        );
        $this->getDB()->update(
            'teinstellungen',
            'cName',
            'warenkorbpers_nutzen',
            (object)['kEinstellungenSektion' => \CONF_GLOBAL]
        );
    }
}
