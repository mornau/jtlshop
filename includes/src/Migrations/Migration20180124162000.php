<?php

/**
 * Rebuild ttrennzeichen and add unique index
 *
 * @author fm
 * @created Wed, 18 Jan 2018 16:20:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Catalog\Separator;
use JTL\Language\LanguageHelper;
use JTL\Shop;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180124162000
 */
class Migration20180124162000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Rebuild ttrennzeichen and add unique index';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->rebuildTable();
        $this->execute('ALTER TABLE `ttrennzeichen` ADD UNIQUE INDEX `unique_lang_unit` (`kSprache`, `nEinheit`)');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `ttrennzeichen` DROP INDEX `unique_lang_unit`');
    }

    private function rebuildTable(): int
    {
        $conf      = Shop::getSettingSection(\CONF_ARTIKELDETAILS);
        $languages = LanguageHelper::getAllLanguages();
        $cache     = Shop::Container()->getCache();
        if (!\is_array($languages) || \count($languages) === 0) {
            return -1;
        }
        $this->db->query('TRUNCATE ttrennzeichen');
        $units = [\JTL_SEPARATOR_WEIGHT, \JTL_SEPARATOR_AMOUNT, \JTL_SEPARATOR_LENGTH];
        foreach ($languages as $language) {
            foreach ($units as $unit) {
                $sep = new Separator(0, $this->db, $cache);
                $dec = 2;
                if ($unit === \JTL_SEPARATOR_WEIGHT) {
                    $dec = ($conf['artikeldetails_gewicht_stellenanzahl'] ?? '') !== ''
                        ? (int)$conf['artikeldetails_gewicht_stellenanzahl']
                        : 2;
                }
                $sep10   = ($conf['artikeldetails_zeichen_nachkommatrenner'] ?? '') !== ''
                    ? $conf['artikeldetails_zeichen_nachkommatrenner']
                    : ',';
                $sep1000 = ($conf['artikeldetails_zeichen_tausendertrenner'] ?? '') !== ''
                    ? $conf['artikeldetails_zeichen_tausendertrenner']
                    : '.';
                $sep->setDezimalstellen($dec)
                    ->setDezimalZeichen($sep10)
                    ->setTausenderZeichen($sep1000)
                    ->setSprache($language->kSprache)
                    ->setEinheit($unit)
                    ->save();
            }
        }
        $cache->flushTags([\CACHING_GROUP_CORE]);

        return $this->db->getAffectedRows(
            'DELETE teinstellungen, teinstellungenconf
                FROM teinstellungenconf
                LEFT JOIN teinstellungen 
                    ON teinstellungen.cName = teinstellungenconf.cWertName
                WHERE teinstellungenconf.kEinstellungenConf IN (1458, 1459, 495, 497, 499, 501)'
        );
    }
}
