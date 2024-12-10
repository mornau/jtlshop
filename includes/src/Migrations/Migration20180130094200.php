<?php

/**
 * Add products per page options
 *
 * @author fm
 * @created Thu, 30 Jan 2018 09:42:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Shopsetting;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180130094200
 */
class Migration20180130094200 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add products per page options';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'products_per_page_list',
            '10,20,30,40,50',
            \CONF_ARTIKELUEBERSICHT,
            'Auswahloptionen Artikel pro Seite in Listenansicht',
            'text',
            845,
            (object)[
                'cBeschreibung' => 'Mit Komma getrennt, -1 für alle',
            ]
        );
        $this->setConfig(
            'products_per_page_gallery',
            '9,12,15,18,21',
            \CONF_ARTIKELUEBERSICHT,
            'Auswahloptionen Artikel pro Seite in Gallerieansicht',
            'text',
            855,
            (object)[
                'cBeschreibung' => 'Mit Komma getrennt, -1 für alle',
            ]
        );
        Shopsetting::getInstance()->reset();
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('products_per_page_list');
        $this->removeConfig('products_per_page_gallery');
    }
}
