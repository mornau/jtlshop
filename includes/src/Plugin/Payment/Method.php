<?php

declare(strict_types=1);

namespace JTL\Plugin\Payment;

use InvalidArgumentException;
use JTL\Cart\Cart;
use JTL\Checkout\Bestellung;
use JTL\Checkout\ZahlungsLog;
use JTL\Customer\Customer;
use JTL\Customer\CustomerGroup;
use JTL\DB\DbInterface;
use JTL\Helpers\Request;
use JTL\Language\LanguageHelper;
use JTL\Mail\Mail\Mail;
use JTL\Plugin\Data\PaymentMethod;
use JTL\Plugin\Helper as PluginHelper;
use JTL\Session\Frontend;
use JTL\Shop;
use stdClass;

/**
 * Class Method
 * @package JTL\Plugin\Payment
 */
class Method implements MethodInterface
{
    /**
     * i.e. za_mbqc_visa_jtl
     *
     * @var string
     */
    public $moduleID;

    /**
     * i.e. mbqc_visa for za_mbqc_visa_jtl
     *
     * @var string|null
     */
    public $moduleAbbr;

    /**
     * Internal Name w/o whitespace, e.g. 'MoneybookersQC'.
     *
     * @var string
     */
    public $name;

    /**
     * E.g. 'Moneybookers Quick Connect'.
     *
     * @var string
     */
    public $caption;

    /**
     * @var int
     */
    public $duringCheckout;

    /**
     * @var string
     */
    public $cModulId;

    /**
     * @var bool
     * @deprecated since 5.0.0 - use self::canPayAgain
     */
    public $bPayAgain;

    /**
     * @var array
     */
    public $paymentConfig;

    /**
     * @var int|null
     */
    public $kZahlungsart;

    /**
     * @var stdClass|null
     */
    public $ZahlungsInfo;

    /**
     * @var DbInterface
     */
    private DbInterface $db;

    /**
     * @param string           $moduleID
     * @param int              $nAgainCheckout
     * @param DbInterface|null $db
     */
    public function __construct(string $moduleID, int $nAgainCheckout = 0, DbInterface $db = null)
    {
        $this->db       = $db ?? Shop::Container()->getDB();
        $this->moduleID = $moduleID;
        // extract: za_mbqc_visa_jtl => myqc_visa
        $pattern = '&za_(.*)_jtl&is';
        \preg_match($pattern, $moduleID, $subpattern);
        $this->moduleAbbr = $subpattern[1] ?? null;

        $this->loadSettings();
        $this->init($nAgainCheckout);
    }

    /**
     * @inheritdoc
     */
    public function init(int $nAgainCheckout = 0)
    {
        $this->name           = '';
        $result               = $this->db->select('tzahlungsart', 'cModulId', $this->moduleID);
        $this->caption        = $result->cName ?? null;
        $this->duringCheckout = (int)($result->nWaehrendBestellung ?? 0);
        if ($nAgainCheckout === 1) {
            $this->duringCheckout = 0;
        }
        if ($this->cModulId === 'za_null_jtl' || $this->moduleID === 'za_null_jtl') {
            $this->kZahlungsart = (int)($result->kZahlungsart ?? '0');
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOrderHash(Bestellung $order): ?string
    {
        $orderId = isset($order->kBestellung)
            ? $this->db->getSingleObject(
                'SELECT cId FROM tbestellid WHERE kBestellung = :oid',
                ['oid' => (int)$order->kBestellung]
            )
            : null;

        return $orderId->cId ?? null;
    }

    /**
     * @inheritdoc
     */
    public function getReturnURL(Bestellung $order): string
    {
        if (
            isset($_SESSION['Zahlungsart']->nWaehrendBestellung)
            && (int)$_SESSION['Zahlungsart']->nWaehrendBestellung > 0
        ) {
            return Shop::Container()->getLinkService()->getStaticRoute('bestellvorgang.php');
        }
        if (Shop::getSettingValue(\CONF_KAUFABWICKLUNG, 'bestellabschluss_abschlussseite') === 'A') {
            // Abschlussseite
            $paymentID = $this->db->getSingleObject(
                'SELECT cId
                    FROM tbestellid
                    WHERE kBestellung = :oid',
                ['oid' => (int)$order->kBestellung]
            );
            if ($paymentID !== null) {
                return Shop::Container()->getLinkService()->getStaticRoute('bestellabschluss.php')
                    . '?i=' . $paymentID->cId;
            }
        }

        return $order->BestellstatusURL;
    }

    /**
     * @inheritdoc
     */
    public function getNotificationURL(string $hash): string
    {
        $key = $this->duringCheckout ? 'sh' : 'ph';

        return Shop::getURL() . '/includes/modules/notify.php?' . $key . '=' . $hash;
    }

    /**
     * @inheritdoc
     */
    public function updateNotificationID(int $orderID, string $cNotifyID)
    {
        if ($orderID > 0) {
            $upd            = new stdClass();
            $upd->cNotifyID = $cNotifyID;
            $upd->dNotify   = 'NOW()';
            $this->db->update('tzahlungsession', 'kBestellung', $orderID, $upd);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getShopTitle(): string
    {
        /** @var string $title */
        $title = Shop::getSettingValue(\CONF_GLOBAL, 'global_shopname');

        return $title;
    }

    /**
     * @inheritdoc
     */
    public function preparePaymentProcess(Bestellung $order): void
    {
        // overwrite!
    }

    /**
     * @inheritdoc
     */
    public function sendErrorMail(string $body)
    {
        $mail = new Mail();
        /** @var string $senderName */
        $senderName = Shop::getSettingValue(\CONF_EMAILS, 'email_master_absender_name');
        /** @var string $sender */
        $sender = Shop::getSettingValue(\CONF_EMAILS, 'email_master_absender');

        Shop::Container()->getMailer()->send(
            $mail->setLanguage(LanguageHelper::getDefaultLanguage())
                ->setToName($senderName)
                ->setToMail($sender)
                ->setSubject(
                    \sprintf(
                        Shop::Lang()->get('errorMailSubject', 'paymentMethods'),
                        $this->getShopTitle()
                    )
                )
                ->setBodyText($body)
        );

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function generateHash(Bestellung $order): string
    {
        if ((int)$this->duringCheckout === 1) {
            if (!isset($_SESSION['IP'])) {
                $_SESSION['IP'] = new stdClass();
            }
            $_SESSION['IP']->cIP = Request::getRealIP();
        }

        if ($order->kBestellung !== null) {
            $orderData               = $this->db->select(
                'tbestellid',
                'kBestellung',
                (int)$order->kBestellung
            );
            $hash                    = $orderData->cId ?? '';
            $paymentID               = new stdClass();
            $paymentID->kBestellung  = $order->kBestellung;
            $paymentID->kZahlungsart = $order->kZahlungsart;
            $paymentID->cId          = $hash;
            $paymentID->txn_id       = '';
            $paymentID->dDatum       = 'NOW()';
            $this->db->insert('tzahlungsid', $paymentID);
        } else {
            $this->db->delete('tzahlungsession', ['cSID', 'kBestellung'], [\session_id(), 0]);
            $paymentSession               = new stdClass();
            $paymentSession->cSID         = \session_id();
            $paymentSession->cNotifyID    = '';
            $paymentSession->dZeitBezahlt = 'NOW()';
            $paymentSession->cZahlungsID  = \uniqid('', true);
            $paymentSession->dZeit        = 'NOW()';
            $this->db->insert('tzahlungsession', $paymentSession);
            $hash = '_' . $paymentSession->cZahlungsID;
        }

        return $hash;
    }

    /**
     * @inheritdoc
     */
    public function deletePaymentHash(string $paymentHash)
    {
        $this->db->delete('tzahlungsid', 'cId', $paymentHash);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addIncomingPayment(Bestellung $order, object $payment)
    {
        $model = (object)\array_merge([
            'kBestellung'       => (int)$order->kBestellung,
            'cZahlungsanbieter' => empty($order->cZahlungsartName) ? $this->name : $order->cZahlungsartName,
            'fBetrag'           => 0,
            'fZahlungsgebuehr'  => 0,
            'cISO'              => Frontend::getCurrency()->getCode(),
            'cEmpfaenger'       => '',
            'cZahler'           => '',
            'dZeit'             => 'NOW()',
            'cHinweis'          => '',
            'cAbgeholt'         => 'N'
        ], (array)$payment);
        $this->db->insert('tzahlungseingang', $model);

        \executeHook(\HOOK_PAYMENT_METHOD_ADDINCOMINGPAYMENT, ['oBestellung' => $order, 'oZahlungseingang' => $model]);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setOrderStatusToPaid(Bestellung $order)
    {
        $upd                = new stdClass();
        $upd->cStatus       = \BESTELLUNG_STATUS_BEZAHLT;
        $upd->dBezahltDatum = 'NOW()';
        $this->db->update('tbestellung', 'kBestellung', (int)$order->kBestellung, $upd);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function sendConfirmationMail(Bestellung $order)
    {
        if ($order->kBestellung === null) {
            return $this;
        }
        $this->sendMail($order->kBestellung, \MAILTEMPLATE_BESTELLUNG_BEZAHLT);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function handleNotification(Bestellung $order, string $hash, array $args): void
    {
        // overwrite!
    }

    /**
     * @inheritdoc
     */
    public function finalizeOrder(Bestellung $order, string $hash, array $args): bool
    {
        // overwrite!
        return false;
    }

    /**
     * @inheritdoc
     */
    public function redirectOnCancel(): bool
    {
        // overwrite!
        return false;
    }

    /**
     * @inheritdoc
     */
    public function redirectOnPaymentSuccess(): bool
    {
        // overwrite!
        return false;
    }

    /**
     * @inheritdoc
     */
    public function doLog(string $msg, int $level = \LOGLEVEL_NOTICE)
    {
        ZahlungsLog::add($this->moduleID, $msg, null, $level);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomerOrderCount(int $customerID): int
    {
        if ($customerID <= 0) {
            return 0;
        }

        return $this->db->getSingleInt(
            "SELECT COUNT(*) AS cnt
                FROM tbestellung
                WHERE (cStatus = '2' || cStatus = '3' || cStatus = '4')
                    AND kKunde = :cid",
            'cnt',
            ['cid' => $customerID]
        );
    }

    /**
     * @inheritdoc
     */
    public function loadSettings()
    {
        $this->paymentConfig = Shop::getSettingSection(\CONF_ZAHLUNGSARTEN);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSetting(string $key)
    {
        $conf = Shop::getSettings([\CONF_ZAHLUNGSARTEN, \CONF_PLUGINZAHLUNGSARTEN]);

        return $conf['zahlungsarten']['zahlungsart_' . $this->moduleAbbr . '_' . $key]
            ?? ($conf['pluginzahlungsarten'][$this->moduleID . '_' . $key] ?? null);
    }

    /**
     *
     * @inheritdoc
     */
    public function isValid(object $customer, Cart $cart): bool
    {
        if (!$this->isValidIntern([$customer, $cart])) {
            return false;
        }

        $customerGroups = PaymentMethod::load($this->db, $this->moduleID)->getCustomerGroups();
        $customerGroup  = (int)($customer->kKundengruppe ?? CustomerGroup::getCurrent());
        if (\count($customerGroups) > 0 && !\in_array($customerGroup, $customerGroups, true)) {
            return false;
        }
        if (($minOrders = $this->getSetting('min_bestellungen')) > 0) {
            if (isset($customer->kKunde) && $customer->kKunde > 0) {
                $count = $this->db->getSingleInt(
                    'SELECT COUNT(*) AS cnt
                        FROM tbestellung
                        WHERE kKunde = :cid
                        AND (cStatus = :stp OR cStatus = :sts)',
                    'cnt',
                    [
                        'cid' => (int)$customer->kKunde,
                        'stp' => \BESTELLUNG_STATUS_BEZAHLT,
                        'sts' => \BESTELLUNG_STATUS_VERSANDT
                    ]
                );
                if ($count < $minOrders) {
                    ZahlungsLog::add(
                        $this->moduleID,
                        'Bestellanzahl ' . $count . ' ist kleiner als die Mindestanzahl von ' .
                        $minOrders,
                        null,
                        \LOGLEVEL_NOTICE
                    );

                    return false;
                }
            } else {
                ZahlungsLog::add($this->moduleID, 'Es ist kein kKunde vorhanden', null, \LOGLEVEL_NOTICE);

                return false;
            }
        }

        $cartTotal = $cart->gibGesamtsummeWarenOhne([\C_WARENKORBPOS_TYP_VERSANDPOS], true);
        /** @var int|string|float $min */
        $min = $this->getSetting('min');
        $min = (float)$min;
        if ($min > 0 && $cartTotal < $min) {
            ZahlungsLog::add(
                $this->moduleID,
                'Bestellwert ' . $cartTotal .
                ' ist kleiner als der Mindestbestellwert von ' . $min,
                null,
                \LOGLEVEL_NOTICE
            );

            return false;
        }
        /** @var int|string|float $max */
        $max = $this->getSetting('max');
        $max = (float)$max;
        if ($max > 0 && $cartTotal >= $max) {
            ZahlungsLog::add(
                $this->moduleID,
                'Bestellwert ' . $cartTotal .
                ' ist groesser als der maximale Bestellwert von ' . $max,
                null,
                \LOGLEVEL_NOTICE
            );

            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function isValidIntern(array $args_arr = []): bool
    {
        // Overwrite
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isSelectable(): bool
    {
        // Overwrite
        return $this->isValid(Frontend::getCustomer(), Frontend::getCart());
    }

    /**
     * @inheritdoc
     */
    public function handleAdditional(array $post): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function validateAdditional(): bool
    {
        return true;
    }

    /**
     *
     * @inheritdoc
     */
    public function addCache(string $cKey, string $cValue)
    {
        $_SESSION[$this->moduleID][$cKey] = $cValue;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function unsetCache(?string $cKey = null)
    {
        if ($cKey === null) {
            unset($_SESSION[$this->moduleID]);
        } else {
            unset($_SESSION[$this->moduleID][$cKey]);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCache(?string $cKey = null)
    {
        if ($cKey === null) {
            return $_SESSION[$this->moduleID] ?? null;
        }

        return $_SESSION[$this->moduleID][$cKey] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function createInvoice(int $orderID, int $languageID): object
    {
        return (object)[
            'nType' => 0,
            'cInfo' => '',
        ];
    }

    /**
     * @inheritdoc
     */
    public function reactivateOrder(int $orderID)
    {
        $this->sendMail($orderID, \MAILTEMPLATE_BESTELLUNG_RESTORNO);
        $upd                = new stdClass();
        $upd->cStatus       = \BESTELLUNG_STATUS_IN_BEARBEITUNG;
        $upd->dBezahltDatum = 'NOW()';
        $this->db->update('tbestellung', 'kBestellung', $orderID, $upd);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function cancelOrder(int $orderID, bool $delete = false)
    {
        if (!$delete) {
            $this->sendMail($orderID, \MAILTEMPLATE_BESTELLUNG_STORNO);
            $upd                = new stdClass();
            $upd->cStatus       = \BESTELLUNG_STATUS_STORNO;
            $upd->dBezahltDatum = 'NOW()';
            $this->db->update('tbestellung', 'kBestellung', $orderID, $upd);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function canPayAgain(): bool
    {
        // overwrite
        return false;
    }

    /**
     * @inheritdoc
     */
    public function sendMail(int $orderID, string $type, $additional = null)
    {
        $order = new Bestellung($orderID, false, $this->db);
        $order->fuelleBestellung(false);
        $customer = new Customer($order->kKunde, null, $this->db);
        $data     = new stdClass();
        $mailer   = Shop::Container()->getMailer();
        $mail     = new Mail();

        switch ($type) {
            case \MAILTEMPLATE_BESTELLBESTAETIGUNG:
            case \MAILTEMPLATE_BESTELLUNG_TEILVERSANDT:
            case \MAILTEMPLATE_BESTELLUNG_AKTUALISIERT:
            case \MAILTEMPLATE_BESTELLUNG_VERSANDT:
                $data->tkunde      = $customer;
                $data->tbestellung = $order;
                if ($customer->cMail !== '') {
                    $mailer->send($mail->createFromTemplateID($type, $data));
                }
                break;

            case \MAILTEMPLATE_BESTELLUNG_BEZAHLT:
                $data->tkunde      = $customer;
                $data->tbestellung = $order;
                if (
                    $order->Zahlungsart !== null
                    && ($order->Zahlungsart->nMailSenden & \ZAHLUNGSART_MAIL_EINGANG)
                    && $customer->cMail !== ''
                ) {
                    $mailer->send($mail->createFromTemplateID($type, $data));
                }
                break;

            case \MAILTEMPLATE_BESTELLUNG_STORNO:
                $data->tkunde      = $customer;
                $data->tbestellung = $order;
                if (
                    $order->Zahlungsart !== null
                    && ($order->Zahlungsart->nMailSenden & \ZAHLUNGSART_MAIL_STORNO)
                    && $customer->cMail !== ''
                ) {
                    $mailer->send($mail->createFromTemplateID($type, $data));
                }
                break;

            case \MAILTEMPLATE_BESTELLUNG_RESTORNO:
                $data->tkunde      = $customer;
                $data->tbestellung = $order;
                if (
                    $order->Zahlungsart !== null
                    && ($order->Zahlungsart->nMailSenden & \ZAHLUNGSART_MAIL_RESTORNO)
                    && $customer->cMail !== ''
                ) {
                    $mailer->send($mail->createFromTemplateID($type, $data));
                }
                break;

            default:
                break;
        }

        return $this;
    }

    /**
     * @param string $moduleID
     * @param int    $nAgainCheckout
     * @return MethodInterface|null
     */
    public static function create(string $moduleID, int $nAgainCheckout = 0): ?MethodInterface
    {
        if ($moduleID === 'za_null_jtl') {
            return new FallbackMethod('za_null_jtl');
        }

        $paymentMethod = null;
        $pluginID      = PluginHelper::getIDByModuleID($moduleID);

        if ($pluginID > 0 && \SAFE_MODE === false) {
            $loader = PluginHelper::getLoaderByPluginID($pluginID);
            try {
                $plugin = $loader->init($pluginID);
            } catch (InvalidArgumentException) {
                $plugin = null;
            }
            if ($plugin !== null) {
                $pluginPaymentMethod = $plugin->getPaymentMethods()->getMethodByID($moduleID);
                if ($pluginPaymentMethod === null) {
                    return null;
                }
                $className = $pluginPaymentMethod->getClassName();
                if (\class_exists($className)) {
                    $paymentMethod = new $className($moduleID, $nAgainCheckout);
                    if (!\is_a($paymentMethod, MethodInterface::class)) {
                        unset($paymentMethod);
                        $paymentMethod = null;
                    }
                }
            }
        }

        return $paymentMethod;
    }

    /**
     * @inheritdoc
     */
    public function getDB(): DbInterface
    {
        return $this->db;
    }

    /**
     * @inheritdoc
     */
    public function setDB(DbInterface $db): void
    {
        $this->db = $db;
    }
}
