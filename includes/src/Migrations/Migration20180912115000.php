<?php

/**
 * Sitemap settings
 *
 * @author fm
 * @created Wed, 12 Sep 2018 11:50:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180912115000
 */
class Migration20180912115000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Sitemap settings';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'sitemap_images_categories',
            'N',
            \CONF_SITEMAP,
            'Kategoriebilder anzeigen',
            'selectbox',
            121,
            (object)[
                'cBeschreibung' => 'Sollen Kategoriebilder mit in die Sitemap aufgenommen werden?',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein',
                ],
            ],
            true
        );
        $this->setConfig(
            'sitemap_images_manufacturers',
            'N',
            \CONF_SITEMAP,
            'Herstellerbilder anzeigen',
            'selectbox',
            122,
            (object)[
                'cBeschreibung' => 'Sollen Herstellerbilder mit in die Sitemap aufgenommen werden?',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein',
                ],
            ],
            true
        );
        $this->setConfig(
            'sitemap_images_newscategory_items',
            'N',
            \CONF_SITEMAP,
            'Newskategoriebilder anzeigen',
            'selectbox',
            123,
            (object)[
                'cBeschreibung' => 'Sollen Newskategoriebilder mit in die Sitemap aufgenommen werden?',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein',
                ],
            ],
            true
        );
        $this->setConfig(
            'sitemap_images_news_items',
            'N',
            \CONF_SITEMAP,
            'Newsbeitragsbilder anzeigen',
            'selectbox',
            124,
            (object)[
                'cBeschreibung' => 'Sollen Newsbeitragsbilder mit in die Sitemap aufgenommen werden?',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein',
                ],
            ],
            true
        );
        $this->setConfig(
            'sitemap_images_attributes',
            'N',
            \CONF_SITEMAP,
            'Merkmalbilder anzeigen',
            'selectbox',
            125,
            (object)[
                'cBeschreibung' => 'Sollen Merkmalbilder mit in die Sitemap aufgenommen werden?',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein',
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
        $this->removeConfig('sitemap_images_categories');
        $this->removeConfig('sitemap_images_manufacturers');
        $this->removeConfig('sitemap_images_newscategory_items');
        $this->removeConfig('sitemap_images_news_items');
        $this->removeConfig('sitemap_images_attributes');
    }
}
