<?php

declare(strict_types=1);

namespace JTL\Router\Controller\Backend;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use JsonException;
use JTL\Backend\Permissions;
use JTL\Backend\ShippingClassWizard\Helper;
use JTL\Backend\ShippingClassWizard\Wizard;
use JTL\Checkout\Versandart;
use JTL\Country\Country;
use JTL\Country\Manager;
use JTL\Customer\CustomerGroup;
use JTL\Helpers\Form;
use JTL\Helpers\Request;
use JTL\Helpers\Tax;
use JTL\Helpers\Text;
use JTL\Language\LanguageHelper;
use JTL\Language\LanguageModel;
use JTL\Pagination\Pagination;
use JTL\Plugin\Helper as PluginHelper;
use JTL\Services\JTL\CountryService;
use JTL\Services\JTL\CountryServiceInterface;
use JTL\Shop;
use JTL\Smarty\JTLSmarty;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

/**
 * Class ShippingMethodsController
 * @package JTL\Router\Controller\Backend
 */
class ShippingMethodsController extends AbstractBackendController
{
    /**
     * @var CountryServiceInterface
     */
    private CountryServiceInterface $countryService;

    /**
     * @var stdClass
     */
    private stdClass $defaultCurrency;

    /**
     * @var stdClass|null
     */
    private ?stdClass $shippingType = null;

    /**
     * @var stdClass|null
     */
    private ?stdClass $shippingMethod = null;

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function getResponse(ServerRequestInterface $request, array $args, JTLSmarty $smarty): ResponseInterface
    {
        $this->smarty = $smarty;
        $this->checkPermissions(Permissions::ORDER_SHIPMENT_VIEW);
        $this->getText->loadAdminLocale('pages/versandarten');
        $this->getText->loadAdminLocale('pages/shippingclass_wizard');
        Tax::setTaxRates();
        $defaultCurrency = $this->db->select('twaehrung', 'cStandard', 'Y');
        if ($defaultCurrency === null) {
            throw new \Exception('Could not find default currency.');
        }
        $this->step            = 'uebersicht';
        $this->defaultCurrency = $defaultCurrency;
        $taxRateKeys           = \array_keys($_SESSION['Steuersatz']);
        $this->countryService  = Shop::Container()->getCountryService();
        $this->assignScrollPosition();

        $postData                   = Text::filterXSS($_POST);
        $manager                    = new Manager(
            $this->db,
            $smarty,
            $this->countryService,
            $this->cache,
            $this->alertService,
            $this->getText
        );
        $missingShippingClassCombis = $this->getMissingShippingClassCombi();
        $smarty->assign('missingShippingClassCombis', $missingShippingClassCombis);
        if (Form::validateToken()) {
            $this->checkPostParams($postData, $manager);
        }
        if ($this->step === 'neue Versandart' && $this->shippingType !== null) {
            $this->actionCreateNew();
        }
        if ($this->step === 'uebersicht') {
            $this->actionOverview();
        }
        if ($this->step === 'Zuschlagsliste') {
            $this->actionSurchargeList($postData);
        }
        $languages = LanguageHelper::getInstance()->gibInstallierteSprachen();
        $tmpID     = (int)($this->shippingMethod->kVersandart ?? 0);
        return $smarty->assign('fSteuersatz', $_SESSION['Steuersatz'][$taxRateKeys[0]])
            ->assign('waehrung', $this->defaultCurrency->cName)
            ->assign('oWaehrung', $this->db->select('twaehrung', 'cStandard', 'Y'))
            ->assign(
                'continents',
                $this->countryService->getCountriesGroupedByContinent(
                    true,
                    \explode(' ', $this->shippingMethod->cLaender ?? '')
                )
            )
            ->assign('customerGroups', CustomerGroup::getGroups())
            ->assign('oVersandartSpracheAssoc_arr', $this->getShippingLanguage($tmpID, $languages))
            ->assign(
                'gesetzteVersandklassen',
                isset($this->shippingMethod->cVersandklassen)
                    ? $this->getActiveShippingClasses($this->shippingMethod->cVersandklassen)
                    : null
            )
            ->assign(
                'gesetzteKundengruppen',
                isset($this->shippingMethod->cKundengruppen)
                    ? $this->getActiveCustomerGroups($this->shippingMethod->cKundengruppen)
                    : null
            )
            ->assign('step', $this->step)
            ->assign('route', $this->route)
            ->getResponse('versandarten.tpl');
    }

    /**
     * @param float|numeric-string $price
     * @param float|numeric-string $taxRate
     * @return float
     * @former berechneVersandpreisBrutto()
     */
    private function getShippingCostsGross(float|string $price, float|string $taxRate): float
    {
        return $price > 0
            ? \round(($price * ((100 + $taxRate) / 100)), 2)
            : 0.0;
    }

    /**
     * @param float|numeric-string $price
     * @param float|numeric-string $taxRate
     * @return float
     * @former berechneVersandpreisNetto()
     */
    private function getShippingCostsNet(float|string $price, float|string $taxRate): float
    {
        return $price > 0
            ? \round($price * ((100 / (100 + $taxRate)) * 100) / 100, 2)
            : 0.0;
    }

    /**
     * @param stdClass[] $objects
     * @return stdClass[]
     */
    private function reorganizeObjectArray(array $objects): array
    {
        $key = 'kZahlungsart';
        $res = [];
        foreach ($objects as $obj) {
            $arr  = \get_object_vars($obj);
            $keys = \array_keys($arr);
            if (!\in_array($key, $keys, true)) {
                continue;
            }
            $res[$obj->$key]           = new stdClass();
            $res[$obj->$key]->checked  = 'checked';
            $res[$obj->$key]->selected = 'selected';
            foreach ($keys as $k) {
                if ($key !== $k) {
                    $res[$obj->$key]->$k = $obj->$k;
                }
            }
        }

        return $res;
    }

    /**
     * @param stdClass[] $arr
     * @return stdClass[]
     * @former P()
     */
    public function transformItem(array $arr): array
    {
        $newArr = [];
        foreach ($arr as $ele) {
            $newArr = $this->buildObjectData($newArr, $ele);
        }

        return $newArr;
    }

    /**
     * @param stdClass[] $arr
     * @param stdClass   $key
     * @return stdClass[]
     * @former bauePot()
     */
    private function buildObjectData(array $arr, stdClass $key): array
    {
        foreach ($arr as $val) {
            $obj                 = new stdClass();
            $obj->kVersandklasse = $val->kVersandklasse . '-' . $key->kVersandklasse;
            $obj->cName          = $val->cName . ', ' . $key->cName;
            $arr[]               = $obj;
        }
        $arr[] = $key;

        return $arr;
    }

    /**
     * @param string $shippingClasses
     * @return array<string, bool>
     * @former gibGesetzteVersandklassen()
     */
    private function getActiveShippingClasses(string $shippingClasses): array
    {
        if (\trim($shippingClasses) === '-1') {
            return ['alle' => true];
        }
        $gesetzteVK = [];
        $uniqueIDs  = [];
        $classes    = \explode(' ', \trim($shippingClasses));
        // $cVersandklassen is a string like "1 3-4 5-6-7 6-8 7-8 3-7 3-8 5-6 5-7"
        foreach ($classes as $idString) {
            // we want the single kVersandklasse IDs to reduce the possible amount of combinations
            foreach (\explode('-', $idString) as $kVersandklasse) {
                $uniqueIDs[] = (int)$kVersandklasse;
            }
        }
        $items = $this->transformItem(
            $this->db->getObjects(
                'SELECT * 
                    FROM tversandklasse
                    WHERE kVersandklasse IN (' . \implode(',', $uniqueIDs) . ')  
                    ORDER BY kVersandklasse'
            )
        );
        foreach ($items as $vk) {
            $gesetzteVK[(string)$vk->kVersandklasse] = \in_array($vk->kVersandklasse, $classes, true);
        }

        return $gesetzteVK;
    }

    /**
     * @param string $shippingClasses
     * @return string[]
     * @former gibGesetzteVersandklassenUebersicht()
     */
    public function getActiveShippingClassesOverview(string $shippingClasses): array
    {
        if (\trim($shippingClasses) === '-1') {
            return [\__('allCombinations')];
        }
        $active    = [];
        $uniqueIDs = [];
        $classes   = \explode(' ', \trim($shippingClasses));
        // $cVersandklassen is a string like "1 3-4 5-6-7 6-8 7-8 3-7 3-8 5-6 5-7"
        foreach ($classes as $idString) {
            // we want the single kVersandklasse IDs to reduce the possible amount of combinations
            foreach (\explode('-', $idString) as $kVersandklasse) {
                $uniqueIDs[] = (int)$kVersandklasse;
            }
        }
        $items = $this->transformItem(
            $this->db->getObjects(
                'SELECT * 
                    FROM tversandklasse 
                    WHERE kVersandklasse IN (' . \implode(',', $uniqueIDs) . ')
                    ORDER BY kVersandklasse'
            )
        );
        foreach ($items as $item) {
            if (\in_array($item->kVersandklasse, $classes, true)) {
                $active[] = $item->cName;
            }
        }

        return $active;
    }

    /**
     * @param string $customerGroupsString
     * @return array<int|string, bool>
     * @former gibGesetzteKundengruppen()
     */
    private function getActiveCustomerGroups(string $customerGroupsString): array
    {
        $activeGroups = [];
        $groups       = Text::parseSSKint($customerGroupsString);
        $groupData    = $this->db->getInts(
            'SELECT kKundengruppe
                FROM tkundengruppe
                ORDER BY kKundengruppe',
            'kKundengruppe'
        );
        foreach ($groupData as $id) {
            $activeGroups[$id] = \in_array($id, $groups, true);
        }
        $activeGroups['alle'] = $customerGroupsString === '-1';

        return $activeGroups;
    }

    /**
     * @param int             $shippingMethodID
     * @param LanguageModel[] $languages
     * @return array<string, stdClass>
     */
    private function getShippingLanguage(int $shippingMethodID, array $languages): array
    {
        $localized        = [];
        $localizedMethods = $this->db->selectAll(
            'tversandartsprache',
            'kVersandart',
            $shippingMethodID
        );
        foreach ($languages as $language) {
            $localized[$language->getCode()] = new stdClass();
        }
        foreach ($localizedMethods as $localizedMethod) {
            if (isset($localizedMethod->kVersandart) && $localizedMethod->kVersandart > 0) {
                $localized[(string)$localizedMethod->cISOSprache] = $localizedMethod;
            }
        }

        return $localized;
    }

    /**
     * @param int $feeID
     * @return array<string, string>
     * @former getZuschlagNames()
     */
    private function getFeeNames(int $feeID): array
    {
        $names = [];
        if (!$feeID) {
            return $names;
        }
        $localized = $this->db->selectAll(
            'tversandzuschlagsprache',
            'kVersandzuschlag',
            $feeID
        );
        foreach ($localized as $name) {
            $names[$name->cISOSprache] = $name->cName;
        }

        return $names;
    }

    /**
     * @param string[] $shipClasses
     * @param int      $length
     * @return array<int, array<int, string>>
     */
    private function getCombinations(array $shipClasses, int $length): array
    {
        $baselen = \count($shipClasses);
        if ($baselen === 0) {
            return [];
        }
        if ($length === 1) {
            $return = [];
            foreach ($shipClasses as $b) {
                $return[] = [$b];
            }

            return $return;
        }

        // get one level lower combinations
        $oneLevelLower = $this->getCombinations($shipClasses, $length - 1);
        // for every one level lower combinations add one element to them
        // that the last element of a combination is preceeded by the element
        // which follows it in base array if there is none, does not add
        $newCombs = [];
        foreach ($oneLevelLower as $oll) {
            $lastEl = $oll[$length - 2];
            $found  = false;
            foreach ($shipClasses as $key => $b) {
                if ($b === $lastEl) {
                    $found = true;
                    continue;
                    // last element found
                }
                if ($found === true && $key < $baselen) {
                    // add to combinations with last element
                    $tmp              = $oll;
                    $newCombination   = \array_slice($tmp, 0);
                    $newCombination[] = $b;
                    $newCombs[]       = \array_slice($newCombination, 0);
                }
            }
        }

        return $newCombs;
    }

    /**
     * @return string[]|int -1 if too many shipping classes exist
     */
    private function getMissingShippingClassCombi(): array|int
    {
        $shippingClasses         = $this->db->selectAll('tversandklasse', [], [], 'kVersandklasse');
        $combinationsInShippings = $this->db->selectAll('tversandart', [], [], 'cVersandklassen');
        $shipClasses             = [];
        $combinationInUse        = [];
        foreach ($shippingClasses as $sc) {
            $shipClasses[] = $sc->kVersandklasse;
        }
        foreach ($combinationsInShippings as $com) {
            foreach (\explode(' ', \trim($com->cVersandklassen)) as $class) {
                $combinationInUse[] = \trim($class);
            }
        }
        // if a shipping method is valid for all classes return
        if (\in_array('-1', $combinationInUse, true)) {
            return [];
        }

        $len = \count($shipClasses);
        if ($len > \SHIPPING_CLASS_MAX_VALIDATION_COUNT) {
            return -1;
        }

        $possibleShippingClassCombinations = [];
        for ($i = 1; $i <= $len; $i++) {
            $result = $this->getCombinations($shipClasses, $i);
            foreach ($result as $c) {
                $possibleShippingClassCombinations[] = \implode('-', $c);
            }
        }
        $res = \array_diff($possibleShippingClassCombinations, $combinationInUse);
        foreach ($res as &$mscc) {
            $mscc = $this->getActiveShippingClassesOverview($mscc)[0];
        }

        return $res;
    }

    /**
     * @return stdClass[]
     */
    private function getShippingTypes(): array
    {
        $shippingTypes = $this->db->getCollection(
            'SELECT *
                FROM tversandberechnung
                ORDER BY cName'
        );

        return $shippingTypes->each(static function (stdClass $e): void {
            $e->kVersandberechnung = (int)$e->kVersandberechnung;
            $e->cName              = \__('shippingType_' . $e->cModulId);
        })->toArray();
    }

    /**
     * @param int $shippingTypeID
     * @return stdClass{kVersandberechnung: int, cName: string}
     * @throws InvalidArgumentException
     */
    private function getShippingType(int $shippingTypeID): stdClass
    {
        $shippingType = $this->db->getSingleObject(
            'SELECT *
                    FROM tversandberechnung
                    WHERE kVersandberechnung = :shippingTypeID
                    ORDER BY cName',
            ['shippingTypeID' => $shippingTypeID]
        );
        if ($shippingType === null) {
            throw new InvalidArgumentException('Shipping type not found');
        }
        $shippingType->kVersandberechnung = (int)$shippingType->kVersandberechnung;
        $shippingType->cName              = \__('shippingType_' . $shippingType->cModulId);

        return $shippingType;
    }

    /**
     * @param array<string, string> $postData
     * @throws InvalidArgumentException
     */
    private function actionSurchargeList(array $postData): void
    {
        /** @var string|null $iso */
        $iso = Request::getVar('cISO') ?? $postData['cISO'] ?? null;
        if ($iso === null) {
            throw new InvalidArgumentException('Country ISO not found');
        }
        $methodID = Request::gInt('kVersandart');
        if (isset($postData['kVersandart'])) {
            $methodID = Request::pInt('kVersandart');
        }
        $shippingMethod = $this->db->select('tversandart', 'kVersandart', $methodID);
        if ($shippingMethod === null) {
            throw new InvalidArgumentException('Shipping method not found');
        }
        $fees = $this->db->selectAll(
            'tversandzuschlag',
            ['kVersandart', 'cISO'],
            [(int)$shippingMethod->kVersandart, $iso],
            '*',
            'fZuschlag'
        );
        foreach ($fees as $item) {
            $item->kVersandzuschlag = (int)$item->kVersandzuschlag;
            $item->kVersandart      = (int)$item->kVersandart;
            $item->zuschlagplz      = $this->db->selectAll(
                'tversandzuschlagplz',
                'kVersandzuschlag',
                $item->kVersandzuschlag
            );
            $item->angezeigterName  = $this->getFeeNames($item->kVersandzuschlag);
        }
        $this->shippingMethod = $shippingMethod;
        $this->getSmarty()->assign('Versandart', $this->shippingMethod)
            ->assign('Land', $this->countryService->getCountry($iso))
            ->assign('Zuschlaege', $fees);
    }

    private function actionOverview(): void
    {
        $taxRateKeys     = \array_keys($_SESSION['Steuersatz']);
        $customerGroups  = $this->db->getObjects(
            'SELECT kKundengruppe, cName FROM tkundengruppe ORDER BY kKundengruppe'
        );
        $shippingMethods = $this->db->getObjects('SELECT * FROM tversandart ORDER BY nSort, cName');
        $taxRate         = $_SESSION['Steuersatz'][$taxRateKeys[0]];
        foreach ($shippingMethods as $method) {
            $method->versandartzahlungsarten = $this->db->getObjects(
                'SELECT tversandartzahlungsart.*
                FROM tversandartzahlungsart
                JOIN tzahlungsart
                    ON tzahlungsart.kZahlungsart = tversandartzahlungsart.kZahlungsart
                WHERE tversandartzahlungsart.kVersandart = :sid
                ORDER BY tzahlungsart.cAnbieter, tzahlungsart.nSort, tzahlungsart.cName',
                ['sid' => (int)$method->kVersandart]
            );

            foreach ($method->versandartzahlungsarten as $smp) {
                $smp->zahlungsart = $this->db->select(
                    'tzahlungsart',
                    'kZahlungsart',
                    (int)$smp->kZahlungsart,
                    'nActive',
                    1
                );
                if ($smp->zahlungsart === null) {
                    continue;
                }
                $smp->cAufpreisTyp = $smp->cAufpreisTyp === 'prozent' ? '%' : '';
                $pluginID          = PluginHelper::getIDByModuleID($smp->zahlungsart->cModulId);
                if ($pluginID > 0) {
                    try {
                        $this->getText->loadPluginLocale(
                            'base',
                            PluginHelper::getLoaderByPluginID($pluginID)->init($pluginID)
                        );
                    } catch (InvalidArgumentException) {
                        $this->getText->loadAdminLocale('pages/zahlungsarten');
                        $this->alertService->addWarning(
                            \sprintf(
                                \__('Plugin for payment method not found'),
                                $smp->zahlungsart->cName,
                                $smp->zahlungsart->cAnbieter
                            ),
                            'notfound_' . $pluginID,
                            [
                                'linkHref' => Shop::getURL(true) . $this->route,
                                'linkText' => \__('paymentTypesOverview')
                            ]
                        );
                        continue;
                    }
                }
                $smp->zahlungsart->cName     = \__($smp->zahlungsart->cName);
                $smp->zahlungsart->cAnbieter = \__($smp->zahlungsart->cAnbieter);
            }
            $method->versandartstaffeln         = $this->db->selectAll(
                'tversandartstaffel',
                'kVersandart',
                (int)$method->kVersandart,
                '*',
                'fBis'
            );
            $method->fPreisBrutto               = $this->getShippingCostsGross($method->fPreis, $taxRate);
            $method->fVersandkostenfreiAbXNetto = $this->getShippingCostsNet($method->fVersandkostenfreiAbX, $taxRate);
            $method->fDeckelungBrutto           = $this->getShippingCostsGross($method->fDeckelung, $taxRate);
            foreach ($method->versandartstaffeln as $j => $oVersandartstaffeln) {
                $method->versandartstaffeln[$j]->fPreisBrutto = $this->getShippingCostsGross(
                    $oVersandartstaffeln->fPreis,
                    $taxRate
                );
            }

            $method->versandberechnung = $this->getShippingType((int)$method->kVersandberechnung);
            $method->versandklassen    = $this->getActiveShippingClassesOverview($method->cVersandklassen);
            if ($method->versandberechnung->cModulId === 'vm_versandberechnung_gewicht_jtl') {
                $method->einheit = 'kg';
            } elseif ($method->versandberechnung->cModulId === 'vm_versandberechnung_warenwert_jtl') {
                $method->einheit = $this->defaultCurrency->cName;
            } elseif ($method->versandberechnung->cModulId === 'vm_versandberechnung_artikelanzahl_jtl') {
                $method->einheit = 'Stück';
            }
            $method->countries                  = new Collection();
            $method->shippingSurchargeCountries = \array_column(
                $this->db->getArrays(
                    'SELECT DISTINCT cISO FROM tversandzuschlag WHERE kVersandart = :shippingMethodID',
                    ['shippingMethodID' => (int)$method->kVersandart]
                ),
                'cISO'
            );
            foreach (\explode(' ', \trim($method->cLaender)) as $item) {
                if (($country = $this->countryService->getCountry($item)) !== null) {
                    $method->countries->push($country);
                }
            }
            $method->countries               = $method->countries->sortBy(static function (Country $country): string {
                return $country->getName();
            });
            $method->cKundengruppenName_arr  = [];
            $method->oVersandartSprachen_arr = $this->db->selectAll(
                'tversandartsprache',
                'kVersandart',
                (int)$method->kVersandart,
                'cName',
                'cISOSprache'
            );
            foreach (Text::parseSSKint($method->cKundengruppen) as $customerGroupID) {
                if ($customerGroupID === -1) {
                    $method->cKundengruppenName_arr[] = \__('allCustomerGroups');
                } else {
                    foreach ($customerGroups as $customerGroup) {
                        if ((int)$customerGroup->kKundengruppe === $customerGroupID) {
                            $method->cKundengruppenName_arr[] = $customerGroup->cName;
                        }
                    }
                }
            }
        }

        $missingShippingClassCombis = $this->getMissingShippingClassCombi();
        if (!empty($missingShippingClassCombis)) {
            $error = $this->getSmarty()->assign('missingShippingClassCombis', $missingShippingClassCombis)
                ->fetch('tpl_inc/versandarten_fehlende_kombis.tpl');
            $this->alertService->addError($error, 'errorMissingShippingClassCombis');
        }

        $this->getSmarty()->assign('versandberechnungen', $this->getShippingTypes())
            ->assign('versandarten', $shippingMethods);
    }

    private function actionCreateNew(): void
    {
        if ($this->shippingType !== null) {
            if ($this->shippingType->cModulId === 'vm_versandberechnung_gewicht_jtl') {
                $this->getSmarty()->assign('einheit', 'kg');
            } elseif ($this->shippingType->cModulId === 'vm_versandberechnung_warenwert_jtl') {
                $this->getSmarty()->assign('einheit', $this->defaultCurrency->cName);
            } elseif ($this->shippingType->cModulId === 'vm_versandberechnung_artikelanzahl_jtl') {
                $this->getSmarty()->assign('einheit', 'Stück');
            }
        }
        // prevent "unusable" payment methods from displaying them in the config section (mainly the null-payment)
        $paymentMethods = $this->db->selectAll(
            'tzahlungsart',
            ['nActive', 'nNutzbar'],
            [1, 1],
            '*',
            'cAnbieter, nSort, cName, cModulId'
        );
        foreach ($paymentMethods as $paymentMethod) {
            $pluginID = PluginHelper::getIDByModuleID($paymentMethod->cModulId);
            if ($pluginID > 0) {
                try {
                    $this->getText->loadPluginLocale(
                        'base',
                        PluginHelper::getLoaderByPluginID($pluginID)->init($pluginID)
                    );
                } catch (InvalidArgumentException) {
                    $this->getText->loadAdminLocale('pages/zahlungsarten');
                    $this->alertService->addWarning(
                        \sprintf(
                            \__('Plugin for payment method not found'),
                            $paymentMethod->cName,
                            $paymentMethod->cAnbieter
                        ),
                        'notfound_' . $pluginID,
                        [
                            'linkHref' => Shop::getURL(true) . $this->route,
                            'linkText' => \__('paymentTypesOverview')
                        ]
                    );
                    continue;
                }
            }
            $paymentMethod->cName     = \__($paymentMethod->cName);
            $paymentMethod->cAnbieter = \__($paymentMethod->cAnbieter);
        }

        $this->appendWizard();
        $this->getSmarty()->assign('zahlungsarten', $paymentMethods)
            ->assign('versandKlassen', $this->db->selectAll('tversandklasse', [], [], '*', 'kVersandklasse'))
            ->assign('versandlaender', $this->countryService->getCountrylist())
            ->assign('versandberechnung', $this->shippingType)
            ->assign('waehrung', $this->defaultCurrency->cName);
    }

    /**
     * @return void
     */
    private function appendWizard(): void
    {
        $helper           = Helper::instance($this->db, $this);
        $shippingClassCnt = $helper->getShippingClassCount();
        $enabled          = $shippingClassCnt > 0 && $shippingClassCnt <= Wizard::MAX_CLASS_COUNT;
        $shippingClasses  = $this->shippingMethod->cVersandklassen ?? '-1';
        $definition       = $helper->loadDefinition((int)($this->shippingMethod->kVersandart ?? 0), $shippingClasses);
        $scHash           = $helper->createResultHash($shippingClasses);

        try {
            $wizardMethods = \json_encode(
                $this->getActiveShippingClassesOverview($shippingClasses),
                \JSON_THROW_ON_ERROR
            );
        } catch (JsonException) {
            $wizardMethods = '';
        }
        $this->getSmarty()->assign('wizardEnabled', $enabled)
            ->assign('wizardJsonShippingMethods', $wizardMethods)
            ->assign('isWizardDefinition', $definition->isEqualHash($scHash));
    }

    /**
     * @param int|null $shippingId
     * @return stdClass
     * @throws InvalidArgumentException
     */
    private function actionEdit(?int $shippingId = null): stdClass
    {
        $shippingId     = $shippingId ?? Request::pInt('edit');
        $this->step     = 'neue Versandart';
        $shippingMethod = $this->db->select('tversandart', 'kVersandart', $shippingId);
        if ($shippingMethod === null) {
            throw new InvalidArgumentException('Shipping method not found');
        }
        $mappedMethods  = $this->db->selectAll(
            'tversandartzahlungsart',
            'kVersandart',
            $shippingId,
            '*',
            'kZahlungsart'
        );
        $shippingScales = $this->db->selectAll(
            'tversandartstaffel',
            'kVersandart',
            Request::pInt('edit'),
            '*',
            'fBis'
        );
        $shippingType   = $this->getShippingType((int)$shippingMethod->kVersandberechnung);

        $shippingMethod->cVersandklassen = \trim($shippingMethod->cVersandklassen);

        $this->appendWizard();
        $this->getSmarty()->assign('VersandartStaffeln', $shippingScales)
            ->assign('VersandartZahlungsarten', $this->reorganizeObjectArray($mappedMethods))
            ->assign('Versandart', $shippingMethod)
            ->assign('gewaehlteLaender', \explode(' ', $shippingMethod->cLaender));
        $this->shippingMethod = $shippingMethod;

        return $shippingType;
    }

    /**
     * @param mixed[] $postData
     * @param Manager $manager
     * @return stdClass
     */
    private function createOrUpdate(array $postData, Manager $manager): stdClass
    {
        $oldShippingMethod = null;
        /** @var string[] $postCountries */
        $postCountries                            = $postData['land'] ?? [];
        $languages                                = LanguageHelper::getInstance()->gibInstallierteSprachen();
        $shippingMethod                           = new stdClass();
        $shippingMethod->cName                    = \htmlspecialchars(
            $postData['cName'],
            \ENT_COMPAT | \ENT_HTML401,
            \JTL_CHARSET
        );
        $shippingMethod->kVersandberechnung       = Request::pInt('kVersandberechnung');
        $shippingMethod->cAnzeigen                = $postData['cAnzeigen'];
        $shippingMethod->cBild                    = $postData['cBild'];
        $shippingMethod->nSort                    = Request::pInt('nSort');
        $shippingMethod->nMinLiefertage           = Request::pInt('nMinLiefertage');
        $shippingMethod->nMaxLiefertage           = Request::pInt('nMaxLiefertage');
        $shippingMethod->cNurAbhaengigeVersandart = $postData['cNurAbhaengigeVersandart'];
        $shippingMethod->cSendConfirmationMail    = $postData['cSendConfirmationMail'] ?? 'Y';
        $shippingMethod->cIgnoreShippingProposal  = $postData['cIgnoreShippingProposal'] ?? 'N';
        $shippingMethod->eSteuer                  = $postData['eSteuer'];
        $shippingMethod->fPreis                   = (float)\str_replace(',', '.', $postData['fPreis'] ?? '0');
        // Versandkostenfrei ab X
        $shippingMethod->fVersandkostenfreiAbX = Request::pInt('versandkostenfreiAktiv') === 1
            ? (float)$postData['fVersandkostenfreiAbX']
            : 0;
        // Deckelung
        $shippingMethod->fDeckelung = Request::pInt('versanddeckelungAktiv') === 1
            ? (float)$postData['fDeckelung']
            : 0;

        $shippingMethod->cLaender = '';
        foreach (\array_unique($postCountries) as $postIso) {
            $shippingMethod->cLaender .= $postIso . ' ';
        }

        $mappedMethods = [];
        foreach (Request::verifyGPDataIntegerArray('kZahlungsart') as $paymentMethodID) {
            $mappedMethod               = new stdClass();
            $mappedMethod->kZahlungsart = $paymentMethodID;
            $surcharge                  = $postData['fAufpreis_' . $paymentMethodID];
            if ((float)$surcharge !== 0.0) {
                $mappedMethod->fAufpreis    = (float)\str_replace(
                    ',',
                    '.',
                    $postData['fAufpreis_' . $paymentMethodID]
                );
                $mappedMethod->cAufpreisTyp = $postData['cAufpreisTyp_' . $paymentMethodID];
            }
            $mappedMethods[] = $mappedMethod;
        }

        $lastScaleTo    = 0.0;
        $shippingScales = [];
        $staffelDa      = true;
        if (
            $this->shippingType !== null
            && ($this->shippingType->cModulId === 'vm_versandberechnung_gewicht_jtl'
                || $this->shippingType->cModulId === 'vm_versandberechnung_warenwert_jtl'
                || $this->shippingType->cModulId === 'vm_versandberechnung_artikelanzahl_jtl')
        ) {
            $staffelDa = false;
            if (\count($postData['bis']) > 0 && \count($postData['preis']) > 0) {
                $staffelDa = true;
            }
            //preisstaffel beachten
            if (
                !isset($postData['bis'][0], $postData['preis'][0])
                || \mb_strlen($postData['bis'][0]) === 0
                || \mb_strlen($postData['preis'][0]) === 0
            ) {
                $staffelDa = false;
            }
            if (\is_array($postData['bis']) && \is_array($postData['preis'])) {
                foreach ($postData['bis'] as $i => $fBis) {
                    if (!isset($postData['preis'][$i]) || \mb_strlen($fBis) === 0) {
                        continue;
                    }
                    $scale         = new stdClass();
                    $scale->fBis   = (float)\str_replace(',', '.', $fBis);
                    $scale->fPreis = (float)\str_replace(',', '.', $postData['preis'][$i]);

                    $shippingScales[] = $scale;
                    $lastScaleTo      = $scale->fBis;
                }
            }
            // Dummy Versandstaffel hinzufuegen,
            // falls Versandart nach Warenwert und Versandkostenfrei ausgewaehlt wurde
            if (
                $this->shippingType->cModulId === 'vm_versandberechnung_warenwert_jtl'
                && Request::pInt('versandkostenfreiAktiv') === 1
            ) {
                $shippingMethod->fVersandkostenfreiAbX = $lastScaleTo + 0.01;

                $scale            = new stdClass();
                $scale->fBis      = 999999999;
                $scale->fPreis    = 0.0;
                $shippingScales[] = $scale;
            }
        }
        // Kundengruppe
        $shippingMethod->cKundengruppen = '';
        if (!isset($postData['kKundengruppe'])) {
            $postData['kKundengruppe'] = ['-1'];
        }
        if (\is_array($postData['kKundengruppe'])) {
            if (\in_array('-1', $postData['kKundengruppe'], true)) {
                $shippingMethod->cKundengruppen = '-1';
            } else {
                $shippingMethod->cKundengruppen = ';' . \implode(';', $postData['kKundengruppe']) . ';';
            }
        }
        // Versandklassen
        $shippingMethod->cVersandklassen = !empty($postData['kVersandklasse'])
        && $postData['kVersandklasse'] !== '-1'
            ? (' ' . $postData['kVersandklasse'] . ' ')
            : '-1';

        if (
            $shippingMethod->cName
            && $staffelDa
            && \count($postCountries) >= 1
            && \count($postData['kZahlungsart'] ?? []) >= 1
        ) {
            if (Request::pInt('kVersandart') === 0) {
                $methodID = $this->db->insert('tversandart', $shippingMethod);
                $this->alertService->addSuccess(
                    \sprintf(\__('successShippingMethodCreate'), $shippingMethod->cName),
                    'successShippingMethodCreate'
                );
            } else {
                //updaten
                $methodID          = Request::pInt('kVersandart');
                $oldShippingMethod = $this->db->select('tversandart', 'kVersandart', $methodID);
                $this->db->update('tversandart', 'kVersandart', $methodID, $shippingMethod);
                $this->db->delete('tversandartzahlungsart', 'kVersandart', $methodID);
                $this->db->delete('tversandartstaffel', 'kVersandart', $methodID);
                $this->alertService->addSuccess(
                    \sprintf(\__('successShippingMethodChange'), $shippingMethod->cName),
                    'successShippingMethodChange'
                );
            }
            $manager->updateRegistrationCountries(
                \array_diff(
                    $oldShippingMethod !== null
                        ? \explode(' ', \trim($oldShippingMethod->cLaender))
                        : [],
                    $postCountries
                )
            );
            if ($methodID > 0) {
                $shippingMethod->methodID = $methodID;
                foreach ($mappedMethods as $mappedMethod) {
                    $mappedMethod->kVersandart = $methodID;
                    $this->db->insert('tversandartzahlungsart', $mappedMethod);
                }

                foreach ($shippingScales as $scale) {
                    $scale->kVersandart = $methodID;
                    $this->db->insert('tversandartstaffel', $scale);
                }
                $localized = new stdClass();

                $localized->kVersandart = $methodID;
                foreach ($languages as $language) {
                    $code = $language->getCode();

                    $localized->cISOSprache = $code;
                    $localized->cName       = '';
                    if (!empty($postData['cName_' . $code])) {
                        $localized->cName = \htmlspecialchars(
                            $postData['cName_' . $code],
                            \ENT_COMPAT | \ENT_HTML401,
                            \JTL_CHARSET
                        );
                    }
                    $localized->cLieferdauer = '';
                    if (!empty($postData['cLieferdauer_' . $code])) {
                        $localized->cLieferdauer = \htmlspecialchars(
                            $postData['cLieferdauer_' . $code],
                            \ENT_COMPAT | \ENT_HTML401,
                            \JTL_CHARSET
                        );
                    }
                    $localized->cHinweistext = '';
                    if (!empty($postData['cHinweistext_' . $code])) {
                        $localized->cHinweistext = $postData['cHinweistext_' . $code];
                    }
                    $localized->cHinweistextShop = '';
                    if (!empty($postData['cHinweistextShop_' . $code])) {
                        $localized->cHinweistextShop = $postData['cHinweistextShop_' . $code];
                    }
                    $this->db->delete('tversandartsprache', ['kVersandart', 'cISOSprache'], [$methodID, $code]);
                    $this->db->insert('tversandartsprache', $localized);
                }
                $this->step = 'uebersicht';
            }
            $this->cache->flushTags([\CACHING_GROUP_OPTION, \CACHING_GROUP_ARTICLE]);
        } else {
            $this->step = 'neue Versandart';
            if (!$shippingMethod->cName) {
                $this->alertService->addError(\__('errorShippingMethodNameMissing'), 'errorShippingMethodNameMissing');
            }
            if (\count($postCountries) < 1) {
                $this->alertService->addError(
                    \__('errorShippingMethodCountryMissing'),
                    'errorShippingMethodCountryMissing'
                );
            }
            if (\count($postData['kZahlungsart'] ?? []) < 1) {
                $this->alertService->addError(
                    \__('errorShippingMethodPaymentMissing'),
                    'errorShippingMethodPaymentMissing'
                );
            }
            if (!$staffelDa) {
                $this->alertService->addError(
                    \__('errorShippingMethodPriceMissing'),
                    'errorShippingMethodPriceMissing'
                );
            }
            if (Request::pInt('kVersandart') > 0) {
                $shippingMethod = $this->db->select(
                    'tversandart',
                    'kVersandart',
                    Request::pInt('kVersandart')
                );
                if ($shippingMethod === null) {
                    throw new InvalidArgumentException('Shipping method not found');
                }
            }

            $this->getSmarty()->assign('VersandartZahlungsarten', $this->reorganizeObjectArray($mappedMethods))
                ->assign('VersandartStaffeln', $shippingScales)
                ->assign('Versandart', $shippingMethod)
                ->assign('gewaehlteLaender', \explode(' ', $shippingMethod->cLaender));
        }
        try {
            Wizard::instance($this->getSmarty(), $this)->save(
                (int)$postData['kVersandart'],
                Request::postVar('wizardShippingMethodDefinition', []),
                Request::pString('wizardShippingMethodHash')
            );
        } catch (JsonException $e) {
            $this->getAlertService()->addError(
                \__('errorSavingWizardDefinition', $e->getMessage()),
                'errorSavingData',
                ['saveInSession' => true]
            );
        }

        return $shippingMethod;
    }

    /**
     * @param array<string, string|int> $postData
     * @param Manager                   $manager
     * @return void
     */
    public function checkPostParams(array $postData, Manager $manager): void
    {
        if (Request::pInt('neu') === 1 && Request::postInt('kVersandberechnung') > 0) {
            $this->step = 'neue Versandart';
        }
        if (Request::pInt('kVersandberechnung') > 0) {
            $this->shippingType = $this->getShippingType(Request::verifyGPCDataInt('kVersandberechnung'));
        }
        if (Request::pInt('del') > 0) {
            $oldShippingMethod = $this->db->select('tversandart', 'kVersandart', (int)$postData['del']);
            Versandart::deleteInDB((int)$postData['del']);
            $manager->updateRegistrationCountries(\explode(' ', \trim($oldShippingMethod->cLaender ?? '')));
            $this->alertService->addSuccess(\__('successShippingMethodDelete'), 'successShippingMethodDelete');
            $this->cache->flushTags([\CACHING_GROUP_OPTION, \CACHING_GROUP_ARTICLE]);
        }
        if (Request::pInt('edit') > 0) {
            $this->shippingType = $this->actionEdit();
        }
        if (Request::pInt('clone') > 0) {
            $this->step = 'uebersicht';
            if (Versandart::cloneShipping((int)$postData['clone'])) {
                $this->alertService->addSuccess(
                    \__('successShippingMethodDuplicated'),
                    'successShippingMethodDuplicated'
                );
                $this->cache->flushTags([\CACHING_GROUP_OPTION]);
            } else {
                $this->alertService->addError(
                    \__('errorShippingMethodDuplicated'),
                    'errorShippingMethodDuplicated'
                );
            }
        }
        if (isset($_GET['cISO']) && Request::gInt('zuschlag') === 1 && Request::gInt('kVersandart') > 0) {
            $this->step = 'Zuschlagsliste';

            $pagination = (new Pagination('surchargeList'))
                ->setRange(4)
                ->setItemArray(
                    (new Versandart(Request::gInt('kVersandart')))
                        ->getShippingSurchargesForCountry($_GET['cISO'])
                )
                ->assemble();

            $this->getSmarty()->assign('surcharges', $pagination->getPageItems())
                ->assign('pagination', $pagination);
        }

        if (Request::pInt('neueVersandart') > 0) {
            $this->shippingMethod = $this->createOrUpdate($postData, $manager);
            if (($this->shippingMethod->methodID ?? 0) > 0 && Request::pString('saveAndContinue')) {
                $this->shippingType = $this->actionEdit($this->shippingMethod->methodID);
            }
        }
        $this->cache->flush(CountryService::CACHE_ID);
    }
}
