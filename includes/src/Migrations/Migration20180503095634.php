<?php

/**
 * implement fallback-payment
 *
 * @author cr
 * @created Thu, 03 May 2018 09:56:34 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180503095634
 */
class Migration20180503095634 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'cr';
    }

    public function getDescription(): string
    {
        return 'Implement fallback-payment';
    }

    protected string $szPaymentModuleId = 'za_null_jtl';

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'INSERT INTO `tzahlungsart`(`kZahlungsart`, `cName`, `cModulId`, `cKundengruppen`,
                           `cBild`, `nMailSenden`, `cAnbieter`, `cTSCode`, `nWaehrendBestellung`)
                VALUES (0, "Keine Zahlung erforderlich", "' . $this->szPaymentModuleId . '", "", "", 1, "", "", 0)'
        );
        $paymentItem = $this->fetchOne(
            'SELECT * FROM `tzahlungsart` WHERE `cModulId` = "' . $this->szPaymentModuleId . '"'
        );
        if ($paymentItem === null) {
            return;
        }

        $this->execute(
            'INSERT INTO `tzahlungsartsprache`(`kZahlungsart`, `cISOSprache`, `cName`, `cGebuehrname`,
                                  `cHinweisText`, `cHinweisTextShop`)
                VALUES(' . $paymentItem->kZahlungsart . ', "ger", "Keine Zahlung erforderlich",
                    "Keine Zahlung erforderlich",
                    "Es ist keine Zahlung erforderlich. Ihr Shop-Guthaben wurde entsprechend verrechenet.",
                    "Es ist keine Zahlung erforderlich. Ihr Shop-Guthaben wurde entsprechend verrechenet.")'
        );
        $this->execute(
            'INSERT INTO `tzahlungsartsprache`(`kZahlungsart`, `cISOSprache`, `cName`, `cGebuehrname`,
                                  `cHinweisText`, `cHinweisTextShop`)
                VALUES(' . $paymentItem->kZahlungsart . ', "eng", "No payment needed",
                 "No payment needed", "There is no further payment needed. Your shop-credit was billed.",
                 "There is no further payment needed. Your shop-credit was billed.")'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $paymentItem = $this->fetchOne(
            'SELECT * FROM `tzahlungsart`
                WHERE `cModulId` = "' . $this->szPaymentModuleId . '"'
        );
        $this->execute('DELETE FROM `tzahlungsart` WHERE `cModulID` = "' . $this->szPaymentModuleId . '"');
        if ($paymentItem === null) {
            return;
        }
        $this->execute('DELETE FROM `tzahlungsartsprache` WHERE `kZahlungsart` = ' . (int)$paymentItem->kZahlungsart);
    }
}
