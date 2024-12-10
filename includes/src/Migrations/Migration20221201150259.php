<?php

/**
 * Add new language vars and update existing ones regarding delivery time.
 *
 * @author sl
 * @created Thu, 01 Dec 2022 15:02:59 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20221201150259
 */
class Migration20221201150259 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'sl';
    }

    public function getDescription(): string
    {
        return 'Add new language vars and update existing ones regarding delivery time.';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $newVars = [
            'deliverytimeEstimationSimpleWeeks'  => [
                'ger' => 'ca. #DELIVERYTIME# Wochen',
                'eng' => 'ca. #DELIVERYTIME# weeks'
            ],
            'deliverytimeEstimationWeeks'        => [
                'ger' => '#MINDELIVERYTIME# - #MAXDELIVERYTIME# Wochen',
                'eng' => '#MINDELIVERYTIME# - #MAXDELIVERYTIME# weeks'
            ],
            'deliverytimeEstimationSimpleMonths' => [
                'ger' => 'ca. #DELIVERYTIME# Monate',
                'eng' => 'ca. #DELIVERYTIME# months'
            ],
            'deliverytimeEstimationMonths'       => [
                'ger' => '#MINDELIVERYTIME# - #MAXDELIVERYTIME# Monate',
                'eng' => '#MINDELIVERYTIME# - #MAXDELIVERYTIME# months'
            ]
        ];
        foreach ($newVars as $newVar => $values) {
            foreach ($values as $iso => $value) {
                $this->setLocalization($iso, 'global', $newVar, $value);
            }
        }

        $this->execute(
            "UPDATE tsprachwerte 
                   SET cWert= REPLACE(cWert, 'DELIVERYDAYS', 'DELIVERYTIME'), 
                       cStandard = REPLACE(cStandard, 'DELIVERYDAYS', 'DELIVERYTIME')
                   WHERE cName IN ('deliverytimeEstimation', 'deliverytimeEstimationSimple')"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "DELETE FROM `tsprachwerte`
                WHERE `kSprachsektion` = 1 
                    AND cName IN (
                        'deliverytimeEstimationWeeks',
                        'deliverytimeEstimationSimpleWeeks',
                        'deliverytimeEstimationSimpleMonths',
                        'deliverytimeEstimationMonths'
                    )
                    AND bSystem = 1"
        );
        $this->execute(
            "UPDATE tsprachwerte
                   SET cWert= REPLACE(cWert, 'DELIVERYTIME', 'DELIVERYDAYS'),
                       cStandard = REPLACE(cStandard, 'DELIVERYTIME', 'DELIVERYDAYS')
                   WHERE cName IN ('deliverytimeEstimation', 'deliverytimeEstimationSimple')"
        );
    }
}
