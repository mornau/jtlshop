<?php

/**
 * add option for xselling show parent
 *
 * @author fp
 * @created Tue, 14 Jun 2016 15:25:25 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160614152525
 */
class Migration20160614152525 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'artikeldetails_xselling_kauf_parent',
            'N',
            \CONF_ARTIKELDETAILS,
            'Immer Vaterartikel anzeigen',
            'selectbox',
            230,
            (object)[
                'cBeschreibung' => 'Es werden immer die zugeh&ouml;rigen Vaterartikel angezeigt, auch wenn ' .
                    'tats&auml;chlich Kindartikel gekauft wurden.',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein',
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('artikeldetails_xselling_kauf_parent');
    }
}
