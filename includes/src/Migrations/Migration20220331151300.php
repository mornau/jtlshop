<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Language\LanguageHelper;
use JTL\Link\Admin\LinkAdmin;
use JTL\Shop;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20220331151300
 */
class Migration20220331151300 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add review page';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $linkGroup = $this->getDB()->getSingleObject(
            "SELECT kLinkgruppe 
                FROM tlinkgruppe
                WHERE cTemplatename = 'hidden'"
        );
        if ($linkGroup === null) {
            return;
        }

        $linkAdmin = new LinkAdmin($this->getDB(), Shop::Container()->getCache());

        $link = [
            'kLinkgruppe'    => (int)$linkGroup->kLinkgruppe,
            'kLink'          => 0,
            'kPlugin'        => 0,
            'cName'          => 'Bewertung',
            'nLinkart'       => 3,
            'nSpezialseite'  => LINKTYP_BEWERTUNG,
            'cKundengruppen' => ['-1'],
            'bIsActive'      => 1,
            'bSSL'           => 0,
            'nSort'          => '',
            'cIdentifier'    => ''
        ];
        foreach (LanguageHelper::getAllLanguages() as $language) {
            $code                              = $language->getIso();
            $link['cName_' . $code]            = '';
            $link['cSeo_' . $code]             = '';
            $link['cTitle_' . $code]           = '';
            $link['cContent_' . $code]         = '';
            $link['cMetaTitle_' . $code]       = '';
            $link['cMetaKeywords_' . $code]    = '';
            $link['cMetaDescription_' . $code] = '';
        }

        if ($this->getDB()->select('tlink', 'nLinkart', LINKTYP_BEWERTUNG) === null) {
            $linkAdmin->createOrUpdateLink($link);
        }
        $this->execute(
            "INSERT INTO `tspezialseite` (`kPlugin`, `cName`, `cDateiname`, `nLinkart`, `nSort`) 
                VALUES ('0', 'Bewertung', 'bewertung.php', 39, 39);"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
