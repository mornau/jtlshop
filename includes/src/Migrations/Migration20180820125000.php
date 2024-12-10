<?php

/**
 * remove old payment methods
 *
 * @author fm
 * @created Mon, 20 Aug 2018 12:50:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180820125000
 */
class Migration20180820125000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Remove old payment methods';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "DELETE tversandartzahlungsart, tzahlungsart, tzahlungsartsprache
            FROM tzahlungsart
            LEFT JOIN tversandartzahlungsart
                ON tzahlungsart.kZahlungsart = tversandartzahlungsart.kZahlungsart
            LEFT JOIN tzahlungsartsprache
                ON tzahlungsart.kZahlungsart = tzahlungsartsprache.kZahlungsart
            WHERE tzahlungsart.cModulId IN (
                'za_paypal_jtl', 
                'za_worldpay_jtl',
                'za_ipayment_jtl',
                'za_safetypay',
                'za_paymentpartner_jtl',
                'za_postfinance_jtl',
                'za_saferpay_jtl',
                'za_iloxx_jtl',
                'za_iclear_jtl'
                'za_wirecard_jtl') 
                OR tzahlungsart.cModulId LIKE 'za_ut_%' OR tzahlungsart.cModulId LIKE 'za_uos_%'"
        );
        $this->execute(
            "DELETE FROM teinstellungenconf WHERE cModulId IN (
            'za_paypal_jtl', 
            'za_worldpay_jtl',
            'za_ipayment_jtl',
            'za_safetypay',
            'za_paymentpartner_jtl',
            'za_postfinance_jtl',
            'za_saferpay_jtl',
            'za_iloxx_jtl',
            'za_iclear_jtl',
            'za_wirecard_jtl'
        ) OR cModulId LIKE 'za_ut_%' OR cModulId LIKE 'za_uos_%'"
        );
        $this->execute(
            "DELETE FROM teinstellungen WHERE cModulId IN (
            'za_paypal_jtl', 
            'za_worldpay_jtl',
            'za_ipayment_jtl',
            'za_safetypay',
            'za_paymentpartner_jtl',
            'za_postfinance_jtl',
            'za_saferpay_jtl',
            'za_iloxx_jtl',
            'za_iclear_jtl',
            'za_wirecard_jtl'
        ) OR cModulId LIKE 'za_ut_%' OR cModulId LIKE 'za_uos_%'"
        );
        $this->execute(
            'DELETE FROM teinstellungenconfwerte 
                WHERE kEinstellungenConf NOT IN (SELECT kEinstellungenConf FROM teinstellungenconf)'
        );
        $this->execute(
            'DELETE FROM tversandartzahlungsart
                WHERE kVersandart NOT IN (SELECT kVersandart FROM tversandart)
                OR kZahlungsart NOT IN (SELECT kZahlungsart FROM tzahlungsart)'
        );
        $this->execute(
            'DELETE FROM tzahlungsartsprache
                WHERE kZahlungsart NOT IN (SELECT kZahlungsart FROM tzahlungsart)'
        );
        $this->removeLocalization('ipaymentDesc');
        $this->removeLocalization('payWithIpayment');
        $this->removeLocalization('payWithWorldpay');
        $this->removeLocalization('worldpayDesc');
        $this->removeLocalization('payWithPaymentPartner');
        $this->removeLocalization('payWithWirecard');
        $this->removeLocalization('iloxxDesc');
        $this->removeLocalization('payWithIclear');
        $this->removeLocalization('iclearError');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
