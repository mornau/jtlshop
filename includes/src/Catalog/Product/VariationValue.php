<?php

declare(strict_types=1);

namespace JTL\Catalog\Product;

use JTL\Catalog\Currency;
use JTL\Helpers\Tax;
use JTL\Helpers\Text;
use JTL\Media\Image;
use JTL\Media\MultiSizeImage;
use stdClass;

/**
 * Class VariationValue
 * @package JTL\Catalog\Product
 */
class VariationValue
{
    use MultiSizeImage;

    /**
     * @var int|null
     */
    public $kEigenschaftWert;

    /**
     * @var int|null
     */
    public $kEigenschaft;

    /**
     * @var string|null
     */
    public $cName;

    /**
     * @var float|null
     */
    public $fAufpreisNetto;

    /**
     * @var float|null
     */
    public $fGewichtDiff;

    /**
     * @var string|null
     */
    public $cArtNr;

    /**
     * @var int|null
     */
    public $nSort;

    /**
     * @var float|null
     */
    public $fLagerbestand;

    /**
     * @var float|null
     */
    public $fPackeinheit;

    /**
     * @var bool|null
     */
    public $inStock;

    /**
     * @var bool|null
     */
    public $notExists;

    /**
     * @var stdClass|null
     */
    public $oVariationsKombi;

    /**
     * @var string|null
     */
    public $cAufpreisLocalized;

    /**
     * @var int|null
     */
    public $nNichtLieferbar;

    /**
     * @var array<int, string>
     */
    public array $cPreisVPEWertAufpreis = [];

    /**
     * @var array<int, string>
     */
    public array $cPreisVPEWertInklAufpreis = [];

    /**
     * @var array<int, string>|null
     */
    public $cPreisInklAufpreis;

    /**
     * @var array<int, float>
     */
    public array $fAufpreis;

    /**
     * @var float|null
     */
    public $fVPEWert;

    /**
     * VariationValue constructor.
     */
    public function __construct()
    {
        $this->setImageType(Image::TYPE_VARIATION);
    }

    /**
     * @param stdClass  $data
     * @param int       $cntVariationen
     * @param int|float $tmpDiscount
     */
    public function init(stdClass $data, int $cntVariationen, $tmpDiscount): void
    {
        $this->kEigenschaftWert = (int)$data->kEigenschaftWert;
        $this->kEigenschaft     = (int)$data->kEigenschaft;
        if (!empty($data->localizedName)) {
            $this->cName = $data->localizedName;
        } else {
            $this->cName = Text::htmlentitiesOnce(
                $data->cName_teigenschaftwert ?? '',
                \ENT_COMPAT | \ENT_HTML401
            );
        }
        $this->fAufpreisNetto = $data->fAufpreisNetto;
        $this->fGewichtDiff   = $data->fGewichtDiff;
        $this->cArtNr         = $data->cArtNr;
        $this->nSort          = (int)$data->teigenschaftwert_nSort;
        $this->fLagerbestand  = $data->fLagerbestand;
        $this->fPackeinheit   = $data->fPackeinheit;
        $this->inStock        = true;
        $this->notExists      = isset($data->nMatched) && (int)$data->nMatched < $cntVariationen - 1;
        if (isset($data->fVPEWert) && $data->fVPEWert > 0) {
            $this->fVPEWert = $data->fVPEWert;
        }
        if ($data->fAufpreisNetto_teigenschaftwertaufpreis !== null) {
            $this->fAufpreisNetto =
                $data->fAufpreisNetto_teigenschaftwertaufpreis * ((100 - $tmpDiscount) / 100);
        }
        if ((int)$this->fPackeinheit === 0) {
            $this->fPackeinheit = 1;
        }
    }

    /**
     * @param stdClass $data
     * @param Artikel  $product
     */
    public function addChildItems(stdClass $data, Artikel $product): void
    {
        $varCombi                         = new stdClass();
        $varCombi->kArtikel               = $data->tartikel_kArtikel ?? null;
        $varCombi->tartikel_fLagerbestand = $data->tartikel_fLagerbestand ?? null;
        $varCombi->cLagerBeachten         = $data->cLagerBeachten ?? null;
        $varCombi->cLagerKleinerNull      = $data->cLagerKleinerNull ?? null;
        $varCombi->cLagerVariation        = $data->cLagerVariation ?? null;

        if ($product->nIstVater === 1 && isset($data->cMergedLagerBeachten)) {
            $varCombi->tartikel_fLagerbestand = $data->fMergedLagerbestand ?? null;
            $varCombi->cLagerBeachten         = $data->cMergedLagerBeachten ?? null;
            $varCombi->cLagerKleinerNull      = $data->cMergedLagerKleinerNull ?? null;
            $varCombi->cLagerVariation        = $data->cMergedLagerVariation ?? null;
        }

        $stockInfo = $product->getStockInfo(
            (object)[
                'cLagerVariation'   => $varCombi->cLagerVariation,
                'fLagerbestand'     => $varCombi->tartikel_fLagerbestand,
                'cLagerBeachten'    => $varCombi->cLagerBeachten,
                'cLagerKleinerNull' => $varCombi->cLagerKleinerNull,
            ]
        );

        $this->inStock          = $stockInfo->inStock;
        $this->notExists        = $this->notExists || $stockInfo->notExists;
        $this->oVariationsKombi = $varCombi;
    }

    /**
     * @param string $path
     * @param string $imageBaseURL
     * @return bool
     */
    public function addImages(string $path, string $imageBaseURL): bool
    {
        if (!$path || !\file_exists(\STORAGE_VARIATIONS . $path)) {
            return false;
        }
        $this->generateAllImageSizes(true, 1, $path);
        $this->generateAllImageDimensions(1, $path);

        return true;
    }

    /**
     * @param Artikel   $product
     * @param int|float $taxRate
     * @param Currency  $currency
     * @param bool|int  $mayViewPrices
     * @param int       $precision
     * @param string    $per
     */
    public function addPrices(
        Artikel $product,
        $taxRate,
        Currency $currency,
        $mayViewPrices,
        int $precision,
        string $per
    ): void {
        if (!isset($this->fAufpreisNetto) || $this->fAufpreisNetto === 0.0) {
            return;
        }
        $surcharge                   = $this->fAufpreisNetto;
        $customerGroupID             = $product->getCustomerGroupID();
        $this->cAufpreisLocalized[0] = Preise::getLocalizedPriceString(
            Tax::getGross($surcharge, $taxRate, 4),
            $currency
        );
        $this->cAufpreisLocalized[1] = Preise::getLocalizedPriceString($surcharge, $currency);
        // Wenn der Artikel ein VarkombiKind ist
        if ($product->kVaterArtikel > 0) {
            $vkNetto = $product->gibPreis(1, [], $customerGroupID, '', false);
        } else {
            $vkNetto = $product->gibPreis(1, [
                $this->kEigenschaft => $this->kEigenschaftWert
            ], $customerGroupID, '', false);
        }
        $this->cPreisInklAufpreis[0] = Preise::getLocalizedPriceString(
            Tax::getGross($vkNetto, $taxRate),
            $currency
        );
        $this->cPreisInklAufpreis[1] = Preise::getLocalizedPriceString($vkNetto, $currency);

        if ($this->fAufpreisNetto > 0) {
            $this->cAufpreisLocalized[0] = '+ ' . $this->cAufpreisLocalized[0];
            $this->cAufpreisLocalized[1] = '+ ' . $this->cAufpreisLocalized[1];
        } else {
            $this->cAufpreisLocalized[0] = \str_replace('-', '- ', $this->cAufpreisLocalized[0]);
            $this->cAufpreisLocalized[1] = \str_replace('-', '- ', $this->cAufpreisLocalized[1]);
        }

        $this->fAufpreis[0] = Tax::getGross($surcharge * $currency->getConversionFactor(), $taxRate);
        $this->fAufpreis[1] = $surcharge * $currency->getConversionFactor();

        if ($surcharge > 0) {
            $product->nVariationsAufpreisVorhanden = 1;
        }

        if ($mayViewPrices && isset($this->fVPEWert) && $this->fVPEWert > 0) {
            $base = [
                0 => $this->fAufpreis[0] / $this->fVPEWert,
                1 => $this->fAufpreis[1] / $this->fVPEWert,
            ];

            $this->cPreisVPEWertAufpreis[0] = Preise::getLocalizedPriceString(
                $base[0],
                $currency,
                true,
                $precision
            ) . $per;
            $this->cPreisVPEWertAufpreis[1] = Preise::getLocalizedPriceString(
                $base[1],
                $currency,
                true,
                $precision
            ) . $per;

            $this->cPreisVPEWertInklAufpreis[0] = Preise::getLocalizedPriceString(
                Tax::getGross($vkNetto, $taxRate),
                $currency,
                true,
                $precision
            ) . $per;
            $this->cPreisVPEWertInklAufpreis[1] = Preise::getLocalizedPriceString(
                $vkNetto,
                $currency,
                true,
                $precision
            ) . $per;
        }
    }

    /**
     * @return int
     */
    public function getID(): int
    {
        return $this->kEigenschaftWert;
    }
}
