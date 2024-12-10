<?php

/**
 * Remove global attribute filter box option
 *
 * @author fm
 * @created Thu, 08 Feb 2018 16:21:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180208162100
 */
class Migration20180208162100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Remove global attribute filter box option';
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setConfig(
            'allgemein_globalmerkmalfilter_benutzen',
            'Y',
            \CONF_NAVIGATIONSFILTER,
            'Globale Merkmalbox benutzen',
            'selectbox',
            110,
            (object)[
                'cBeschreibung' => 'Sollen die globalen Merkmale in einer Box angezeigt werden?',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein'
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->removeConfig('allgemein_globalmerkmalfilter_benutzen');
    }
}
