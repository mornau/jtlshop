<?php

declare(strict_types=1);

namespace JTL\Plugin\Admin\Installation\Items;

use JTL\Language\LanguageHelper;
use JTL\Plugin\InstallCode;
use stdClass;

/**
 * Class MailTemplates
 * @package JTL\Plugin\Admin\Installation\Items
 */
class MailTemplates extends AbstractItem
{
    /**
     * @inheritdoc
     */
    public function getNode(): array
    {
        return isset($this->baseNode['Install'][0]['Emailtemplate'][0]['Template'])
        && \is_array($this->baseNode['Install'][0]['Emailtemplate'][0]['Template'])
            ? $this->baseNode['Install'][0]['Emailtemplate'][0]['Template']
            : [];
    }

    /**
     * @inheritdoc
     */
    public function install(): int
    {
        foreach ($this->getNode() as $i => $template) {
            $i = (string)$i;
            \preg_match('/\d+\sattr/', $i, $hits1);
            \preg_match('/\d+/', $i, $hits2);
            if (\mb_strlen($hits2[0]) !== \mb_strlen($i)) {
                continue;
            }
            $mailTpl                = new stdClass();
            $mailTpl->kPlugin       = $this->getPlugin()->kPlugin;
            $mailTpl->cName         = $template['Name'];
            $mailTpl->cBeschreibung = \is_array($template['Description'])
                ? $template['Description'][0]
                : $template['Description'];
            $mailTpl->cMailTyp      = $template['Type'] ?? 'text/html';
            $mailTpl->cModulId      = $template['ModulId'];
            $mailTpl->cDateiname    = $template['Filename'] ?? null;
            $mailTpl->cAktiv        = $template['Active'] ?? 'N';
            $mailTpl->nAKZ          = (int)($template['AKZ'] ?? 0);
            $mailTpl->nAGB          = (int)($template['AGB'] ?? 0);
            $mailTpl->nWRB          = (int)($template['WRB'] ?? 0);
            $mailTpl->nWRBForm      = (int)($template['WRBForm'] ?? 0);
            $mailTpl->nDSE          = (int)($template['DSE'] ?? 0);
            $mailTplID              = $this->getDB()->insert('temailvorlage', $mailTpl);
            if ($mailTplID <= 0) {
                return InstallCode::SQL_CANNOT_SAVE_EMAIL_TEMPLATE;
            }
            $iso                    = '';
            $allLanguages           = LanguageHelper::getAllLanguages(2, true);
            $fallbackLocalization   = null;
            $availableLocalizations = [];
            $addedLanguages         = [];
            $first                  = true;
            $prevTemplateID         = 0;
            if ($this->oldPlugin !== null) {
                $prevTemplateID = $this->getDB()->getSingleInt(
                    'SELECT kEmailvorlage
                        FROM temailvorlage
                        WHERE kPlugin = :pid AND cModulId = :mid',
                    'kEmailvorlage',
                    [
                        'pid' => $this->oldPlugin->getID(),
                        'mid' => $mailTpl->cModulId
                    ],
                );
            }
            foreach ($template['TemplateLanguage'] as $l => $localized) {
                $l = (string)$l;
                \preg_match('/\d+\sattr/', $l, $hits1);
                \preg_match('/\d+/', $l, $hits2);
                if (isset($hits1[0]) && \mb_strlen($hits1[0]) === \mb_strlen($l)) {
                    $iso = \mb_convert_case($localized['iso'], \MB_CASE_LOWER);
                } elseif (isset($hits2[0]) && \mb_strlen($hits2[0]) === \mb_strlen($l)) {
                    $localizedTpl                = new stdClass();
                    $localizedTpl->kEmailvorlage = $mailTplID;
                    $localizedTpl->kSprache      = $allLanguages[$iso]->kSprache ?? 0;
                    $localizedTpl->cBetreff      = $localized['Subject'];
                    $localizedTpl->cContentHtml  = $localized['ContentHtml'];
                    $localizedTpl->cContentText  = $localized['ContentText'];
                    $localizedTpl->cPDFS         = $localized['PDFS'] ?? null;
                    $localizedTpl->cPDFNames     = $localized['Filename'] ?? null;
                    $availableLocalizations[]    = $localizedTpl;
                    if ($fallbackLocalization === null) {
                        $fallbackLocalization = $localizedTpl;
                    }
                }
            }
            foreach ($availableLocalizations as $localizedTpl) {
                if ($localizedTpl->kSprache === 0) {
                    continue;
                }
                $addedLanguages[] = $localizedTpl->kSprache;
                if ($this->oldPlugin === null || $prevTemplateID < 1) {
                    $this->getDB()->insert('temailvorlagesprache', $localizedTpl);
                }
                $this->getDB()->insert('temailvorlagespracheoriginal', $localizedTpl);
            }
            // Sind noch Sprachen im Shop die das Plugin nicht berücksichtigt?
            foreach ($allLanguages as $language) {
                if (\in_array($language->getId(), $addedLanguages, true)) {
                    continue;
                }
                if ($first === true) {
                    $this->getDB()->update(
                        'temailvorlage',
                        'kEmailvorlage',
                        $mailTplID,
                        (object)['nFehlerhaft' => 1, 'cAktiv' => 'N']
                    );
                    $first = false;
                }
                $fallbackLocalization->kSprache = $language->getId();
                if (!isset($this->oldPlugin->kPlugin) || !$this->oldPlugin->kPlugin) {
                    $this->getDB()->insert('temailvorlagesprache', $fallbackLocalization);
                }
                $this->getDB()->insert('temailvorlagespracheoriginal', $fallbackLocalization);
            }
        }

        return InstallCode::OK;
    }
}
