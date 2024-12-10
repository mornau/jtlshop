<?php

declare(strict_types=1);

namespace JTL\Cart;

use JTL\Catalog\Product\Artikel;
use JTL\Catalog\Product\EigenschaftWert;
use JTL\Catalog\Product\Preise;
use JTL\Checkout\Eigenschaft;
use JTL\Extensions\Config\Item;
use JTL\Helpers\ShippingMethod;
use JTL\Helpers\Tax;
use JTL\Session\Frontend;
use JTL\Shop;
use stdClass;

use function Functional\select;

/**
 * Class CartItem
 * @package JTL\Cart
 */
class CartItem
{
    /**
     * @var int|null
     */
    public ?int $kWarenkorbPos = null;

    /**
     * @var int|null
     */
    public ?int $kWarenkorb = null;

    /**
     * @var int|null
     */
    public ?int $kArtikel = null;

    /**
     * @var int
     */
    public int $kSteuerklasse = 0;

    /**
     * @var int
     */
    public int $kVersandklasse = 0;

    /**
     * @var int|string|null
     */
    public $nAnzahl;

    /**
     * @var int|null
     */
    public ?int $nPosTyp = null;

    /**
     * @var float|string|null
     */
    public $fPreisEinzelNetto;

    /**
     * @var float|string|null
     */
    public $fPreis;

    /**
     * @var float|null
     */
    public $fMwSt;

    /**
     * @var float|null
     */
    public $fGesamtgewicht;

    /**
     * @var array|string|null
     */
    public $cName;

    /**
     * @var string
     */
    public $cEinheit = '';

    /**
     * @var array|null
     */
    public ?array $cGesamtpreisLocalized = [];

    /**
     * @var string|null
     */
    public ?string $cHinweis = '';

    /**
     * @var string|false
     */
    public string|bool $cUnique = false;

    /**
     * @var string
     */
    public string $cResponsibility = '';

    /**
     * @var int|null
     */
    public ?int $kKonfigitem = null;

    /**
     * @var array|null
     */
    public ?array $cKonfigpreisLocalized = [];

    /**
     * @var Artikel|null
     */
    public ?Artikel $Artikel = null;

    /**
     * @var CartItemProperty[]
     */
    public array $WarenkorbPosEigenschaftArr = [];

    /**
     * @var stdClass[]
     */
    public array $variationPicturesArr = [];

    /**
     * @var int
     */
    public int $nZeitLetzteAenderung = 0;

    /**
     * @var float|string
     */
    public $fLagerbestandVorAbschluss = 0.0;

    /**
     * @var int
     */
    public int $kBestellpos = 0;

    /**
     * @var array|string
     */
    public $cLieferstatus = '';

    /**
     * @var string
     */
    public string $cArtNr = '';

    /**
     * @var int|null
     */
    public $nAnzahlEinzel;

    /**
     * @var array|null
     */
    public $cEinzelpreisLocalized;

    /**
     * @var array|null
     */
    public $cKonfigeinzelpreisLocalized;

    /**
     * @var string
     */
    public string $cEstimatedDelivery = '';

    /**
     * @var string|null
     */
    public $cArticleNameAffix;

    /**
     * @var string|null
     */
    public $discountForArticle;

    /**
     * @var array|null
     */
    public $fVK;

    /**
     * @var object {
     *      localized: string,
     *      longestMin: int,
     *      longestMax: int,
     * }
     */
    public $oEstimatedDelivery;

    /**
     * @var int[]
     */
    public array $kLieferschein_arr = [];

    /**
     * @var int|float|string
     */
    public $nAusgeliefert = 0;

    /**
     * @var int|float|string
     */
    public $nAusgeliefertGesamt = 0;

    /**
     * @var int|string
     */
    public $nOffenGesamt = 0;

    public bool $bAusgeliefert = false;

    public int $nLongestMaxDelivery = 0;

    public int $nLongestMinDelivery = 0;

    public ?string $dMHD = null;

    public ?string $dMHD_de = null;

    public ?string $cSeriennummer = null;

    public function __wakeup(): void
    {
        if ($this->kArtikel <= 0) {
            return;
        }
        $this->Artikel         = new Artikel();
        $options               = Artikel::getDefaultOptions();
        $options->nStueckliste = 1;
        $options->nVariationen = 1;
        if ($this->kKonfigitem > 0) {
            $options->nKeineSichtbarkeitBeachten = 1;
        }
        $langID = Shop::getLanguageID() ?: ((int)($_SESSION['kSprache'] ?? 0));
        $cgID   = Frontend::getCustomer()->getGroupID() ?: ((int)($_SESSION['kKundengruppe'] ?? 0));
        $this->Artikel->fuelleArtikel($this->kArtikel, $options, $cgID, $langID);
    }

    /**
     * @return int[]
     */
    public function __sleep(): array
    {
        return select(\array_keys(\get_object_vars($this)), static function (string $e): bool {
            return $e !== 'Artikel';
        });
    }

    /**
     * CartItem constructor.
     * @param int $id
     */
    public function __construct(int $id = 0)
    {
        if ($id > 0) {
            $this->loadFromDB($id);
        }
    }

    /**
     * Setzt in dieser Position einen Eigenschaftswert der angegebenen Eigenschaft.
     * Existiert ein EigenschaftsWert für die Eigenschaft, so wir er überschrieben, ansonsten neu angelegt
     *
     * @param int    $propertyID
     * @param int    $valueID
     * @param string $freeText
     * @return bool
     */
    public function setzeVariationsWert(int $propertyID, int $valueID, string $freeText = ''): bool
    {
        $db                                = Shop::Container()->getDB();
        $attributeValue                    = new EigenschaftWert($valueID, $db);
        $attribute                         = new Eigenschaft($propertyID, $db);
        $newAttributes                     = new CartItemProperty();
        $newAttributes->kEigenschaft       = $propertyID;
        $newAttributes->kEigenschaftWert   = $valueID;
        $newAttributes->fGewichtsdifferenz = $attributeValue->fGewichtDiff;
        $newAttributes->fAufpreis          = $attributeValue->fAufpreisNetto;
        $surcharge                         = $db->select(
            'teigenschaftwertaufpreis',
            'kEigenschaftWert',
            $newAttributes->kEigenschaftWert,
            'kKundengruppe',
            Frontend::getCustomerGroup()->getID()
        );
        if ($surcharge !== null && !empty($surcharge->fAufpreisNetto)) {
            if ($this->Artikel->Preise->rabatt > 0) {
                $newAttributes->fAufpreis  = $surcharge->fAufpreisNetto -
                    (($this->Artikel->Preise->rabatt / 100) * $surcharge->fAufpreisNetto);
                $surcharge->fAufpreisNetto = $newAttributes->fAufpreis;
            } else {
                $newAttributes->fAufpreis = $surcharge->fAufpreisNetto;
            }
        }
        $newAttributes->cTyp               = $attribute->cTyp;
        $newAttributes->cAufpreisLocalized = Preise::getLocalizedPriceString($newAttributes->fAufpreis);
        //posname lokalisiert ablegen
        $newAttributes->cEigenschaftName     = [];
        $newAttributes->cEigenschaftWertName = [];
        foreach (Frontend::getLanguages() as $language) {
            $code = $language->getCode();

            $newAttributes->cEigenschaftName[$code]     = $attribute->cName;
            $newAttributes->cEigenschaftWertName[$code] = $attributeValue->cName;
            if ($language->isDefault() === false) {
                $localized = $db->select(
                    'teigenschaftsprache',
                    'kEigenschaft',
                    $newAttributes->kEigenschaft,
                    'kSprache',
                    $language->getId()
                );
                if ($localized !== null && !empty($localized->cName)) {
                    $newAttributes->cEigenschaftName[$code] = $localized->cName;
                }
                $localizedValue = $db->select(
                    'teigenschaftwertsprache',
                    'kEigenschaftWert',
                    $newAttributes->kEigenschaftWert,
                    'kSprache',
                    $language->getId()
                );
                if ($localizedValue !== null && !empty($localizedValue->cName)) {
                    $newAttributes->cEigenschaftWertName[$code] = $localizedValue->cName;
                }
            }
            if ($freeText || \mb_strlen(\trim($freeText)) > 0) {
                $newAttributes->cEigenschaftWertName[$code] = $db->escape($freeText);
            }
        }
        $this->WarenkorbPosEigenschaftArr[] = $newAttributes;
        $this->fGesamtgewicht               = $this->gibGesamtgewicht();

        return true;
    }

    /**
     * gibt EigenschaftsWert zu einer Eigenschaft bei dieser Position
     *
     * @param int $propertyID - ID der Eigenschaft
     * @return int - gesetzter Wert. Falls nicht gesetzt, wird 0 zurückgegeben
     */
    public function gibGesetztenEigenschaftsWert(int $propertyID): int
    {
        foreach ($this->WarenkorbPosEigenschaftArr as $WKPosEigenschaft) {
            if ($WKPosEigenschaft->kEigenschaft === $propertyID) {
                return $WKPosEigenschaft->kEigenschaftWert;
            }
        }

        return 0;
    }

    /**
     * gibt Summe der Aufpreise der Variationen dieser Position zurück
     *
     * @return float
     */
    public function gibGesamtAufpreis()
    {
        $aufpreis = 0;
        foreach ($this->WarenkorbPosEigenschaftArr as $WKPosEigenschaft) {
            if ($WKPosEigenschaft->fAufpreis != 0) {
                $aufpreis += $WKPosEigenschaft->fAufpreis;
            }
        }

        return $aufpreis;
    }

    /**
     * gibt Gewicht dieser Position zurück. Variationen und PosAnzahl berücksichtigt
     *
     * @return float
     */
    public function gibGesamtgewicht()
    {
        $gewicht = $this->Artikel->fGewicht * $this->nAnzahl;

        if (!$this->Artikel->kVaterArtikel) {
            foreach ($this->WarenkorbPosEigenschaftArr as $WKPosEigenschaft) {
                if ($WKPosEigenschaft->fGewichtsdifferenz != 0) {
                    $gewicht += $WKPosEigenschaft->fGewichtsdifferenz * $this->nAnzahl;
                }
            }
        }

        return $gewicht;
    }

    /**
     * Calculate the total weight of a config item and his components.
     *
     * @return float|int
     */
    public function getTotalConfigWeight()
    {
        $weight = $this->Artikel->fGewicht * $this->nAnzahl;
        if ($this->kKonfigitem === 0 && !empty($this->cUnique)) {
            foreach (Frontend::getCart()->PositionenArr as $item) {
                if ($item->cUnique === $this->cUnique && $item->istKonfigKind()) {
                    $weight += $item->fGesamtgewicht;
                }
            }
        }

        return $weight;
    }

    /**
     * gibt Gesamtpreis inkl. aller Aufpreise * Positionsanzahl lokalisiert als String zurück
     *
     * @return $this
     */
    public function setzeGesamtpreisLocalized(): self
    {
        if (!\is_array($_SESSION['Waehrungen'])) {
            return $this;
        }
        $tax = self::getTaxRate($this);
        foreach (Frontend::getCurrencies() as $currency) {
            $currencyName = $currency->getName();
            // Standardartikel
            $this->cGesamtpreisLocalized[0][$currencyName] = Preise::getLocalizedPriceString(
                Tax::getGross($this->fPreis * $this->nAnzahl, $tax, 4),
                $currency
            );
            $this->cGesamtpreisLocalized[1][$currencyName] = Preise::getLocalizedPriceString(
                $this->fPreis * $this->nAnzahl,
                $currency
            );
            $this->cEinzelpreisLocalized[0][$currencyName] = Preise::getLocalizedPriceString(
                Tax::getGross($this->fPreis, $tax, 4),
                $currency
            );
            $this->cEinzelpreisLocalized[1][$currencyName] = Preise::getLocalizedPriceString($this->fPreis, $currency);
            if (
                !empty($this->Artikel->cVPEEinheit)
                && isset($this->Artikel->cVPE)
                && $this->Artikel->cVPE === 'Y'
                && $this->Artikel->fVPEWert > 0
            ) {
                $this->Artikel->baueVPE($this->fPreis);
            }
            if ($this->istKonfigVater()) {
                $this->cKonfigpreisLocalized[0][$currencyName]       = Preise::getLocalizedPriceString(
                    Tax::getGross($this->fPreis * $this->nAnzahl, $tax, 4),
                    $currency
                );
                $this->cKonfigpreisLocalized[1][$currencyName]       = Preise::getLocalizedPriceString(
                    $this->fPreis * $this->nAnzahl,
                    $currency
                );
                $this->cKonfigeinzelpreisLocalized[0][$currencyName] = Preise::getLocalizedPriceString(
                    Tax::getGross($this->fPreis, $tax, 4),
                    $currency
                );
                $this->cKonfigeinzelpreisLocalized[1][$currencyName] = Preise::getLocalizedPriceString(
                    $this->fPreis,
                    $currency
                );
            }
            if ($this->istKonfigKind()) {
                $net       = 0;
                $gross     = 0;
                $parentIdx = null;
                if (!empty($this->cUnique)) {
                    foreach (Frontend::getCart()->PositionenArr as $idx => $item) {
                        if ($this->cUnique === $item->cUnique) {
                            $configItemTax = self::getTaxRate($item);
                            $net           += $item->fPreis * $item->nAnzahl;
                            $gross         += Tax::getGross(
                                $item->fPreis * $item->nAnzahl,
                                $configItemTax,
                                4
                            );

                            if ($item->istKonfigVater()) {
                                $parentIdx = $idx;
                            }
                        }
                    }
                }
                if ($parentIdx !== null) {
                    $parent = Frontend::getCart()->PositionenArr[$parentIdx];
                    if (\is_object($parent)) {
                        $this->nAnzahlEinzel = $this->isIgnoreMultiplier()
                            ? $this->nAnzahl
                            : $this->nAnzahl / $parent->nAnzahl;

                        $parent->cKonfigpreisLocalized[0][$currencyName]       = Preise::getLocalizedPriceString(
                            $gross,
                            $currency
                        );
                        $parent->cKonfigpreisLocalized[1][$currencyName]       = Preise::getLocalizedPriceString(
                            $net,
                            $currency
                        );
                        $parent->cKonfigeinzelpreisLocalized[0][$currencyName] = Preise::getLocalizedPriceString(
                            $gross / $parent->nAnzahl,
                            $currency
                        );
                        $parent->cKonfigeinzelpreisLocalized[1][$currencyName] = Preise::getLocalizedPriceString(
                            $net / $parent->nAnzahl,
                            $currency
                        );
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function loadFromDB(int $id): self
    {
        $obj = Shop::Container()->getDB()->select('twarenkorbpos', 'kWarenkorbPos', $id);
        if ($obj === null) {
            return $this;
        }
        $this->kSteuerklasse             = 0; //@todo: why?
        $this->kWarenkorbPos             = (int)$obj->kWarenkorbPos;
        $this->kWarenkorb                = (int)$obj->kWarenkorb;
        $this->kArtikel                  = (int)$obj->kArtikel;
        $this->kVersandklasse            = (int)$obj->kVersandklasse;
        $this->cName                     = $obj->cName;
        $this->cLieferstatus             = $obj->cLieferstatus;
        $this->cArtNr                    = $obj->cArtNr;
        $this->cEinheit                  = $obj->cEinheit;
        $this->fPreisEinzelNetto         = $obj->fPreisEinzelNetto;
        $this->fPreis                    = $obj->fPreis;
        $this->fMwSt                     = $obj->fMwSt;
        $this->nAnzahl                   = $obj->nAnzahl;
        $this->nPosTyp                   = (int)$obj->nPosTyp;
        $this->cHinweis                  = $obj->cHinweis;
        $this->cUnique                   = $obj->cUnique;
        $this->cResponsibility           = $obj->cResponsibility;
        $this->kKonfigitem               = (int)$obj->kKonfigitem;
        $this->kBestellpos               = (int)$obj->kBestellpos;
        $this->fLagerbestandVorAbschluss = $obj->fLagerbestandVorAbschluss;
        $this->nLongestMinDelivery       = (int)$obj->nLongestMinDelivery;
        $this->nLongestMaxDelivery       = (int)$obj->nLongestMaxDelivery;
        self::setEstimatedDelivery($this, $this->nLongestMinDelivery, $this->nLongestMaxDelivery);

        return $this;
    }

    /**
     * @return int
     */
    public function insertInDB(): int
    {
        $obj                            = new stdClass();
        $obj->kWarenkorb                = $this->kWarenkorb;
        $obj->kArtikel                  = $this->kArtikel;
        $obj->kVersandklasse            = $this->kVersandklasse;
        $obj->cName                     = $this->cName;
        $obj->cLieferstatus             = $this->cLieferstatus;
        $obj->cArtNr                    = $this->cArtNr;
        $obj->cEinheit                  = $this->cEinheit;
        $obj->fPreisEinzelNetto         = $this->fPreisEinzelNetto;
        $obj->fPreis                    = $this->fPreis;
        $obj->fMwSt                     = $this->fMwSt;
        $obj->nAnzahl                   = $this->nAnzahl;
        $obj->nPosTyp                   = $this->nPosTyp;
        $obj->cHinweis                  = $this->cHinweis ?? '';
        $obj->cUnique                   = $this->cUnique;
        $obj->cResponsibility           = !empty($this->cResponsibility) ? $this->cResponsibility : 'core';
        $obj->kKonfigitem               = $this->kKonfigitem;
        $obj->kBestellpos               = $this->kBestellpos;
        $obj->fLagerbestandVorAbschluss = $this->fLagerbestandVorAbschluss;
        $obj->nLongestMinDelivery       = $this->nLongestMinDelivery;
        $obj->nLongestMaxDelivery       = $this->nLongestMaxDelivery;
        if (isset($this->oEstimatedDelivery->longestMin, $this->oEstimatedDelivery->longestMax)) {
            // Lieferzeiten nur speichern, wenn sie gesetzt sind, also z.B. nicht bei Versandkosten etc.
            $obj->nLongestMinDelivery = $this->oEstimatedDelivery->longestMin;
            $obj->nLongestMaxDelivery = $this->oEstimatedDelivery->longestMax;
        }

        $this->kWarenkorbPos = Shop::Container()->getDB()->insert('twarenkorbpos', $obj);

        if ($this->nPosTyp === \C_WARENKORBPOS_TYP_GRATISGESCHENK) {
            Shop::Container()->getFreeGiftService()->saveFreeGift(
                productID: $this->kArtikel,
                basketID : $this->kWarenkorb,
                quantity : $this->nAnzahl
            );
        }

        return $this->kWarenkorbPos;
    }

    /**
     * @return bool
     */
    public function istKonfigVater(): bool
    {
        return \is_string($this->cUnique) && !empty($this->cUnique) && $this->kKonfigitem === 0;
    }

    /**
     * @return bool
     */
    public function istKonfigKind(): bool
    {
        return \is_string($this->cUnique) && !empty($this->cUnique) && $this->kKonfigitem > 0;
    }

    /**
     * @return bool
     */
    public function istKonfig(): bool
    {
        return $this->istKonfigVater() || $this->istKonfigKind();
    }

    /**
     * @param CartItem $cartPos
     * @param int|null $minDelivery
     * @param int|null $maxDelivery
     */
    public static function setEstimatedDelivery($cartPos, int $minDelivery = null, int $maxDelivery = null): void
    {
        $cartPos->oEstimatedDelivery = (object)[
            'localized'  => '',
            'longestMin' => 0,
            'longestMax' => 0,
        ];
        if ($minDelivery !== null && $maxDelivery !== null) {
            $cartPos->nLongestMaxDelivery            = $maxDelivery;
            $cartPos->nLongestMinDelivery            = $minDelivery;
            $cartPos->oEstimatedDelivery->longestMin = $minDelivery;
            $cartPos->oEstimatedDelivery->longestMax = $maxDelivery;
            $cartPos->oEstimatedDelivery->localized  = (!empty($minDelivery) && !empty($maxDelivery))
                ? ShippingMethod::getDeliverytimeEstimationText($minDelivery, $maxDelivery)
                : '';
        }
        $cartPos->cEstimatedDelivery = &$cartPos->oEstimatedDelivery->localized;
    }

    /**
     * Return value of config item property bIgnoreMultiplier
     *
     * @return bool|int
     */
    public function isIgnoreMultiplier()
    {
        static $ignoreMultipliers = null;

        if ($ignoreMultipliers === null || !\array_key_exists($this->kKonfigitem, $ignoreMultipliers)) {
            $konfigItem        = new Item($this->kKonfigitem);
            $ignoreMultipliers = [
                $this->kKonfigitem => $konfigItem->ignoreMultiplier(),
            ];
        }

        return $ignoreMultipliers[$this->kKonfigitem];
    }

    /**
     * @param string $isoCode
     * @param bool   $excludeShippingCostAttributes
     * @return bool
     */
    public function isUsedForShippingCostCalculation(string $isoCode, bool $excludeShippingCostAttributes = false): bool
    {
        return (!$excludeShippingCostAttributes
            || $this->nPosTyp !== \C_WARENKORBPOS_TYP_ARTIKEL
            || ($this->Artikel && $this->Artikel->isUsedForShippingCostCalculation($isoCode))
        );
    }

    /**
     * @param object $item
     * @return float
     */
    public static function getTaxRate(object $item): float
    {
        if (($item->kSteuerklasse ?? 0) === 0) {
            if (isset($item->fMwSt)) {
                $taxRate = $item->fMwSt;
            } elseif (isset($item->Artikel)) {
                $taxRate = ($item->Artikel->kSteuerklasse ?? 0) > 0
                    ? Tax::getSalesTax($item->Artikel->kSteuerklasse)
                    : $item->Artikel->fMwSt;
            } else {
                $taxRate = Tax::getSalesTax(0);
            }
        } else {
            $taxRate = Tax::getSalesTax($item->kSteuerklasse);
        }

        return (float)$taxRate;
    }

    /**
     * @param string|null $idx
     * @return string
     * @since 5.4.0
     */
    public function getName(?string $idx = null): string
    {
        if (\is_array($this->cName)) {
            return $this->cName[$idx ?? Shop::getLanguageCode()] ?? '';
        }

        return \is_string($this->cName) ? $this->cName : '';
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        $res            = \get_object_vars($this);
        $res['Artikel'] = '*truncated*';

        return $res;
    }
}
