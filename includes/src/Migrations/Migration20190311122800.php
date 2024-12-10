<?php

/**
 * remove global html entity config
 *
 * @author fm
 * @created Mon, 11 Mar 2019 12:28:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190311122800
 */
class Migration20190311122800 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Remove global html entity config';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->removeConfig('global_artikelname_htmlentities');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setConfig(
            'global_artikelname_htmlentities',
            'N',
            \CONF_GLOBAL,
            'HTML-Code Umwandlung bei Artikelnamen',
            'selectbox',
            280,
            (object)[
                'cBeschreibung' => 'Sollen Sonderzeichen im Artikelnamen in HTML Entities umgewandelt werden',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein',
                ],
            ]
        );
    }
}
