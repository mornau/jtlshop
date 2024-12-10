<?php

declare(strict_types=1);

namespace JTL\Link\Admin;

use Illuminate\Support\Collection;
use JTL\Backend\Revision;
use JTL\Cache\JTLCacheInterface;
use JTL\DB\DbInterface;
use JTL\Helpers\GeneralObject;
use JTL\Helpers\Seo;
use JTL\Language\LanguageHelper;
use JTL\Link\Link;
use JTL\Shop;
use stdClass;

/**
 * Class LinkAdmin
 * @package JTL\Link\Admin
 */
final class LinkAdmin
{
    public const ERROR_LINK_ALREADY_EXISTS = 1;

    public const ERROR_LINK_NOT_FOUND = 2;

    public const ERROR_LINK_GROUP_NOT_FOUND = 3;

    /**
     * LinkAdmin constructor.
     * @param DbInterface       $db
     * @param JTLCacheInterface $cache
     */
    public function __construct(private readonly DbInterface $db, private readonly JTLCacheInterface $cache)
    {
    }

    /**
     * @param int   $linkType
     * @param int   $linkID
     * @param int[] $customerGroups
     * @return bool
     */
    public static function isDuplicateSpecialLink(int $linkType, int $linkID, array $customerGroups): bool
    {
        $link = new Link(Shop::Container()->getDB());
        $link->setCustomerGroups($customerGroups);
        $link->setLinkType($linkType);
        $link->setID($linkID);

        return $link->hasDuplicateSpecialLink();
    }

    /**
     * @param int $id
     * @return stdClass[]
     * @todo!!! used in template
     */
    public function getMissingLinkTranslations(int $id): array
    {
        return $this->db->getObjects(
            'SELECT tlink.*, tsprache.*
                FROM tlink
                JOIN tsprache
                LEFT JOIN tlinksprache
                    ON tlink.kLink = tlinksprache.kLink
                    AND tlinksprache.cISOSprache = tsprache.cISO
                LEFT JOIN tsprache t2
                    ON t2.cISO = tlinksprache.cISOSprache
                    AND t2.cISO = tsprache.cISO
                WHERE t2.cISO IS NULL
                    AND tsprache.active = 1
                    AND tlink.reference = 0
                    AND tlink.kLink = :lid',
            ['lid' => $id]
        );
    }

    /**
     * @return Collection
     */
    public function getUntranslatedPageIDs(): Collection
    {
        return $this->db->getCollection(
            'SELECT DISTINCT tlink.kLink AS id
                FROM tlink
                JOIN tsprache
                LEFT JOIN tlinksprache loc
                    ON tlink.kLink = loc.kLink
                    AND loc.cISOSprache = tsprache.cISO
                LEFT JOIN tsprache t2
                    ON t2.cISO = loc.cISOSprache
                    AND t2.cISO = tsprache.cISO
                WHERE t2.cISO IS NULL
                    AND tlink.reference = 0'
        )->map(static function (stdClass $e): int {
            return (int)$e->id;
        });
    }

    /**
     * @return Collection
     */
    public function getMissingSystemPages(): Collection
    {
        $all          = $this->db->getCollection(
            'SELECT kLink, nLinkart
                FROM tlink'
        )->map(static function (stdClass $link): stdClass {
            $link->kLink    = (int)$link->kLink;
            $link->nLinkart = (int)$link->nLinkart;

            return $link;
        });
        $missingTypes = new Collection();
        foreach ($this->getSpecialPageTypes() as $specialPage) {
            if (
                \in_array(
                    $specialPage->nLinkart,
                    [
                        \LINKTYP_NEWSLETTERARCHIV,
                        \LINKTYP_GRATISGESCHENK,
                        \LINKTYP_AUSWAHLASSISTENT,
                        \LINKTYP_BATTERIEGESETZ_HINWEISE
                    ],
                    true
                )
            ) {
                continue;
            }
            $hit = $all->first(static function ($val, $key) use ($specialPage): bool {
                return $val->nLinkart === $specialPage->nLinkart;
            });
            if ($hit === null) {
                $missingTypes->add((object)['nLinkart' => $specialPage->nLinkart, 'cName' => $specialPage->cName]);
            }
        }

        return $missingTypes->unique();
    }

    /**
     * @param int $id
     * @return stdClass[]
     */
    public function getMissingLinkGroupTranslations(int $id): array
    {
        return $this->db->getObjects(
            'SELECT tlinkgruppe.*, tsprache.* 
                FROM tlinkgruppe
                JOIN tsprache
                LEFT JOIN tlinkgruppesprache
                    ON tlinkgruppe.kLinkgruppe = tlinkgruppesprache.kLinkgruppe
                    AND tlinkgruppesprache.cISOSprache = tsprache.cISO
                LEFT JOIN tsprache t2
                    ON t2.cISO = tlinkgruppesprache.cISOSprache
                    AND t2.cISO = tsprache.cISO
                WHERE t2.cISO IS NULL
                    AND tsprache.active = 1
                    AND tlinkgruppe.kLinkgruppe = :lgid',
            ['lgid' => $id]
        );
    }

    /**
     * @param array $post
     * @return stdClass
     */
    private function createLinkData(array $post): stdClass
    {
        $link                     = new stdClass();
        $link->kLink              = (int)$post['kLink'];
        $link->kPlugin            = (int)$post['kPlugin'];
        $link->cName              = $this->specialChars($post['cName']);
        $link->nLinkart           = (int)$post['nLinkart'];
        $link->nSort              = !empty($post['nSort']) ? $post['nSort'] : 0;
        $link->bSSL               = (int)$post['bSSL'];
        $link->bIsActive          = 1;
        $link->cSichtbarNachLogin = 'N';
        $link->cNoFollow          = 'N';
        $link->cIdentifier        = $post['cIdentifier'];
        $link->bIsFluid           = (isset($post['bIsFluid']) && $post['bIsFluid'] === '1') ? 1 : 0;
        $link->target             = $post['linkTarget'] ?? '_self';
        if (GeneralObject::isCountable('cKundengruppen', $post)) {
            $link->cKundengruppen = \implode(';', $post['cKundengruppen']) . ';';
            if (\in_array('-1', $post['cKundengruppen'], true)) {
                $link->cKundengruppen = '_DBNULL_';
            }
        }
        if (isset($post['bIsActive']) && (int)$post['bIsActive'] !== 1) {
            $link->bIsActive = 0;
        }
        if (isset($post['cSichtbarNachLogin']) && $post['cSichtbarNachLogin'] === 'Y') {
            $link->cSichtbarNachLogin = 'Y';
        }
        if (isset($post['cNoFollow']) && $post['cNoFollow'] === 'Y') {
            $link->cNoFollow = 'Y';
        }
        if ($link->nLinkart > 2 && isset($post['nSpezialseite']) && (int)$post['nSpezialseite'] > 0) {
            $link->nLinkart = (int)$post['nSpezialseite'];
        }
        $type            = $link->nLinkart;
        $link->bIsSystem = (int)$this->getSpecialPageTypes()->contains(static function ($value) use ($type): bool {
            return $value->nLinkart === $type;
        });

        return $link;
    }

    /**
     * @param array $post
     * @return Link
     */
    public function createOrUpdateLink(array $post): Link
    {
        $link = $this->createLinkData($post);
        if ($link->kLink === 0) {
            $kLink              = $this->db->insert('tlink', $link);
            $assoc              = new stdClass();
            $assoc->linkID      = $kLink;
            $assoc->linkGroupID = (int)$post['kLinkgruppe'];
            $this->db->insert('tlinkgroupassociations', $assoc);
        } else {
            $kLink    = $link->kLink;
            $revision = new Revision($this->db);
            $revision->addRevision('link', $kLink, true);
            $this->db->update('tlink', 'kLink', $kLink, $link);
        }
        $localized        = new stdClass();
        $localized->kLink = $kLink;
        foreach (LanguageHelper::getAllLanguages(0, true) as $language) {
            $code                   = $language->getIso();
            $localized->cISOSprache = $code;
            $localized->cName       = $link->cName;
            $localized->cTitle      = '';
            $localized->cContent    = '';
            $keepUnderscore         = false;
            if (!empty($post['cName_' . $code])) {
                $localized->cName = $this->specialChars($post['cName_' . $code]);
            }
            if (!empty($post['cTitle_' . $code])) {
                $localized->cTitle = $this->specialChars($post['cTitle_' . $code]);
            }
            if (!empty($post['cContent_' . $code])) {
                $localized->cContent = $this->parseText($post['cContent_' . $code], $kLink);
            }
            $localized->cSeo = $localized->cName;
            if (!empty($post['cSeo_' . $code])) {
                $localized->cSeo = $post['cSeo_' . $code];
                $keepUnderscore  = true;
            }
            $localized->cMetaTitle = $localized->cTitle;
            $idx                   = 'cMetaTitle_' . $code;
            if (isset($post[$idx])) {
                $localized->cMetaTitle = $this->specialChars($post[$idx]);
            }
            $localized->cMetaKeywords    = $this->specialChars($post['cMetaKeywords_' . $code] ?? '');
            $localized->cMetaDescription = $this->specialChars($post['cMetaDescription_' . $code] ?? '');
            $this->db->delete('tlinksprache', ['kLink', 'cISOSprache'], [$kLink, $code]);
            $localized->cSeo = $link->nLinkart === \LINKTYP_EXTERNE_URL
                ? $localized->cSeo
                : Seo::getSeo($localized->cSeo, $keepUnderscore);
            $this->db->insert('tlinksprache', $localized);
            $prev = $this->db->select(
                'tseo',
                ['cKey', 'kKey', 'kSprache'],
                ['kLink', $localized->kLink, $language->getId()]
            );
            $this->db->delete(
                'tseo',
                ['cKey', 'kKey', 'kSprache'],
                ['kLink', $localized->kLink, $language->getId()]
            );
            $seo           = new stdClass();
            $seo->cSeo     = Seo::checkSeo($localized->cSeo);
            $seo->kKey     = $localized->kLink;
            $seo->cKey     = 'kLink';
            $seo->kSprache = $language->getId();
            $this->db->insert('tseo', $seo);
            if ($prev !== null) {
                $this->db->update('topcpage', 'cPageUrl', '/' . $prev->cSeo, (object)['cPageUrl' => '/' . $seo->cSeo]);
            }
        }
        $linkInstance = new Link($this->db);
        $linkInstance->load($kLink);

        return $linkInstance;
    }

    /**
     * @param string $text
     * @param int    $linkID
     * @return string
     */
    private function parseText(string $text, int $linkID): string
    {
        $uploadDir = \PFAD_ROOT . \PFAD_BILDER . \PFAD_LINKBILDER;
        $baseURL   = Shop::getURL() . '/' . \PFAD_BILDER . \PFAD_LINKBILDER;
        $images    = [];
        $sort      = [];
        if (\is_dir($uploadDir . $linkID) && ($dirHandle = \opendir($uploadDir . $linkID)) !== false) {
            while (($file = \readdir($dirHandle)) !== false) {
                if ($file !== '.' && $file !== '..') {
                    $imageNumber          = (int)\mb_substr(
                        \str_replace('Bild', '', $file),
                        0,
                        \mb_strpos(\str_replace('Bild', '', $file), '.') ?: null
                    );
                    $images[$imageNumber] = $file;
                    $sort[]               = $imageNumber;
                }
            }
            \closedir($dirHandle);
        }
        \usort($sort, static function ($a, $b): int {
            return $a <=> $b;
        });

        foreach ($sort as $no) {
            $text = \str_replace(
                '$#Bild' . $no . '#$',
                '<img src="' . $baseURL . $linkID . '/' . $images[$no] . '" />',
                $text
            );
        }

        return $text;
    }

    /**
     * @return bool
     */
    public function clearCache(): bool
    {
        $this->cache->flushTags([\CACHING_GROUP_CORE]);
        $this->db->query('UPDATE tglobals SET dLetzteAenderung = NOW()');

        return true;
    }

    /**
     * @return Collection
     */
    public function getDuplicateSpecialLinks(): Collection
    {
        $group = Shop::Container()->getLinkService()->getAllLinkGroups()->getLinkgroupByTemplate('specialpages');
        if ($group === null) {
            return new Collection();
        }

        return $group->getLinks()->filter(static function (Link $link): bool {
            return $link->hasDuplicateSpecialLink();
        });
    }

    /**
     * @return Collection<stdClass>
     */
    public function getSpecialPageTypes(): Collection
    {
        return $this->db->getCollection(
            'SELECT *
                FROM tspezialseite
                ORDER BY nSort'
        )->map(static function (stdClass $link): stdClass {
            $link->kSpezialseite = (int)$link->kSpezialseite;
            $link->kPlugin       = (int)$link->kPlugin;
            $link->nLinkart      = (int)$link->nLinkart;
            $link->nSort         = (int)$link->nSort;

            return $link;
        });
    }

    /**
     * @param string $text
     * @return string
     */
    private function specialChars(string $text): string
    {
        return \htmlspecialchars($text, \ENT_COMPAT | \ENT_HTML401, \JTL_CHARSET, false);
    }
}
