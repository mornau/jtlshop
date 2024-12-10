<?php

/**
 * update_order_confirmation_mail_item_numbers
 *
 * @author sh
 * @created Mon, 29 Feb 2016 11:18:11 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160229111811
 */
class Migration20160229111811 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'sh';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'UPDATE temailvorlagesprache
                SET `cContentHtml` = REPLACE(`cContentHtml`,"{$Position->cArtNr}","{$Position->Artikel->cArtNr}"),
                `cContentText` = REPLACE(`cContentText`,"{$Position->cArtNr}","{$Position->Artikel->cArtNr}")
                WHERE kEmailvorlage = (SELECT kEmailvorlage
                                            FROM temailvorlage
                                            WHERE cModulId = "core_jtl_bestellbestaetigung")'
        );
        $this->execute(
            'UPDATE temailvorlagespracheoriginal
                SET `cContentHtml` = REPLACE(`cContentHtml`,"{$Position->cArtNr}","{$Position->Artikel->cArtNr}"),
                `cContentText` = REPLACE(`cContentText`,"{$Position->cArtNr}","{$Position->Artikel->cArtNr}")
                WHERE kEmailvorlage = (SELECT kEmailvorlage
                                            FROM temailvorlage
                                            WHERE cModulId = "core_jtl_bestellbestaetigung")'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'UPDATE temailvorlagesprache
                SET `cContentHtml` = REPLACE(`cContentHtml`,"{$Position->Artikel->cArtNr}","{$Position->cArtNr}"),
 `cContentText` = REPLACE(`cContentText`,"{$Position->Artikel->cArtNr}","{$Position->cArtNr}")
                WHERE kEmailvorlage = (SELECT kEmailvorlage
                                            FROM temailvorlage
                                            WHERE cModulId = "core_jtl_bestellbestaetigung")'
        );
        $this->execute(
            'UPDATE temailvorlagespracheoriginal
                SET `cContentHtml`=REPLACE(`cContentHtml`,"{$Position->Artikel->cArtNr}","{$Position->cArtNr}"),
 `cContentText` = REPLACE(`cContentText`,"{$Position->Artikel->cArtNr}","{$Position->cArtNr}")
                WHERE kEmailvorlage = (SELECT kEmailvorlage
                                            FROM temailvorlage
                                            WHERE cModulId = "core_jtl_bestellbestaetigung")'
        );
    }
}
