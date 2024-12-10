<?php

/**
 * Add product filter config
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180109100600
 */
class Migration20180109100600 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add product filter config';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'tag_filter_type',
            'A',
            \CONF_NAVIGATIONSFILTER,
            'Typ des Tagfilters',
            'selectbox',
            176,
            (object)[
                'cBeschreibung' => 'Erlaubt Verorderung oder Verundung der Filterwerte',
                'inputOptions'  => [
                    'A' => 'Verundung',
                    'O' => 'Veroderung'
                ]
            ]
        );
        $this->setConfig(
            'category_filter_type',
            'A',
            \CONF_NAVIGATIONSFILTER,
            'Typ des Kategoriefilters',
            'selectbox',
            148,
            (object)[
                'cBeschreibung' => 'Erlaubt Verorderung oder Verundung der Filterwerte',
                'inputOptions'  => [
                    'A' => 'Verundung',
                    'O' => 'Veroderung'
                ]
            ]
        );
        $this->setConfig(
            'manufacturer_filter_type',
            'A',
            \CONF_NAVIGATIONSFILTER,
            'Typ des Herstellerfilters',
            'selectbox',
            121,
            (object)[
                'cBeschreibung' => 'Erlaubt Verorderung oder Verundung der Filterwerte',
                'inputOptions'  => [
                    'A' => 'Verundung',
                    'O' => 'Veroderung'
                ]
            ]
        );
        $this->setConfig(
            'search_special_filter_type',
            'A',
            \CONF_NAVIGATIONSFILTER,
            'Typ des Suchspezialfilters',
            'selectbox',
            141,
            (object)[
                'cBeschreibung' => 'Erlaubt Verorderung oder Verundung der Filterwerte',
                'inputOptions'  => [
                    'A' => 'Verundung',
                    'O' => 'Veroderung'
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('tag_filter_type');
        $this->removeConfig('category_filter_type');
        $this->removeConfig('manufacturer_filter_type');
        $this->removeConfig('search_special_filter_type');
    }
}
