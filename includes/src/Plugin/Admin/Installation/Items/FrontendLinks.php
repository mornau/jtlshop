<?php

declare(strict_types=1);

namespace JTL\Plugin\Admin\Installation\Items;

use JTL\Helpers\Seo;
use JTL\Language\LanguageHelper;
use JTL\Language\LanguageModel;
use JTL\Plugin\InstallCode;
use stdClass;

/**
 * Class FrontendLinks
 * @package JTL\Plugin\Admin\Installation\Items
 */
class FrontendLinks extends AbstractItem
{
    /**
     * @inheritdoc
     */
    public function getNode(): array
    {
        return \is_array($this->baseNode['Install'][0]['FrontendLink'][0]['Link'] ?? null)
            ? $this->baseNode['Install'][0]['FrontendLink'][0]['Link']
            : [];
    }

    /**
     * @inheritdoc
     */
    public function install(): int
    {
        $pluginID    = $this->getPlugin()->kPlugin;
        $oldPluginID = $this->oldPlugin === null ? 0 : $this->oldPlugin->getID();
        foreach ($this->getNode() as $i => $links) {
            $i = (string)$i;
            \preg_match('/\d+\sattr/', $i, $hits1);
            \preg_match('/\d+/', $i, $hits2);
            if (\mb_strlen($hits2[0]) !== \mb_strlen($i)) {
                continue;
            }
            if (empty($links['LinkGroup'])) {
                $links['LinkGroup'] = 'hidden'; // linkgroup not set? default to 'hidden'
            }
            $linkGroupID = $this->getLinkGroup($links['LinkGroup']);
            if ($linkGroupID === 0) {
                return InstallCode::SQL_CANNOT_FIND_LINK_GROUP;
            }
            $linkID = $this->addLink($pluginID, $links);
            if ($linkID <= 0) {
                return InstallCode::SQL_CANNOT_SAVE_LINK;
            }
            $this->getDB()->insert(
                'tlinkgroupassociations',
                (object)['linkGroupID' => $linkGroupID, 'linkID' => $linkID]
            );
            $allLanguages    = LanguageHelper::getAllLanguages(2, true, true);
            $linkLang        = new stdClass();
            $linkLang->kLink = $linkID;
            $bLinkStandard   = false;
            $defaultLang     = new stdClass();
            $oldLink         = $oldPluginID === 0
                ? null
                : $this->getDB()->select('tlink', 'kPlugin', $oldPluginID, 'cName', $links['Name']);
            if ($oldLink !== null) {
                $oldLinkGroup   = $this->getDB()->select('tlinkgroupassociations', 'linkID', (int)$oldLink->kLink);
                $oldLinkGroupID = (int)($oldLinkGroup->linkGroupID ?? 0);
                if ($oldLinkGroupID > 0) {
                    $this->getDB()->update(
                        'tlinkgroupassociations',
                        'linkID',
                        $linkID,
                        (object)['linkGroupID' => $oldLinkGroupID]
                    );
                }
            }
            foreach ($links['LinkLanguage'] as $l => $localized) {
                $l = (string)$l;
                \preg_match('/\d+\sattr/', $l, $hits1);
                \preg_match('/\d+/', $l, $hits2);
                if (isset($hits1[0]) && \mb_strlen($hits1[0]) === \mb_strlen($l)) {
                    $linkLang->cISOSprache = \mb_convert_case($localized['iso'], \MB_CASE_LOWER);
                } elseif (\mb_strlen($hits2[0]) === \mb_strlen($l)) {
                    $linkLang->cSeo             = Seo::checkSeo(Seo::getSeo($localized['Seo'], true));
                    $linkLang->cName            = $localized['Name'];
                    $linkLang->cTitle           = $localized['Title'];
                    $linkLang->cContent         = '';
                    $linkLang->cMetaTitle       = $localized['MetaTitle'];
                    $linkLang->cMetaKeywords    = $localized['MetaKeywords'];
                    $linkLang->cMetaDescription = $localized['MetaDescription'];
                    $this->getDB()->insert('tlinksprache', $linkLang);
                    if (!$bLinkStandard) {
                        $defaultLang   = $linkLang;
                        $bLinkStandard = true;
                    }
                    if (($allLanguages[$linkLang->cISOSprache]->kSprache ?? 0) > 0) {
                        $or = isset($oldLink->kLink) ? (' OR kKey = ' . (int)$oldLink->kLink) : '';
                        $this->getDB()->queryPrepared(
                            "DELETE FROM tseo
                                WHERE cKey = 'kLink'
                                    AND (kKey = :lnk" . $or . ')
                                    AND kSprache = :lid',
                            [
                                'lid' => (int)$allLanguages[$linkLang->cISOSprache]->kSprache,
                                'lnk' => $linkID
                            ]
                        );
                        $seo           = new stdClass();
                        $seo->cSeo     = Seo::checkSeo(Seo::getSeo($localized['Seo'], true));
                        $seo->cKey     = 'kLink';
                        $seo->kKey     = $linkID;
                        $seo->kSprache = $allLanguages[$linkLang->cISOSprache]->kSprache;
                        $this->getDB()->insert('tseo', $seo);
                    }
                    if (isset($allLanguages[$linkLang->cISOSprache])) {
                        unset($allLanguages[$linkLang->cISOSprache]);
                        $allLanguages = \array_merge($allLanguages);
                    }
                }
            }
            if (!$this->addHook($pluginID)) {
                return InstallCode::SQL_CANNOT_SAVE_HOOK;
            }
            $this->addMissingTranslations($allLanguages, $defaultLang, $linkID);
            $this->addLinkFile($pluginID, $linkID, $links);
        }

        return InstallCode::OK;
    }

    /**
     * Sind noch Sprachen im Shop die das Plugin nicht berücksichtigt?
     *
     * @param LanguageModel[] $languages
     * @param stdClass        $defaultLang
     * @param int             $linkID
     */
    private function addMissingTranslations(array $languages, stdClass $defaultLang, int $linkID): void
    {
        foreach ($languages as $language) {
            $this->getDB()->delete(
                'tseo',
                ['cKey', 'kKey', 'kSprache'],
                ['kLink', $linkID, $language->getId()]
            );
            $seo           = new stdClass();
            $seo->cSeo     = Seo::checkSeo(Seo::getSeo($defaultLang->cSeo, true));
            $seo->cKey     = 'kLink';
            $seo->kKey     = $linkID;
            $seo->kSprache = $language->getId();
            $this->getDB()->insert('tseo', $seo);
            $defaultLang->cSeo        = $seo->cSeo;
            $defaultLang->cISOSprache = $language->getCode();
            $this->getDB()->insert('tlinksprache', $defaultLang);
        }
    }

    /**
     * @param int                   $pluginID
     * @param int                   $linkID
     * @param array<string, string> $links
     * @return int
     */
    private function addLinkFile(int $pluginID, int $linkID, array $links): int
    {
        $linkFile                      = new stdClass();
        $linkFile->kPlugin             = $pluginID;
        $linkFile->kLink               = $linkID;
        $linkFile->cDatei              = $links['Filename'] ?? '';
        $linkFile->cTemplate           = $links['Template'] ?? '_DBNULL_';
        $linkFile->cFullscreenTemplate = $links['FullscreenTemplate'] ?? '_DBNULL_';

        return $this->getDB()->insert('tpluginlinkdatei', $linkFile);
    }

    /**
     * @param int                   $pluginID
     * @param array<string, string> $links
     * @return int
     */
    private function addLink(int $pluginID, array $links): int
    {
        $link                     = new stdClass();
        $link->kPlugin            = $pluginID;
        $link->cName              = $links['Name'];
        $link->nLinkart           = \LINKTYP_PLUGIN;
        $link->cSichtbarNachLogin = $links['VisibleAfterLogin'] ?? 'N';
        $link->cDruckButton       = $links['PrintButton'] ?? 'N';
        $link->cNoFollow          = $links['NoFollow'] ?? 'N';
        $link->cIdentifier        = $links['Identifier'] ?? '';
        $link->nSort              = 0;
        $link->bSSL               = (int)($links['SSL'] ?? 0);

        return $this->getDB()->insert('tlink', $link);
    }

    /**
     * @param int $pluginID
     * @return int
     */
    private function addHook(int $pluginID): int
    {
        $hook             = new stdClass();
        $hook->kPlugin    = $pluginID;
        $hook->nHook      = \HOOK_SEITE_PAGE_IF_LINKART;
        $hook->cDateiname = \PLUGIN_SEITENHANDLER;

        return $this->getDB()->insert('tpluginhook', $hook);
    }

    /**
     * @param string $name
     * @return int
     */
    private function getLinkGroup(string $name): int
    {
        $linkGroup = $this->getDB()->select('tlinkgruppe', 'cName', $name);
        if ($linkGroup !== null) {
            return (int)$linkGroup->kLinkgruppe;
        }
        $linkGroup                = new stdClass();
        $linkGroup->cName         = $name;
        $linkGroup->cTemplatename = $name;
        $linkGroup->kLinkgruppe   = $this->getDB()->insert('tlinkgruppe', $linkGroup);

        return $linkGroup->kLinkgruppe;
    }
}
