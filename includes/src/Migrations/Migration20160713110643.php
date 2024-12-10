<?php

/**
 * Configuration for vCard upload
 *
 * @author root
 * @created Wed, 13 Jul 2016 11:06:43 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160713110643
 */
class Migration20160713110643 extends Migration implements IMigration
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
            'kundenregistrierung_vcardupload',
            'Y',
            \CONF_KUNDEN,
            'vCard Upload erlauben',
            'selectbox',
            240,
            (object)[
                'cBeschreibung' => 'Erlaubt dem Kunden bei der Registrierung das Hochladen einer elektronischen ' .
                    'Visitenkarte (vCard) im vcf-Format.',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein',
                ]
            ]
        );

        $this->setLocalization('ger', 'account data', 'uploadVCard', 'vCard hochladen');
        $this->setLocalization('eng', 'account data', 'uploadVCard', 'Upload vCard');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('kundenregistrierung_vcardupload');
        $this->execute("DELETE FROM `tsprachwerte` WHERE cName = 'uploadVCard'");
    }
}
