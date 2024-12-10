<?php

/**
 * Add option to switch sitemap ping to Google and Bing on or off
 *
 * @author dr
 * @created Wed, 21 Sep 2016 10:32:17 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160921103217
 */
class Migration20160921103217 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'Add option to switch sitemap ping to Google and Bing on or off';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'sitemap_google_ping',
            'N',
            \CONF_SITEMAP,
            'Sitemap an Google und Bing &uuml;bermitteln nach Export',
            'selectbox',
            180,
            (object)[
                'cBeschreibung' => 'Soll nach dem erfolgreichen Export der sitemap.xml und der sitemap_index.xml ein ' .
                    'Ping an Google und Bing durchgef&uuml;hrt werden, so dass die Website schnellstm&ouml;glich ' .
                    'gecrawlt wird?',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein',
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('sitemap_google_ping');
    }
}
