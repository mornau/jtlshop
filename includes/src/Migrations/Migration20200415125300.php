<?php

/**
 * Add availability filter
 *
 * @author mh
 * @created Wed, 15 Apr 2020 12:53:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Router\Controller\Backend\BoxController;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200415125300
 */
class Migration20200415125300 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add availability filter';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "DELETE `tboxvorlage`, `tboxen`, `tboxensichtbar`
                  FROM `tboxvorlage`
                  LEFT JOIN `tboxen`
                    ON tboxen.kBoxvorlage = tboxvorlage.kBoxvorlage
                  LEFT JOIN `tboxensichtbar`
                    ON tboxen.kBox = tboxensichtbar.kBox
                  WHERE tboxvorlage.cTemplate = 'box_filter_availability.tpl'"
        );
        $oldBoxID = $this->getDB()->getLastInsertedID(
            'INSERT INTO `tboxvorlage` (kCustomID, eTyp, cName, cVerfuegbar, cTemplate)
                SELECT kCustomID, eTyp, cName, cVerfuegbar, cTemplate
                  FROM `tboxvorlage`
                  WHERE kBoxvorlage = 103'
        );
        $this->getDB()->queryPrepared(
            'UPDATE `tboxen`
              SET kBoxvorlage = :oldBoxID
              WHERE kBoxvorlage = 103',
            ['oldBoxID' => $oldBoxID]
        );
        $this->getDB()->delete('tboxvorlage', 'kBoxvorlage', 103);

        $this->execute(
            "INSERT INTO `tboxvorlage`
                VALUES (103, 0, 'tpl', 'Filter (Verfügbarkeit)', '2', 'box_filter_availability.tpl')"
        );

        $id = $this->getDB()->insert(
            'tboxen',
            (object)[
                'kBoxvorlage' => 103,
                'kCustomID'   => 0,
                'kContainer'  => 0,
                'cTitel'      => 'Filter (Verfügbarkeit)',
                'ePosition'   => 'left'
            ]
        );
        foreach (BoxController::getValidPageTypes() as $box) {
            $this->execute("INSERT INTO `tboxensichtbar` VALUES ('" . $id . "', '" . $box . "', '6', '1', '')");
        }

        $this->setConfig(
            'allgemein_availabilityfilter_benutzen',
            'Y',
            \CONF_NAVIGATIONSFILTER,
            'Verfügbarkeitsfilter benutzen',
            'selectbox',
            117,
            (object)[
                'cBeschreibung' => 'Soll die Verfügbarkeitsfilterung beim Filtern benutzt werden?',
                'inputOptions'  => [
                    'content' => 'Ja, im Contentbereich',
                    'box'     => 'Ja, in Navigationsbox',
                    'Y'       => 'Ja, im Contentbereich und der Navigationsbox',
                    'N'       => 'Nein'
                ]
            ]
        );
        $this->setConfig(
            'configgroup_110_availability_filter',
            'Verfügbarkeitsfilter',
            \CONF_NAVIGATIONSFILTER,
            'Verfügbarkeitsfilter',
            null,
            115,
            (object)['cConf' => 'N']
        );

        $this->setLocalization('ger', 'global', 'filterAvailability', 'Verfügbarkeit');
        $this->setLocalization('eng', 'global', 'filterAvailability', 'Availability');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'DELETE `tboxvorlage`, `tboxen`, `tboxensichtbar`
                  FROM `tboxvorlage`
                  LEFT JOIN `tboxen`
                    ON tboxen.kBoxvorlage = tboxvorlage.kBoxvorlage
                  LEFT JOIN `tboxensichtbar`
                    ON tboxen.kBox = tboxensichtbar.kBox
                  WHERE tboxvorlage.kBoxvorlage = 103'
        );

        $this->removeConfig('allgemein_availabilityfilter_benutzen');
        $this->removeConfig('configgroup_110_availability_filter');

        $this->removeLocalization('filterAvailability', 'global');
    }
}
