<?php

/**
 * Add SEO URLs
 *
 * @author fm
 * @created Thu, 16 Nov 2017 11:47:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Helpers\Seo;
use JTL\Language\LanguageHelper;
use JTL\Language\LanguageModel;
use JTL\Update\IMigration;
use JTL\Update\Migration;
use stdClass;

/**
 * Class Migration20171116114700
 */
class Migration20171116114700 extends Migration implements IMigration
{
    /**
     * @var int
     */
    private int $hiddenLinkGroupID = 0;

    /**
     * @var LanguageModel[]
     */
    private array $languages = [];

    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add SEO-URLs';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $hiddenLinkGroup = $this->getDB()->select('tlinkgruppe', 'cName', 'hidden');
        if ($hiddenLinkGroup === null) {
            $hiddenLinkGroup                = new stdClass();
            $hiddenLinkGroup->cName         = 'hidden';
            $hiddenLinkGroup->cTemplatename = 'hidden';
            $this->hiddenLinkGroupID        = $this->getDB()->insert('tlinkgruppe', $hiddenLinkGroup);
        } else {
            $this->hiddenLinkGroupID = (int)$hiddenLinkGroup->kLinkgruppe;
        }
        $this->languages = LanguageHelper::getAllLanguages();

        $this->createSeo(LINKTYP_WARENKORB, 'Warenkorb', 'Warenkorb', 'Cart');
        $this->createSeo(LINKTYP_BESTELLVORGANG, 'Bestellvorgang', 'Bestellvorgang', 'Checkout');
        $this->createSeo(LINKTYP_BESTELLABSCHLUSS, 'Bestellabschluss', 'Bestellabschluss', 'Checkout-Complete');
        $this->createSeo(LINKTYP_REGISTRIEREN, 'Registrieren', 'Registrieren', 'Register');
        $this->createSeo(LINKTYP_LOGIN, 'Konto', 'Konto', 'Account');
        $this->createSeo(LINKTYP_PASSWORD_VERGESSEN, 'Passwort vergessen', 'Passwort-vergessen', 'Forgot-password');
        $this->createSeo(LINKTYP_WUNSCHLISTE, 'Wunschliste', 'Wunschliste', 'Wishlist');
        $this->createSeo(LINKTYP_VERGLEICHSLISTE, 'Vergleichsliste', 'Vergleichsliste', 'Comparelist');
        $this->createSeo(LINKTYP_NEWS, 'News', 'News', 'Blog');
        $this->createSeo(LINKTYP_NEWSLETTER, 'Newsletter', 'Newsletter', 'Newsletter');
    }

    /**
     * @param int    $linkType
     * @param string $cmsName
     * @param string $seoGER
     * @param string $seoENG
     */
    private function createSeo(int $linkType, string $cmsName, string $seoGER, string $seoENG): void
    {
        $links = $this->fetchOne(
            "SELECT tlink.kLink, tseo.cSeo, tsprache.cISO 
                FROM tlink
                LEFT JOIN tseo
                  ON tseo.cKey = 'kLink' AND tseo.kKey = tlink.kLink
                LEFT JOIN tsprache
                  ON tsprache.kSprache = tseo.kSprache
                WHERE tlink.nLinkart = " . $linkType
        );
        if ($links === null || $links->cSeo === null) {
            $link = new stdClass();
            if ($links === null) {
                $link->kVaterLink     = 0;
                $link->cName          = $cmsName;
                $link->nLinkart       = $linkType;
                $link->bIsActive      = 1;
                $link->kLinkgruppe    = $this->hiddenLinkGroupID;
                $link->cKundengruppen = 'NULL';
                $link->kLink          = $this->getDB()->insert('tlink', $link);
            } else {
                $link->kLink = (int)$links->kLink;
            }

            if ($link->kLink > 0) {
                $linkLanguage = $this->fetchOne('SELECT * FROM tlinksprache WHERE kLink = ' . $link->kLink);

                $seo       = new stdClass();
                $seo->kKey = $link->kLink;
                $seo->cKey = 'kLink';

                $langObj             = new stdClass();
                $langObj->kLink      = $link->kLink;
                $langObj->cName      = $cmsName;
                $langObj->cTitle     = $cmsName;
                $langObj->cMetaTitle = $cmsName;
                $langObj->cContent   = '';

                foreach ($this->languages as $language) {
                    $seo->kSprache = $language->kSprache;
                    if ($language->cISO === 'ger') {
                        $seo->cSeo = Seo::checkSeo(Seo::getSeo($seoGER));
                        $this->getDB()->insert('tseo', $seo);
                        if ($linkLanguage === null) {
                            $langObj->cISOSprache = $language->cISO;
                            $langObj->cSeo        = $seo->cSeo;
                            $this->getDB()->insert('tlinksprache', $langObj);
                        }
                    } elseif ($language->cISO === 'eng') {
                        $seo->cSeo = Seo::checkSeo(Seo::getSeo($seoENG));
                        $this->getDB()->insert('tseo', $seo);
                        if ($linkLanguage === null) {
                            $langObj->cISOSprache = $language->cISO;
                            $langObj->cSeo        = $seo->cSeo;
                            $this->getDB()->insert('tlinksprache', $langObj);
                        }
                    }
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
