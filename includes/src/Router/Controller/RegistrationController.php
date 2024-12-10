<?php

declare(strict_types=1);

namespace JTL\Router\Controller;

use JTL\Campaign;
use JTL\CheckBox;
use JTL\Checkout\Lieferadresse;
use JTL\Customer\Customer;
use JTL\Customer\CustomerAttributes;
use JTL\Customer\CustomerFields;
use JTL\Customer\DataHistory;
use JTL\Customer\Registration\Form as CustomerForm;
use JTL\Helpers\Form;
use JTL\Helpers\Request;
use JTL\Helpers\ShippingMethod;
use JTL\Helpers\Tax;
use JTL\Helpers\Text;
use JTL\Language\LanguageHelper;
use JTL\Mail\Mail\Mail;
use JTL\Session\Frontend;
use JTL\Shop;
use JTL\Smarty\JTLSmarty;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

/**
 * Class RegistrationController
 * @package JTL\Router\Controller
 */
class RegistrationController extends PageController
{
    /**
     * @var string
     */
    protected string $step = 'formular';

    /**
     * @inheritdoc
     */
    public function init(): bool
    {
        parent::init();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getResponse(ServerRequestInterface $request, array $args, JTLSmarty $smarty): ResponseInterface
    {
        $this->smarty = $smarty;
        Shop::setPageType(\PAGE_REGISTRIERUNG);
        $linkHelper = Shop::Container()->getLinkService();
        if (Request::verifyGPCDataInt('editRechnungsadresse') === 0 && Frontend::getCustomer()->getID() > 0) {
            return new RedirectResponse($linkHelper->getStaticRoute('jtl.php'), 301);
        }

        require_once \PFAD_ROOT . \PFAD_INCLUDES . 'bestellvorgang_inc.php';
        require_once \PFAD_ROOT . \PFAD_INCLUDES . 'registrieren_inc.php';

        $this->step = 'formular';
        $edit       = Request::gInt('editRechnungsadresse');
        if (isset($_POST['editRechnungsadresse'])) {
            $edit = (int)$_POST['editRechnungsadresse'];
        }
        if (Form::validateToken() && Request::pInt('form') === 1) {
            $this->saveCustomer($_POST);
        }
        $title = Request::gInt('editRechnungsadresse') === 1
            ? Shop::Lang()->get('editData', 'login')
            : Shop::Lang()->get('newAccount', 'login');
        if ($this->step === 'formular') {
            $this->getFormData(Request::verifyGPCDataInt('checkout'));
        }
        $this->smarty->assign('editRechnungsadresse', $edit)
            ->assign('Ueberschrift', $title)
            ->assign('Link', $this->currentLink)
            ->assign('step', $this->step)
            ->assign('nAnzeigeOrt', \CHECKBOX_ORT_REGISTRIERUNG)
            ->assign('code_registrieren', false)
            ->assign('unregForm', 0);

        $this->canonicalURL = $this->currentLink->getURL();

        $this->preRender();
        if (($this->config['kunden']['kundenregistrierung_pruefen_zeit'] ?? 'N') === 'Y') {
            $_SESSION['dRegZeit'] = \time();
        }
        if (Request::verifyGPCDataInt('accountDeleted') === 1) {
            $this->alertService->addSuccess(
                Shop::Lang()->get('accountDeleted', 'messages'),
                'accountDeleted'
            );
        }

        \executeHook(\HOOK_REGISTRIEREN_PAGE);

        return $this->smarty->getResponse('register/index.tpl');
    }

    /**
     * @param array $post
     * @return array|int
     * @former kundeSpeichern()
     * @since 5.2.0
     */
    public function saveCustomer(array $post): array|int
    {
        unset($_SESSION['Lieferadresse'], $_SESSION['Versandart'], $_SESSION['Zahlungsart']);
        $conf = $this->config['global']['global_kundenkonto_aktiv'];
        $cart = Frontend::getCart();
        $cart->loescheSpezialPos(\C_WARENKORBPOS_TYP_VERSANDPOS)
            ->loescheSpezialPos(\C_WARENKORBPOS_TYP_ZAHLUNGSART);

        $edit         = (int)$post['editRechnungsadresse'] > 0;
        $this->step   = 'formular';
        $form         = new CustomerForm();
        $filteredPost = Text::filterXSS($post);
        $this->smarty->assign('cPost_arr', $filteredPost)
            ->assign('cPost_var', $filteredPost);
        $missingData        = $form->checkKundenFormular(true, !$edit);
        $customerData       = $form->getCustomerData($post, true, false);
        $customerAttributes = $form->getCustomerAttributes($post);
        $checkbox           = new CheckBox(0, $this->db);
        $missingData        = \array_merge(
            $missingData,
            $checkbox->validateCheckBox(\CHECKBOX_ORT_REGISTRIERUNG, $this->customerGroupID, $post, true)
        );

        if (isset($post['shipping_address'])) {
            if ((int)$post['shipping_address'] === 0) {
                $post['kLieferadresse'] = 0;
                $post['lieferdaten']    = 1;
                $form->pruefeLieferdaten($post);
            } elseif (isset($post['kLieferadresse']) && (int)$post['kLieferadresse'] > 0) {
                $form->pruefeLieferdaten($post);
            } elseif (isset($post['register']['shipping_address'])) {
                $form->pruefeLieferdaten($post['register']['shipping_address'], $missingData);
            }
        } elseif (isset($post['lieferdaten']) && (int)$post['lieferdaten'] === 1) {
            // compatibility with older template
            $form->pruefeLieferdaten($post, $missingData);
        }
        $nReturnValue = Form::hasNoMissingData($missingData);

        \executeHook(\HOOK_REGISTRIEREN_PAGE_REGISTRIEREN_PLAUSI, [
            'nReturnValue'    => &$nReturnValue,
            'fehlendeAngaben' => &$missingData
        ]);

        if ($nReturnValue) {
            // CheckBox Spezialfunktion ausführen
            $checkbox->triggerSpecialFunction(
                \CHECKBOX_ORT_REGISTRIERUNG,
                $this->customerGroupID,
                true,
                $post,
                ['oKunde' => $customerData]
            )->checkLogging(\CHECKBOX_ORT_REGISTRIERUNG, $this->customerGroupID, $post, true);

            if ($edit && $_SESSION['Kunde']->kKunde > 0) {
                $customerData->cAbgeholt = 'N';
                $customerData->updateInDB();
                $customerData->cPasswort = null;
                // Kundendatenhistory
                DataHistory::saveHistory($_SESSION['Kunde'], $customerData, DataHistory::QUELLE_BESTELLUNG);

                $_SESSION['Kunde'] = $customerData;
                // Update Kundenattribute
                $customerAttributes->save();

                $_SESSION['Kunde'] = new Customer($_SESSION['Kunde']->kKunde, null, $this->db);
                $_SESSION['Kunde']->getCustomerAttributes()->load($_SESSION['Kunde']->kKunde);
            } else {
                $customerData->kKundengruppe     = $this->customerGroupID;
                $customerData->kSprache          = $this->languageID;
                $customerData->cAbgeholt         = 'N';
                $customerData->cSperre           = 'N';
                $customerData->cAktiv            = $conf === 'A' ? 'N' : 'Y';
                $cleartextPassword               = $customerData->cPasswort;
                $customerData->cPasswort         = Shop::Container()->getPasswordService()->hash($cleartextPassword);
                $customerData->dErstellt         = 'NOW()';
                $customerData->nRegistriert      = 1;
                $customerData->angezeigtesLand   = LanguageHelper::getCountryCodeByCountryName($customerData->cLand);
                $country                         = $customerData->cLand;
                $customerData->cPasswortKlartext = $cleartextPassword;
                $obj                             = new stdClass();
                $obj->tkunde                     = $customerData;

                $mailer = Shop::Container()->getMailer();
                $mail   = new Mail();
                $mail->setCustomerGroupID($this->customerGroupID);
                $mailer->send($mail->createFromTemplateID(\MAILTEMPLATE_NEUKUNDENREGISTRIERUNG, $obj));

                $customerData->cLand = $country;
                unset($customerData->cPasswortKlartext, $customerData->Anrede);

                $customerData->kKunde = $customerData->insertInDB();

                \executeHook(\HOOK_REGISTRATION_CUSTOMER_CREATED, [
                    'customerID' => $customerData->kKunde,
                ]);

                Campaign::setCampaignAction(\KAMPAGNE_DEF_ANMELDUNG, $customerData->kKunde, 1.0); // Anmeldung
                $customerAttributes->setCustomerID($customerData->kKunde);
                $customerAttributes->save();
                if ($conf !== 'A') {
                    $_SESSION['Kunde'] = new Customer($customerData->kKunde, null, $this->db);
                    $_SESSION['Kunde']->getCustomerAttributes()->load($customerData->kKunde);
                } else {
                    $this->step = 'formular eingegangen';
                }
            }
            if (isset($cart->kWarenkorb) && $cart->gibAnzahlArtikelExt([\C_WARENKORBPOS_TYP_ARTIKEL]) > 0) {
                Tax::setTaxRates();
                $cart->gibGesamtsummeWarenLocalized();
            }
            if ((int)$post['checkout'] === 1) {
                \header(
                    'Location: ' . Shop::Container()->getLinkService()
                        ->getStaticRoute('bestellvorgang.php') . '?reg=1',
                    true,
                    303
                );
                exit;
            }
            if (isset($post['ajaxcheckout_return']) && (int)$post['ajaxcheckout_return'] === 1) {
                return 1;
            }
            if ($conf !== 'A') {
                \header(
                    'Location: ' . Shop::Container()->getLinkService()
                        ->getStaticRoute('jtl.php') . '?reg=1',
                    true,
                    303
                );
                exit;
            }
        } else {
            $customerData->getCustomerAttributes()->assign($customerAttributes);
            if ((int)$post['checkout'] === 1) {
                $_SESSION['checkout.register']        = 1;
                $_SESSION['checkout.fehlendeAngaben'] = $missingData;
                $_SESSION['checkout.cPost_arr']       = $post;
                if (isset($post['register']['shipping_address'])) {
                    $_SESSION['Lieferadresse'] = Lieferadresse::createFromPost($post['register']['shipping_address']);
                }
                \header(
                    'Location: ' . Shop::Container()->getLinkService()
                        ->getStaticRoute('bestellvorgang.php') . '?reg=1',
                    true,
                    303
                );
                exit;
            }
            $this->smarty->assign('fehlendeAngaben', $missingData);

            return $missingData;
        }

        return [];
    }

    /**
     * @param int $checkout
     * @former gibFormularDaten()
     * @since 5.2.0
     */
    public function getFormData(int $checkout = 0): void
    {
        $customer = Frontend::getCustomer();
        $this->smarty->assign('herkunfte', [])
            ->assign('Kunde', $customer)
            ->assign(
                'customerAttributes',
                \is_a($customer, Customer::class)
                    ? $customer->getCustomerAttributes()
                    : new CustomerAttributes()
            )
            ->assign(
                'laender',
                ShippingMethod::getPossibleShippingCountries($this->customerGroupID, false, true)
            )
            ->assign(
                'warning_passwortlaenge',
                Shop::Lang()->get(
                    'minCharLen',
                    'messages',
                    $this->config['kunden']['kundenregistrierung_passwortlaenge']
                )
            )
            ->assign('oKundenfeld_arr', new CustomerFields($this->languageID, $this->db, $this->cache));

        if ($checkout === 1) {
            $this->smarty->assign('checkout', 1)
                ->assign('bestellschritt', [1 => 1, 2 => 3, 3 => 3, 4 => 3, 5 => 3]); // Rechnungsadresse ändern
        }
    }
}
