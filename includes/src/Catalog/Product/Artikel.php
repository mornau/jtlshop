<?php

declare(strict_types=1);

namespace JTL\Catalog\Product;

use DateTime;
use JTL\Cache\JTLCacheInterface;
use JTL\Catalog\Category\KategorieListe;
use JTL\Catalog\Currency;
use JTL\Catalog\Hersteller;
use JTL\Catalog\Separator;
use JTL\Catalog\UnitsOfMeasure;
use JTL\Catalog\Warehouse;
use JTL\Checkout\Versandart;
use JTL\Contracts\RoutableInterface;
use JTL\Country\Country;
use JTL\Customer\Customer;
use JTL\Customer\CustomerGroup;
use JTL\DB\DbInterface;
use JTL\DB\SqlObject;
use JTL\Extensions\Config\Configurator;
use JTL\Extensions\Config\Item;
use JTL\Extensions\Download\Download;
use JTL\Filter\Metadata;
use JTL\Helpers\Product as ProductHelper;
use JTL\Helpers\Request;
use JTL\Helpers\SearchSpecial;
use JTL\Helpers\ShippingMethod;
use JTL\Helpers\Tax;
use JTL\Helpers\Text;
use JTL\Language\LanguageHelper;
use JTL\Media\Image;
use JTL\Media\Image\Overlay;
use JTL\Media\Image\Product;
use JTL\Media\Image\Variation as VariationImage;
use JTL\Media\MediaImageRequest;
use JTL\Media\MultiSizeImage;
use JTL\Media\Video;
use JTL\Router\RoutableTrait;
use JTL\Router\Router;
use JTL\Session\Frontend;
use JTL\Shop;
use JTL\Shopsetting;
use stdClass;

use function Functional\map;
use function Functional\reduce_left;
use function Functional\select;

/**
 * Class Artikel
 * @package JTL\Catalog\Product
 */
class Artikel implements RoutableInterface
{
    use RoutableTrait;
    use MultiSizeImage;

    /**
     * @var int|null
     */
    public ?int $kArtikel = null;

    /**
     * @var int|null
     */
    public ?int $kHersteller = null;

    /**
     * @var int|null
     */
    public ?int $kLieferstatus = null;

    /**
     * @var int|null
     */
    public ?int $kSteuerklasse = null;

    /**
     * @var int|null
     */
    public ?int $kEinheit = null;

    /**
     * @var int|null
     */
    public ?int $kVersandklasse = null;

    /**
     * @var int|null
     */
    public ?int $kStueckliste = null;

    /**
     * @var int|null
     */
    public ?int $kMassEinheit = null;

    /**
     * @var int|null
     */
    public ?int $kGrundpreisEinheit = null;

    /**
     * @var int|null
     */
    public ?int $kWarengruppe = null;

    /**
     * @var int|null - Spiegelt in JTL-Wawi die Beschaffungszeit vom Lieferanten zum Händler wider.
     * Darf nur dann berücksichtigt werden, wenn $nAutomatischeLiefertageberechnung == 0 (also fixe Beschaffungszeit)
     */
    public ?int $nLiefertageWennAusverkauft = null;

    /**
     * @var int|null
     */
    public ?int $nAutomatischeLiefertageberechnung = null;

    /**
     * @var int|null
     */
    public ?int $nBearbeitungszeit = null;

    /**
     * @var float|string|null
     */
    public string|null|float $fLagerbestand = null;

    /**
     * @var int|null
     */
    public null|int $nNichtLieferbar = 0;

    /**
     * @var float|string|null
     */
    public string|null|float $fMindestbestellmenge = null;

    /**
     * @var float|string|null
     */
    public string|null|float $fPackeinheit = null;

    /**
     * @var float|string|null
     */
    public string|null|float $fAbnahmeintervall = null;

    /**
     * @var float|string|null
     */
    public string|null|float $fGewicht = null;

    /**
     * @var float|string|null
     */
    public string|null|float $fUVP = null;

    /**
     * @var float|string|null
     */
    public string|null|float $fUVPBrutto = null;

    /**
     * @var float|string|null
     */
    public string|null|float $fVPEWert = null;

    /**
     * @var float|string|null
     */
    public string|null|float $fZulauf = 0.0;

    /**
     * @var float|string|null
     */
    public string|null|float $fMassMenge = null;

    /**
     * @var float|string|null
     */
    public string|null|float $fGrundpreisMenge = null;

    /**
     * @var float|string|null
     */
    public string|null|float $fBreite = null;

    /**
     * @var float|string|null
     */
    public string|null|float $fHoehe = null;

    /**
     * @var float|string|null
     */
    public string|null|float $fLaenge = null;

    /**
     * @var string|null
     */
    public ?string $cName = null;

    /**
     * @var string|null
     */
    public ?string $cSeo = null;

    /**
     * @var string|null
     */
    public ?string $cBeschreibung = null;

    /**
     * @var string|null
     */
    public ?string $cAnmerkung = null;

    /**
     * @var string|null
     */
    public ?string $cArtNr = null;

    /**
     * @var string|null
     */
    public ?string $cURL = null;

    /**
     * @var string|null
     */
    public ?string $cURLFull = null;

    /**
     * @var string|null
     */
    public ?string $cVPE = null;

    /**
     * @var string|null
     */
    public ?string $cVPEEinheit = null;

    /**
     * @var string|null
     */
    public ?string $cSuchbegriffe = null;

    /**
     * @var string|null
     */
    public ?string $cTeilbar = null;

    /**
     * @var string|null
     */
    public ?string $cBarcode = null;

    /**
     * @var string|null
     */
    public ?string $cLagerBeachten = null;

    /**
     * @var string|null
     */
    public ?string $cLagerKleinerNull = null;

    /**
     * @var string|null
     */
    public ?string $cLagerVariation = null;

    /**
     * @var string|null
     */
    public ?string $cKurzBeschreibung = null;

    /**
     * @var string|null
     */
    public ?string $cMwstVersandText = null;

    /**
     * @var string|null
     */
    public ?string $cLieferstatus = null;

    /**
     * @var string|null
     */
    public ?string $cVorschaubild = null;

    /**
     * @var string|null
     */
    public ?string $cVorschaubildURL = null;

    /**
     * @var string|null
     */
    public ?string $cHerstellerMetaTitle = null;

    /**
     * @var string|null
     */
    public ?string $cHerstellerMetaKeywords = null;

    /**
     * @var string|null
     */
    public ?string $cHerstellerMetaDescription = null;

    /**
     * @var string|null
     */
    public ?string $cHerstellerBeschreibung = null;

    /**
     * @var string|null
     */
    public ?string $dZulaufDatum = null;

    /**
     * @var string|null
     */
    public ?string $dMHD = null;

    /**
     * @var string|null
     */
    public ?string $dErscheinungsdatum = null;

    /**
     * string|null 'Y'/'N'
     */
    public ?string $cTopArtikel = null;

    /**
     * string|null 'Y'/'N'
     */
    public ?string $cNeu = null;

    /**
     * @var Preise|null
     */
    public ?Preise $Preise = null;

    /**
     * @var stdClass[]
     */
    public array $Bilder = [];

    /**
     * @var array|null
     */
    public ?array $FunktionsAttribute = null;

    /**
     * @var array|null
     */
    public ?array $Attribute = null;

    /**
     * @var array|null
     */
    public ?array $AttributeAssoc = null;

    /**
     * @var Variation[]
     */
    public array $Variationen = [];

    /**
     * @var array
     */
    public array $nonAllowedVariationValues = [];

    /**
     * @var array|null
     */
    public ?array $Sonderpreise = null;

    /**
     * @var array|null
     */
    public ?array $bSuchspecial_arr = null;

    /**
     * @var Image\Overlay|null
     */
    public ?Image\Overlay $oSuchspecialBild = null;

    /**
     * @var int|null
     */
    public ?int $bIsBestseller = null;

    /**
     * @var int|null
     */
    public ?int $bIsTopBewertet = null;

    /**
     * @var array
     */
    public array $oProduktBundle_arr = [];

    /**
     * @var array
     */
    public array $oMedienDatei_arr = [];

    /**
     * @var array
     */
    public array $cMedienTyp_arr = [];

    /**
     * @var int|null
     */
    public ?int $nVariationsAufpreisVorhanden = null;

    /**
     * @var string|null
     */
    public ?string $cMedienDateiAnzeige = null;

    /**
     * @var array
     */
    public array $oVariationKombi_arr = [];

    /**
     * @var Variation[]
     */
    public array $VariationenOhneFreifeld = [];

    /**
     * @var array
     */
    public array $oVariationenNurKind_arr = [];

    /**
     * @var stdClass|null
     */
    public ?stdClass $Lageranzeige = null;

    /**
     * @var int|null
     */
    public ?int $kEigenschaftKombi = null;

    /**
     * @var int|null
     */
    public ?int $kVaterArtikel = null;

    /**
     * @var int|null
     */
    public ?int $nIstVater = null;

    /**
     * @var array|null
     */
    public ?array $cVaterVKLocalized = null;

    /**
     * @var array|null
     */
    public ?array $oKategorie_arr = null;

    /**
     * @var array|null
     */
    public ?array $oKonfig_arr = null;

    /**
     * @var bool
     */
    public bool $bHasKonfig = false;

    /**
     * @var Merkmal[]|null
     */
    public ?array $oMerkmale_arr = null;

    /**
     * @var array|null
     */
    public ?array $cMerkmalAssoc_arr = null;

    /**
     * @var string|null
     */
    public ?string $cVariationKombi = null;

    /**
     * @var array|null
     */
    public ?array $kEigenschaftKombi_arr = null;

    /**
     * @var array|null
     */
    public ?array $oVariationDetailPreisKind_arr = null;

    /**
     * @var array|null
     */
    public ?array $oVariationDetailPreis_arr = null;

    /**
     * @var Artikel|null
     */
    public ?Artikel $oProduktBundleMain = null;

    /**
     * @var stdClass|null
     */
    public ?stdClass $oProduktBundlePrice = null;

    /**
     * @var int|null
     */
    public ?int $inWarenkorbLegbar = null;

    /**
     * @var array|null
     */
    public ?array $oVariBoxMatrixBild_arr = null;

    /**
     * @var array|null
     */
    public ?array $oVariationKombiVorschau_arr = null;

    /**
     * @var bool|null
     */
    public ?bool $cVariationenbilderVorhanden = null;

    /**
     * @var int
     */
    public int $nVariationenVerfuegbar = 0;

    /**
     * @var int
     */
    public int $nVariationAnzahl = 0;

    /**
     * @var int
     */
    public int $nVariationOhneFreifeldAnzahl = 0;

    /**
     * @var Bewertung|null
     */
    public ?Bewertung $Bewertungen = null;

    /**
     * @var float|string|null
     */
    public string|null|float $fDurchschnittsBewertung = null;

    /**
     * @var Bewertung|null
     */
    public ?Bewertung $HilfreichsteBewertung = null;

    /**
     * @var array|null
     */
    public ?array $similarProducts = null;

    /**
     * @var string|null
     */
    public ?string $cacheID = null;

    /**
     * @var Versandart|null
     */
    public ?Versandart $oFavourableShipping = null;

    /**
     * @var int|null
     */
    public ?int $favourableShippingID = null;

    /**
     * @var string|null
     */
    public ?string $cCachedCountryCode = null;

    /**
     * @var float|string|null
     */
    public string|null|float $fLieferantenlagerbestand = 0.0;

    /**
     * @var float|string|null
     */
    public string|null|float $fLieferzeit = 0.0;

    /**
     * @var string|null
     */
    public ?string $cEstimatedDelivery = null;

    /**
     * @var int|null
     */
    public ?int $kVPEEinheit = null;

    /**
     * @var float|string|null
     */
    public string|null|float $fMwSt = null;

    /**
     * @var float|string|null
     */
    public string|null|float $fArtikelgewicht = null;

    /**
     * @var int
     */
    public int $nSort = 0;

    /**
     * @var string|null
     */
    public ?string $dErstellt = null;

    /**
     * @var string|null
     */
    public ?string $dErstellt_de = null;

    /**
     * @var string|null
     */
    public ?string $dLetzteAktualisierung = null;

    /**
     * @var string|null
     */
    public ?string $cSerie = null;

    /**
     * @var string|null
     */
    public ?string $cISBN = null;

    /**
     * @var string|null
     */
    public ?string $cASIN = null;

    /**
     * @var string|null
     */
    public ?string $cHAN = null;

    /**
     * @var string|null
     */
    public ?string $cUNNummer = null;

    /**
     * @var string|null
     */
    public ?string $cGefahrnr = null;

    /**
     * @var string|null
     */
    public ?string $cTaric = null;

    /**
     * @var string|null
     */
    public ?string $cUPC = null;

    /**
     * @var string|null
     */
    public ?string $cHerkunftsland = null;

    /**
     * @var string|null
     */
    public ?string $cEPID = null;

    /**
     * @var array
     */
    public array $oStueckliste_arr = [];

    /**
     * @var int
     */
    public int $nErscheinendesProdukt = 0;

    /**
     * @var int|null
     */
    public ?int $nMinDeliveryDays = null;

    /**
     * @var int|null
     */
    public ?int $nMaxDeliveryDays = null;

    /**
     * @var string|null
     */
    public ?string $cEinheit = '';

    /**
     * @var string|null
     */
    public ?string $Erscheinungsdatum_de = null;

    /**
     * @var string|null
     */
    public ?string $cVersandklasse = null;

    /**
     * @var float|string|null
     */
    public string|null|float $fNettoPreis = null;

    /**
     * @var string|null
     */
    public ?string $cAktivSonderpreis = null;

    /**
     * @var string|null
     */
    public ?string $dSonderpreisStart_en = null;

    /**
     * @var string|null
     */
    public ?string $dSonderpreisEnde_en = null;

    /**
     * @var string|null
     */
    public ?string $dSonderpreisStart_de = null;

    /**
     * @var string|null
     */
    public ?string $dSonderpreisEnde_de = null;

    /**
     * @var string|null
     */
    public ?string $dZulaufDatum_de = null;

    /**
     * @var string|null
     */
    public ?string $dMHD_de = null;

    /**
     * @var string|null
     */
    public ?string $cBildpfad_thersteller = null;

    /**
     * @var string|null
     */
    public ?string $cHersteller = null;

    /**
     * @var string|null
     */
    public ?string $cHerstellerSeo = null;

    /**
     * @var string|null
     */
    public ?string $cHerstellerURL = null;

    /**
     * @var string|null
     */
    public ?string $cHerstellerHomepage = null;

    /**
     * @var string|null
     */
    public ?string $cHerstellerBildKlein = null;

    /**
     * @var string|null
     */
    public ?string $cHerstellerBildNormal = null;

    /**
     * @var string|null
     */
    public ?string $cHerstellerBildURLKlein = null;

    /**
     * @var string|null
     */
    public ?string $cHerstellerBildURLNormal = null;

    /**
     * @var int
     */
    public int $manufacturerImageWidthSM = 0;

    /**
     * @var int
     */
    public int $manufacturerImageWidthMD = 0;

    /**
     * @var int|null
     */
    public ?int $cHerstellerSortNr = null;

    /**
     * @var array|null
     */
    public ?array $oDownload_arr = null;

    /**
     * @var array|null
     */
    public ?array $oVariationKombiKinderAssoc_arr = null;

    /**
     * @var array
     */
    public array $oWarenlager_arr = [];

    /**
     * @var array|null
     */
    public ?array $cLocalizedVPE = null;

    /**
     * @var array
     */
    public array $cStaffelpreisLocalizedVPE1 = [];

    /**
     * @var array
     */
    public array $cStaffelpreisLocalizedVPE2 = [];

    /**
     * @var array
     */
    public array $cStaffelpreisLocalizedVPE3 = [];

    /**
     * @var array
     */
    public array $cStaffelpreisLocalizedVPE4 = [];

    /**
     * @var array
     */
    public array $cStaffelpreisLocalizedVPE5 = [];

    /**
     * @var array
     */
    public array $fStaffelpreisVPE1 = [];

    /**
     * @var array
     */
    public array $fStaffelpreisVPE2 = [];

    /**
     * @var array
     */
    public array $fStaffelpreisVPE3 = [];

    /**
     * @var array
     */
    public array $fStaffelpreisVPE4 = [];

    /**
     * @var array
     */
    public array $fStaffelpreisVPE5 = [];

    /**
     * @var array
     */
    public array $fStaffelpreisVPE_arr = [];

    /**
     * @var array
     */
    public array $cStaffelpreisLocalizedVPE_arr = [];

    /**
     * @var string|null
     */
    public ?string $cGewicht = null;

    /**
     * @var string|null
     */
    public ?string $cArtikelgewicht = null;

    /**
     * @var array
     */
    public array $cSprachURL_arr = [];

    /**
     * @var string|null
     */
    public ?string $cUVPLocalized = null;

    /**
     * @var int|null
     */
    public ?int $verfuegbarkeitsBenachrichtigung = null;

    /**
     * @var int|null
     */
    public ?int $kArtikelVariKombi = null;

    /**
     * @var int|null
     */
    public ?int $kVariKindArtikel = null;

    /**
     * @var string|null
     */
    public ?string $cMasseinheitCode = null;

    /**
     * @var string|null
     */
    public ?string $cMasseinheitName = null;

    /**
     * @var string|null
     */
    public ?string $cGrundpreisEinheitCode = null;

    /**
     * @var string|null
     */
    public ?string $cGrundpreisEinheitName = null;

    /**
     * @var bool
     */
    public bool $isSimpleVariation = false;

    /**
     * @var string|null
     */
    public ?string $metaKeywords = null;

    /**
     * @var string|null
     */
    public ?string $metaTitle = null;

    /**
     * @var string|null
     */
    public ?string $metaDescription = null;

    /**
     * @var array
     */
    public array $staffelPreis_arr = [];

    /**
     * @var array
     */
    public array $taxData = [];

    /**
     * @var string|mixed
     */
    public mixed $cMassMenge = '';

    /**
     * @var bool
     */
    public bool $cacheHit = false;

    /**
     * @var string
     */
    public string $cKurzbezeichnung = '';

    /**
     * @var string
     */
    public string $originalName = '';

    /**
     * @var string
     */
    public string $originalSeo = '';

    /**
     * @var string|null
     */
    public ?string $customImgName = null;

    /**
     * @var int|null
     */
    private ?int $kSprache = null;

    /**
     * @var int|null
     */
    private ?int $kKundengruppe = null;

    /**
     * @var array|null
     */
    protected ?array $conf = null;

    /**
     * @var stdClass|null
     */
    protected ?stdClass $options = null;

    /**
     * @var stdClass|null
     */
    public ?stdClass $SieSparenX = null;

    /**
     * @var string|null
     */
    public ?string $cVaterURL = null;

    /**
     * @var string|null
     */
    public ?string $cVaterURLFull = null;

    /**
     * @var array|null
     */
    public ?array $VaterFunktionsAttribute = null;

    /**
     * @var float|string|null
     */
    public string|null|float $fAnzahl_stueckliste = null;

    /**
     * @var string|null
     */
    public ?string $cURLDEL = null;

    /**
     * @var string|null
     */
    public ?string $cBestellwert = null;

    /**
     * @var int|null
     */
    public ?int $nGGAnzahl = null;

    /**
     * @var bool|null
     */
    public ?bool $isKonfigItem = null;

    /**
     * @var self[]
     */
    private static array $products = [];

    /**
     * @var bool
     */
    protected bool $compressed = false;

    /**
     * @var float[]
     */
    protected array $categoryDiscounts = [];

    /**
     * @var bool
     */
    public bool $hasUploads = false;

    public function __wakeup(): void
    {
        if ($this->kSteuerklasse === null) {
            return;
        }
        $this->db                  = null;
        $this->cache               = null;
        $this->conf                = null;
        $this->oFavourableShipping = null;
        $this->customerGroup       = null;
        if (Shop::getLanguageID() === 0 && isset($_SESSION['kSprache'], $_SESSION['cISOSprache'])) {
            Shop::setLanguage($_SESSION['kSprache'], $_SESSION['cISOSprache']);
        }
    }

    /**
     * @return string[]
     */
    public function __sleep()
    {
        return select(\array_keys(\get_object_vars($this)), static function (string $e): bool {
            return !\in_array(
                $e,
                [
                    'conf',
                    'db',
                    'cache',
                    'oVariationKombiKinderAssoc_arr',
                    'oFavourableShipping',
                    'customerGroup',
                    'currentImagePath'
                ],
                true
            );
        });
    }

    /**
     * @param DbInterface|null       $db
     * @param CustomerGroup|null     $customerGroup
     * @param Currency|null          $currency
     * @param JTLCacheInterface|null $cache
     */
    public function __construct(
        private ?DbInterface $db = null,
        protected ?CustomerGroup $customerGroup = null,
        protected ?Currency $currency = null,
        private ?JTLCacheInterface $cache = null
    ) {
        $this->setRouteType(Router::TYPE_PRODUCT);
        $this->setImageType(Image::TYPE_PRODUCT);
        $this->db            = $db ?? Shop::Container()->getDB();
        $this->cache         = $this->cache ?? Shop::Container()->getCache();
        $this->customerGroup = $this->customerGroup ?? Frontend::getCustomerGroup();
        $this->currency      = $this->currency ?? Frontend::getCurrency();
        $this->options       = new stdClass();
        $this->setConfig($this->getConfig());
    }

    /**
     * @return DbInterface
     */
    public function getDB(): DbInterface
    {
        if ($this->db === null || $this->db->isConnected() === false) {
            $this->setDB(Shop::Container()->getDB());
        }

        return $this->db;
    }

    /**
     * @param DbInterface $db
     */
    public function setDB(DbInterface $db): void
    {
        $this->db = $db;
    }

    /**
     * @return CustomerGroup
     * @since 5.3.0
     */
    public function getCustomerGroup(): CustomerGroup
    {
        if ($this->customerGroup === null) {
            $this->setCustomerGroup(Frontend::getCustomerGroup());
        }

        return $this->customerGroup;
    }

    /**
     * @param CustomerGroup $customerGroup
     * @return void
     * @since 5.3.0
     */
    protected function setCustomerGroup(CustomerGroup $customerGroup): void
    {
        $this->customerGroup = $customerGroup;
    }

    /**
     * @return JTLCacheInterface
     */
    public function getCache(): JTLCacheInterface
    {
        if ($this->cache === null) {
            $this->setCache(Shop::Container()->getCache());
        }

        return $this->cache;
    }

    /**
     * @param JTLCacheInterface $cache
     */
    protected function setCache(JTLCacheInterface $cache): void
    {
        $this->cache = $cache;
    }

    /**
     * @param string $section
     * @param string $option
     * @return mixed
     * @since 5.3.0
     */
    protected function getConfigValue(string $section, string $option): mixed
    {
        if ($this->conf === null) {
            $this->setConfig($this->getConfig());
        }

        return $this->conf[$section][$option] ?? null;
    }

    /**
     * @return array
     */
    protected function getConfig(): array
    {
        return Shopsetting::getInstance()->getSettings([
            \CONF_GLOBAL,
            \CONF_ARTIKELDETAILS,
            \CONF_ARTIKELUEBERSICHT,
            \CONF_BOXEN,
            \CONF_METAANGABEN,
            \CONF_BEWERTUNG
        ]);
    }

    /**
     * @param array $config
     * @return void
     * @since 5.3.0
     */
    protected function setConfig(array $config): void
    {
        $this->conf = $config;
    }

    /**
     * @param int|null $customerGroupID
     * @return int
     */
    public function gibKategorie(?int $customerGroupID = null): int
    {
        if ($this->kArtikel <= 0) {
            return 0;
        }
        $id = $this->kArtikel;
        // Ist der Artikel in Variationskombi Kind? Falls ja, hol den Vater und die Kategorie von ihm
        if ($this->kEigenschaftKombi > 0) {
            $id = $this->kVaterArtikel;
        } elseif (!empty($this->oKategorie_arr)) {
            // oKategorie_arr already has all categories for this product in it
            if (isset($_SESSION['LetzteKategorie'])) {
                $lastCategoryID = (int)$_SESSION['LetzteKategorie'];
                if (\in_array($lastCategoryID, $this->oKategorie_arr, true)) {
                    return $lastCategoryID;
                }
            }

            return (int)$this->oKategorie_arr[0];
        }
        $params         = ['cgid' => $customerGroupID ?? $this->kKundengruppe, 'pid' => $id];
        $categoryFilter = '';
        if (isset($_SESSION['LetzteKategorie']) && \is_numeric($_SESSION['LetzteKategorie'])) {
            $categoryFilter = ' AND tkategorieartikel.kKategorie = :fcid';
            $params['fcid'] = $_SESSION['LetzteKategorie'];
        }
        $category = $this->getDB()->getSingleObject(
            'SELECT tkategorieartikel.kKategorie
                FROM tkategorieartikel
                LEFT JOIN tkategoriesichtbarkeit
                    ON tkategoriesichtbarkeit.kKategorie = tkategorieartikel.kKategorie
                    AND tkategoriesichtbarkeit.kKundengruppe = :cgid
                JOIN tkategorie
                    ON tkategorie.kKategorie = tkategorieartikel.kKategorie
                WHERE tkategoriesichtbarkeit.kKategorie IS NULL
                    AND kArtikel = :pid' . $categoryFilter . '
                ORDER BY tkategorie.nSort
                LIMIT 1',
            $params
        );

        return (int)($category->kKategorie ?? 0);
    }

    /**
     * @param int            $customerGroupID
     * @param Artikel|object $tmpProduct
     * @param int            $customerID - always keep at 0 when saving the result to cache
     * @return $this
     */
    public function holPreise(int $customerGroupID, $tmpProduct, int $customerID = 0): self
    {
        $this->Preise = new Preise(
            $customerGroupID,
            (int)$tmpProduct->kArtikel,
            $customerID,
            (int)$tmpProduct->kSteuerklasse,
            $this->getDB()
        );
        if ($this->getOption('nHidePrices', 0) === 1 || !$this->getCustomerGroup()->mayViewPrices()) {
            $this->Preise->setPricesToZero();
        }
        $this->Preise->localizePreise();

        return $this;
    }

    /**
     * @param int $customerGroupID
     * @param int $customerID
     * @return $this
     */
    protected function getCustomerPrice(int $customerGroupID, int $customerID): self
    {
        if (!$this->Preise->customerHasCustomPriceForProduct($customerID, $this->kArtikel)) {
            return $this;
        }
        $this->Preise = new Preise(
            $customerGroupID,
            $this->kArtikel,
            $customerID,
            $this->kSteuerklasse,
            $this->getDB()
        );
        if ($this->getOption('nHidePrices', 0) === 1 || !$this->getCustomerGroup()->mayViewPrices()) {
            $this->Preise->setPricesToZero();
        }
        $this->Preise->localizePreise();
        $this->getVariationDetailPrice($customerGroupID, $customerID);
        $this->staffelPreis_arr = $this->getTierPrices();

        return $this;
    }

    /**
     * @param int $customerGroupID
     * @return $this
     */
    private function rabattierePreise(int $customerGroupID): self
    {
        if ($this->Preise !== null && \method_exists($this->Preise, 'rabbatierePreise')) {
            $discount = $this->getDiscount($customerGroupID);
            if ($discount !== 0) {
                $this->Preise->rabbatierePreise($discount)->localizePreise();
            }
        }

        return $this;
    }

    /**
     * @param float $maxDiscount
     * @return float|null
     */
    public function gibKundenRabatt($maxDiscount)
    {
        $customer = Frontend::getCustomer();

        return ($customer->getID() > 0 && (double)$customer->fRabatt > $maxDiscount)
            ? (double)$customer->fRabatt
            : $maxDiscount;
    }

    /**
     * @param int|float    $amount
     * @param array        $attributes
     * @param int          $customerGroupID
     * @param string|false $unique
     * @param bool         $assign
     * @return float|null
     */
    public function gibPreis(
        $amount,
        array $attributes,
        int $customerGroupID = 0,
        string|bool $unique = '',
        bool $assign = true
    ) {
        if (!$this->getCustomerGroup()->mayViewPrices()) {
            return null;
        }
        if ($this->kArtikel === null) {
            return 0;
        }
        if (!$customerGroupID) {
            $customerGroupID = $this->kKundengruppe ?? $this->getCustomerGroup()->getID();
        }
        $customerID = Frontend::getCustomer()->getID();
        $prices     = new Preise(
            $customerGroupID,
            $this->kArtikel,
            $customerID,
            $this->kSteuerklasse,
            $this->getDB()
        );
        $prices->rabbatierePreise($this->getDiscount($customerGroupID));
        if ($assign) {
            $this->Preise = $prices;
        }
        $price = $prices->fVKNetto;
        if ($this->getFunctionalAttributevalue(\FKT_ATTRIBUT_VOUCHER_FLEX)) {
            $customCalculated = (float)Frontend::get(
                'customCalculated_' . $unique,
                Request::postVar(\FKT_ATTRIBUT_VOUCHER_FLEX . 'Value')
            );
            if ($customCalculated > 0) {
                $price = Tax::getNet($customCalculated, Tax::getSalesTax($this->kSteuerklasse), 4);
                Frontend::set('customCalculated_' . $unique, $customCalculated);
            }
        }
        foreach ($prices->fPreis_arr as $i => $fPreis) {
            if ($prices->nAnzahl_arr[$i] <= $amount) {
                $price = $fPreis;
            }
        }
        $net = $this->getCustomerGroup()->isMerchant();
        // Ticket #1247
        $price = $net
            ? \round($price, 4)
            : Tax::getGross(
                $price,
                Tax::getSalesTax($this->kSteuerklasse),
                4
            ) / ((100 + Tax::getSalesTax($this->kSteuerklasse)) / 100);
        // Falls es sich um eine Variationskombination handelt, spielen Variationsaufpreise keine Rolle,
        // da Vakombis Ihre Aufpreise direkt im Artikelpreis definieren.
        if ($this->nIstVater === 1 || $this->kVaterArtikel > 0) {
            return $price;
        }
        foreach ($attributes as $item) {
            if (isset($item->cTyp) && ($item->cTyp === 'FREIFELD' || $item->cTyp === 'PFLICHT-FREIFELD')) {
                continue;
            }
            $propValueID = 0;
            if (isset($item->kEigenschaftWert) && $item->kEigenschaftWert > 0) {
                $propValueID = (int)$item->kEigenschaftWert;
            } elseif ($item > 0) {
                $propValueID = (int)$item;
            }
            $propValue       = new EigenschaftWert($propValueID, $this->getDB());
            $extraCharge     = (float)($propValue->fAufpreisNetto ?? 0);
            $propExtraCharge = $this->getDB()->select(
                'teigenschaftwertaufpreis',
                'kEigenschaftWert',
                $propValueID,
                'kKundengruppe',
                $customerGroupID
            );
            if (!\is_object($propExtraCharge) && $prices->isDiscountable()) {
                $propExtraCharge = $this->getDB()->select(
                    'teigenschaftwert',
                    'kEigenschaftWert',
                    $propValueID
                );
            }
            if ($propExtraCharge !== null) {
                $fMaxRabatt  = $this->getDiscount($customerGroupID);
                $extraCharge = $propExtraCharge->fAufpreisNetto * ((100 - $fMaxRabatt) / 100);
            }
            // Ticket #1247
            $extraCharge = $net
                ? \round($extraCharge, 4)
                : Tax::getGross(
                    $extraCharge,
                    Tax::getSalesTax($this->kSteuerklasse),
                    4
                ) / ((100 + Tax::getSalesTax($this->kSteuerklasse)) / 100);

            $price += $extraCharge;
        }

        return $price;
    }

    /**
     * @return $this
     */
    public function holBilder(): self
    {
        $this->Bilder = [];
        if ($this->kArtikel === 0 || $this->kArtikel === null) {
            return $this;
        }
        $images  = [];
        $baseURL = Shop::getImageBaseURL();

        $this->cVorschaubild    = \BILD_KEIN_ARTIKELBILD_VORHANDEN;
        $this->cVorschaubildURL = $baseURL . \BILD_KEIN_ARTIKELBILD_VORHANDEN;
        // pruefe ob Funktionsattribut "artikelbildlink" \ART_ATTRIBUT_BILDLINK gesetzt ist
        // Falls ja, lade die Bilder des anderen Artikels
        if ($this->getFunctionalAttributevalue(\ART_ATTRIBUT_BILDLINK) !== null) {
            $images = $this->getDB()->getObjects(
                'SELECT tartikelpict.cPfad, tartikelpict.nNr
                    FROM tartikelpict
                    JOIN tartikel
                         ON tartikelpict.kArtikel = tartikel.kArtikel
                    WHERE tartikel.cArtNr = :cartnr
                    ORDER BY tartikelpict.nNr',
                ['cartnr' => $this->getFunctionalAttributevalue(\ART_ATTRIBUT_BILDLINK)]
            );
        }

        if (\count($images) === 0) {
            $images = $this->getDB()->getObjects(
                'SELECT cPfad, nNr
                    FROM tartikelpict
                    WHERE kArtikel = :pid
                    ORDER BY nNr',
                ['pid' => (int)$this->kArtikel]
            );
        }
        if ($this->getFunctionalAttributevalue(\FKT_ATTRIBUT_BILDNAME) !== null) {
            $this->customImgName = $this->getFunctionalAttributevalue(\FKT_ATTRIBUT_BILDNAME);
        }
        if (\count($images) === 0) {
            $image               = new stdClass();
            $image->cPfadMini    = \BILD_KEIN_ARTIKELBILD_VORHANDEN;
            $image->cPfadKlein   = \BILD_KEIN_ARTIKELBILD_VORHANDEN;
            $image->cPfadNormal  = \BILD_KEIN_ARTIKELBILD_VORHANDEN;
            $image->cPfadGross   = \BILD_KEIN_ARTIKELBILD_VORHANDEN;
            $image->cURLMini     = $baseURL . \BILD_KEIN_ARTIKELBILD_VORHANDEN;
            $image->cURLKlein    = $baseURL . \BILD_KEIN_ARTIKELBILD_VORHANDEN;
            $image->cURLNormal   = $baseURL . \BILD_KEIN_ARTIKELBILD_VORHANDEN;
            $image->cURLGross    = $baseURL . \BILD_KEIN_ARTIKELBILD_VORHANDEN;
            $image->nNr          = 1;
            $image->cAltAttribut = \strip_tags(\str_replace(['"', "'"], '', $this->cName));
            $image->imageSizes   = $this->prepareImageDetails($image, false);
            $image->galleryJSON  = \json_encode($image->imageSizes, \JSON_FORCE_OBJECT);

            $this->Bilder[0] = $image;

            return $this;
        }
        $paths = [];
        foreach ($images as $i => $item) {
            if (\in_array($item->cPfad, $paths, true)) {
                continue;
            }
            $paths[] = $item->cPfad;
            $imgNo   = (int)$item->nNr;
            $image   = new stdClass();
            $this->generateAllImageSizes(false, $imgNo, $item->cPfad);
            $image->cPfadMini   = $this->images[$imgNo][Image::SIZE_XS];
            $image->cPfadKlein  = $this->images[$imgNo][Image::SIZE_SM];
            $image->cPfadNormal = $this->images[$imgNo][Image::SIZE_MD];
            $image->cPfadGross  = $this->images[$imgNo][Image::SIZE_LG];
            $image->nNr         = $imgNo;
            $image->cURLMini    = $baseURL . $image->cPfadMini;
            $image->cURLKlein   = $baseURL . $image->cPfadKlein;
            $image->cURLNormal  = $baseURL . $image->cPfadNormal;
            $image->cURLGross   = $baseURL . $image->cPfadGross;

            if ($i === 0) {
                $this->cVorschaubild    = $image->cPfadKlein;
                $this->cVorschaubildURL = $baseURL . $this->cVorschaubild;
            }
            // Lookup image alt attribute
            $idx                 = 'img_alt_' . $imgNo;
            $image->cAltAttribut = \strip_tags(
                \str_replace(
                    ['"', "'"],
                    '',
                    $this->AttributeAssoc[$idx] ?? $this->cName
                )
            );
            if (isset($this->AttributeAssoc[$idx])) {
                $image->cAltAttribut = Text::htmlentitiesOnce($image->cAltAttribut, \ENT_COMPAT | \ENT_HTML401);
            }

            $image->imageSizes  = $this->prepareImageDetails($image, false);
            $image->galleryJSON = \json_encode($image->imageSizes, \JSON_FORCE_OBJECT);
            $this->Bilder[]     = $image;
        }
        unset($this->images);

        return $this;
    }

    /**
     * @param stdClass $image
     * @param bool     $json
     * @return false|object|string
     */
    private function prepareImageDetails(stdClass $image, bool $json = true)
    {
        $result = (object)[
            'xs' => $this->getProductImageSize($image, 'xs'),
            'sm' => $this->getProductImageSize($image, 'sm'),
            'md' => $this->getProductImageSize($image, 'md'),
            'lg' => $this->getProductImageSize($image, 'lg')
        ];

        return $json === true ? \json_encode($result, \JSON_FORCE_OBJECT) : $result;
    }

    /**
     * @param stdClass $image
     * @param string   $size
     * @return object|null
     */
    private function getProductImageSize(stdClass $image, string $size)
    {
        $imagePath = match ($size) {
            'xs'    => $image->cPfadMini,
            'sm'    => $image->cPfadKlein,
            'md'    => $image->cPfadNormal,
            default => $image->cPfadGross,
        };
        if ($imagePath !== null && \file_exists(\PFAD_ROOT . $imagePath)) {
            [$width, $height, $type] = \getimagesize(\PFAD_ROOT . $imagePath);
        } else {
            $req = Product::toRequest($imagePath);

            if (!\is_object($req)) {
                return new stdClass();
            }

            $settings = Image::getSettings();
            $sizeType = $req->getSizeType();
            if (!isset($settings['size'][$sizeType])) {
                return null;
            }
            $size = $settings['size'][$sizeType];

            if ($settings['container'] === true) {
                $width  = $size['width'];
                $height = $size['height'];
                $type   = $settings['format'] === 'png' ? \IMAGETYPE_PNG : \IMAGETYPE_JPEG;
            } else {
                [$oldWidth, $oldHeight, $type] = \getimagesize($req->getRaw());

                if ($oldWidth > 0 && $oldHeight > 0) {
                    $scale  = \min($size['width'] / $oldWidth, $size['height'] / $oldHeight);
                    $width  = \ceil($scale * $oldWidth);
                    $height = \ceil($scale * $oldHeight);
                } else {
                    $width  = $size['width'];
                    $height = $size['height'];
                }
            }
        }

        return (object)[
            'src'  => Shop::getImageBaseURL() . $imagePath,
            'size' => (object)[
                'width'  => $width,
                'height' => $height
            ],
            'type' => $type,
            'alt'  => \str_replace('"', '', $image->cAltAttribut)
        ];
    }

    /**
     * @param stdClass $image
     * @return string
     */
    public function getArtikelImageJSON(stdClass $image): string
    {
        return $this->prepareImageDetails($image);
    }

    /**
     * @return $this
     */
    public function holArtikelAttribute(): self
    {
        $this->FunktionsAttribute = [];
        if ($this->kArtikel > 0) {
            $attributes = $this->getDB()->selectAll(
                'tartikelattribut',
                'kArtikel',
                $this->kArtikel,
                'cName, cWert',
                'kArtikelAttribut'
            );
            foreach ($attributes as $att) {
                $this->FunktionsAttribute[\mb_convert_case($att->cName, \MB_CASE_LOWER)] = $att->cWert;
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function holAttribute(): self
    {
        $this->Attribute      = [];
        $this->AttributeAssoc = [];
        $attributes           = $this->getDB()->selectAll(
            'tattribut',
            'kArtikel',
            $this->kArtikel,
            '*',
            'nSort'
        );
        $isDefaultLanguage    = LanguageHelper::isDefaultLanguageActive(languageID: $this->kSprache);
        foreach ($attributes as $att) {
            $attribute            = new stdClass();
            $attribute->nSort     = (int)$att->nSort;
            $attribute->kArtikel  = (int)$att->kArtikel;
            $attribute->kAttribut = (int)$att->kAttribut;
            $attribute->cName     = $att->cName;
            $attribute->cWert     = $att->cTextWert ?: $att->cStringWert;
            if ($att->kAttribut > 0 && $this->kSprache > 0 && !$isDefaultLanguage) {
                $attributsprache = $this->getDB()->select(
                    'tattributsprache',
                    'kAttribut',
                    (int)$att->kAttribut,
                    'kSprache',
                    $this->kSprache
                );
                if ($attributsprache !== null && !empty($attributsprache->cName)) {
                    $attribute->cName = $attributsprache->cName;
                    if ($attributsprache->cStringWert) {
                        $attribute->cWert = $attributsprache->cStringWert;
                    } elseif ($attributsprache->cTextWert) {
                        $attribute->cWert = $attributsprache->cTextWert;
                    }
                }
            }
            //assoc array mit attr erstellen
            if ($attribute->cName && $attribute->cWert) {
                $this->AttributeAssoc[$attribute->cName] = $attribute->cWert;
            }
            if (!$this->filterAttribut(\mb_convert_case($attribute->cName, \MB_CASE_LOWER))) {
                $this->Attribute[] = $attribute;
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function holeMerkmale(): self
    {
        $this->oMerkmale_arr = [];
        $db                  = $this->getDB();
        $characteristics     = $db->getObjects(
            'SELECT tartikelmerkmal.kMerkmal, tartikelmerkmal.kMerkmalWert
                FROM tartikelmerkmal
                JOIN tmerkmal
                    ON tmerkmal.kMerkmal = tartikelmerkmal.kMerkmal
                JOIN tmerkmalwert
                    ON tmerkmalwert.kMerkmalWert = tartikelmerkmal.kMerkmalWert
                WHERE tartikelmerkmal.kArtikel = :kArtikel
                ORDER BY tmerkmal.nSort, tmerkmalwert.nSort, tartikelmerkmal.kMerkmal',
            ['kArtikel' => $this->kArtikel]
        );
        if (\count($characteristics) === 0) {
            return $this;
        }
        foreach ($characteristics as $item) {
            $item->kMerkmal     = (int)$item->kMerkmal;
            $item->kMerkmalWert = (int)$item->kMerkmalWert;
            $charValue          = new MerkmalWert($item->kMerkmalWert, $this->kSprache, $db);
            if (!isset($this->oMerkmale_arr[$item->kMerkmal])) {
                $this->oMerkmale_arr[$item->kMerkmal] = new Merkmal($item->kMerkmal, false, $this->kSprache, $db);
                $this->oMerkmale_arr[$item->kMerkmal]->setCharacteristicValues([]);
            }
            $this->oMerkmale_arr[$item->kMerkmal]->addCharacteristicValue($charValue);
        }
        $this->cMerkmalAssoc_arr = [];
        foreach ($this->oMerkmale_arr as $item) {
            $name = \preg_replace('/[^öäüÖÄÜßa-zA-Z\d\.\-_]/u', '', $item->getName($this->kSprache) ?? '');
            if (\mb_strlen($name) > 0) {
                $values                         = \array_filter(
                    \array_map(
                        static function (MerkmalWert $e) {
                            return $e->getValue();
                        },
                        $item->getCharacteristicValues()
                    )
                );
                $this->cMerkmalAssoc_arr[$name] = \implode(', ', $values);
            }
        }

        return $this;
    }

    /**
     * @param int  $customerGroupID
     * @param bool $getInvisibleParts
     * @return $this
     */
    public function holeStueckliste(int $customerGroupID = 0, bool $getInvisibleParts = false): self
    {
        if ($this->kArtikel <= 0 || $this->kStueckliste <= 0) {
            return $this;
        }
        $cond  = $getInvisibleParts ? '' : ' WHERE tartikelsichtbarkeit.kArtikel IS NULL';
        $parts = $this->getDB()->getObjects(
            'SELECT tartikel.kArtikel, tstueckliste.fAnzahl
                  FROM tartikel
                  JOIN tstueckliste
                      ON tstueckliste.kArtikel = tartikel.kArtikel
                      AND tstueckliste.kStueckliste = :plid
                  LEFT JOIN tartikelsichtbarkeit
                      ON tstueckliste.kArtikel = tartikelsichtbarkeit.kArtikel
                      AND tartikelsichtbarkeit.kKundengruppe = :cgid' . $cond,
            ['plid' => $this->kStueckliste, 'cgid' => $customerGroupID]
        );

        $options                             = self::getDefaultOptions();
        $options->nKeineSichtbarkeitBeachten = $getInvisibleParts ? 1 : 0;
        foreach ($parts as $i => $partList) {
            $product = new self($this->getDB(), $this->getCustomerGroup(), $this->currency, $this->getCache());
            $product->fuelleArtikel((int)$partList->kArtikel, $options, $customerGroupID, $this->kSprache);
            $product->holeBewertungDurchschnitt();
            $product->fAnzahl_stueckliste = $partList->fAnzahl;

            $this->oStueckliste_arr[$i] = $product;
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function getProductBundle(): self
    {
        $this->oProduktBundleMain              = new self(
            $this->getDB(),
            $this->getCustomerGroup(),
            $this->currency,
            $this->getCache()
        );
        $this->oProduktBundlePrice             = new stdClass();
        $this->oProduktBundlePrice->fVKNetto   = 0.0;
        $this->oProduktBundlePrice->fPriceDiff = 0.0;
        $this->oProduktBundle_arr              = [];

        $main = $this->getDB()->getSingleObject(
            'SELECT tartikel.kArtikel, tartikel.kStueckliste
                FROM tstueckliste
                INNER JOIN tartikel ON tartikel.kStueckliste = tstueckliste.kStueckliste
                LEFT JOIN tartikelsichtbarkeit t on tartikel.kArtikel = t.kArtikel
                    AND t.kKundengruppe = :customerGroupId
                WHERE tstueckliste.kArtikel = :kArtikel
                    AND t.kArtikel IS NULL',
            [
                'kArtikel'        => $this->kArtikel,
                'customerGroupId' => $this->getCustomerGroupID()
            ]
        );
        if ($main !== null && $main->kArtikel > 0 && $main->kStueckliste > 0) {
            $opt               = self::getDefaultOptions();
            $opt->nStueckliste = 1;
            $this->oProduktBundleMain->fuelleArtikel((int)$main->kArtikel, $opt, $this->kKundengruppe, $this->kSprache);

            $bundles = $this->getDB()->selectAll(
                'tstueckliste',
                'kStueckliste',
                $main->kStueckliste,
                'kArtikel, fAnzahl'
            );
            foreach ($bundles as $bundle) {
                $product = new self($this->getDB(), $this->getCustomerGroup(), $this->currency, $this->getCache());
                $product->fuelleArtikel((int)$bundle->kArtikel, $opt, $this->kKundengruppe, $this->kSprache);
                if ($product->kArtikel > 0) {
                    $this->oProduktBundle_arr[]          = $product;
                    $this->oProduktBundlePrice->fVKNetto += $product->Preise->fVKNetto * $bundle->fAnzahl;
                }
            }

            $this->oProduktBundlePrice->fPriceDiff         = $this->oProduktBundlePrice->fVKNetto -
                ($this->oProduktBundleMain->Preise->fVKNetto ?? 0);
            $this->oProduktBundlePrice->fVKNetto           = $this->oProduktBundleMain->Preise->fVKNetto ?? 0;
            $this->oProduktBundlePrice->cPriceLocalized    = [];
            $this->oProduktBundlePrice->cPriceLocalized[0] = Preise::getLocalizedPriceString(
                Tax::getGross(
                    $this->oProduktBundlePrice->fVKNetto,
                    $_SESSION['Steuersatz'][$this->oProduktBundleMain->kSteuerklasse] ?? null
                ),
                $this->currency
            );

            $this->oProduktBundlePrice->cPriceLocalized[1]     = Preise::getLocalizedPriceString(
                $this->oProduktBundlePrice->fVKNetto,
                $this->currency
            );
            $this->oProduktBundlePrice->cPriceDiffLocalized    = [];
            $this->oProduktBundlePrice->cPriceDiffLocalized[0] = Preise::getLocalizedPriceString(
                Tax::getGross(
                    $this->oProduktBundlePrice->fPriceDiff,
                    $_SESSION['Steuersatz'][$this->oProduktBundleMain->kSteuerklasse] ?? null
                ),
                $this->currency
            );
            $this->oProduktBundlePrice->cPriceDiffLocalized[1] = Preise::getLocalizedPriceString(
                $this->oProduktBundlePrice->fPriceDiff,
                $this->currency
            );
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function getMediaFiles(): self
    {
        $kDefaultLanguage       = LanguageHelper::getDefaultLanguage()->kSprache;
        $this->oMedienDatei_arr = [];
        $mediaTypes             = [];
        // Funktionsattribut gesetzt? Tab oder Beschreibung
        $tabs = $this->getFunctionalAttributevalue(\FKT_ATTRIBUT_MEDIENDATEIEN);
        if ($tabs === 'tab') {
            $this->cMedienDateiAnzeige = 'tab';
        } elseif ($tabs === 'beschreibung') {
            $this->cMedienDateiAnzeige = 'beschreibung';
        }
        if ($this->kSprache === $kDefaultLanguage) {
            $conditionalFields   = 'lang.cName, lang.cBeschreibung, lang.kSprache';
            $conditionalLeftJoin = 'LEFT JOIN tmediendateisprache AS lang
                                    ON lang.kMedienDatei = tmediendatei.kMedienDatei
                                    AND lang.kSprache = ' . $this->kSprache;
        } else {
            $conditionalFields   = "IF(TRIM(IFNULL(lang.cName, '')) != '', lang.cName, deflang.cName) cName,
                                        IF(TRIM(IFNULL(lang.cBeschreibung, '')) != '',
                                        lang.cBeschreibung, deflang.cBeschreibung) cBeschreibung,
                                        IF(TRIM(IFNULL(lang.kSprache, '')) != '',
                                        lang.kSprache, deflang.kSprache) kSprache";
            $conditionalLeftJoin = 'LEFT JOIN tmediendateisprache AS deflang
                                        ON deflang.kMedienDatei = tmediendatei.kMedienDatei
                                    AND deflang.kSprache = ' . $kDefaultLanguage . '
                                    LEFT JOIN tmediendateisprache AS lang
                                        ON deflang.kMedienDatei = lang.kMedienDatei
                                        AND lang.kSprache = ' . $this->kSprache;
        }
        $this->oMedienDatei_arr = $this->getDB()->getObjects(
            'SELECT tmediendatei.kMedienDatei, tmediendatei.cPfad, tmediendatei.cURL, tmediendatei.cTyp,
            tmediendatei.nSort, ' . $conditionalFields . '
                FROM tmediendatei
                ' . $conditionalLeftJoin . '
                WHERE tmediendatei.kArtikel = :pid
                ORDER BY tmediendatei.nSort ASC',
            ['pid' => $this->kArtikel]
        );
        foreach ($this->oMedienDatei_arr as $mediaFile) {
            $mediaFile->kMedienDatei             = (int)$mediaFile->kMedienDatei;
            $mediaFile->kSprache                 = (int)$mediaFile->kSprache;
            $mediaFile->nSort                    = (int)$mediaFile->nSort;
            $mediaFile->oMedienDateiAttribut_arr = [];
            $mediaFile->nErreichbar              = 1; // Beschreibt, ob eine Datei vorhanden ist
            $mediaFile->cMedienTyp               = ''; // Wird zum Aufbau der Reiter gebraucht
            if (\mb_strlen($mediaFile->cTyp) > 0) {
                if ($mediaFile->cTyp === '.*') {
                    $extMatch = [];
                    \preg_match('/\.\w{3,4}($|\?)/', $mediaFile->cPfad, $extMatch);
                    $mediaFile->cTyp = $extMatch[0] ?? '.*';
                }
                $mapped                = $this->mapMediaType($mediaFile->cTyp);
                $mediaFile->cMedienTyp = $mapped->cName;
                $mediaFile->nMedienTyp = $mapped->nTyp;
                $mediaFile->videoType  = $mapped->videoType;
            }
            if ($mediaFile->cPfad !== '' && $mediaFile->cPfad[0] === '/') {
                //remove double slashes
                $mediaFile->cPfad = \mb_substr($mediaFile->cPfad, 1);
            }
            // Hole alle Attribute zu einer Mediendatei (falls vorhanden)
            $mediaFile->oMedienDateiAttribut_arr = $this->getDB()->selectAll(
                'tmediendateiattribut',
                ['kMedienDatei', 'kSprache'],
                [$mediaFile->kMedienDatei, $this->kSprache]
            );
            // pruefen, ob ein Attribut mit "tab" gesetzt wurde => falls ja, den Reiter anlegen
            $mediaFile->cAttributTab = '';
            foreach ($mediaFile->oMedienDateiAttribut_arr as $oMedienDateiAttribut) {
                if ($oMedienDateiAttribut->cName === 'tab') {
                    $mediaFile->cAttributTab = $oMedienDateiAttribut->cWert;
                }
            }
            $mediaFile->video = Video::fromMediaFile($mediaFile);
            $mediaTypeName    = \mb_strlen($mediaFile->cAttributTab) > 0
                ? $mediaFile->cAttributTab
                : $mediaFile->cMedienTyp;
            // group all tab names by corresponding seo tab name, use first found tab name
            $mediaTypeNameSeo = $this->getSeoString($mediaTypeName);
            $mediaItem        = $mediaTypes[$mediaTypeNameSeo] ?? null;
            if ($mediaItem !== null) {
                ++$mediaItem->count;
            } else {
                $mediaTypes[$mediaTypeNameSeo] = (object)[
                    'count' => 1,
                    'name'  => $mediaTypeName
                ];
            }
        }
        $this->setMediaTypes($mediaTypes);

        return $this;
    }

    /**
     * @return array
     */
    public function getMediaTypes(): array
    {
        return $this->cMedienTyp_arr;
    }

    /**
     * @param array $mediaTypes
     * @return Artikel
     */
    private function setMediaTypes(array $mediaTypes): self
    {
        $this->cMedienTyp_arr = $mediaTypes;

        return $this;
    }

    /**
     * @param string $attributeName
     * @return bool
     */
    public function filterAttribut(string $attributeName): bool
    {
        $sub = \mb_substr($attributeName, 0, 7);
        if ($sub === 'intern_' || $sub === 'img_alt') {
            return true;
        }
        if (\mb_stripos($attributeName, 'T') === 0) {
            for ($i = 1; $i < 11; $i++) {
                $stl = \mb_convert_case($attributeName, \MB_CASE_LOWER);
                if ($stl === 'tab' . $i . ' name' || $stl === 'tab' . $i . ' inhalt') {
                    return true;
                }
            }
        }
        $names = [
            \ART_ATTRIBUT_STEUERTEXT,
            \ART_ATTRIBUT_METATITLE,
            \ART_ATTRIBUT_METADESCRIPTION,
            \ART_ATTRIBUT_METAKEYWORDS,
            \ART_ATTRIBUT_AMPELTEXT_GRUEN,
            \ART_ATTRIBUT_AMPELTEXT_GELB,
            \ART_ATTRIBUT_AMPELTEXT_ROT,
            \ART_ATTRIBUT_SHORTNAME
        ];

        return \in_array($attributeName, $names, true);
    }

    /**
     * @param int    $perPage
     * @param int    $page
     * @param int    $stars
     * @param string $unlock
     * @param int    $opt
     * @param bool   $allLanguages
     * @return $this
     */
    public function holeBewertung(
        int $perPage = 10,
        int $page = 1,
        int $stars = 0,
        string $unlock = 'N',
        int $opt = 0,
        bool $allLanguages = false
    ): self {
        $this->Bewertungen = new Bewertung(
            $this->kArtikel,
            $this->kSprache,
            $perPage,
            $page,
            $stars,
            $unlock,
            $opt,
            $allLanguages
        );

        return $this;
    }

    /**
     * @param int $minStars
     * @return $this
     */
    public function holeBewertungDurchschnitt(int $minStars = 1): self
    {
        // when $this->bIsTopBewertet === null, there were no ratings found at all -
        // so we don't need to calculate an average.
        if ($minStars > 0 && $this->bIsTopBewertet !== null) {
            $productID = ($this->kEigenschaftKombi !== null && (int)$this->kEigenschaftKombi > 0)
                ? (int)$this->kVaterArtikel
                : (int)$this->kArtikel;
            $productID = $productID > 0 ? $productID : (int)$this->kArtikel;
            $rating    = $this->getDB()->getSingleObject(
                'SELECT fDurchschnittsBewertung
                    FROM tartikelext
                    WHERE ROUND(fDurchschnittsBewertung) >= :ms
                        AND kArtikel = :pid',
                ['ms' => $minStars, 'pid' => $productID]
            );
            if ($rating !== null) {
                $this->fDurchschnittsBewertung = \round($rating->fDurchschnittsBewertung * 2) / 2;
            }
        }

        return $this;
    }

    /**
     * @param string $unlock
     * @return $this
     */
    public function holehilfreichsteBewertung(string $unlock = 'N'): self
    {
        $this->HilfreichsteBewertung = new Bewertung(
            $this->kArtikel,
            $this->kSprache,
            0,
            0,
            0,
            $unlock,
            1,
            $this->getConfigValue('bewertung', 'bewertung_alle_sprachen') === 'Y'
        );

        return $this;
    }

    /**
     * @param int  $customerGroupID
     * @param bool $exportWorkaround
     * @return stdClass[]
     */
    protected function execVariationSQL(int $customerGroupID, bool $exportWorkaround = false): array
    {
        $isDefaultLang = LanguageHelper::isDefaultLanguageActive(false, $this->kSprache);
        // Nicht Standardsprache?
        $propertySQL  = new SqlObject();
        $propValueSQL = new SqlObject();
        if ($this->kSprache > 0 && !$isDefaultLang) {
            $propertySQL->setSelect('teigenschaftsprache.cName AS cName_teigenschaftsprache, ');
            $propertySQL->setJoin(
                ' LEFT JOIN teigenschaftsprache
                    ON teigenschaftsprache.kEigenschaft = teigenschaft.kEigenschaft
                    AND teigenschaftsprache.kSprache = :lid'
            );
            $propertySQL->addParam(':lid', $this->kSprache);

            $propValueSQL->setSelect('teigenschaftwertsprache.cName AS localizedName, ');
            $propValueSQL->setJoin(
                ' LEFT JOIN teigenschaftwertsprache
                    ON teigenschaftwertsprache.kEigenschaftWert = teigenschaftwert.kEigenschaftWert
                    AND teigenschaftwertsprache.kSprache = :lid'
            );
            $propValueSQL->addParam(':lid', $this->kSprache);
        }
        // Vater?
        if ($this->nIstVater === 1) {
            $variations = $this->getDB()->getObjects(
                'SELECT tartikel.kArtikel AS tartikel_kArtikel, tartikel.fLagerbestand AS tartikel_fLagerbestand,
                    tartikel.cLagerBeachten, tartikel.cLagerKleinerNull, tartikel.cLagerVariation,
                    teigenschaftkombiwert.kEigenschaft, tartikel.fVPEWert, teigenschaftkombiwert.kEigenschaftKombi,
                    teigenschaft.kArtikel, teigenschaftkombiwert.kEigenschaftWert, teigenschaft.cName,
                    teigenschaft.cWaehlbar, teigenschaft.cTyp, teigenschaft.nSort, '
                    . $propertySQL->getSelect() . ' teigenschaftwert.cName AS cName_teigenschaftwert, '
                    . $propValueSQL->getSelect() . ' teigenschaftwert.fAufpreisNetto, teigenschaftwert.fGewichtDiff,
                    teigenschaftwert.cArtNr, teigenschaftwert.nSort AS teigenschaftwert_nSort,
                    teigenschaftwert.fLagerbestand, teigenschaftwert.fPackeinheit,
                    teigenschaftwertpict.kEigenschaftWertPict, teigenschaftwertpict.cPfad, NULL AS cType,
                    teigenschaftwertaufpreis.fAufpreisNetto AS fAufpreisNetto_teigenschaftwertaufpreis,
                    IF(MIN(tartikel.cLagerBeachten) = MAX(tartikel.cLagerBeachten), MIN(tartikel.cLagerBeachten), \'N\')
                        AS cMergedLagerBeachten,
                    IF(MIN(tartikel.cLagerKleinerNull) = MAX(tartikel.cLagerKleinerNull),
                        MIN(tartikel.cLagerKleinerNull), \'Y\') AS cMergedLagerKleinerNull,
                    IF(MIN(tartikel.cLagerVariation) = MAX(tartikel.cLagerVariation),
                        MIN(tartikel.cLagerVariation), \'Y\') AS cMergedLagerVariation,
                    SUM(tartikel.fLagerbestand) AS fMergedLagerbestand
                    FROM teigenschaftkombiwert
                    JOIN tartikel
                        ON tartikel.kEigenschaftKombi = teigenschaftkombiwert.kEigenschaftKombi
                        AND tartikel.kVaterArtikel = :pid
                    LEFT JOIN teigenschaft
                            ON teigenschaft.kEigenschaft = teigenschaftkombiwert.kEigenschaft
                    LEFT JOIN teigenschaftwert
                            ON teigenschaftwert.kEigenschaftWert = teigenschaftkombiwert.kEigenschaftWert
                    ' . $propertySQL->getJoin() . '
                    ' . $propValueSQL->getJoin() . '
                    LEFT JOIN teigenschaftsichtbarkeit
                        ON teigenschaft.kEigenschaft = teigenschaftsichtbarkeit.kEigenschaft
                        AND teigenschaftsichtbarkeit.kKundengruppe = :cgid
                    LEFT JOIN teigenschaftwertsichtbarkeit
                        ON teigenschaftwert.kEigenschaftWert = teigenschaftwertsichtbarkeit.kEigenschaftWert
                        AND teigenschaftwertsichtbarkeit.kKundengruppe = :cgid
                    LEFT JOIN teigenschaftwertpict
                        ON teigenschaftwertpict.kEigenschaftWert = teigenschaftwert.kEigenschaftWert
                    LEFT JOIN teigenschaftwertaufpreis
                        ON teigenschaftwertaufpreis.kEigenschaftWert = teigenschaftwert.kEigenschaftWert
                        AND teigenschaftwertaufpreis.kKundengruppe = :cgid
                    WHERE teigenschaftsichtbarkeit.kEigenschaft IS NULL
                        AND teigenschaftwertsichtbarkeit.kEigenschaftWert IS NULL
                    GROUP BY teigenschaftkombiwert.kEigenschaftWert
                    ORDER BY teigenschaft.nSort, teigenschaft.cName, teigenschaftwert.nSort, teigenschaftwert.cName',
                \array_merge(
                    ['cgid' => $customerGroupID, 'pid' => $this->kArtikel],
                    $propertySQL->getParams(),
                    $propValueSQL->getParams()
                )
            );

            $tmpVariationsParent = $this->getDB()->getObjects(
                'SELECT teigenschaft.kEigenschaft, teigenschaft.kArtikel, teigenschaft.cName, teigenschaft.cWaehlbar,
                    teigenschaft.cTyp, teigenschaft.nSort, '
                    . $propertySQL->getSelect() . '
                    NULL AS kEigenschaftWert, NULL AS cName_teigenschaftwert,
                    NULL AS localizedName, NULL AS fAufpreisNetto,
                    NULL AS fGewichtDiff, NULL AS cArtNr,
                    NULL AS teigenschaftwert_nSort, NULL AS fLagerbestand,
                    NULL AS fPackeinheit, NULL AS kEigenschaftWertPict,
                    NULL AS cPfad, NULL AS cType,
                    NULL AS fAufpreisNetto_teigenschaftwertaufpreis
                    FROM teigenschaft
                    ' . $propertySQL->getJoin() . '
                    LEFT JOIN teigenschaftsichtbarkeit
                        ON teigenschaft.kEigenschaft = teigenschaftsichtbarkeit.kEigenschaft
                        AND teigenschaftsichtbarkeit.kKundengruppe = :cgid
                    WHERE teigenschaft.kArtikel = :pid
                        AND teigenschaftsichtbarkeit.kEigenschaft IS NULL
                        AND teigenschaft.cTyp IN (\'FREIFELD\', \'PFLICHT-FREIFELD\')
                        ORDER BY teigenschaft.nSort, teigenschaft.cName',
                \array_merge(['pid' => $this->kArtikel, 'cgid' => $customerGroupID], $propertySQL->getParams())
            );

            $variations = \array_merge($variations, $tmpVariationsParent);
        } elseif ($this->kVaterArtikel > 0) { //child?
            $score = new SqlObject();
            if (!$exportWorkaround) {
                $score->setSelect(', COALESCE(ek.score, 0) nMatched');
                $score->setJoin('LEFT JOIN (
                    SELECT teigenschaftkombiwert.kEigenschaftKombi,
                    COUNT(teigenschaftkombiwert.kEigenschaftWert) AS score
                    FROM teigenschaftkombiwert
                    INNER JOIN tartikel ON tartikel.kEigenschaftKombi = teigenschaftkombiwert.kEigenschaftKombi
                    LEFT JOIN tartikelsichtbarkeit ON tartikelsichtbarkeit.kArtikel = tartikel.kArtikel
                        AND tartikelsichtbarkeit.kKundengruppe = :cgid
                    WHERE (kEigenschaft, kEigenschaftWert) IN (
                        SELECT kEigenschaft, kEigenschaftWert
                            FROM teigenschaftkombiwert
                            WHERE kEigenschaftKombi = :kek
                    ) AND tartikelsichtbarkeit.kArtikel IS NULL
                    GROUP BY teigenschaftkombiwert.kEigenschaftKombi
                ) ek ON ek.kEigenschaftKombi = teigenschaftkombiwert.kEigenschaftKombi');
                $score->addParam(':cgid', $customerGroupID);
                $score->addParam(':kek', $this->kEigenschaftKombi);
            }
            $baseQuery = new SqlObject();
            $baseQuery->setStatement(
                'SELECT tartikel.kArtikel AS tartikel_kArtikel,
                    tartikel.fLagerbestand AS tartikel_fLagerbestand, tartikel.cLagerBeachten,
                    tartikel.cLagerKleinerNull, tartikel.cLagerVariation,
                    teigenschaftkombiwert.kEigenschaft, tartikel.fVPEWert, teigenschaftkombiwert.kEigenschaftKombi,
                    teigenschaft.kArtikel, teigenschaftkombiwert.kEigenschaftWert, teigenschaft.cName,
                    teigenschaft.cWaehlbar, teigenschaft.cTyp, teigenschaft.nSort, '
                    . $propertySQL->getSelect() . ' teigenschaftwert.cName AS cName_teigenschaftwert, '
                    . $propValueSQL->getSelect() . ' teigenschaftwert.fAufpreisNetto,
                    teigenschaftwert.fGewichtDiff, teigenschaftwert.cArtNr,
                    teigenschaftwert.nSort AS teigenschaftwert_nSort, teigenschaftwert.fLagerbestand,
                    teigenschaftwert.fPackeinheit, NULL AS cType,
                    teigenschaftwertpict.kEigenschaftWertPict, teigenschaftwertpict.cPfad,
                    teigenschaftwertaufpreis.fAufpreisNetto AS fAufpreisNetto_teigenschaftwertaufpreis'
                    . $score->getSelect() . '
                FROM tartikel
                JOIN teigenschaftkombiwert
                    ON tartikel.kEigenschaftKombi = teigenschaftkombiwert.kEigenschaftKombi
                LEFT JOIN teigenschaft
                    ON teigenschaft.kEigenschaft = teigenschaftkombiwert.kEigenschaft
                LEFT JOIN teigenschaftwert
                    ON teigenschaftwert.kEigenschaftWert = teigenschaftkombiwert.kEigenschaftWert
                ' . $propertySQL->getJoin() . '
                ' . $propValueSQL->getJoin() . '
                ' . $score->getJoin() . '
                LEFT JOIN teigenschaftsichtbarkeit
                    ON teigenschaftsichtbarkeit.kEigenschaft = teigenschaftkombiwert.kEigenschaft
                    AND teigenschaftsichtbarkeit.kKundengruppe = :cgid
                LEFT JOIN teigenschaftwertsichtbarkeit
                    ON teigenschaftwertsichtbarkeit.kEigenschaftWert = teigenschaftkombiwert.kEigenschaftWert
                    AND teigenschaftwertsichtbarkeit.kKundengruppe = :cgid
                LEFT JOIN teigenschaftwertpict
                    ON teigenschaftwertpict.kEigenschaftWert = teigenschaftkombiwert.kEigenschaftWert
                LEFT JOIN teigenschaftwertaufpreis
                    ON teigenschaftwertaufpreis.kEigenschaftWert = teigenschaftkombiwert.kEigenschaftWert
                    AND teigenschaftwertaufpreis.kKundengruppe = :cgid
                WHERE tartikel.kVaterArtikel = :ppid
                    AND teigenschaftsichtbarkeit.kEigenschaft IS NULL
                    AND teigenschaftwertsichtbarkeit.kEigenschaftWert IS NULL'
            );
            $baseQuery->setParams(
                \array_merge(
                    [':cgid' => $customerGroupID, ':ppid' => (int)$this->kVaterArtikel],
                    $propertySQL->getParams(),
                    $propValueSQL->getParams(),
                    $score->getParams()
                )
            );
            if ($exportWorkaround === false) {
                /* Workaround for performance-issue in MySQL 5.5 with large varcombis */
                $allCombinations = $this->getDB()->getObjects(
                    'SELECT CONCAT(\'(\', pref.kEigenschaftWert, \',\', MAX(pref.score), \')\') combine
                        FROM (
                            SELECT teigenschaftkombiwert.kEigenschaftKombi,
                                teigenschaftkombiwert.kEigenschaftWert
                                , COUNT(ek.kEigenschaftWert) score
                            FROM tartikel
                            JOIN teigenschaftkombiwert
                                ON tartikel.kEigenschaftKombi = teigenschaftkombiwert.kEigenschaftKombi
                            LEFT JOIN teigenschaftkombiwert ek
                                ON ek.kEigenschaftKombi = teigenschaftkombiwert.kEigenschaftKombi
                                AND ek.kEigenschaftWert IN (
                                    SELECT kEigenschaftWert
                                        FROM teigenschaftkombiwert
                                        WHERE kEigenschaftKombi = :kek
                                )
                            LEFT JOIN tartikel art
                                ON art.kEigenschaftKombi = ek.kEigenschaftKombi
                            LEFT JOIN tartikelsichtbarkeit
                                ON tartikelsichtbarkeit.kArtikel = art.kArtikel
                                AND tartikelsichtbarkeit.kKundengruppe = :cid
                            WHERE tartikel.kVaterArtikel = :ppid
                                AND tartikelsichtbarkeit.kArtikel IS NULL
                            GROUP BY teigenschaftkombiwert.kEigenschaftKombi, teigenschaftkombiwert.kEigenschaftWert
                        ) pref
                        GROUP BY pref.kEigenschaftWert',
                    [
                        'kek'  => $this->kEigenschaftKombi,
                        'cid'  => $this->kKundengruppe ?? $this->getCustomerGroup()->getID(),
                        'ppid' => $this->kVaterArtikel
                    ]
                );
                $combinations    = \array_reduce($allCombinations, static function ($cArry, $item) {
                    return (empty($cArry) ? '' : $cArry . ', ') . $item->combine;
                }, '');
                $variations      = empty($combinations) ? [] : $this->getDB()->getObjects(
                    $baseQuery->getStatement()
                    . ' AND (teigenschaftkombiwert.kEigenschaftWert, COALESCE(ek.score, 0)) IN (' .
                    $combinations . '
                        )
                        GROUP BY teigenschaftkombiwert.kEigenschaftWert
                        ORDER BY teigenschaft.nSort, teigenschaft.cName, teigenschaftwert.nSort',
                    $baseQuery->getParams()
                );
            } else {
                $variations = $this->getDB()->getObjects(
                    $baseQuery->getStatement() .
                    ' AND teigenschaftwertsichtbarkeit.kEigenschaftWert IS NULL
                        GROUP BY teigenschaftkombiwert.kEigenschaftWert
                        ORDER BY teigenschaft.nSort, teigenschaft.cName,
                        teigenschaftwert.nSort, teigenschaftwert.cName',
                    $baseQuery->getParams()
                );
            }
            $tmpVariationsParent = $this->getDB()->getObjects(
                'SELECT teigenschaft.kEigenschaft, teigenschaft.kArtikel, teigenschaft.cName, teigenschaft.cWaehlbar,
                    teigenschaft.cTyp, teigenschaft.nSort, '
                    . $propertySQL->getSelect() . '
                    NULL AS kEigenschaftWert, NULL AS cName_teigenschaftwert,
                    NULL AS localizedName, NULL AS fAufpreisNetto, NULL AS fGewichtDiff,
                    NULL AS cArtNr, NULL AS teigenschaftwert_nSort,
                    NULL AS fLagerbestand, NULL AS fPackeinheit,
                    NULL AS kEigenschaftWertPict, NULL AS cPfad,
                    NULL AS cType,
                    NULL AS fAufpreisNetto_teigenschaftwertaufpreis
                    FROM teigenschaft
                    ' . $propertySQL->getJoin() . '
                    LEFT JOIN teigenschaftsichtbarkeit
                        ON teigenschaft.kEigenschaft = teigenschaftsichtbarkeit.kEigenschaft
                        AND teigenschaftsichtbarkeit.kKundengruppe = :cgid
                    WHERE (teigenschaft.kArtikel = :ppid
                            OR teigenschaft.kArtikel = :pid)
                        AND teigenschaftsichtbarkeit.kEigenschaft IS NULL
                        AND teigenschaft.cTyp IN (\'FREIFELD\', \'PFLICHT-FREIFELD\')
                        ORDER BY teigenschaft.nSort, teigenschaft.cName',
                \array_merge(
                    ['pid' => $this->kArtikel, 'ppid' => $this->kVaterArtikel, 'cgid' => $customerGroupID],
                    $propertySQL->getParams()
                )
            );

            $variations = \array_merge($variations, $tmpVariationsParent);
            // VariationKombi gesetzte Eigenschaften und EigenschaftWerte vom Kind
            $this->oVariationKombi_arr = $this->getDB()->getObjects(
                'SELECT teigenschaftkombiwert.*
                    FROM teigenschaftkombiwert
                    JOIN tartikel
                      ON tartikel.kArtikel = :pid
                      AND tartikel.kEigenschaftKombi = teigenschaftkombiwert.kEigenschaftKombi',
                ['pid' => $this->kArtikel]
            );
            $this->holeVariationDetailPreisKind(); // Baut die Variationspreise für ein Variationskombkind
            // String für javascript Funktion vorbereiten um Variationen auszufüllen
            $this->cVariationKombi = '';
            foreach ($this->oVariationKombi_arr as $j => $oVariationKombi) {
                $oVariationKombi->kEigenschaftKombi = (int)$oVariationKombi->kEigenschaftKombi;
                $oVariationKombi->kEigenschaftWert  = (int)$oVariationKombi->kEigenschaftWert;
                $oVariationKombi->kEigenschaft      = (int)$oVariationKombi->kEigenschaft;
                if ($j > 0) {
                    $this->cVariationKombi .= ';' . $oVariationKombi->kEigenschaft . '_' .
                        $oVariationKombi->kEigenschaftWert;
                } else {
                    $this->cVariationKombi .= $oVariationKombi->kEigenschaft . '_' . $oVariationKombi->kEigenschaftWert;
                }
            }
        } else {
            $variations = $this->getDB()->getObjects(
                'SELECT teigenschaft.kEigenschaft, teigenschaft.kArtikel, teigenschaft.cName, teigenschaft.cWaehlbar,
                    teigenschaft.cTyp, teigenschaft.nSort, ' . $propertySQL->getSelect() . '
                    teigenschaftwert.kEigenschaftWert, teigenschaftwert.cName AS cName_teigenschaftwert, ' .
                    $propValueSQL->getSelect() . '
                    teigenschaftwert.fAufpreisNetto, teigenschaftwert.fGewichtDiff, teigenschaftwert.cArtNr,
                    teigenschaftwert.nSort AS teigenschaftwert_nSort, teigenschaftwert.fLagerbestand,
                    teigenschaftwert.fPackeinheit, teigenschaftwertpict.kEigenschaftWertPict,
                    teigenschaftwertpict.cPfad, NULL AS cType,
                    teigenschaftwertaufpreis.fAufpreisNetto AS fAufpreisNetto_teigenschaftwertaufpreis
                    FROM teigenschaft
                    LEFT JOIN teigenschaftwert
                        ON teigenschaftwert.kEigenschaft = teigenschaft.kEigenschaft
                    ' . $propertySQL->getJoin() . '
                    ' . $propValueSQL->getJoin() . '
                    LEFT JOIN teigenschaftsichtbarkeit
                        ON teigenschaft.kEigenschaft = teigenschaftsichtbarkeit.kEigenschaft
                        AND teigenschaftsichtbarkeit.kKundengruppe = :cgid
                    LEFT JOIN teigenschaftwertsichtbarkeit
                        ON teigenschaftwert.kEigenschaftWert = teigenschaftwertsichtbarkeit.kEigenschaftWert
                        AND teigenschaftwertsichtbarkeit.kKundengruppe = :cgid
                    LEFT JOIN teigenschaftwertpict
                        ON teigenschaftwertpict.kEigenschaftWert = teigenschaftwert.kEigenschaftWert
                    LEFT JOIN teigenschaftwertaufpreis
                        ON teigenschaftwertaufpreis.kEigenschaftWert = teigenschaftwert.kEigenschaftWert
                        AND teigenschaftwertaufpreis.kKundengruppe = :cgid
                    WHERE teigenschaft.kArtikel = :pid
                        AND teigenschaftsichtbarkeit.kEigenschaft IS NULL
                        AND teigenschaftwertsichtbarkeit.kEigenschaftWert IS NULL
                    ORDER BY teigenschaft.nSort ASC, teigenschaft.cName,
                    teigenschaftwert.nSort ASC, teigenschaftwert.cName',
                \array_merge(
                    ['pid' => $this->kArtikel, 'cgid' => $customerGroupID],
                    $propertySQL->getParams(),
                    $propValueSQL->getParams()
                )
            );
        }
        foreach ($variations as $variation) {
            $variation->kEigenschaft           = (int)$variation->kEigenschaft;
            $variation->kArtikel               = (int)$variation->kArtikel;
            $variation->nSort                  = (int)$variation->nSort;
            $variation->kEigenschaftWert       = (int)$variation->kEigenschaftWert;
            $variation->teigenschaftwert_nSort = (int)$variation->teigenschaftwert_nSort;
            if (isset($variation->kEigenschaftKombi)) {
                $variation->kEigenschaftKombi = (int)$variation->kEigenschaftKombi;
            }
        }

        return $variations;
    }

    /**
     * @param int  $customerGroupID
     * @param bool $exportWorkaround
     * @return $this
     */
    private function holVariationen(int $customerGroupID = 0, bool $exportWorkaround = false): self
    {
        if ($this->kArtikel === null || $this->kArtikel <= 0) {
            return $this;
        }
        if (!$customerGroupID) {
            $customerGroupID = $this->kKundengruppe ?? $this->getCustomerGroup()->getID();
        }
        $this->nVariationsAufpreisVorhanden = 0;
        $this->Variationen                  = [];
        $this->VariationenOhneFreifeld      = [];
        $this->oVariationenNurKind_arr      = [];

        $imageBaseURL  = Shop::getImageBaseURL();
        $mayViewPrices = $this->getCustomerGroup()->mayViewPrices();
        $variations    = $this->execVariationSQL($customerGroupID, $exportWorkaround);
        if (\count($variations) === 0) {
            return $this;
        }
        $lastID      = 0;
        $counter     = -1;
        $tmpDiscount = $this->Preise->isDiscountable() ? $this->getDiscount($customerGroupID) : 0;
        $outOfStock  = ' (' . Shop::Lang()->get('outofstock', 'productDetails') . ')';
        $precision   = $this->getPrecision();
        $per         = ' ' . Shop::Lang()->get('vpePer') . ' ' . $this->cVPEEinheit;
        $taxRate     = $_SESSION['Steuersatz'][$this->kSteuerklasse];
        $matrixConf  = $this->getConfigValue('artikeldetails', 'artikeldetails_warenkorbmatrix_lagerbeachten') === 'Y';
        $prodFilter  = (int)$this->getConfigValue('global', 'artikel_artikelanzeigefilter');

        $cntVariationen = $exportWorkaround
            ? 0
            : $this->getDB()->getSingleInt(
                'SELECT COUNT(teigenschaft.kEigenschaft) AS cnt
                    FROM teigenschaft
                    LEFT JOIN teigenschaftsichtbarkeit
                        ON teigenschaftsichtbarkeit.kEigenschaft = teigenschaft.kEigenschaft
                        AND teigenschaftsichtbarkeit.kKundengruppe = :cgid
                    WHERE kArtikel = :pid
                        AND teigenschaft.cTyp NOT IN (\'FREIFELD\', \'PFLICHT-FREIFELD\')
                        AND teigenschaftsichtbarkeit.kEigenschaft IS NULL',
                'cnt',
                ['cgid' => $customerGroupID, 'pid' => $this->kVaterArtikel]
            );
        foreach ($variations as $i => $tmpVariation) {
            if ($tmpVariation->cTyp === null) {
                continue;
            }
            if ($lastID !== $tmpVariation->kEigenschaft) {
                ++$counter;
                $lastID    = $tmpVariation->kEigenschaft;
                $variation = new Variation();
                $variation->init($tmpVariation);
                $this->Variationen[$counter] = $variation;
            }
            // Fix #1517
            if (!isset($tmpVariation->fAufpreisNetto_teigenschaftwertaufpreis) && $tmpVariation->fAufpreisNetto != 0) {
                $tmpVariation->fAufpreisNetto_teigenschaftwertaufpreis = $tmpVariation->fAufpreisNetto;
            }
            $value = new VariationValue();
            $value->init($tmpVariation, $cntVariationen, $tmpDiscount);
            if ($this->kVaterArtikel > 0 || $this->nIstVater === 1) {
                $value->addChildItems($tmpVariation, $this);
            }
            if (
                $this->cLagerBeachten === 'Y'
                && $this->cLagerVariation === 'Y'
                && $this->cLagerKleinerNull !== 'Y'
                && $value->fLagerbestand <= 0
                && (int)$this->getConfigValue('global', 'artikeldetails_variationswertlager') === 3
            ) {
                unset($value);
                continue;
            }
            $this->Variationen[$counter]->nLieferbareVariationswerte++;

            if (
                $this->cLagerBeachten === 'Y'
                && $this->cLagerVariation === 'Y'
                && $this->cLagerKleinerNull !== 'Y'
                && $this->nIstVater === 0
                && $this->kVaterArtikel === 0
                && $value->fLagerbestand <= 0
                && (int)$this->getConfigValue('global', 'artikeldetails_variationswertlager') === 2
            ) {
                $value->cName .= $outOfStock;
            }
            if ($tmpVariation->cPfad !== null && $value->addImages($tmpVariation->cPfad, $imageBaseURL)) {
                $this->cVariationenbilderVorhanden = true;
            }
            if (!$mayViewPrices) {
                unset($value->fAufpreisNetto, $value->cAufpreisLocalized, $value->cPreisInklAufpreis);
            }
            $value->addPrices($this, $taxRate, $this->currency, $mayViewPrices, $precision, $per);
            $this->Variationen[$counter]->Werte[$i] = $value;
        }
        foreach ($this->Variationen as $i => $item) {
            $item->Werte = \array_merge($item->Werte);
            if ($item->nLieferbareVariationswerte === 0) {
                $this->inWarenkorbLegbar = \INWKNICHTLEGBAR_LAGERVAR;
            }
            if ($item->cTyp === 'FREIFELD' || $item->cTyp === 'PFLICHT-FREIFELD') {
                continue;
            }
            foreach ($item->Werte as $value) {
                $this->nonAllowedVariationValues[$value->kEigenschaftWert] =
                    ProductHelper::getNonAllowedAttributeValues($value->kEigenschaftWert);
            }
            $this->VariationenOhneFreifeld[$i] = $item;
            if ($this->kVaterArtikel > 0 || $this->nIstVater === 1) {
                $members = \array_keys(\get_object_vars($item));
                foreach ($members as $member) {
                    if (!isset($this->oVariationenNurKind_arr[$i])) {
                        $this->oVariationenNurKind_arr[$i] = new stdClass();
                    }
                    $this->oVariationenNurKind_arr[$i]->$member = $item->$member;
                }
                $this->oVariationenNurKind_arr[$i]->Werte = [];
            }
            foreach ($this->VariationenOhneFreifeld[$i]->Werte as $oVariationsWert) {
                // Variationskombi
                if ($this->kVaterArtikel > 0 || $this->nIstVater === 1) {
                    foreach ($this->oVariationKombi_arr as $oVariationKombi) {
                        if ($oVariationKombi->kEigenschaftWert === $oVariationsWert->kEigenschaftWert) {
                            $this->oVariationenNurKind_arr[$i]->Werte[] = $oVariationsWert;
                        }
                    }
                    // Lagerbestand beachten?
                    if (
                        $oVariationsWert->oVariationsKombi->cLagerBeachten === 'Y'
                        && ($oVariationsWert->oVariationsKombi->cLagerKleinerNull === 'N'
                            || $prodFilter === \EINSTELLUNGEN_ARTIKELANZEIGEFILTER_LAGER)
                        && $oVariationsWert->oVariationsKombi->tartikel_fLagerbestand <= 0
                        && $matrixConf === true
                    ) {
                        $oVariationsWert->nNichtLieferbar = 1;
                    }
                } elseif (
                    $this->cLagerVariation === 'Y'
                    && $this->cLagerBeachten === 'Y'
                    && ($this->cLagerKleinerNull === 'N'
                        || $prodFilter === \EINSTELLUNGEN_ARTIKELANZEIGEFILTER_LAGER)
                    && $oVariationsWert->fLagerbestand <= 0
                    && $matrixConf === true
                ) {
                    $oVariationsWert->nNichtLieferbar = 1;
                }
            }
        }
        $this->nVariationenVerfuegbar       = 1;
        $this->nVariationAnzahl             = ($counter + 1);
        $this->nVariationOhneFreifeldAnzahl = \count($this->VariationenOhneFreifeld);
        // Ausverkauft aus Varkombis mit mehr als 1 Variation entfernen
        if (($this->kVaterArtikel > 0 || $this->nIstVater === 1) && \count($this->VariationenOhneFreifeld) > 1) {
            foreach ($this->VariationenOhneFreifeld as $oVariationenOhneFreifeld) {
                foreach ($oVariationenOhneFreifeld->Werte as $oVariationsWert) {
                    $oVariationsWert->cName = \str_replace(
                        $outOfStock,
                        '',
                        $oVariationsWert->cName
                    );
                }
            }
        }
        // Variationskombination (Vater)
        if ($this->nIstVater === 1) {
            // Gibt es nur 1 Variation?
            if (\count($this->VariationenOhneFreifeld) === 1) {
                // Baue Warenkorbmatrix Bildvorschau
                $variBoxMatrixImages = $this->getDB()->getObjects(
                    'SELECT tartikelpict.cPfad, tartikel.cName, tartikel.cSeo, tartikel.cArtNr,
                        tartikel.cBarcode, tartikel.kArtikel, teigenschaftkombiwert.kEigenschaft,
                        teigenschaftkombiwert.kEigenschaftWert
                        FROM teigenschaftkombiwert
                        JOIN tartikel
                            ON tartikel.kVaterArtikel = :kArtikel
                            AND tartikel.kEigenschaftKombi = teigenschaftkombiwert.kEigenschaftKombi
                        LEFT JOIN tartikelsichtbarkeit
                            ON tartikel.kArtikel = tartikelsichtbarkeit.kArtikel
                            AND tartikelsichtbarkeit.kKundengruppe = :kKundengruppe
                        LEFT JOIN teigenschaftwertsichtbarkeit
                            ON teigenschaftkombiwert.kEigenschaftWert = teigenschaftwertsichtbarkeit.kEigenschaftWert
                            AND teigenschaftwertsichtbarkeit.kKundengruppe = :kKundengruppe
                        JOIN tartikelpict
                            ON tartikelpict.kArtikel = tartikel.kArtikel
                            AND tartikelpict.nNr = 1
                        WHERE tartikelsichtbarkeit.kArtikel IS NULL
                            AND teigenschaftwertsichtbarkeit.kKundengruppe IS NULL',
                    [
                        'kArtikel'      => $this->kArtikel,
                        'kKundengruppe' => $customerGroupID,
                    ]
                );
                foreach ($variBoxMatrixImages as $image) {
                    $req          = Product::getRequest(
                        Image::TYPE_PRODUCT,
                        $image->kArtikel,
                        $image,
                        Image::SIZE_XS,
                        0
                    );
                    $image->cBild = $req->getThumbUrl(Image::SIZE_XS);
                }
                $variBoxMatrixImages = \array_merge($variBoxMatrixImages);

                $this->oVariBoxMatrixBild_arr = $variBoxMatrixImages;
            } elseif (\count($this->VariationenOhneFreifeld) === 2) {
                // Gibt es 2 Variationen?
                // Baue Warenkorbmatrix Bildvorschau
                $this->oVariBoxMatrixBild_arr = [];

                $matrixImages = [];
                $matrixImgRes = $this->getDB()->getObjects(
                    'SELECT tartikelpict.cPfad, teigenschaftkombiwert.kEigenschaft,
                            teigenschaftkombiwert.kEigenschaftWert
                        FROM teigenschaftkombiwert
                        JOIN tartikel
                            ON tartikel.kVaterArtikel = :kArtikel
                            AND tartikel.kEigenschaftKombi = teigenschaftkombiwert.kEigenschaftKombi
                        LEFT JOIN tartikelsichtbarkeit
                            ON tartikel.kArtikel = tartikelsichtbarkeit.kArtikel
                            AND tartikelsichtbarkeit.kKundengruppe = :kKundengruppe
                        LEFT JOIN teigenschaftwertsichtbarkeit
                            ON teigenschaftkombiwert.kEigenschaftWert = teigenschaftwertsichtbarkeit.kEigenschaftWert
                            AND teigenschaftwertsichtbarkeit.kKundengruppe = :kKundengruppe
                        JOIN tartikelpict
                            ON tartikelpict.kArtikel = tartikel.kArtikel
                            AND tartikelpict.nNr = 1
                        WHERE tartikelsichtbarkeit.kArtikel IS NULL
                            AND teigenschaftwertsichtbarkeit.kKundengruppe IS NULL
                        ORDER BY teigenschaftkombiwert.kEigenschaft, teigenschaftkombiwert.kEigenschaftWert',
                    [
                        'kArtikel'      => $this->kArtikel,
                        'kKundengruppe' => $customerGroupID,
                    ]
                );
                foreach ($matrixImgRes as $matrixImage) {
                    $matrixImage->kEigenschaftWert = (int)$matrixImage->kEigenschaftWert;
                    if (!isset($matrixImages[$matrixImage->kEigenschaftWert])) {
                        $matrixImages[$matrixImage->kEigenschaftWert]               = new stdClass();
                        $matrixImages[$matrixImage->kEigenschaftWert]->cPfad        = $matrixImage->cPfad;
                        $matrixImages[$matrixImage->kEigenschaftWert]->kEigenschaft = $matrixImage->kEigenschaft;
                    }
                }
                // Prüfe ob Bilder Horizontal gesetzt werden
                $vertical   = [];
                $horizontal = [];
                $valid      = true;
                if (\is_array($this->VariationenOhneFreifeld[0]->Werte)) {
                    // Laufe Variation 1 durch
                    foreach ($this->VariationenOhneFreifeld[0]->Werte as $i => $varVal) {
                        $imageHashes = [];
                        if (
                            \is_array($this->VariationenOhneFreifeld[1]->Werte)
                            && \count($this->VariationenOhneFreifeld[1]->Werte) > 0
                        ) {
                            $vertical[$i] = new stdClass();
                            if (isset($matrixImages[$varVal->kEigenschaftWert]->cPfad)) {
                                $req                 = MediaImageRequest::create([
                                    'type' => 'product',
                                    'id'   => $this->kArtikel,
                                    'path' => $matrixImages[$varVal->kEigenschaftWert]->cPfad
                                ]);
                                $vertical[$i]->cBild = $req->getThumbUrl('xs');
                            } else {
                                $vertical[$i]->cBild = '';
                            }
                            $vertical[$i]->kEigenschaftWert = $varVal->kEigenschaftWert;
                            $vertical[$i]->nRichtung        = 0; // Vertikal
                            // Laufe Variationswerte von Variation 2 durch
                            foreach ($this->VariationenOhneFreifeld[1]->Werte as $oVariationWert1) {
                                if (!empty($matrixImages[$oVariationWert1->kEigenschaftWert]->cPfad)) {
                                    $req   = MediaImageRequest::create([
                                        'type' => 'product',
                                        'id'   => $this->kArtikel,
                                        'path' => $matrixImages[$oVariationWert1->kEigenschaftWert]->cPfad
                                    ]);
                                    $thumb = \PFAD_ROOT . $req->getThumb('xs');
                                    if (\file_exists($thumb)) {
                                        $fileHash = \md5_file($thumb);
                                        if (!\in_array($fileHash, $imageHashes, true)) {
                                            $imageHashes[] = $fileHash;
                                        }
                                    }
                                } else {
                                    $valid = false;
                                    break;
                                }
                            }
                        }
                        // Prüfe ob Dateigröße gleich ist
                        $valid = $valid && \count($imageHashes) === 1;
                    }
                    if ($valid) {
                        $this->oVariBoxMatrixBild_arr = $vertical;
                    }
                    // Prüfe ob Bilder Vertikal gesetzt werden
                    if (\count($this->oVariBoxMatrixBild_arr) === 0) {
                        $valid = true;
                        if (\is_array($this->VariationenOhneFreifeld[1]->Werte)) {
                            // Laufe Variationswerte von Variation 2 durch
                            foreach ($this->VariationenOhneFreifeld[1]->Werte as $i => $oVariationWert1) {
                                $imageHashes = [];
                                if (
                                    \is_array($this->VariationenOhneFreifeld[0]->Werte)
                                    && \count($this->VariationenOhneFreifeld[0]->Werte) > 0
                                ) {
                                    $req = MediaImageRequest::create([
                                        'type' => 'product',
                                        'id'   => $this->kArtikel,
                                        'path' => $matrixImages[$oVariationWert1->kEigenschaftWert]->cPfad ?? null
                                    ]);

                                    $horizontal                       = [];
                                    $horizontal[$i]                   = new stdClass();
                                    $horizontal[$i]->cBild            = $req->getThumbUrl('xs');
                                    $horizontal[$i]->kEigenschaftWert = $oVariationWert1->kEigenschaftWert;
                                    $horizontal[$i]->nRichtung        = 1; // Horizontal
                                    // Laufe Variation 1 durch
                                    foreach ($this->VariationenOhneFreifeld[0]->Werte as $varVal) {
                                        if (!empty($matrixImages[$varVal->kEigenschaftWert]->cPfad)) {
                                            $req   = MediaImageRequest::create([
                                                'type' => 'product',
                                                'id'   => $this->kArtikel,
                                                'path' => $matrixImages[$varVal->kEigenschaftWert]->cPfad
                                            ]);
                                            $thumb = \PFAD_ROOT . $req->getThumb('xs');
                                            if (\file_exists($thumb)) {
                                                $fileHash = \md5_file(\PFAD_ROOT . $req->getThumb('xs'));
                                                if (!\in_array($fileHash, $imageHashes, true)) {
                                                    $imageHashes[] = $fileHash;
                                                }
                                            }
                                        } else {
                                            $valid = false;
                                            break;
                                        }
                                    }
                                }
                                // Prüfe ob Dateigröße gleich ist
                                $valid = $valid && \count($imageHashes) === 1;
                            }
                            if ($valid) {
                                $this->oVariBoxMatrixBild_arr = $horizontal;
                            }
                        }
                    }
                }
            }
        } elseif ($this->kVaterArtikel === 0) { // Keine Variationskombination
            $variBoxMatrixImages = [];
            if (\count($this->VariationenOhneFreifeld) === 1) {
                // Baue Warenkorbmatrix Bildvorschau
                $variBoxMatrixImages = $this->getDB()->getObjects(
                    'SELECT teigenschaftwertpict.cPfad, teigenschaft.kEigenschaft, teigenschaftwertpict.kEigenschaftWert
                        FROM teigenschaft
                        JOIN teigenschaftwert
                            ON teigenschaftwert.kEigenschaft = teigenschaft.kEigenschaft
                        JOIN teigenschaftwertpict
                            ON teigenschaftwertpict.kEigenschaftWert = teigenschaftwert.kEigenschaftWert
                        LEFT JOIN teigenschaftsichtbarkeit
                            ON teigenschaft.kEigenschaft = teigenschaftsichtbarkeit.kEigenschaft
                            AND teigenschaftsichtbarkeit.kKundengruppe = :cgid
                        LEFT JOIN teigenschaftwertsichtbarkeit
                            ON teigenschaftwert.kEigenschaftWert = teigenschaftwertsichtbarkeit.kEigenschaftWert
                            AND teigenschaftwertsichtbarkeit.kKundengruppe = :cgid
                        WHERE teigenschaft.kArtikel = :pid
                            AND teigenschaftsichtbarkeit.kEigenschaft IS NULL
                            AND teigenschaftwertsichtbarkeit.kEigenschaftWert IS NULL
                        ORDER BY teigenschaft.nSort, teigenschaft.cName,
                            teigenschaftwert.nSort, teigenschaftwert.cName',
                    ['pid' => $this->kArtikel, 'cgid' => $customerGroupID]
                );
            } elseif (\count($this->VariationenOhneFreifeld) === 2) {
                // Baue Warenkorbmatrix Bildvorschau
                $variBoxMatrixImages = $this->getDB()->getObjects(
                    'SELECT teigenschaftwertpict.cPfad, teigenschaft.kEigenschaft, teigenschaftwertpict.kEigenschaftWert
                        FROM teigenschaft
                        JOIN teigenschaftwert
                            ON teigenschaftwert.kEigenschaft = teigenschaft.kEigenschaft
                        JOIN teigenschaftwertpict
                            ON teigenschaftwertpict.kEigenschaftWert = teigenschaftwert.kEigenschaftWert
                        LEFT JOIN teigenschaftsichtbarkeit
                            ON teigenschaft.kEigenschaft = teigenschaftsichtbarkeit.kEigenschaft
                            AND teigenschaftsichtbarkeit.kKundengruppe = :cgid
                        LEFT JOIN teigenschaftwertsichtbarkeit
                            ON teigenschaftwert.kEigenschaftWert = teigenschaftwertsichtbarkeit.kEigenschaftWert
                            AND teigenschaftwertsichtbarkeit.kKundengruppe = :cgid
                        WHERE teigenschaft.kArtikel = :pid
                            AND teigenschaftsichtbarkeit.kEigenschaft IS NULL
                            AND teigenschaftwertsichtbarkeit.kEigenschaftWert IS NULL
                        ORDER BY teigenschaft.nSort, teigenschaft.cName,
                                 teigenschaftwert.nSort, teigenschaftwert.cName',
                    ['pid' => $this->kArtikel, 'cgid' => $customerGroupID]
                );
            }
            $error = false;
            if (\count($variBoxMatrixImages) > 0) {
                $attributeIDs = [];
                // Gleiche Farben entfernen + komplette Vorschau nicht anzeigen
                foreach ($variBoxMatrixImages as $image) {
                    $image->kEigenschaft     = (int)$image->kEigenschaft;
                    $image->kEigenschaftWert = (int)$image->kEigenschaftWert;
                    $variThumb               = VariationImage::getThumb(
                        Image::TYPE_VARIATION,
                        $image->kEigenschaftWert,
                        $image,
                        Image::SIZE_SM
                    );
                    $image->cPfad            = \basename($variThumb);
                    $image->cBild            = $imageBaseURL . $variThumb;
                    if (!\in_array($image->kEigenschaft, $attributeIDs, true) && \count($attributeIDs) > 0) {
                        $error = true;
                        break;
                    }
                    $attributeIDs[] = $image->kEigenschaft;
                }
                $variBoxMatrixImages = \array_merge($variBoxMatrixImages);
            }
            $this->oVariBoxMatrixBild_arr = $error ? [] : $variBoxMatrixImages;
        }

        return $this;
    }

    /**
     * Hole für einen kVaterArtikel alle Kinderobjekte und baue ein Assoc in der Form
     * [$kEigenschaft0:$kEigenschaftWert0_$kEigenschaft1:$kEigenschaftWert1]
     *
     * @param int $customerGroupID
     * @return array
     */
    public function holeVariationKombiKinderAssoc(int $customerGroupID): array
    {
        $varCombChildren = [];
        if (!($customerGroupID > 0 && $this->kSprache > 0 && $this->nIstVater)) {
            return [];
        }
        $childProperties = $this->getDB()->getObjects(
            'SELECT tartikel.kArtikel, teigenschaft.kEigenschaft, teigenschaftwert.kEigenschaftWert
                FROM tartikel
                JOIN teigenschaftkombiwert
                    ON tartikel.kEigenschaftKombi = teigenschaftkombiwert.kEigenschaftKombi
                JOIN teigenschaft
                    ON teigenschaft.kEigenschaft = teigenschaftkombiwert.kEigenschaft
                JOIN teigenschaftwert
                    ON teigenschaftwert.kEigenschaftWert = teigenschaftkombiwert.kEigenschaftWert
                LEFT JOIN tartikelsichtbarkeit
                    ON tartikel.kArtikel = tartikelsichtbarkeit.kArtikel
                    AND tartikelsichtbarkeit.kKundengruppe = :cgid
                WHERE tartikel.kVaterArtikel = :pid
                AND tartikelsichtbarkeit.kArtikel IS NULL
                ORDER BY tartikel.kArtikel ASC, teigenschaft.nSort ASC,
                         teigenschaft.cName, teigenschaftwert.nSort ASC, teigenschaftwert.cName',
            ['cgid' => $customerGroupID, 'pid' => $this->kArtikel]
        );
        if (\count($childProperties) === 0) {
            return [];
        }
        // generate identifiers, build new assoc-arr
        $identifier  = '';
        $lastProduct = 0;
        foreach ($childProperties as $varkombi) {
            $varkombi->kArtikel         = (int)$varkombi->kArtikel;
            $varkombi->kEigenschaft     = (int)$varkombi->kEigenschaft;
            $varkombi->kEigenschaftWert = (int)$varkombi->kEigenschaftWert;
            if ($lastProduct > 0 && $varkombi->kArtikel === $lastProduct) {
                $identifier .= '_' . $varkombi->kEigenschaft . ':' . $varkombi->kEigenschaftWert;
            } else {
                if ($lastProduct > 0) {
                    $varCombChildren[$identifier] = $lastProduct;
                }
                $identifier = $varkombi->kEigenschaft . ':' . $varkombi->kEigenschaftWert;
            }
            $lastProduct = $varkombi->kArtikel;
        }
        $varCombChildren[$identifier] = $lastProduct; //last item

        // Preise holen bzw. Artikel
        if (($cnt = \count($varCombChildren)) > 0 && $cnt <= \ART_MATRIX_MAX) {
            $tmp                                = [];
            $per                                = ' ' . Shop::Lang()->get('vpePer') . ' ';
            $taxRate                            = $_SESSION['Steuersatz'][$this->kSteuerklasse];
            $options                            = self::getDefaultOptions();
            $options->nKeinLagerbestandBeachten = 1;
            foreach ($varCombChildren as $i => $productID) {
                if (isset($tmp[$productID])) {
                    $varCombChildren[$i] = $tmp[$productID];
                } else {
                    $product = new self($this->getDB(), $this->getCustomerGroup(), $this->currency, $this->getCache());
                    $product->fuelleArtikel($productID, $options, $customerGroupID, $this->kSprache);
                    $tmp[$productID]     = $product;
                    $varCombChildren[$i] = $product;
                }
                // GrundPreis nicht vom Vater => Ticket #1228
                if ($varCombChildren[$i]->fVPEWert > 0) {
                    $precision = isset($varCombChildren[$i]->FunktionsAttribute[\FKT_ATTRIBUT_GRUNDPREISGENAUIGKEIT])
                    && (int)$varCombChildren[$i]->FunktionsAttribute[\FKT_ATTRIBUT_GRUNDPREISGENAUIGKEIT] > 0
                        ? (int)$varCombChildren[$i]->FunktionsAttribute[\FKT_ATTRIBUT_GRUNDPREISGENAUIGKEIT]
                        : 2;

                    $varCombChildren[$i]->Preise->cPreisVPEWertInklAufpreis[0] = Preise::getLocalizedPriceString(
                        Tax::getGross(
                            $varCombChildren[$i]->Preise->fVKNetto / $varCombChildren[$i]->fVPEWert,
                            $taxRate
                        ),
                        $this->currency,
                        true,
                        $precision
                    ) . $per . $varCombChildren[$i]->cVPEEinheit;
                    $varCombChildren[$i]->Preise->cPreisVPEWertInklAufpreis[1] = Preise::getLocalizedPriceString(
                        $varCombChildren[$i]->Preise->fVKNetto / $varCombChildren[$i]->fVPEWert,
                        $this->currency,
                        true,
                        $precision
                    ) . $per . $varCombChildren[$i]->cVPEEinheit;
                }
                // Lieferbar?
                if (
                    $varCombChildren[$i]->cLagerBeachten === 'Y'
                    && ($varCombChildren[$i]->cLagerKleinerNull === 'N'
                        || (int)$this->getConfigValue('global', 'artikel_artikelanzeigefilter') ===
                        \EINSTELLUNGEN_ARTIKELANZEIGEFILTER_LAGER
                    )
                    && $varCombChildren[$i]->fLagerbestand <= 0
                ) {
                    $varCombChildren[$i]->nNichtLieferbar = 1;
                }
            }
            $this->sortVarCombinationArray($varCombChildren);
        }

        return $varCombChildren;
    }

    /**
     * @param Artikel[] $array
     */
    public function sortVarCombinationArray(array &$array): void
    {
        \uasort($array, static function (Artikel $a, Artikel $b): int {
            foreach (['nSort', 'cName'] as $sortBy) {
                $aProp = (string)($a->$sortBy ?? null);
                $bProp = (string)($b->$sortBy ?? null);
                if ($aProp !== $bProp) {
                    return \strnatcasecmp($aProp, $bProp);
                }
            }

            return 0;
        });
    }

    /**
     * Holt den Endpreis für die Variationen eines Variationskind
     *
     * @return $this
     */
    private function holeVariationDetailPreisKind(): self
    {
        $this->oVariationDetailPreisKind_arr = [];

        $per       = ' ' . Shop::Lang()->get('vpePer') . ' ' . $this->cVPEEinheit;
        $taxRate   = $_SESSION['Steuersatz'][$this->kSteuerklasse];
        $precision = $this->getPrecision();
        foreach ($this->oVariationKombi_arr as $vk) {
            $this->oVariationDetailPreisKind_arr[$vk->kEigenschaftWert]         = new stdClass();
            $this->oVariationDetailPreisKind_arr[$vk->kEigenschaftWert]->Preise = $this->Preise;
            // Grundpreis?
            if ($this->cVPE !== 'Y' || $this->fVPEWert <= 0) {
                continue;
            }
            $this->oVariationDetailPreisKind_arr[$vk->kEigenschaftWert]->Preise->PreisecPreisVPEWertInklAufpreis[0] =
                Preise::getLocalizedPriceString(
                    Tax::getGross($this->Preise->fVKNetto / $this->fVPEWert, $taxRate),
                    $this->currency,
                    true,
                    $precision
                ) . $per;
            $this->oVariationDetailPreisKind_arr[$vk->kEigenschaftWert]->Preise->PreisecPreisVPEWertInklAufpreis[1] =
                Preise::getLocalizedPriceString(
                    $this->Preise->fVKNetto / $this->fVPEWert,
                    $this->currency,
                    true,
                    $precision
                ) . $per;
        }

        return $this;
    }

    /**
     * Holt die Endpreise für VariationsKinder
     * Wichtig fuer die Anzeige von Aufpreisen
     *
     * @param int $customerGroupID
     * @param int $customerID - always keep at 0 when saving the result to cache
     * @return $this
     */
    private function getVariationDetailPrice(int $customerGroupID, int $customerID = 0): self
    {
        $this->oVariationDetailPreis_arr = [];
        if ($this->nVariationOhneFreifeldAnzahl !== 1) {
            return $this;
        }
        $varDetailPrices = $this->getDB()->getObjects(
            'SELECT tartikel.kArtikel, teigenschaftkombiwert.kEigenschaft, teigenschaftkombiwert.kEigenschaftWert
                FROM teigenschaftkombiwert
                JOIN tartikel
                    ON tartikel.kVaterArtikel = :pid
                    AND tartikel.kEigenschaftKombi = teigenschaftkombiwert.kEigenschaftKombi
                LEFT JOIN tartikelsichtbarkeit
                    ON tartikel.kArtikel = tartikelsichtbarkeit.kArtikel
                    AND tartikelsichtbarkeit.kKundengruppe = :cgid
                ' . Preise::getPriceJoinSql($customerGroupID) . '
                WHERE tartikelsichtbarkeit.kArtikel IS NULL',
            ['pid' => $this->kArtikel, 'cgid' => $customerGroupID]
        );
        if ($this->nIstVater === 1) {
            $this->cVaterVKLocalized = $this->Preise->cVKLocalized;
        }
        $lastProduct = 0;
        $tmpProduct  = null;
        $per         = ' ' . Shop::Lang()->get('vpePer') . ' ';
        $taxRate     = $_SESSION['Steuersatz'][$this->kSteuerklasse];
        $precision   = $this->getPrecision();
        $prodVkNetto = $this->gibPreis(1, [], $customerGroupID, '', false);
        foreach ($varDetailPrices as $varDetailPrice) {
            $varDetailPrice->kArtikel         = (int)$varDetailPrice->kArtikel;
            $varDetailPrice->kEigenschaft     = (int)$varDetailPrice->kEigenschaft;
            $varDetailPrice->kEigenschaftWert = (int)$varDetailPrice->kEigenschaftWert;

            $idx = $varDetailPrice->kEigenschaftWert;
            if ($varDetailPrice->kArtikel !== $lastProduct || $tmpProduct === null) {
                $lastProduct = $varDetailPrice->kArtikel;
                $tmpProduct  = new self($this->getDB(), $this->getCustomerGroup(), $this->currency, $this->getCache());
                $tmpProduct->getPriceData($varDetailPrice->kArtikel, $customerGroupID, $customerID);
            }

            $varVKNetto             = $tmpProduct->gibPreis(1, [], $customerGroupID, '', false);
            $variationPrice         = $this->oVariationDetailPreis_arr[$idx] ?? new stdClass();
            $variationPrice->Preise = clone $tmpProduct->Preise;
            // Variationsaufpreise - wird benötigt wenn Einstellung 119 auf (Aufpreise / Rabatt anzeigen) steht
            $prefix = '';
            if ($varVKNetto > $prodVkNetto) {
                $prefix = '+ ';
            } elseif ($varVKNetto < $prodVkNetto) {
                $prefix = '- ';
            }
            $discount = $this->Preise->isDiscountable() ? $this->getDiscount($customerGroupID) : 0;
            $variationPrice->Preise->rabbatierePreise($discount)->localizePreise($this->currency);

            if ($varVKNetto !== $prodVkNetto) {
                $variationPrice->Preise->cAufpreisLocalized[0] = $prefix
                    . Preise::getLocalizedPriceString(
                        \abs(Tax::getGross($varVKNetto, $taxRate) - Tax::getGross($prodVkNetto, $taxRate)),
                        $this->currency
                    );
                $variationPrice->Preise->cAufpreisLocalized[1] = $prefix
                    . Preise::getLocalizedPriceString(
                        \abs($varVKNetto - $prodVkNetto),
                        $this->currency
                    );
            }

            // Grundpreis?
            if (!empty($tmpProduct->cVPE) && $tmpProduct->cVPE === 'Y' && $tmpProduct->fVPEWert > 0) {
                $variationPrice->Preise->PreisecPreisVPEWertInklAufpreis[0] =
                    Preise::getLocalizedPriceString(
                        Tax::getGross($varVKNetto / $tmpProduct->fVPEWert, $taxRate),
                        $this->currency,
                        true,
                        $precision
                    ) . $per . $tmpProduct->cVPEEinheit;
                $variationPrice->Preise->PreisecPreisVPEWertInklAufpreis[1] =
                    Preise::getLocalizedPriceString(
                        $varVKNetto / $tmpProduct->fVPEWert,
                        $this->currency,
                        true,
                        $precision
                    ) . $per . $tmpProduct->cVPEEinheit;
            }

            $this->oVariationDetailPreis_arr[$idx] = $variationPrice;
        }

        return $this;
    }

    /**
     * @param int $productID
     * @return SqlObject
     */
    private function getLocalizationSQL(int $productID): SqlObject
    {
        $lang = new SqlObject();
        if ($this->kSprache > 0 && !LanguageHelper::isDefaultLanguageActive(false, $this->kSprache)) {
            $lang->setSelect(
                'tartikelsprache.cName AS cName_spr, tartikelsprache.cBeschreibung AS cBeschreibung_spr,
                 tartikelsprache.cKurzBeschreibung AS cKurzBeschreibung_spr, '
            );
            $lang->setJoin(
                ' LEFT JOIN tartikelsprache
                      ON tartikelsprache.kArtikel = :pid
                      AND tartikelsprache.kSprache = :lid'
            );
            $lang->addParam(':pid', $productID);
            $lang->addParam(':lid', $this->kSprache);
        }

        return $lang;
    }

    /**
     * @return $this
     * @former baueArtikelSprachURL()
     */
    private function buildURLs(): self
    {
        $slugs = $this->getDB()->getObjects(
            'SELECT cSeo, kSprache
                FROM tseo
                WHERE cKey = :key
                    AND kKey = :id',
            ['key' => 'kArtikel', 'id' => $this->kArtikel]
        );
        foreach ($slugs as $slug) {
            $this->setSlug($slug->cSeo, (int)$slug->kSprache);
        }
        $this->createBySlug($this->kArtikel);
        $this->cURL     = \ltrim($this->getURLPath($this->kSprache), '/');
        $this->cURLFull = $this->getURL($this->kSprache);
        foreach (Frontend::getLanguages() as $language) {
            $code = $language->getCode();
            //$this->cSprachURL_arr[$code] = '?a=' . $this->kArtikel . '&amp;lang=' . $code;
            foreach ($this->getURLs() as $langID => $url) {
                if ($language->getId() === $langID) {
                    $this->cSprachURL_arr[$code] = $url;
                    break;
                }
            }
        }

        return $this;
    }

    /**
     * @return string[]
     */
    private static function getAllOptions(): array
    {
        return [
            'nMerkmale',
            'nAttribute',
            'nArtikelAttribute',
            'nMedienDatei',
            'nVariationDetailPreis',
            'nWarenkorbmatrix',
            'nStueckliste',
            'nProductBundle',
            'nKeinLagerbestandBeachten',
            'nKeineSichtbarkeitBeachten',
            'nDownload',
            'nKategorie',
            'nKonfig',
            'nMain',
            'nWarenlager',
            'bSimilar',
            'nRatings',
            'nVariationen',
        ];
    }

    /**
     * create a bitmask that is indepentend from the order of submitted options to generate cacheID
     * without this there could potentially be redundant cache entries with the same content
     *
     * @param stdClass|null $options
     * @return string
     */
    private function getOptionsHash(?stdClass $options): string
    {
        $options = $options ?? self::getDefaultOptions();
        $given   = \get_object_vars($options);
        $mask    = '';
        if (isset($options->nDownload) && $options->nDownload === 1 && !Download::checkLicense()) {
            // unset download-option if there is no license for the download module
            $options->nDownload = 0;
        }
        foreach (self::getAllOptions() as $_opt) {
            $mask .= empty($given[$_opt]) ? 0 : 1;
        }

        return $mask;
    }

    /**
     * @return stdClass
     */
    public static function getDetailOptions(): stdClass
    {
        $conf                           = Shop::getSettingSection(\CONF_ARTIKELDETAILS);
        $options                        = new stdClass();
        $options->nMerkmale             = 1;
        $options->nKategorie            = 1;
        $options->nAttribute            = 1;
        $options->nArtikelAttribute     = 1;
        $options->nMedienDatei          = 1;
        $options->nVariationen          = 1;
        $options->nWarenlager           = 1;
        $options->nVariationDetailPreis = 1;
        $options->nRatings              = 1;
        $options->nWarenkorbmatrix      = (int)($conf['artikeldetails_warenkorbmatrix_anzeige'] === 'Y');
        $options->nStueckliste          = (int)($conf['artikeldetails_stueckliste_anzeigen'] === 'Y');
        $options->nProductBundle        = (int)($conf['artikeldetails_produktbundle_nutzen'] === 'Y');
        $options->nDownload             = 1;
        $options->nKonfig               = 1;
        $options->nMain                 = 1;
        $options->bSimilar              = true;

        return $options;
    }

    /**
     * @return stdClass
     */
    public static function getDefaultOptions(): stdClass
    {
        $options                    = new stdClass();
        $options->nMerkmale         = 1;
        $options->nAttribute        = 1;
        $options->nArtikelAttribute = 1;
        $options->nKonfig           = 1;
        $options->nDownload         = 1;
        $options->nVariationen      = 0;

        return $options;
    }

    /**
     * @return stdClass
     */
    public static function getDefaultConfigOptions(): stdClass
    {
        $options                             = static::getDefaultOptions();
        $options->nKeineSichtbarkeitBeachten = 1;

        return $options;
    }

    /**
     * @return stdClass
     */
    public static function getExportOptions(): stdClass
    {
        $options                            = new stdClass();
        $options->nMerkmale                 = 1;
        $options->nAttribute                = 1;
        $options->nArtikelAttribute         = 1;
        $options->nKategorie                = 1;
        $options->nKeinLagerbestandBeachten = 1;
        $options->nMedienDatei              = 1;
        $options->nVariationen              = 1;

        return $options;
    }

    /**
     * @param int           $productID
     * @param stdClass|null $options @see Artikel::getAllOptions()
     * @param int           $customerGroupID
     * @param int           $langID
     * @param bool          $noCache
     * @return null|$this
     * @throws \Exception
     */
    public function fuelleArtikel(
        int $productID,
        ?stdClass $options = null,
        int $customerGroupID = 0,
        int $langID = 0,
        bool $noCache = false
    ): ?self {
        if (!$productID) {
            return null;
        }
        $options         = $options ?? self::getDefaultOptions();
        $customerGroupID = $customerGroupID ?: $this->getCustomerGroup()->getID();
        if ($this->getCustomerGroup()->getID() !== $customerGroupID) {
            $this->setCustomerGroup(CustomerGroup::reset($customerGroupID));
        }
        $langID = $langID ?: Shop::getLanguageID();
        if (!$langID) {
            $langID = LanguageHelper::getDefaultLanguage()->getId();
        }
        $this->kKundengruppe = $customerGroupID;
        $this->kSprache      = $langID;
        $this->options       = (object)\array_merge((array)$this->options, (array)$options);
        if ($noCache === false) {
            $product = $this->loadFromCache($productID, $customerGroupID);
            if ($product === null || $product instanceof self) {
                return $product;
            }
        }
        $this->cCachedCountryCode = $_SESSION['cLieferlandISO'] ?? null;

        $productSQL = $this->getProductSQL($productID, $customerGroupID);
        $tmpProduct = $this->getDB()->getSingleObject($productSQL->getStatement(), $productSQL->getParams());
        $test       = $this->retryWithoutStockFilter($tmpProduct, $productID, $customerGroupID, $noCache);
        if ($test !== false) {
            return $test;
        }
        if ($tmpProduct === null || $tmpProduct->kArtikel === $tmpProduct->kVaterArtikel) {
            $cacheTags = [\CACHING_GROUP_ARTICLE . '_' . $productID, \CACHING_GROUP_ARTICLE];
            \executeHook(\HOOK_ARTIKEL_CLASS_FUELLEARTIKEL, [
                'oArtikel'  => &$this,
                'cacheTags' => &$cacheTags,
                'cached'    => false
            ]);
            if ($noCache === false) {
                $this->getCache()->set($this->cacheID, null, $cacheTags);
            }
            if ($tmpProduct !== null && $tmpProduct->kArtikel === $tmpProduct->kVaterArtikel) {
                Shop::Container()->getLogService()->warning(
                    'Product {pid} has invalid parent.',
                    ['pid' => (int)$tmpProduct->kArtikel]
                );
            }
            return null;
        }
        // EXPERIMENTAL_MULTILANG_SHOP
        if ($tmpProduct->cSeo === null && \EXPERIMENTAL_MULTILANG_SHOP === true) {
            // redo the query with modified seo join - without language ID
            $statement  = \str_replace(
                $this->getSeoSQL()->getJoin(),
                'LEFT JOIN tseo ON tseo.cKey = \'kArtikel\' AND tseo.kKey = tartikel.kArtikel',
                $productSQL->getStatement()
            );
            $tmpProduct = $this->getDB()->getSingleObject($statement, $productSQL->getParams());
        }
        // EXPERIMENTAL_MULTILANG_SHOP END
        if (!isset($tmpProduct->kArtikel)) {
            return $this;
        }
        $this->sanitizeProductData($tmpProduct);
        $this->addManufacturerData();
        if ($this->getOption('bSimilar', false) === true) {
            $this->similarProducts = $this->getSimilarProducts();
        }
        // Datumsrelevante Abhängigkeiten beachten
        $this->checkDateDependencies();
        //wenn ja fMaxRabatt setzen
        // fMaxRabatt = 0, wenn Sonderpreis aktiv
        if ($this->cAktivSonderpreis !== 'Y' && (double)$this->fNettoPreis >= 0) {
            $tmpProduct->cAktivSonderpreis = null;
            $tmpProduct->dStart_en         = null;
            $tmpProduct->dStart_de         = null;
            $tmpProduct->dEnde_en          = null;
            $tmpProduct->dEnde_de          = null;
            $tmpProduct->fNettoPreis       = null;
        }
        $this->holPreise($customerGroupID, $tmpProduct);
        $this->setCategoryDiscounts($tmpProduct, Frontend::getCustomer());
        $this->initLanguageID($this->kSprache, Shop::Lang()->getIsoFromLangID($this->kSprache)->cISO ?? null);
        if ($this->getOption('nArtikelAttribute', 0) === 1) {
            $this->holArtikelAttribute();
        }
        $this->inWarenkorbLegbar = 1;
        if ($this->getOption('nAttribute', 0) === 1) {
            $this->holAttribute();
        }
        $this->holBilder();
        if ($this->getOption('nWarenlager', 0) === 1) {
            $this->getWarehouse();
        }
        if ($this->getOption('nMerkmale', 0) === 1) {
            $this->holeMerkmale();
        }
        if ($this->getOption('nMedienDatei', 0) === 1) {
            $this->getMediaFiles();
        }
        if (
            $this->getOption('nStueckliste', 0) === 1
            || $this->getFunctionalAttributevalue(\FKT_ATTRIBUT_STUECKLISTENKOMPONENTEN, true) === 1
        ) {
            $this->holeStueckliste($customerGroupID);
        }
        if ($this->getOption('nProductBundle', 0) === 1) {
            $this->getProductBundle();
        }
        // Kategorie
        if ($this->getOption('nKategorie', 0) === 1) {
            $productID            = $this->kVaterArtikel > 0 ? $this->kVaterArtikel : $this->kArtikel;
            $this->oKategorie_arr = $this->getCategories($productID, $customerGroupID);
        }
        if ($this->getOption('nVariationen', 0) === 1) {
            $this->holVariationen(
                $customerGroupID,
                $noCache === true || (array)$options === (array)self::getExportOptions()
            );
        }
        $this->checkVariationExtraCharge();
        if ($this->nIstVater === 1 && $this->getOption('nVariationDetailPreis', 0) === 1) {
            $this->getVariationDetailPrice($customerGroupID);
        }
        $this->addVariationChildren($customerGroupID);
        $this->cMwstVersandText = $this->gibMwStVersandString($this->getCustomerGroup()->isMerchant());
        if ($this->getOption('nDownload', 0) === 1) {
            $this->oDownload_arr = Download::getDownloads(['kArtikel' => $this->kArtikel], $langID);
        }
        $this->bHasKonfig = Configurator::hasKonfig($this->kArtikel);
        if ($this->bHasKonfig && $this->getOption('nKonfig', 0) === 1) {
            if (Configurator::validateKonfig($this->kArtikel)) {
                $this->oKonfig_arr = Configurator::getKonfig($this->kArtikel, $langID);
            } else {
                Shop::Container()->getLogService()->error(
                    'Konfigurator für Artikel (Art.Nr.: {artno}) konnte nicht geladen werden.',
                    ['artno' => $this->cArtNr]
                );
            }
        }
        $this->checkCanBePurchased();
        $this->getStockDisplay();
        $this->cUVPLocalized = Preise::getLocalizedPriceString($this->fUVP);
        // Lieferzeit abhaengig vom Session-Lieferland aktualisieren
        if ($this->inWarenkorbLegbar >= 1 && $this->nIstVater !== 1) {
            $this->cEstimatedDelivery = $this->getDeliveryTime($_SESSION['cLieferlandISO']);
        }
        $this->getSearchSpecialOverlay();
        $this->isSimpleVariation = false;
        if (\count($this->Variationen) > 0) {
            $this->isSimpleVariation = $this->kVaterArtikel === 0 && $this->nIstVater === 0;
        }
        $this->metaKeywords    = $this->getMetaKeywords();
        $this->metaTitle       = $this->getMetaTitle();
        $this->metaDescription = $this->setMetaDescription();
        $this->taxData         = $this->getShippingAndTaxData();
        if ($this->getConfigValue('bewertung', 'bewertung_anzeigen') === 'Y' && $this->getOption('nRatings', 0) === 1) {
            $this->holehilfreichsteBewertung()
                ->holeBewertung(
                    -1,
                    1,
                    0,
                    $this->getConfigValue('bewertung', 'bewertung_freischalten'),
                    0,
                    $this->getConfigValue('bewertung', 'bewertung_alle_sprachen') === 'Y'
                );
        }
        $this->buildURLs();
        $this->cKurzbezeichnung = !empty($this->AttributeAssoc[\ART_ATTRIBUT_SHORTNAME])
            ? $this->AttributeAssoc[\ART_ATTRIBUT_SHORTNAME]
            : $this->cName;

        $cacheTags = [\CACHING_GROUP_ARTICLE . '_' . $this->kArtikel, \CACHING_GROUP_ARTICLE];
        $basePrice = clone $this->Preise;
        $this->rabattierePreise($customerGroupID);
        $this->staffelPreis_arr = $this->getTierPrices();
        if ($this->cVPE === 'Y' && $this->fVPEWert > 0 && $this->cVPEEinheit && !empty($this->Preise)) {
            // Grundpreis beim Artikelpreis
            $this->baueVPE();
            // Grundpreis bei Staffelpreise
            $this->getScaleBasePrice();
        }
        // Versandkostenfrei-Länder aufgrund rabattierter Preise neu setzen
        $this->taxData['shippingFreeCountries'] = $this->gibMwStVersandLaenderString(true, $customerGroupID);
        \executeHook(\HOOK_ARTIKEL_CLASS_FUELLEARTIKEL, [
            'oArtikel'  => &$this,
            'cacheTags' => &$cacheTags,
            'cached'    => false
        ]);

        if ($noCache === false) {
            // oVariationKombiKinderAssoc_arr can contain a lot of product objects, prices may depend on customers
            // so do not save to cache
            $toSave         = clone $this;
            $toSave->Preise = $basePrice;
            if (\COMPRESS_DESCRIPTIONS === true) {
                $toSave->cBeschreibung    = \gzcompress($toSave->cBeschreibung, \COMPRESSION_LEVEL);
                $toSave->cKurzbezeichnung = \gzcompress($toSave->cKurzbezeichnung, \COMPRESSION_LEVEL);
                $toSave->compressed       = true;
            }
            $this->getCache()->set($this->cacheID, $toSave, $cacheTags);
            self::$products[$this->cacheID] = $toSave;
        }
        $this->getCustomerPrice($customerGroupID, Frontend::getCustomer()->getID());

        return $this;
    }

    /**
     * @param mixed $tmpProduct
     * @param int   $productID
     * @param int   $customerGroupID
     * @param bool  $noCache
     * @return $this|bool
     */
    private function retryWithoutStockFilter($tmpProduct, int $productID, int $customerGroupID, bool $noCache)
    {
        if (
            ($tmpProduct === false || $tmpProduct === null)
            && (!isset($this->options->nKeinLagerbestandBeachten) || $this->options->nKeinLagerbestandBeachten !== 1)
            && ($this->getConfigValue('global', 'artikel_artikelanzeigefilter_seo') === 'seo')
        ) {
            $tmpOptions = clone $this->options;
            $hidePrice  = $this->getConfigValue('global', 'artikel_artikelanzeigefilter_seo') === 'seo' ? 0 : 1;

            $tmpOptions->nKeinLagerbestandBeachten = 1;
            $tmpOptions->nHidePrices               = $hidePrice;
            $tmpOptions->nShowOnlyOnSEORequest     = 1;

            if ($this->fuelleArtikel($productID, $tmpOptions, $customerGroupID, $this->kSprache, $noCache) !== null) {
                $this->inWarenkorbLegbar = \INWKNICHTLEGBAR_LAGER;
            }

            return $this;
        }

        return false;
    }

    /**
     * @param int $productID
     * @param int $customerGroupID
     * @return $this|bool|null
     */
    private function loadFromCache(int $productID, int $customerGroupID)
    {
        $langID        = $this->kSprache;
        $options       = $this->options;
        $baseID        = $this->getCache()->getBaseID(false, false, $customerGroupID, $langID);
        $taxClass      = isset($_SESSION['Steuersatz']) ? \implode('_', $_SESSION['Steuersatz']) : '';
        $customerID    = Frontend::getCustomer()->getID();
        $productHash   = \md5($baseID . $this->getOptionsHash($options) . $taxClass);
        $this->cacheID = 'fa_' . $productID . '_' . $productHash;
        $product       = $this->getCache()->get($this->cacheID);
        if ($product === false) {
            if (isset(self::$products[$this->cacheID])) {
                $product = self::$products[$this->cacheID];
            } else {
                return false;
            }
        }
        if ($product === null) {
            return null;
        }
        $classMatches = \get_class($product) === self::class;
        foreach (\get_object_vars($product) as $k => $v) {
            if (
                $k !== 'db' && $k !== 'cache' && $k !== 'customerGroup'
                && ($classMatches || \property_exists($this, $k))
            ) {
                $this->$k = $v;
            }
        }
        $this->setConfig($this->getConfig());
        $this->taxData = $this->getShippingAndTaxData();
        if ($this->compressed === true) {
            $this->cBeschreibung    = \gzuncompress($this->cBeschreibung);
            $this->cKurzbezeichnung = \gzuncompress($this->cKurzbezeichnung);
            $this->compressed       = false;
        }
        if ($this->favourableShippingID > 0) {
            $this->oFavourableShipping = new Versandart($this->favourableShippingID);
        }
        $maxDiscount = $this->getDiscount($customerGroupID);
        if (
            $this->Preise !== null
            && (int)$this->Preise->fVKNetto === 0
            && (int)$this->getConfigValue('global', 'global_sichtbarkeit') === 2
            && Frontend::getCustomerGroup()->mayViewPrices()
        ) {
            // zero-ed prices were saved to cache
            $this->Preise = null;
        }
        if ($this->Preise === null || !\method_exists($this->Preise, 'rabbatierePreise')) {
            $this->holPreise($customerGroupID, $this);
        }
        $this->getCustomerPrice($customerGroupID, $customerID);
        if ($maxDiscount > 0) {
            $this->rabattierePreise($customerGroupID);
        }
        //#7595 - do not use cached result if special price is expired
        $return = true;
        if ($this->cAktivSonderpreis === 'Y' && $this->dSonderpreisEnde_en !== null) {
            $endDate = new DateTime($this->dSonderpreisEnde_en);
            $return  = $endDate >= (new DateTime())->setTime(0, 0);
        } elseif ($this->cAktivSonderpreis === 'N' && $this->dSonderpreisStart_en !== null) {
            // do not use cached result if a special price started in the mean time
            $startDate = new DateTime($this->dSonderpreisStart_en);
            $today     = (new DateTime())->setTime(0, 0);
            $endDate   = $this->dSonderpreisEnde_en === null
                ? $today
                : new DateTime($this->dSonderpreisEnde_en);
            $return    = ($startDate > $today || $endDate < $today);
        }
        if ($return !== true) {
            return false;
        }
        $this->cacheHit = true;
        $this->addVariationChildren($customerGroupID);
        \executeHook(\HOOK_ARTIKEL_CLASS_FUELLEARTIKEL, [
            'oArtikel'  => &$this,
            'cacheTags' => [],
            'cached'    => true
        ]);

        return $this;
    }

    /**
     * @return SqlObject
     */
    private function getSeoSQL(): SqlObject
    {
        $obj = new SqlObject();
        $obj->setSelect('tseo.cSeo, ');
        $obj->setJoin(
            'LEFT JOIN tseo ON tseo.cKey = \'kArtikel\' AND tseo.kKey = tartikel.kArtikel
                            AND tseo.kSprache = :lid'
        );
        $obj->addParam(':lid', $this->kSprache);

        return $obj;
    }

    /**
     * @param int $productID
     * @return SqlObject
     */
    private function getBomSQL(int $productID): SqlObject
    {
        $bom = $this->getDB()->getSingleObject(
            'SELECT kStueckliste AS id, fLagerbestand AS stock
                FROM tartikel
                WHERE kArtikel = :pid',
            ['pid' => $productID]
        );
        $obj = new SqlObject();
        $obj->setStatement(' tartikel.fLagerbestand, ');
        if ($bom === null || empty($bom->id)) {
            return $obj;
        }
        if (!$bom->stock) {
            $bom->stock = 0;
        }
        $obj->setStatement(
            'IF(tartikel.kStueckliste > 0,
                (SELECT LEAST(IFNULL(FLOOR(MIN(tartikel.fLagerbestand / tstueckliste.fAnzahl)),
                9999999), :stk) AS fMin
                FROM tartikel
                JOIN tstueckliste ON tstueckliste.kArtikel = tartikel.kArtikel
                    AND tstueckliste.kStueckliste = :bid
                    AND tartikel.fLagerbestand > 0
                    AND tartikel.cLagerBeachten  = \'Y\'
                WHERE tartikel.cLagerKleinerNull = \'N\'), tartikel.fLagerbestand) AS fLagerbestand,'
        );
        $obj->addParam('stk', $bom->stock);
        $obj->addParam('bid', (int)$bom->id);

        return $obj;
    }

    /**
     * @param int $productID
     * @param int $customerGroupID
     * @return SqlObject
     */
    private function getProductSQL(int $productID, int $customerGroupID): SqlObject
    {
        $langID             = $this->kSprache;
        $bestsellerMinSales = (float)($this->getConfigValue('global', 'global_bestseller_minanzahl') ?? 10);
        $topratedMinRatings = (int)($this->getConfigValue('boxen', 'boxen_topbewertet_minsterne') ?? 4);
        $localizationSQL    = $this->getLocalizationSQL($productID);
        // Work Around Lagerbestand nicht beachten wenn es sich um ein VariKind handelt
        // Da das Kind geladen werden muss.
        // Erst nach dem Laden wird angezeigt, dass der Lagerbestand auf "ausverkauft" steht
        $stockLevelSQL = $this->getOption('nKeinLagerbestandBeachten', 0) === 1
            ? ''
            : Shop::getProductFilter()->getFilterSQL()->getStockFilterSQL();
        // Nicht sichtbare Artikel je nach ArtikelOption trotzdem laden
        $visibilitySQL = $this->getOption('nKeineSichtbarkeitBeachten', 0) === 1
            ? ''
            : ' AND tartikelsichtbarkeit.kArtikel IS NULL ';

        $bomSQL = $this->getBomSQL($productID);
        $seoSQL = $this->getSeoSQL();

        $sql = 'SELECT tartikel.kArtikel, tartikel.kHersteller, tartikel.kLieferstatus, tartikel.kSteuerklasse,
                tartikel.kEinheit, tartikel.kVPEEinheit, tartikel.kVersandklasse, tartikel.kEigenschaftKombi,
                tartikel.kVaterArtikel, tartikel.kStueckliste, tartikel.kWarengruppe,
                tartikel.cArtNr, tartikel.cName, tartikel.cBeschreibung, tartikel.cAnmerkung, '
                . $bomSQL->getStatement() . ' tartikel.fMwSt, tartikel.cSeo AS originalSeo,
                IF (tartikelabnahme.fMindestabnahme IS NOT NULL,
                    tartikelabnahme.fMindestabnahme, tartikel.fMindestbestellmenge) AS fMindestbestellmenge,
                IF (tartikelabnahme.fIntervall IS NOT NULL,
                    tartikelabnahme.fIntervall, tartikel.fAbnahmeintervall) AS fAbnahmeintervall,
                tartikel.cBarcode, tartikel.cTopArtikel,
                tartikel.fGewicht, tartikel.fArtikelgewicht, tartikel.cNeu, tartikel.cKurzBeschreibung, tartikel.fUVP,
                tartikel.cLagerBeachten, tartikel.cLagerKleinerNull, tartikel.cLagerVariation, tartikel.cTeilbar,
                tartikel.fPackeinheit, tartikel.cVPE, tartikel.fVPEWert, tartikel.cVPEEinheit, tartikel.cSuchbegriffe,
                tartikel.nSort, tartikel.dErscheinungsdatum, tartikel.dErstellt, tartikel.dLetzteAktualisierung,
                tartikel.cSerie, tartikel.cISBN, tartikel.cASIN, tartikel.cHAN, tartikel.cUNNummer, tartikel.cGefahrnr,
                tartikel.nIstVater, date_format(tartikel.dErscheinungsdatum, \'%d.%m.%Y\') AS Erscheinungsdatum_de,
                tartikel.cTaric, tartikel.cUPC, tartikel.cHerkunftsland, tartikel.cEPID, tartikel.fZulauf,
                tartikel.dZulaufDatum, DATE_FORMAT(tartikel.dZulaufDatum, \'%d.%m.%Y\') AS dZulaufDatum_de,
                tartikel.fLieferantenlagerbestand, tartikel.fLieferzeit,
                tartikel.dMHD, DATE_FORMAT(tartikel.dMHD, \'%d.%m.%Y\') AS dMHD_de,
                tartikel.kMassEinheit, tartikel.kGrundPreisEinheit, tartikel.fMassMenge, tartikel.fGrundpreisMenge,
                tartikel.fBreite, tartikel.fHoehe, tartikel.fLaenge, tartikel.nLiefertageWennAusverkauft,
                tartikel.nAutomatischeLiefertageberechnung, tartikel.nBearbeitungszeit, me.cCode AS cMasseinheitCode,
                mes.cName AS cMasseinheitName, gpme.cCode AS cGrundpreisEinheitCode,
                gpmes.cName AS cGrundpreisEinheitName,
                ' . $seoSQL->getSelect() . '
                ' . $localizationSQL->getSelect() . '
                tsonderpreise.fNettoPreis, tartikelext.fDurchschnittsBewertung,
                 tlieferstatus.cName AS cName_tlieferstatus, teinheit.cName AS teinheitcName,
                tartikelsonderpreis.cAktiv AS cAktivSonderpreis, tartikelsonderpreis.dStart AS dStart_en,
                DATE_FORMAT(tartikelsonderpreis.dStart, \'%d.%m.%Y\') AS dStart_de,
                tartikelsonderpreis.dEnde AS dEnde_en,
                DATE_FORMAT(tartikelsonderpreis.dEnde, \'%d.%m.%Y\') AS dEnde_de,
                tversandklasse.cName AS cVersandklasse,
                tbestseller.isBestseller AS bIsBestseller,
                ROUND(tartikelext.fDurchschnittsBewertung) >= :trmr AS bIsTopBewertet,
                COALESCE((SELECT 1 FROM tuploadschema ULS WHERE ULS.kCustomID = :pid LIMIT 1), 0) AS hasUploads
                FROM tartikel
                LEFT JOIN tartikelabnahme
                    ON tartikel.kArtikel = tartikelabnahme.kArtikel
                    AND tartikelabnahme.kKundengruppe = :cgid
                LEFT JOIN tartikelsonderpreis
                    ON tartikelsonderpreis.kArtikel = tartikel.kArtikel
                    AND tartikelsonderpreis.cAktiv = \'Y\'
                    AND (tartikelsonderpreis.nAnzahl <= tartikel.fLagerbestand OR tartikelsonderpreis.nIstAnzahl = 0)
                LEFT JOIN tsonderpreise ON tartikelsonderpreis.kArtikelSonderpreis = tsonderpreise.kArtikelSonderpreis
                    AND tsonderpreise.kKundengruppe = :cgid
                ' . $seoSQL->getJoin() . '
                ' . $localizationSQL->getJoin() . '
                LEFT JOIN tbestseller
                ON tbestseller.kArtikel = tartikel.kArtikel
                LEFT JOIN tartikelext
                    ON tartikelext.kArtikel = tartikel.kArtikel
                LEFT JOIN tlieferstatus
                    ON tlieferstatus.kLieferstatus = tartikel.kLieferstatus
                    AND tlieferstatus.kSprache = :lid
                LEFT JOIN teinheit
                    ON teinheit.kEinheit = tartikel.kEinheit
                    AND teinheit.kSprache = :lid
                LEFT JOIN tartikelsichtbarkeit
                    ON tartikel.kArtikel = tartikelsichtbarkeit.kArtikel
                    AND tartikelsichtbarkeit.kKundengruppe = :cgid
                LEFT JOIN tversandklasse
                    ON tversandklasse.kVersandklasse = tartikel.kVersandklasse
                LEFT JOIN tmasseinheit me ON me.kMassEinheit = tartikel.kMassEinheit
                LEFT JOIN tmasseinheitsprache mes
                    ON mes.kMassEinheit = me.kMassEinheit
                    AND mes.kSprache = :lid
                LEFT JOIN tmasseinheit gpme
                    ON gpme.kMassEinheit = tartikel.kGrundpreisEinheit
                LEFT JOIN tmasseinheitsprache gpmes
                    ON gpmes.kMassEinheit = gpme.kMassEinheit
                    AND gpmes.kSprache = :lid
                WHERE tartikel.kArtikel = :pid'
                . $visibilitySQL . ' ' . $stockLevelSQL;

        $obj = new SqlObject();
        $obj->setStatement($sql);
        $params = [
            ':lid'  => $langID,
            ':cgid' => $customerGroupID,
            ':pid'  => $productID,
            ':bsms' => $bestsellerMinSales,
            ':trmr' => $topratedMinRatings
        ];
        $obj->setParams(
            \array_merge(
                $params,
                $bomSQL->getParams(),
                $seoSQL->getParams(),
                $localizationSQL->getParams()
            )
        );

        return $obj;
    }

    /**
     * @param stdClass $data
     * @return stdClass
     */
    private function localizeData(stdClass $data): stdClass
    {
        if (!isset($data->cName_spr)) {
            return $data;
        }
        if (\trim($data->cName_spr)) {
            $data->cName = $data->cName_spr;
        }
        if (\trim($data->cBeschreibung_spr)) {
            $data->cBeschreibung = $data->cBeschreibung_spr;
        }
        if (\trim($data->cKurzBeschreibung_spr)) {
            $data->cKurzBeschreibung = $data->cKurzBeschreibung_spr;
        }

        return $data;
    }

    /**
     * @param stdClass $data
     */
    private function sanitizeProductData(stdClass $data): void
    {
        $this->originalName                      = $data->cName;
        $this->originalSeo                       = $data->originalSeo;
        $data                                    = $this->localizeData($data);
        $this->kArtikel                          = (int)$data->kArtikel;
        $this->kHersteller                       = (int)$data->kHersteller;
        $this->kLieferstatus                     = (int)$data->kLieferstatus;
        $this->kSteuerklasse                     = (int)$data->kSteuerklasse;
        $this->kEinheit                          = (int)$data->kEinheit;
        $this->kVersandklasse                    = (int)$data->kVersandklasse;
        $this->kWarengruppe                      = (int)$data->kWarengruppe;
        $this->kVPEEinheit                       = (int)$data->kVPEEinheit;
        $this->fLagerbestand                     = $data->fLagerbestand;
        $this->fMindestbestellmenge              = $data->fMindestbestellmenge;
        $this->fPackeinheit                      = $data->fPackeinheit;
        $this->fAbnahmeintervall                 = $data->fAbnahmeintervall;
        $this->fZulauf                           = $data->fZulauf;
        $this->fGewicht                          = $data->fGewicht;
        $this->fArtikelgewicht                   = $data->fArtikelgewicht;
        $this->fUVP                              = $data->fUVP;
        $this->fUVPBrutto                        = $data->fUVP;
        $this->fVPEWert                          = $data->fVPEWert;
        $this->cName                             = Text::htmlentitiesOnce($data->cName, \ENT_COMPAT | \ENT_HTML401);
        $this->cSeo                              = $data->cSeo;
        $this->cBeschreibung                     = $data->cBeschreibung;
        $this->cAnmerkung                        = $data->cAnmerkung;
        $this->cArtNr                            = $data->cArtNr;
        $this->cVPE                              = $data->cVPE;
        $this->cVPEEinheit                       = $data->cVPEEinheit;
        $this->cSuchbegriffe                     = $data->cSuchbegriffe;
        $this->cEinheit                          = $data->teinheitcName;
        $this->cTeilbar                          = $data->cTeilbar;
        $this->cBarcode                          = $data->cBarcode;
        $this->cLagerBeachten                    = $data->cLagerBeachten;
        $this->cLagerKleinerNull                 = $data->cLagerKleinerNull;
        $this->cLagerVariation                   = $data->cLagerVariation;
        $this->cKurzBeschreibung                 = $data->cKurzBeschreibung;
        $this->cLieferstatus                     = $data->cName_tlieferstatus;
        $this->cTopArtikel                       = $data->cTopArtikel;
        $this->cNeu                              = $data->cNeu;
        $this->fMwSt                             = $data->fMwSt;
        $this->dErscheinungsdatum                = $data->dErscheinungsdatum;
        $this->Erscheinungsdatum_de              = $data->Erscheinungsdatum_de;
        $this->fDurchschnittsBewertung           = \round($data->fDurchschnittsBewertung * 2) / 2;
        $this->cVersandklasse                    = $data->cVersandklasse;
        $this->cSerie                            = $data->cSerie;
        $this->cISBN                             = $data->cISBN;
        $this->cASIN                             = $data->cASIN;
        $this->cHAN                              = $data->cHAN;
        $this->cUNNummer                         = $data->cUNNummer;
        $this->cGefahrnr                         = $data->cGefahrnr;
        $this->nIstVater                         = (int)$data->nIstVater;
        $this->kEigenschaftKombi                 = (int)$data->kEigenschaftKombi;
        $this->kVaterArtikel                     = (int)$data->kVaterArtikel;
        $this->kStueckliste                      = (int)$data->kStueckliste;
        $this->dErstellt                         = $data->dErstellt;
        $this->dErstellt_de                      = \date_format(\date_create($this->dErstellt ?? 'now'), 'd.m.Y');
        $this->nSort                             = (int)$data->nSort;
        $this->fNettoPreis                       = $data->fNettoPreis;
        $this->bIsBestseller                     = (int)$data->bIsBestseller;
        $this->bIsTopBewertet                    = (int)$data->bIsTopBewertet;
        $this->cTaric                            = $data->cTaric;
        $this->cUPC                              = $data->cUPC;
        $this->cHerkunftsland                    = $data->cHerkunftsland;
        $this->cEPID                             = $data->cEPID;
        $this->fLieferantenlagerbestand          = $data->fLieferantenlagerbestand;
        $this->fLieferzeit                       = $data->fLieferzeit;
        $this->cAktivSonderpreis                 = $data->cAktivSonderpreis;
        $this->dSonderpreisStart_en              = $data->dStart_en;
        $this->dSonderpreisEnde_en               = $data->dEnde_en;
        $this->dSonderpreisStart_de              = $data->dStart_de;
        $this->dSonderpreisEnde_de               = $data->dEnde_de;
        $this->dZulaufDatum                      = $data->dZulaufDatum;
        $this->dZulaufDatum_de                   = $data->dZulaufDatum_de;
        $this->dMHD                              = $data->dMHD;
        $this->dMHD_de                           = $data->dMHD_de;
        $this->kMassEinheit                      = (int)$data->kMassEinheit;
        $this->kGrundpreisEinheit                = (int)$data->kGrundPreisEinheit;
        $this->fMassMenge                        = (float)$data->fMassMenge;
        $this->fGrundpreisMenge                  = (float)$data->fGrundpreisMenge;
        $this->fBreite                           = (float)$data->fBreite;
        $this->fHoehe                            = (float)$data->fHoehe;
        $this->fLaenge                           = (float)$data->fLaenge;
        $this->nLiefertageWennAusverkauft        = (int)$data->nLiefertageWennAusverkauft;
        $this->nAutomatischeLiefertageberechnung = (int)$data->nAutomatischeLiefertageberechnung;
        $this->nBearbeitungszeit                 = (int)$data->nBearbeitungszeit;
        $this->cMasseinheitCode                  = $data->cMasseinheitCode;
        $this->cMasseinheitName                  = $data->cMasseinheitName;
        $this->cGrundpreisEinheitCode            = $data->cGrundpreisEinheitCode;
        $this->cGrundpreisEinheitName            = $data->cGrundpreisEinheitName;
        $this->oDownload_arr                     = [];
        $this->bHasKonfig                        = false;
        $this->oKonfig_arr                       = [];
        $this->hasUploads                        = (int)$data->hasUploads > 0;
        // short baseprice measurement unit e.g. "ml"
        $abbr = UnitsOfMeasure::getPrintAbbreviation($this->cGrundpreisEinheitCode);
        if (!empty($abbr)) {
            $this->cGrundpreisEinheitName = UnitsOfMeasure::getPrintAbbreviation($this->cGrundpreisEinheitCode);
        }
        // short measurement unit e.g. "ml"
        $abbr = UnitsOfMeasure::getPrintAbbreviation($this->cMasseinheitCode);
        if (!empty($abbr)) {
            $this->cMasseinheitName = $abbr;
        }
        if ($this->kSprache > 0 && !LanguageHelper::isDefaultLanguageActive(languageID: $this->kSprache)) {
            $unit = $this->getDB()->getSingleObject(
                'SELECT cName
                    FROM teinheit
                    WHERE kEinheit = (SELECT kEinheit
                                        FROM teinheit
                                        WHERE cName = :vpe LIMIT 0, 1)
                                            AND kSprache = :lid LIMIT 0, 1',
                ['vpe' => $this->cVPEEinheit, 'lid' => $this->kSprache]
            );
            if ($unit !== null && \mb_strlen($unit->cName) > 0) {
                $this->cVPEEinheit = $unit->cName;
            }
        }
        $this->cGewicht        = Separator::getUnit(\JTL_SEPARATOR_WEIGHT, $this->kSprache, $this->fGewicht);
        $this->cArtikelgewicht = Separator::getUnit(\JTL_SEPARATOR_WEIGHT, $this->kSprache, $this->fArtikelgewicht);

        if ($this->fMassMenge != 0) {
            $this->cMassMenge = Separator::getUnit(\JTL_SEPARATOR_AMOUNT, $this->kSprache, $this->fMassMenge);
        }
        if ($this->fPackeinheit == 0) {
            $this->fPackeinheit = 1;
        }
    }

    /**
     * @param int $productID
     * @param int $customerGroupID
     * @param int $customerID
     * @return $this
     */
    private function getPriceData(int $productID, int $customerGroupID, int $customerID = 0): self
    {
        $tmp = $this->getDB()->getSingleObject(
            'SELECT tartikel.kArtikel, tartikel.kEinheit, tartikel.kVPEEinheit, tartikel.kSteuerklasse,
                tartikel.fPackeinheit, tartikel.cVPE, tartikel.fVPEWert, tartikel.cVPEEinheit
                FROM tartikel
                LEFT JOIN tartikelsichtbarkeit
                    ON tartikel.kArtikel = tartikelsichtbarkeit.kArtikel
                    AND tartikelsichtbarkeit.kKundengruppe = :cgid
                WHERE tartikelsichtbarkeit.kArtikel IS NULL
                    AND tartikel.kArtikel = :pid',
            ['pid' => $productID, 'cgid' => $customerGroupID]
        );

        if ($tmp !== null) {
            $this->kArtikel      = (int)$tmp->kArtikel;
            $this->kEinheit      = (int)$tmp->kEinheit;
            $this->kVPEEinheit   = (int)$tmp->kVPEEinheit;
            $this->kSteuerklasse = (int)$tmp->kSteuerklasse;
            $this->fPackeinheit  = $tmp->fPackeinheit;
            $this->cVPE          = $tmp->cVPE;
            $this->fVPEWert      = $tmp->fVPEWert;
            $this->cVPEEinheit   = $tmp->cVPEEinheit;
            $this->holPreise($customerGroupID, $this, $customerID);
        }

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLanguageURLs(): array
    {
        return $this->cSprachURL_arr;
    }

    /**
     * @return Artikel
     */
    private function addManufacturerData(): self
    {
        if ($this->kHersteller <= 0) {
            return $this;
        }
        $manufacturer = new Hersteller($this->kHersteller, $this->kSprache);

        $this->cHersteller                = $manufacturer->getName($this->kSprache);
        $this->cHerstellerSeo             = $manufacturer->getSlug($this->kSprache);
        $this->cHerstellerURL             = $manufacturer->getURL($this->kSprache);
        $this->cHerstellerHomepage        = $manufacturer->getHomepage();
        $this->cHerstellerMetaTitle       = $manufacturer->getMetaTitle($this->kSprache);
        $this->cHerstellerMetaKeywords    = $manufacturer->getMetaKeywords($this->kSprache);
        $this->cHerstellerMetaDescription = $manufacturer->getMetaDescription($this->kSprache);
        $this->cHerstellerBeschreibung    = $manufacturer->getDescription($this->kSprache);
        $this->cHerstellerSortNr          = $manufacturer->getSortNo();
        if ($manufacturer->getImagePath() !== '') {
            $this->cHerstellerBildKlein     = $manufacturer->getImagePathSmall();
            $this->cHerstellerBildNormal    = $manufacturer->getImagePathNormal();
            $this->cBildpfad_thersteller    = $manufacturer->getImage(Image::SIZE_XS);
            $this->cHerstellerBildURLKlein  = $this->cBildpfad_thersteller;
            $this->cHerstellerBildURLNormal = $manufacturer->getImage();
            $this->manufacturerImageWidthSM = $manufacturer->getImageWidth(Image::SIZE_SM);
            $this->manufacturerImageWidthMD = $manufacturer->getImageWidth(Image::SIZE_MD);
        }

        return $this;
    }

    /**
     * Warenkorbmatrix Variationskinder holen
     *
     * @param int $customerGroupID
     */
    private function addVariationChildren(int $customerGroupID): void
    {
        if (
            $this->getOption('nWarenkorbmatrix', 0) === 1
            || ($this->getFunctionalAttributevalue(\FKT_ATTRIBUT_WARENKORBMATRIX, true) === 1
                && $this->getOption('nMain', 0) === 1)
        ) {
            $this->oVariationKombiKinderAssoc_arr = $this->holeVariationKombiKinderAssoc($customerGroupID);
        }
    }

    private function checkCanBePurchased(): void
    {
        if ($this->nErscheinendesProdukt && $this->getConfigValue('global', 'global_erscheinende_kaeuflich') !== 'Y') {
            $this->inWarenkorbLegbar = \INWKNICHTLEGBAR_NICHTVORBESTELLBAR;
        }
        if (
            $this->fLagerbestand <= 0
            && $this->cLagerBeachten === 'Y'
            && ($this->cLagerKleinerNull !== 'Y'
                || (int)$this->getConfigValue('global', 'artikel_artikelanzeigefilter') ===
                \EINSTELLUNGEN_ARTIKELANZEIGEFILTER_LAGER
            )
            && $this->cLagerVariation !== 'Y'
        ) {
            $this->inWarenkorbLegbar = \INWKNICHTLEGBAR_LAGER;
        }
        if (
            !$this->bHasKonfig
            && $this->Preise->fVKNetto === 0.0
            && $this->getConfigValue('global', 'global_preis0') === 'N'
            && isset($this->Preise->fVKNetto)
            && $this->getConfigValue('global', 'global_preis0') !== null
            && $this->getFunctionalAttributevalue(\FKT_ATTRIBUT_VOUCHER_FLEX) === null
        ) {
            $this->inWarenkorbLegbar = \INWKNICHTLEGBAR_PREISAUFANFRAGE;
        }
        if (!empty($this->FunktionsAttribute[\FKT_ATTRIBUT_UNVERKAEUFLICH])) {
            $this->inWarenkorbLegbar = \INWKNICHTLEGBAR_UNVERKAEUFLICH;
        }
        if ($this->bHasKonfig && Configurator::hasUnavailableGroup($this->oKonfig_arr)) {
            $this->inWarenkorbLegbar = \INWKNICHTLEGBAR_LAGER;
        }
    }

    /**
     * @param int $productID
     * @param int $customerGroupID
     * @return int[]
     */
    private function getCategories(int $productID, int $customerGroupID): array
    {
        return $this->getDB()->getInts(
            'SELECT tkategorieartikel.kKategorie
                FROM tkategorieartikel
                LEFT JOIN tkategoriesichtbarkeit
                    ON tkategoriesichtbarkeit.kKategorie = tkategorieartikel.kKategorie
                    AND tkategoriesichtbarkeit.kKundengruppe = :cgid
                JOIN tkategorie
                    ON tkategorie.kKategorie = tkategorieartikel.kKategorie
                WHERE tkategoriesichtbarkeit.kKategorie IS NULL
                    AND tkategorieartikel.kKategorie > 0
                    AND tkategorieartikel.kArtikel = :pid',
            'kKategorie',
            ['cgid' => $customerGroupID, 'pid' => $productID]
        );
    }

    /**
     * @return $this
     * @throws \Exception
     */
    private function getSearchSpecialOverlay(): self
    {
        $customBadge       = new stdClass();
        $tmp               = \explode(
            ';',
            $this->getFunctionalAttributevalue(\FKT_ATTRIBUT_CUSTOM_ITEM_BADGE) ?? ''
        );
        $customBadge->text = $tmp[0] ?? '';
        if ($customBadge->text !== '') {
            $customBadge->text  = Shop::Lang()->get($customBadge->text, 'custom');
            $customBadge->class = '';
            $customBadge->style = '';
            $textColor          = $tmp[1] ?? '';
            $bgColor            = $tmp[2] ?? '';

            if (\str_starts_with($textColor, '#')) {
                $customBadge->style .= 'color:' . $textColor . ';';
            } else {
                $customBadge->class .= ' text-' . $textColor;
            }
            if (\str_starts_with($bgColor, '#')) {
                $customBadge->style .= 'border-right-color: ' . $bgColor . ';background-color:' . $bgColor . ';';
            } else {
                $customBadge->class .= ' bg-' . $bgColor;
            }
            $overlay = new Overlay(\SEARCHSPECIALS_CUSTOMBADGE, Shop::getLanguageID());
            $overlay->setPriority(0);
            $overlay->setCssAndText($customBadge);
            $this->oSuchspecialBild = $overlay;
            return $this;
        }

        $searchSpecials = SearchSpecial::getAll($this->kSprache);
        // Suchspecialbildoverlay
        // Kleinste Prio und somit die Wichtigste, steht immer im Element 0 vom Array (nPrio ASC)
        if (empty($searchSpecials)) {
            return $this;
        }
        $specials = [
            \SEARCHSPECIALS_BESTSELLER    => $this->isBestseller(),
            \SEARCHSPECIALS_SPECIALOFFERS => $this->Preise !== null && $this->Preise->Sonderpreis_aktiv === 1,
            \SEARCHSPECIALS_NEWPRODUCTS   => false,
            \SEARCHSPECIALS_TOPOFFERS     => $this->cTopArtikel === 'Y',
            \SEARCHSPECIALS_PREORDER      => false
        ];

        $now = new DateTime();
        // Neu im Sortiment
        if (!empty($this->cNeu) && $this->cNeu === 'Y') {
            $days        = ($this->getConfigValue('boxen', 'box_neuimsortiment_alter_tage') !== null
                            && (int)$this->getConfigValue('boxen', 'box_neuimsortiment_alter_tage') > 0)
                ? (int)$this->getConfigValue('boxen', 'box_neuimsortiment_alter_tage')
                : 30;
            $dateCreated = new DateTime($this->dErstellt);
            $dateCreated->modify('+' . $days . ' day');
            $specials[\SEARCHSPECIALS_NEWPRODUCTS] = $now < $dateCreated;
        }
        // In kürze Verfügbar
        $specials[\SEARCHSPECIALS_UPCOMINGPRODUCTS] = $this->dErscheinungsdatum !== null
            && $now < new DateTime($this->dErscheinungsdatum);
        // Top bewertet
        // No need to check with custom function.. this value is set in fuelleArtikel()?
        $specials[\SEARCHSPECIALS_TOPREVIEWS] = (int)$this->bIsTopBewertet === 1;

        // VariationskombiKinder Lagerbestand 0
        if ($this->kVaterArtikel > 0) {
            $variChildren = $this->getDB()->selectAll(
                'tartikel',
                'kVaterArtikel',
                $this->kVaterArtikel,
                'fLagerbestand, cLagerBeachten, cLagerKleinerNull'
            );
            $bLieferbar   = \array_reduce($variChildren, static function ($carry, $item): bool {
                return $carry
                    || $item->fLagerbestand > 0
                    || $item->cLagerBeachten === 'N'
                    || $item->cLagerKleinerNull === 'Y';
            }, false);

            $specials[\SEARCHSPECIALS_OUTOFSTOCK] = !$bLieferbar;
        } else {
            // Normal Lagerbestand 0
            $specials[\SEARCHSPECIALS_OUTOFSTOCK] = ($this->fLagerbestand <= 0
                && $this->cLagerBeachten === 'Y'
                && $this->cLagerKleinerNull !== 'Y')
                || ($this->inWarenkorbLegbar === \INWKNICHTLEGBAR_LAGER
                    || $this->inWarenkorbLegbar === \INWKNICHTLEGBAR_LAGERVAR
                );
        }
        // Auf Lager
        $specials[\SEARCHSPECIALS_ONSTOCK] = ($this->fLagerbestand > 0 && $this->cLagerBeachten === 'Y');
        // Vorbestellbar
        if (
            $specials[\SEARCHSPECIALS_UPCOMINGPRODUCTS]
            && $this->getConfigValue('global', 'global_erscheinende_kaeuflich') === 'Y'
        ) {
            $specials[\SEARCHSPECIALS_PREORDER] = true;
        }
        $this->bSuchspecial_arr = $specials;
        // SuchspecialBild anhand der höchsten Prio und des gesetzten Suchspecials festlegen
        foreach ($searchSpecials as $overlay) {
            if (empty($this->bSuchspecial_arr[$overlay->getType()])) {
                continue;
            }
            $this->oSuchspecialBild = $overlay;
        }

        return $this;
    }

    /**
     * Sobald ein KindArtikel teurer ist als der Vaterartikel, muss nVariationsAufpreisVorhanden auf 1
     * gesetzt werden damit in der Artikelvorschau ein "Preis ab ..." erscheint
     * aber nur wenn auch Preise angezeigt werden, this->Preise also auch vorhanden ist
     */
    private function checkVariationExtraCharge(): void
    {
        if ($this->kVaterArtikel === 0 && $this->nIstVater === 1 && \is_object($this->Preise)) {
            $this->nVariationsAufpreisVorhanden = (int)$this->Preise->oPriceRange->isRange();
        }
    }

    /**
     * @return $this
     * @throws \Exception
     */
    private function checkDateDependencies(): self
    {
        $releaseDate           = new DateTime($this->dErscheinungsdatum ?? '');
        $supplyDate            = new DateTime($this->dZulaufDatum ?? '');
        $bestBeforeDate        = new DateTime($this->dMHD ?? '');
        $specialPriceStartDate = new DateTime($this->dSonderpreisStart_en ?? '');
        $specialPriceEndDate   = new DateTime($this->dSonderpreisEnde_en ?? '');
        $specialPriceEndDate->modify('+1 day');

        $now           = new DateTime();
        $bMHD          = $bestBeforeDate > $now ? 1 : 0;
        $hasSupplyDate = $supplyDate > $now ? 1 : 0;

        $this->nErscheinendesProdukt = $releaseDate > $now ? 1 : 0;

        if (!$bMHD) {
            $this->dMHD_de = null;
        }
        if (!$hasSupplyDate) {
            $this->dZulaufDatum_de = null;
        }
        $this->cAktivSonderpreis = $this->dSonderpreisStart_en !== null
            && $specialPriceStartDate <= $now
            && ($this->dSonderpreisEnde_en === null || $specialPriceEndDate >= $now) ? 'Y' : 'N';

        return $this->getSearchSpecialOverlay();
    }

    /**
     * check if current product is a bestseller
     *
     * @return bool
     */
    private function isBestseller(): bool
    {
        if ($this->bIsBestseller !== null) {
            return (bool)$this->bIsBestseller;
        }
        if ($this->kArtikel <= 0) {
            return false;
        }
        $bestseller = $this->getDB()->getSingleObject(
            'SELECT isBestseller
                FROM tbestseller
                WHERE kArtikel = :pid',
            ['pid' => $this->kArtikel]
        );

        return (bool)($bestseller->isBestseller ?? false);
    }

    /**
     * nStatus: 0 = Nicht verfuegbar, 1 = Knapper Lagerbestand, 2 = Verfuegbar
     *
     * @return $this
     */
    private function getStockDisplay(): self
    {
        $this->Lageranzeige = new stdClass();
        $lang               = LanguageHelper::getInstance();
        if ($this->cLagerBeachten === 'Y') {
            if ($this->fLagerbestand > 0) {
                $this->Lageranzeige->cLagerhinweis['genau']          = $this->fLagerbestand . ' ' .
                    $this->cEinheit . ' ' . $lang->get('inStock');
                $this->Lageranzeige->cLagerhinweis['verfuegbarkeit'] = $lang->get('productAvailable');
                if ($this->getConfigValue('artikeldetails', 'artikel_lagerbestandsanzeige') === 'verfuegbarkeit') {
                    $this->Lageranzeige->cLagerhinweis['verfuegbarkeit'] = $lang->get('ampelGruen');
                }
            } elseif (
                $this->cLagerKleinerNull === 'Y'
                && $this->getConfigValue('global', 'artikel_ampel_lagernull_gruen') === 'Y'
            ) {
                $this->Lageranzeige->cLagerhinweis['genau']          = $lang->get('ampelGruen');
                $this->Lageranzeige->cLagerhinweis['verfuegbarkeit'] = $lang->get('ampelGruen');
            } else {
                $this->Lageranzeige->cLagerhinweis['genau']          = $lang->get('productNotAvailable');
                $this->Lageranzeige->cLagerhinweis['verfuegbarkeit'] = $lang->get('productNotAvailable');
            }
        } else {
            $this->Lageranzeige->cLagerhinweis['genau']          = $lang->get('ampelGruen');
            $this->Lageranzeige->cLagerhinweis['verfuegbarkeit'] = $lang->get('ampelGruen');
        }
        if ($this->cLagerBeachten === 'Y') {
            // ampel
            $this->Lageranzeige->nStatus   = 1;
            $this->Lageranzeige->AmpelText = !empty($this->AttributeAssoc[\ART_ATTRIBUT_AMPELTEXT_GELB])
                ? $this->AttributeAssoc[\ART_ATTRIBUT_AMPELTEXT_GELB]
                : $lang->get('ampelGelb');
            $this->setToParentStockText(\ART_ATTRIBUT_AMPELTEXT_GELB, 'ampelGelb');

            if ($this->fLagerbestand <= (int)$this->getConfigValue('global', 'artikel_lagerampel_rot')) {
                $this->Lageranzeige->nStatus   = 0;
                $this->Lageranzeige->AmpelText = !empty($this->AttributeAssoc[\ART_ATTRIBUT_AMPELTEXT_ROT])
                    ? $this->AttributeAssoc[\ART_ATTRIBUT_AMPELTEXT_ROT]
                    : $lang->get('ampelRot');
                $this->setToParentStockText(\ART_ATTRIBUT_AMPELTEXT_ROT, 'ampelRot');
            }
            if (
                $this->fLagerbestand >= (int)$this->getConfigValue('global', 'artikel_lagerampel_gruen')
                || ($this->cLagerKleinerNull === 'Y'
                    && $this->getConfigValue('global', 'artikel_ampel_lagernull_gruen') === 'Y')
            ) {
                $this->Lageranzeige->nStatus   = 2;
                $this->Lageranzeige->AmpelText = !empty($this->AttributeAssoc[\ART_ATTRIBUT_AMPELTEXT_GRUEN])
                    ? $this->AttributeAssoc[\ART_ATTRIBUT_AMPELTEXT_GRUEN]
                    : $lang->get('ampelGruen');
                $this->setToParentStockText(\ART_ATTRIBUT_AMPELTEXT_GRUEN, 'ampelGruen');
            }
        } else {
            $this->Lageranzeige->nStatus = (int)$this->getConfigValue('global', 'artikel_lagerampel_keinlager');

            switch ($this->Lageranzeige->nStatus) {
                case 1:
                    $this->Lageranzeige->AmpelText = !empty($this->AttributeAssoc[\ART_ATTRIBUT_AMPELTEXT_GELB])
                        ? $this->AttributeAssoc[\ART_ATTRIBUT_AMPELTEXT_GELB]
                        : $lang->get('ampelGelb');
                    $this->setToParentStockText(\ART_ATTRIBUT_AMPELTEXT_GELB, 'ampelGelb');
                    break;
                case 0:
                    $this->Lageranzeige->AmpelText = !empty($this->AttributeAssoc[\ART_ATTRIBUT_AMPELTEXT_ROT])
                        ? $this->AttributeAssoc[\ART_ATTRIBUT_AMPELTEXT_ROT]
                        : $lang->get('ampelRot');
                    $this->setToParentStockText(\ART_ATTRIBUT_AMPELTEXT_ROT, 'ampelRot');
                    break;
                default:
                    $this->Lageranzeige->nStatus   = 2;
                    $this->Lageranzeige->AmpelText = !empty($this->AttributeAssoc[\ART_ATTRIBUT_AMPELTEXT_GRUEN])
                        ? $this->AttributeAssoc[\ART_ATTRIBUT_AMPELTEXT_GRUEN]
                        : $lang->get('ampelGruen');
                    $this->setToParentStockText(\ART_ATTRIBUT_AMPELTEXT_GRUEN, 'ampelGruen');
                    break;
            }
        }
        if ($this->bHasKonfig && Configurator::hasUnavailableGroup($this->oKonfig_arr)) {
            $this->Lageranzeige->cLagerhinweis['genau']          = $lang->get('productNotAvailable');
            $this->Lageranzeige->cLagerhinweis['verfuegbarkeit'] = $lang->get('productNotAvailable');

            $this->Lageranzeige->nStatus   = 0;
            $this->Lageranzeige->AmpelText = !empty($this->AttributeAssoc[\ART_ATTRIBUT_AMPELTEXT_ROT])
                ? $this->AttributeAssoc[\ART_ATTRIBUT_AMPELTEXT_ROT]
                : $lang->get('ampelRot');
        }

        return $this;
    }

    /**
     * Set stock text to parent product if it's a child and ampel_text_ attribute is set
     *
     * @param string $stockTextConstant
     * @param string $stockTextLangVar
     */
    private function setToParentStockText(string $stockTextConstant, string $stockTextLangVar): void
    {
        if ($this->kVaterArtikel > 0 && empty($this->AttributeAssoc[$stockTextConstant])) {
            $parentProduct = new self($this->getDB(), $this->getCustomerGroup(), $this->currency, $this->getCache());
            $parentProduct->fuelleArtikel(
                $this->kVaterArtikel,
                (object)['nAttribute' => 1],
                $this->kKundengruppe,
                $this->kSprache
            );
            $this->Lageranzeige->AmpelText = (!empty($parentProduct->AttributeAssoc[$stockTextConstant]))
                ? $parentProduct->AttributeAssoc[$stockTextConstant]
                : Shop::Lang()->get($stockTextLangVar, 'global');
        }
    }

    /**
     * @return $this
     */
    private function getWarehouse(): self
    {
        $options = [
            'cLagerBeachten'                => $this->cLagerBeachten,
            'cEinheit'                      => $this->cEinheit,
            'cLagerKleinerNull'             => $this->cLagerKleinerNull,
            'artikel_lagerampel_rot'        => $this->getConfigValue('global', 'artikel_lagerampel_rot'),
            'artikel_lagerampel_gruen'      => $this->getConfigValue('global', 'artikel_lagerampel_gruen'),
            'artikel_lagerampel_keinlager'  => $this->getConfigValue('global', 'artikel_lagerampel_keinlager'),
            'artikel_ampel_lagernull_gruen' => $this->getConfigValue('global', 'artikel_ampel_lagernull_gruen'),
            'attribut_ampeltext_gelb'       => !empty($this->AttributeAssoc[\ART_ATTRIBUT_AMPELTEXT_GELB])
                ? $this->AttributeAssoc[\ART_ATTRIBUT_AMPELTEXT_GELB]
                : Shop::Lang()->get('ampelGelb'),
            'attribut_ampeltext_gruen'      => !empty($this->AttributeAssoc[\ART_ATTRIBUT_AMPELTEXT_GRUEN])
                ? $this->AttributeAssoc[\ART_ATTRIBUT_AMPELTEXT_GRUEN]
                : Shop::Lang()->get('ampelGruen'),
            'attribut_ampeltext_rot'        => !empty($this->AttributeAssoc[\ART_ATTRIBUT_AMPELTEXT_ROT])
                ? $this->AttributeAssoc[\ART_ATTRIBUT_AMPELTEXT_ROT]
                : Shop::Lang()->get('ampelRot')
        ];

        $this->oWarenlager_arr = Warehouse::getByProduct($this->kArtikel, $this->kSprache, $options);

        return $this;
    }

    /**
     * @param int|float $scalePrice
     * @return $this
     */
    public function baueVPE($scalePrice = 0): self
    {
        $basepriceUnit = ($this->kGrundpreisEinheit > 0 && $this->fGrundpreisMenge > 0)
            ? \sprintf('%s %s', $this->fGrundpreisMenge, $this->cGrundpreisEinheitName)
            : $this->cVPEEinheit;
        $precision     = $this->getPrecision();
        $price         = ($scalePrice > 0) ? $scalePrice : $this->Preise->fVKNetto;
        $per           = ' ' . Shop::Lang()->get('vpePer') . ' ' . $basepriceUnit;
        $ust           = Tax::getSalesTax($this->kSteuerklasse);

        if (
            $this->Preise->oPriceRange !== null
            && Shop::getPageType() === \PAGE_ARTIKELLISTE
            && $this->Preise->oPriceRange->isRange()
        ) {
            if (
                $this->Preise->oPriceRange->rangeWidth() <=
                $this->getConfigValue('artikeluebersicht', 'articleoverview_pricerange_width')
            ) {
                $this->cLocalizedVPE[0] = Preise::getLocalizedPriceString(
                    Tax::getGross(
                        $this->Preise->oPriceRange->minNettoPrice / $this->fVPEWert,
                        $ust,
                        $precision
                    ),
                    $this->currency,
                    true,
                    $precision
                ) . ' - '
                    . Preise::getLocalizedPriceString(
                        Tax::getGross(
                            $this->Preise->oPriceRange->maxNettoPrice / $this->fVPEWert,
                            $ust,
                            $precision
                        ),
                        $this->currency,
                        true,
                        $precision
                    ) . $per;
                $this->cLocalizedVPE[1] = Preise::getLocalizedPriceString(
                    $this->Preise->oPriceRange->minNettoPrice / $this->fVPEWert,
                    $this->currency,
                    true,
                    $precision
                ) . ' - '
                    . Preise::getLocalizedPriceString(
                        $this->Preise->oPriceRange->maxNettoPrice / $this->fVPEWert,
                        $this->currency,
                        true,
                        $precision
                    ) . $per;
            } else {
                $this->cLocalizedVPE[0] =
                    Preise::getLocalizedPriceString(
                        Tax::getGross(
                            $this->Preise->oPriceRange->minNettoPrice / $this->fVPEWert,
                            $ust,
                            $precision
                        ),
                        $this->currency,
                        true,
                        $precision
                    ) . $per;
                $this->cLocalizedVPE[1] =
                    Preise::getLocalizedPriceString(
                        $this->Preise->oPriceRange->minNettoPrice / $this->fVPEWert,
                        $this->currency,
                        true,
                        $precision
                    ) . $per;
            }
        } else {
            $price = $this->Preise->oPriceRange !== null && $this->Preise->oPriceRange->isRange()
                ? $this->Preise->oPriceRange->minNettoPrice
                : $price;

            $this->cLocalizedVPE[0] = Preise::getLocalizedPriceString(
                Tax::getGross($price / $this->fVPEWert, $ust, $precision),
                $this->currency,
                true,
                $precision
            ) . $per;
            $this->cLocalizedVPE[1] = Preise::getLocalizedPriceString(
                $price / $this->fVPEWert,
                $this->currency,
                true,
                $precision
            ) . $per;
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function getScaleBasePrice(): self
    {
        $precision     = $this->getPrecision();
        $per           = ' ' . Shop::Lang()->get('vpePer') . ' ';
        $basePriceUnit = ProductHelper::getBasePriceUnit($this, $this->Preise->fPreis1, $this->Preise->nAnzahl1);

        $this->cStaffelpreisLocalizedVPE1[0] = Preise::getLocalizedPriceString(
            Tax::getGross(
                $basePriceUnit->fBasePreis,
                Tax::getSalesTax($this->kSteuerklasse),
                $precision
            ),
            $this->currency,
            true,
            $precision
        ) . $per . $basePriceUnit->cVPEEinheit;
        $this->cStaffelpreisLocalizedVPE1[1] = Preise::getLocalizedPriceString(
            $basePriceUnit->fBasePreis,
            $this->currency,
            true,
            $precision
        ) . $per . $basePriceUnit->cVPEEinheit;
        $this->fStaffelpreisVPE1[0]          = Tax::getGross(
            $basePriceUnit->fBasePreis,
            Tax::getSalesTax($this->kSteuerklasse),
            $precision
        );
        $this->fStaffelpreisVPE1[1]          = $basePriceUnit->fBasePreis;

        $basePriceUnit = ProductHelper::getBasePriceUnit($this, $this->Preise->fPreis2, $this->Preise->nAnzahl2);

        $this->cStaffelpreisLocalizedVPE2[0] = Preise::getLocalizedPriceString(
            Tax::getGross(
                $basePriceUnit->fBasePreis,
                Tax::getSalesTax($this->kSteuerklasse),
                $precision
            ),
            $this->currency,
            true,
            $precision
        ) . $per . $basePriceUnit->cVPEEinheit;
        $this->cStaffelpreisLocalizedVPE2[1] = Preise::getLocalizedPriceString(
            $basePriceUnit->fBasePreis,
            $this->currency,
            true,
            $precision
        ) . $per . $basePriceUnit->cVPEEinheit;
        $this->fStaffelpreisVPE2[0]          = Tax::getGross(
            $basePriceUnit->fBasePreis,
            Tax::getSalesTax($this->kSteuerklasse),
            $precision
        );
        $this->fStaffelpreisVPE2[1]          = $basePriceUnit->fBasePreis;

        $basePriceUnit = ProductHelper::getBasePriceUnit($this, $this->Preise->fPreis3, $this->Preise->nAnzahl3);

        $this->cStaffelpreisLocalizedVPE3[0] = Preise::getLocalizedPriceString(
            Tax::getGross(
                $basePriceUnit->fBasePreis,
                Tax::getSalesTax($this->kSteuerklasse),
                $precision
            ),
            $this->currency,
            true,
            $precision
        ) . $per . $basePriceUnit->cVPEEinheit;
        $this->cStaffelpreisLocalizedVPE3[1] = Preise::getLocalizedPriceString(
            $basePriceUnit->fBasePreis,
            $this->currency,
            true,
            $precision
        ) . $per . $basePriceUnit->cVPEEinheit;
        $this->fStaffelpreisVPE3[0]          = Tax::getGross(
            $basePriceUnit->fBasePreis,
            Tax::getSalesTax($this->kSteuerklasse),
            $precision
        );
        $this->fStaffelpreisVPE3[1]          = $basePriceUnit->fBasePreis;

        $basePriceUnit = ProductHelper::getBasePriceUnit($this, $this->Preise->fPreis4, $this->Preise->nAnzahl4);

        $this->cStaffelpreisLocalizedVPE4[0] = Preise::getLocalizedPriceString(
            Tax::getGross(
                $basePriceUnit->fBasePreis,
                Tax::getSalesTax($this->kSteuerklasse),
                $precision
            ),
            $this->currency,
            true,
            $precision
        ) . $per . $basePriceUnit->cVPEEinheit;
        $this->cStaffelpreisLocalizedVPE4[1] = Preise::getLocalizedPriceString(
            $basePriceUnit->fBasePreis,
            $this->currency,
            true,
            $precision
        ) . $per . $basePriceUnit->cVPEEinheit;
        $this->fStaffelpreisVPE4[0]          = Tax::getGross(
            $basePriceUnit->fBasePreis,
            Tax::getSalesTax($this->kSteuerklasse),
            $precision
        );
        $this->fStaffelpreisVPE4[1]          = $basePriceUnit->fBasePreis;

        $basePriceUnit = ProductHelper::getBasePriceUnit($this, $this->Preise->fPreis5, $this->Preise->nAnzahl5);

        $this->cStaffelpreisLocalizedVPE5[0] = Preise::getLocalizedPriceString(
            Tax::getGross(
                $basePriceUnit->fBasePreis,
                Tax::getSalesTax($this->kSteuerklasse),
                $precision
            ),
            $this->currency,
            true,
            $precision
        ) . $per . $basePriceUnit->cVPEEinheit;
        $this->cStaffelpreisLocalizedVPE5[1] = Preise::getLocalizedPriceString(
            $basePriceUnit->fBasePreis,
            $this->currency,
            true,
            $precision
        ) . $per . $basePriceUnit->cVPEEinheit;
        $this->fStaffelpreisVPE5[0]          = Tax::getGross(
            $basePriceUnit->fBasePreis,
            Tax::getSalesTax($this->kSteuerklasse),
            $precision
        );
        $this->fStaffelpreisVPE5[1]          = $basePriceUnit->fBasePreis;

        foreach ($this->Preise->fPreis_arr as $key => $price) {
            $basePriceUnit = ProductHelper::getBasePriceUnit($this, $price, $this->Preise->nAnzahl_arr[$key]);

            $this->cStaffelpreisLocalizedVPE_arr[] = [
                Preise::getLocalizedPriceString(
                    Tax::getGross(
                        $basePriceUnit->fBasePreis,
                        Tax::getSalesTax($this->kSteuerklasse),
                        $precision
                    ),
                    $this->currency,
                    true,
                    $precision
                ) . $per . $basePriceUnit->cVPEEinheit,
                Preise::getLocalizedPriceString(
                    $basePriceUnit->fBasePreis,
                    $this->currency,
                    true,
                    $precision
                ) . $per . $basePriceUnit->cVPEEinheit
            ];

            $this->fStaffelpreisVPE_arr[] = [
                Tax::getGross(
                    $basePriceUnit->fBasePreis,
                    Tax::getSalesTax($this->kSteuerklasse),
                    $precision
                ),
                $basePriceUnit->fBasePreis,
            ];

            $this->staffelPreis_arr[$key]['cBasePriceLocalized'] = $this->cStaffelpreisLocalizedVPE_arr[$key] ?? null;
        }

        return $this;
    }

    /**
     * @param Artikel|object|null $product
     * @return bool
     */
    public function aufLagerSichtbarkeit($product = null): bool
    {
        $product = $product ?? $this;
        $conf    = (int)$this->getConfigValue('global', 'artikel_artikelanzeigefilter');
        if ($conf === \EINSTELLUNGEN_ARTIKELANZEIGEFILTER_LAGER) {
            if (isset($product->cLagerVariation) && $product->cLagerVariation === 'Y') {
                return true;
            }
            if ($product->fLagerbestand <= 0 && $product->cLagerBeachten === 'Y') {
                return false;
            }
        }
        if ($conf === \EINSTELLUNGEN_ARTIKELANZEIGEFILTER_LAGERNULL) {
            if (
                (isset($product->cLagerVariation) && $product->cLagerVariation === 'Y')
                || $product->cLagerKleinerNull === 'Y'
            ) {
                return true;
            }
            if ($product->fLagerbestand <= 0 && $product->cLagerBeachten === 'Y') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Artikel|stdClass|null $product
     * @return stdClass
     * @since 4.06.7
     */
    public function getStockInfo($product = null): stdClass
    {
        $product = $product ?? $this;
        $result  = (object)[
            'inStock'   => false,
            'notExists' => false,
        ];

        switch ((int)$this->getConfigValue('global', 'artikel_artikelanzeigefilter')) {
            case \EINSTELLUNGEN_ARTIKELANZEIGEFILTER_LAGER:
                if (
                    (isset($product->cLagerVariation) && $product->cLagerVariation === 'Y')
                    || $product->fLagerbestand > 0
                    || $product->cLagerBeachten !== 'Y'
                ) {
                    $result->inStock = true;
                } else {
                    $result->inStock   = false;
                    $result->notExists = true;
                }
                break;
            case \EINSTELLUNGEN_ARTIKELANZEIGEFILTER_LAGERNULL:
                if (
                    (isset($product->cLagerVariation) && $product->cLagerVariation === 'Y')
                    || $product->fLagerbestand > 0
                    || $product->cLagerBeachten !== 'Y'
                    || $product->cLagerKleinerNull === 'Y'
                ) {
                    $result->inStock = true;
                } else {
                    $result->inStock   = false;
                    $result->notExists = true;
                }
                break;
            case \EINSTELLUNGEN_ARTIKELANZEIGEFILTER_ALLE:
            default:
                if (
                    (isset($product->cLagerVariation) && $product->cLagerVariation === 'Y')
                    || $product->fLagerbestand > 0
                    || $product->cLagerBeachten !== 'Y'
                    || $product->cLagerKleinerNull === 'Y'
                ) {
                    $result->inStock = true;
                }
        }

        return $result;
    }

    /**
     * @param string $name
     * @return bool|string
     */
    public function gibAttributWertNachName(string $name)
    {
        if (
            $this->kArtikel === null
            || $this->kArtikel <= 0
            || LanguageHelper::isDefaultLanguageActive(languageID: $this->kSprache)
        ) {
            return false;
        }
        $att = $this->getDB()->select('tattribut', 'kArtikel', $this->kArtikel, 'cName', $name);
        if ($att !== null && $this->kSprache > 0 && isset($att->kAttribut) && $att->kAttribut > 0) {
            $att   = $this->getDB()->select(
                'tattributsprache',
                'kAttribut',
                $att->kAttribut,
                'kSprache',
                $this->kSprache
            );
            $value = $att->cStringWert;
            if ($att->cTextWert) {
                $value = $att->cTextWert;
            }

            return $value;
        }

        return false;
    }

    /**
     * @param int $show
     * @return $this
     */
    public function berechneSieSparenX(int $show = 1): self
    {
        if ($this->fUVP <= 0) {
            return $this;
        }
        $this->SieSparenX = new stdClass();
        if (!$this->getCustomerGroup()->mayViewPrices()) {
            return $this;
        }
        $this->SieSparenX->anzeigen = $show;
        if ($this->getCustomerGroup()->isMerchant()) {
            $this->fUVP /= (1 + Tax::getSalesTax($this->kSteuerklasse) / 100);

            $this->SieSparenX->nProzent    = \round(
                (($this->fUVP - $this->Preise->fVKNetto) * 100) / $this->fUVP,
                2
            );
            $this->SieSparenX->fSparbetrag = $this->fUVP - $this->Preise->fVKNetto;
        } else {
            $this->SieSparenX->nProzent    = \round(
                (($this->fUVP - Tax::getGross(
                    $this->Preise->fVKNetto,
                    Tax::getSalesTax($this->kSteuerklasse)
                )) * 100)
                / $this->fUVP,
                2
            );
            $this->SieSparenX->fSparbetrag = $this->fUVP - Tax::getGross(
                $this->Preise->fVKNetto,
                Tax::getSalesTax($this->kSteuerklasse)
            );
        }
        $this->SieSparenX->cLocalizedSparbetrag = Preise::getLocalizedPriceString($this->SieSparenX->fSparbetrag);

        return $this;
    }

    /**
     * @param string|null $countryCode ISO Alpha-2 Country-Code e.g. DE
     * @param int|null    $shippingID special shippingID, if null will select cheapest
     * @return Versandart|object|null - cheapest shipping except shippings that offer cash payment or that are excluded
     */
    public function getFavourableShipping(string $countryCode = null, int $shippingID = null)
    {
        if (
            !empty($_SESSION['Versandart']->kVersandart)
            && isset($_SESSION['Versandart']->nMinLiefertage)
            && $countryCode === $this->cCachedCountryCode
        ) {
            return $_SESSION['Versandart'];
        }
        // if nothing changed, return cached shipping-object
        if ($this->oFavourableShipping !== null && $countryCode === $this->cCachedCountryCode) {
            return $this->oFavourableShipping;
        }
        // if shippingID is given - use this shipping
        if ($shippingID !== null) {
            $this->favourableShippingID = $shippingID;
            $this->oFavourableShipping  = new Versandart($this->favourableShippingID);

            return $this->oFavourableShipping;
        }
        if ($countryCode === null && isset($_SESSION['cLieferlandISO'])) {
            $countryCode = (string)$_SESSION['cLieferlandISO'];
        }
        if ($this->fGewicht === null) {
            $this->fGewicht = 0;
        }
        $hasProductShippingCost = $this->isUsedForShippingCostCalculation($countryCode) ? 'N' : 'Y';
        $dep                    = " AND va.cNurAbhaengigeVersandart = '" . $hasProductShippingCost . "' ";

        // cheapest shipping except shippings that offer cash payment
        $shipping = $this->getDB()->getSingleObject(
            'SELECT va.kVersandart, IF(vas.fPreis IS NOT NULL, vas.fPreis, va.fPreis) AS minPrice, va.nSort
                FROM tversandart va
                LEFT JOIN tversandartstaffel vas
                    ON vas.kVersandart = va.kVersandart
                WHERE cIgnoreShippingProposal != \'Y\'
                AND va.cLaender LIKE :ccode
                AND (va.cVersandklassen = \'-1\'
                    OR va.cVersandklassen RLIKE :sclass)
                AND (va.cKundengruppen = \'-1\'
                    OR FIND_IN_SET(:cgid, REPLACE(va.cKundengruppen, \';\', \',\')) > 0)
                AND va.kVersandart NOT IN (
                    SELECT vaza.kVersandart
                        FROM tversandartzahlungsart vaza
                        WHERE kZahlungsart = 6)
                AND (
                    va.kVersandberechnung = 1
                    OR va.kVersandberechnung = 4
                    OR ( va.kVersandberechnung = 2 AND vas.fBis > 0 AND :wght <= vas.fBis )
                    OR ( va.kVersandberechnung = 3
                        AND vas.fBis = (
                          SELECT MIN(fBis)
                            FROM tversandartstaffel
                            WHERE fBis > :net
                              AND tversandartstaffel.kVersandart = va.kVersandart
                          )
                        )
                    ) ' . $dep . '
                ORDER BY minPrice, nSort ASC LIMIT 1',
            [
                'ccode'  => '%' . $countryCode . '%',
                'cgid'   => $this->kKundengruppe ?? $this->getCustomerGroup()->getID(),
                'sclass' => '^([0-9 -]* )?' . $this->kVersandklasse . ' ',
                'wght'   => $this->fGewicht,
                'net'    => $this->Preise->fVKNetto
            ]
        );
        if ($shipping === null) {
            return null;
        }
        $this->favourableShippingID = (int)$shipping->kVersandart;
        $this->oFavourableShipping  = new Versandart($this->favourableShippingID);

        return $this->oFavourableShipping;
    }

    /**
     * @param string|null    $countryCode - ISO Alpha-2 Country-Code e.g. DE
     * @param null|int|float $purchaseQuantity
     * @param null|int|float $stockLevel
     * @param null|string    $languageISO
     * @param int|null       $shippingID gets DeliveryTime for a special shipping
     * @return string
     * @throws \Exception
     */
    public function getDeliveryTime(
        ?string $countryCode,
        $purchaseQuantity = null,
        $stockLevel = null,
        ?string $languageISO = null,
        ?int $shippingID = null
    ): string {
        if (!isset($_SESSION['cISOSprache'])) {
            $defaultLanguage = LanguageHelper::getDefaultLanguage();
            if ($languageISO !== null) {
                foreach (LanguageHelper::getAllLanguages() as $language) {
                    if ($language->getCode() === $languageISO) {
                        $defaultLanguage = $language;
                        break;
                    }
                }
            }
            Shop::setLanguage($defaultLanguage->getId(), $defaultLanguage->getCode());
        }
        if ($purchaseQuantity !== null) {
            $purchaseQuantity = (float)$purchaseQuantity;
        } else {
            $purchaseQuantity = ($this->fAbnahmeintervall > 0)
                ? $this->fAbnahmeintervall
                : 1; // + $this->getPurchaseQuantityFromCart();
        }
        if (!\is_numeric($purchaseQuantity) || $purchaseQuantity <= 0) {
            $purchaseQuantity = 1;
        }
        $stockLevel  = \is_numeric($stockLevel) ? (float)$stockLevel : $this->fLagerbestand;
        $favShipping = $this->getFavourableShipping($countryCode, $shippingID);
        if ($favShipping === null || $this->inWarenkorbLegbar <= 0) {
            return '';
        }
        // set default values
        $minDeliveryDays = $favShipping->nMinLiefertage ?? 2;
        $maxDeliveryDays = $favShipping->nMaxLiefertage ?? 3;
        // get all pieces (even invisible) to calc delivery
        $nAllPieces = empty($this->kStueckliste) ? 0 : (int)($this->getDB()->getSingleObject(
            'SELECT COUNT(tstueckliste.kArtikel) AS nAnzahl
                FROM tstueckliste
                JOIN tartikel
                     ON tstueckliste.kArtikel = tartikel.kArtikel
                WHERE tstueckliste.kStueckliste = :plid',
            ['plid' => $this->kStueckliste]
        )->nAnzahl ?? 0);
        // check if this is a set product - if so, calculate the delivery time from the set of products
        // we don't have loaded the list of pieces yet, do so!
        $partList = null;
        if (
            (!empty($this->kStueckliste) && empty($this->oStueckliste_arr)) ||
            (!empty($this->oStueckliste_arr) && \count($this->oStueckliste_arr) !== $nAllPieces)
        ) {
            $resetArray             = true;
            $partList               = $this->oStueckliste_arr;
            $this->oStueckliste_arr = [];
            $this->holeStueckliste($this->kKundengruppe ?? $this->getCustomerGroup()->getID(), true);
        }
        $isPartsList = !empty($this->oStueckliste_arr) && !empty($this->kStueckliste);
        if ($isPartsList) {
            $piecesNotInShop = (int)($this->getDB()->getSingleObject(
                'SELECT COUNT(tstueckliste.kArtikel) AS nAnzahl
                    FROM tstueckliste
                    WHERE tstueckliste.kStueckliste = :plid',
                ['plid' => $this->kStueckliste]
            )->nAnzahl ?? 0) - $nAllPieces;

            if ($piecesNotInShop > 0) {
                // this list has potentially invisible parts and can't calculated correctly
                // handle this parts list as a normal product
                $isPartsList = false;
            } else {
                // all parts of this list are accessible
                /** @var Artikel $piece */
                foreach ($this->oStueckliste_arr as $piece) {
                    if (!empty($piece->kArtikel)) {
                        $piece->getDeliveryTime(
                            $countryCode,
                            $purchaseQuantity * (float)$piece->fAnzahl_stueckliste,
                            null,
                            null,
                            $shippingID
                        );
                        if (isset($piece->nMaxDeliveryDays) && $piece->nMaxDeliveryDays > $maxDeliveryDays) {
                            $maxDeliveryDays = $piece->nMaxDeliveryDays;
                        }
                        if (isset($piece->nMinDeliveryDays) && $piece->nMinDeliveryDays > $minDeliveryDays) {
                            $minDeliveryDays = $piece->nMinDeliveryDays;
                        }
                    }
                }
            }
            if (!empty($resetArray)) {
                $this->oStueckliste_arr = $partList;
            }
        }
        if ($this->bHasKonfig && !empty($this->oKonfig_arr)) {
            $parentMinDeliveryDays = $minDeliveryDays;
            $parentMaxDeliveryDays = $maxDeliveryDays;
            foreach ($this->oKonfig_arr as $gruppe) {
                /** @var Item $piece */
                foreach ($gruppe->oItem_arr as $piece) {
                    $konfigItemProduct = $piece->getArtikel();
                    if ($konfigItemProduct !== null) {
                        $konfigItemProduct->getDeliveryTime(
                            $countryCode,
                            $purchaseQuantity * (float)$piece->getInitial(),
                            null,
                            null,
                            $shippingID
                        );
                        // find shortest shipping time in configuration
                        if (isset($konfigItemProduct->nMaxDeliveryDays)) {
                            $maxDeliveryDays = \min($maxDeliveryDays, $konfigItemProduct->nMaxDeliveryDays);
                        }
                        if (isset($konfigItemProduct->nMinDeliveryDays)) {
                            $minDeliveryDays = \min($minDeliveryDays, $konfigItemProduct->nMinDeliveryDays);
                        }
                    }
                }
            }
            $minDeliveryDays = \max($minDeliveryDays, $parentMinDeliveryDays);
            $maxDeliveryDays = \max($maxDeliveryDays, $parentMaxDeliveryDays);
        }
        $customProcessingTime = $this->getFunctionalAttributevalue('processingtime', true);
        if ((!$isPartsList && $this->nBearbeitungszeit > 0) || $customProcessingTime > 0) {
            $processingTime  = $this->nBearbeitungszeit > 0
                ? $this->nBearbeitungszeit
                : $customProcessingTime;
            $minDeliveryDays += $processingTime;
            $maxDeliveryDays += $processingTime;
        }
        // product coming soon? then add remaining days. stocklevel doesnt matter, see #13604
        if ($this->nErscheinendesProdukt && new DateTime($this->dErscheinungsdatum) > new DateTime()) {
            $daysToRelease = $this->calculateDaysBetween($this->dErscheinungsdatum, \date('Y-m-d'));
            if ($isPartsList) {
                // if this is a parts list...
                if ($minDeliveryDays < $daysToRelease) {
                    // ...and release date is after min delivery date from list parts,
                    // then release date is the new min delivery date
                    $offset          = $maxDeliveryDays - $minDeliveryDays;
                    $minDeliveryDays = $daysToRelease;
                    $maxDeliveryDays = $minDeliveryDays + $offset;
                }
            } else {
                $minDeliveryDays += $daysToRelease;
                $maxDeliveryDays += $daysToRelease;
            }
        } elseif (
            !$isPartsList
            && ($this->cLagerBeachten === 'Y' && ($stockLevel <= 0 || ($stockLevel - $purchaseQuantity < 0)))
        ) {
            $customDeliveryTime = $this->getFunctionalAttributevalue('deliverytime_outofstock', true);
            $customSupplyTime   = $this->getFunctionalAttributevalue('supplytime', true);
            if ($customDeliveryTime > 0) {
                // prio on attribute "deliverytime_outofstock" for simple deliverytimes
                $deliverytime_outofstock = $customDeliveryTime;
                $minDeliveryDays         = $deliverytime_outofstock; //overrides parcel and processingtime!
                $maxDeliveryDays         = $deliverytime_outofstock; //overrides parcel and processingtime!
            } elseif (
                ($this->nAutomatischeLiefertageberechnung === 0 && $this->nLiefertageWennAusverkauft > 0)
                || $customSupplyTime > 0
            ) {
                // attribute "supplytime" for merchants who do not use JTL-Wawis purchase-system
                $supplyTime      = ($this->nLiefertageWennAusverkauft > 0)
                    ? $this->nLiefertageWennAusverkauft
                    : $customSupplyTime;
                $minDeliveryDays += $supplyTime;
                $maxDeliveryDays += $supplyTime;
            } elseif (
                $this->dZulaufDatum !== null
                && $this->fZulauf > 0
                && new DateTime($this->dZulaufDatum) >= new DateTime()
            ) {
                // supplierOrder incoming?
                $offset          = $this->calculateDaysBetween($this->dZulaufDatum, \date('Y-m-d'));
                $minDeliveryDays += $offset;
                $maxDeliveryDays += $offset;
            } elseif ($this->fLieferzeit > 0 && !$this->nErscheinendesProdukt) {
                $minDeliveryDays += (int)$this->fLieferzeit;
                $maxDeliveryDays += (int)$this->fLieferzeit;
            }
        }
        // set estimatedDeliverytime text
        $estimatedDelivery      = ShippingMethod::getDeliverytimeEstimationText($minDeliveryDays, $maxDeliveryDays);
        $this->nMinDeliveryDays = $minDeliveryDays;
        $this->nMaxDeliveryDays = $maxDeliveryDays;

        return $estimatedDelivery;
    }

    /**
     * Gets total quantity of product in shoppingcart.
     *
     * @return float|int - 0 if shoppingcart does not contain product. Else total product-quantity in shoppingcart.
     */
    public function getPurchaseQuantityFromCart()
    {
        return reduce_left(
            select(
                Frontend::getCart()->PositionenArr,
                function ($item): bool {
                    return $item->nPosTyp === \C_WARENKORBPOS_TYP_ARTIKEL
                        && (int)$item->Artikel->kArtikel === $this->kArtikel;
                }
            ),
            static function ($value, $index, $collection, $reduction) {
                return $reduction + $value->nAnzahl;
            },
            0.0
        );
    }

    /**
     * @return bool
     */
    public function isChild(): bool
    {
        return (int)$this->kVaterArtikel > 0;
    }

    /**
     * @param string $type
     * @return stdClass
     */
    private function mapMediaType(string $type): stdClass
    {
        $mapping            = new stdClass();
        $mapping->videoType = null;
        switch ($type) {
            case '.bmp':
            case '.gif':
            case '.ico':
            case '.jpg':
            case '.png':
            case '.tga':
                $mapping->cName = Shop::Lang()->get('tabPicture', 'media');
                $mapping->nTyp  = 1;
                break;
            case '.wav':
            case '.mp3':
            case '.wma':
            case '.m4a':
            case '.aac':
            case '.ra':
                $mapping->cName = Shop::Lang()->get('tabMusic', 'media');
                $mapping->nTyp  = 2;
                break;
            case '.ogg':
            case '.ac3':
            case '.fla':
            case '.swf':
            case '.avi':
            case '.mov':
            case '.h264':
            case '.mp4':
            case '.flv':
            case '.3gp':
                $mapping->cName     = Shop::Lang()->get('tabVideo', 'media');
                $mapping->nTyp      = 3;
                $mapping->videoType = \strtolower(\str_replace('.', '', $type));
                break;
            case '.pdf':
                $mapping->cName = Shop::Lang()->get('tabPdf', 'media');
                $mapping->nTyp  = 5;
                break;
            case '.zip':
            case '.rar':
            case '.tar':
            case '.gz':
            case '.tar.gz':
            case '':
            default:
                $mapping->cName = Shop::Lang()->get('tabMisc', 'media');
                $mapping->nTyp  = 4;
                break;
        }

        return $mapping;
    }

    /**
     * @return array
     */
    public function holeAehnlicheArtikel(): array
    {
        return $this->buildProductsFromSimilarProducts();
    }

    /**
     * build actual similar products
     *
     * @return array
     */
    private function buildProductsFromSimilarProducts(): array
    {
        $data     = $this->similarProducts; // this was created at fuelleArtikel() before and therefore cached
        $products = $data['oArtikelArr'];
        $keys     = $data['kArtikelXSellerKey_arr'];
        $similar  = [];
        if (\is_array($products) && \count($products) > 0) {
            $defaultOptions = self::getDefaultOptions();
            foreach ($products as $productData) {
                $product = new self($this->getDB(), $this->getCustomerGroup(), $this->currency, $this->getCache());
                $product->fuelleArtikel(
                    ($productData->kVaterArtikel > 0)
                        ? (int)$productData->kVaterArtikel
                        : (int)$productData->kArtikel,
                    $defaultOptions,
                    $this->kKundengruppe,
                    $this->kSprache
                );
                if ($product->kArtikel > 0) {
                    $similar[] = $product;
                }
            }
        }
        \executeHook(\HOOK_ARTIKEL_INC_AEHNLICHEARTIKEL, [
            'kArtikel'     => $this->kArtikel,
            'oArtikel_arr' => &$similar
        ]);

        if (\count($similar) > 0 && \is_array($keys) && \count($keys) > 0) {
            // remove x-sellers
            foreach ($similar as $i => $product) {
                foreach ($keys as $xsellID) {
                    if ($product->kArtikel === (int)$xsellID) {
                        unset($similar[$i]);
                    }
                }
            }
        }

        return $similar;
    }

    /**
     * get list of similar products
     *
     * @return array
     */
    public function getSimilarProducts(): array
    {
        $productID = (int)$this->kArtikel;
        $return    = [
            'kArtikelXSellerKey_arr' => [],
            'oArtikelArr'            => [],
            'Standard'               => null,
            'Kauf'                   => null,
        ];
        // Gibt es X-Seller? Aus der Artikelmenge der änhlichen Artikel, dann alle X-Seller rausfiltern
        $xSeller  = ProductHelper::getXSellingIDs($productID, $this->nIstVater > 0, $this->conf['artikeldetails']);
        $xSellIDs = [];
        if ($xSeller !== null) {
            $return['Standard'] = $xSeller->Standard;
            $return['Kauf']     = $xSeller->Kauf ?? null;
            foreach ($xSeller->Standard->XSellGruppen as $group) {
                foreach ($group->productIDs as $item) {
                    if (!\in_array($item, $xSellIDs, true)) {
                        $xSellIDs[] = $item;
                    }
                }
            }
        }
        $xSellSQL                         = \count($xSellIDs) > 0
            ? ' AND tartikel.kArtikel NOT IN (' . \implode(',', $xSellIDs) . ') '
            : '';
        $return['kArtikelXSellerKey_arr'] = $xSellIDs;
        if ($productID === 0) {
            return $return;
        }
        $limit = (int)$this->getConfigValue('artikeldetails', 'artikeldetails_aehnlicheartikel_anzahl');
        if ($limit < 1) {
            $return['oArtikelArr'] = [];

            return $return;
        }
        $customerGroupID       = $this->kKundengruppe ?? $this->getCustomerGroup()->getID();
        $stockFilterSQL        = Shop::getProductFilter()->getFilterSQL()->getStockFilterSQL();
        $return['oArtikelArr'] = $this->getDB()->getObjects(
            'SELECT tartikelmerkmal.kArtikel, tartikel.kVaterArtikel
                FROM tartikelmerkmal
                    JOIN tartikel
                        ON tartikel.kArtikel = tartikelmerkmal.kArtikel
                        AND tartikel.kVaterArtikel != :kArtikel
                        AND (tartikel.nIstVater = 1 OR tartikel.kEigenschaftKombi = 0)
                    JOIN tartikelmerkmal similarMerkmal
                        ON similarMerkmal.kArtikel = :kArtikel
                        AND similarMerkmal.kMerkmal = tartikelmerkmal.kMerkmal
                        AND similarMerkmal.kMerkmalWert = tartikelmerkmal.kMerkmalWert
                    LEFT JOIN tartikelsichtbarkeit
                        ON tartikelsichtbarkeit.kArtikel = tartikel.kArtikel
                        AND tartikelsichtbarkeit.kKundengruppe = :customerGroupID
                WHERE tartikelsichtbarkeit.kArtikel IS NULL
                    AND tartikelmerkmal.kArtikel != :kArtikel
                    ' . $stockFilterSQL . '
                    ' . $xSellSQL . '
                GROUP BY tartikelmerkmal.kArtikel
                ORDER BY COUNT(tartikelmerkmal.kMerkmal) DESC
                LIMIT :lmt',
            [
                'kArtikel'        => $productID,
                'customerGroupID' => $customerGroupID,
                'lmt'             => $limit
            ]
        );
        if (\count($return['oArtikelArr']) < 1) {
            // Falls es keine Merkmale gibt, in tsuchcachetreffer und ttagartikel suchen
            $return['oArtikelArr'] = $this->getDB()->getObjects(
                'SELECT tsuchcachetreffer.kArtikel, tartikel.kVaterArtikel
                    FROM
                    (
                        SELECT kSuchCache
                        FROM tsuchcachetreffer
                        WHERE kArtikel = :pid
                            AND nSort <= 10
                    ) AS ssSuchCache
                    JOIN tsuchcachetreffer
                        ON tsuchcachetreffer.kSuchCache = ssSuchCache.kSuchCache
                        AND tsuchcachetreffer.kArtikel != :pid
                    LEFT JOIN tartikelsichtbarkeit
                        ON tsuchcachetreffer.kArtikel = tartikelsichtbarkeit.kArtikel
                        AND tartikelsichtbarkeit.kKundengruppe = :cgid
                    JOIN tartikel
                        ON tartikel.kArtikel = tsuchcachetreffer.kArtikel
                        AND tartikel.kVaterArtikel != :pid
                    WHERE tartikelsichtbarkeit.kArtikel IS NULL
                        ' . $stockFilterSQL . '
                        ' . $xSellSQL . '
                    GROUP BY tsuchcachetreffer.kArtikel
                    ORDER BY COUNT(*) DESC
                    LIMIT :lmt',
                [
                    'pid'  => $productID,
                    'cgid' => $customerGroupID,
                    'lmt'  => $limit
                ]
            );
        }
        foreach ($return['oArtikelArr'] as $item) {
            $item->kArtikel      = (int)$item->kArtikel;
            $item->kVaterArtikel = (int)$item->kVaterArtikel;
        }

        return $return;
    }

    /**
     * @param int $parentID
     * @param int $visibilityFilter
     * @return bool|int
     */
    public static function beachteVarikombiMerkmalLagerbestand(int $parentID, int $visibilityFilter = 0)
    {
        if ($parentID <= 0) {
            return false;
        }
        $filterSQL = $visibilityFilter !== 1
            ? ' AND (tartikel.fLagerbestand > 0
                    OR tartikel.cLagerBeachten = \'N\'
                    OR tartikel.cLagerKleinerNull = \'Y\')'
            : '';
        Shop::Container()->getDB()->delete('tartikelmerkmal', 'kArtikel', $parentID);

        return Shop::Container()->getDB()->getAffectedRows(
            'INSERT INTO tartikelmerkmal
                (SELECT tartikelmerkmal.kMerkmal, tartikelmerkmal.kMerkmalWert, ' . $parentID . '
                    FROM tartikelmerkmal
                    JOIN tartikel
                        ON tartikel.kArtikel = tartikelmerkmal.kArtikel
                    WHERE tartikel.kVaterArtikel = ' . $parentID . '
                    ' . $filterSQL . '
                    GROUP BY tartikelmerkmal.kMerkmalWert)'
        );
    }

    /**
     * @param stdClass      $tmpProduct
     * @param Customer|null $customer
     * @return void
     */
    private function setCategoryDiscounts(stdClass $tmpProduct, ?Customer $customer = null): void
    {
        $customer = $customer ?: Frontend::getCustomer();
        $cacheID  = 'hasCategoryDiscounts';
        if (!Shop::has($cacheID)) {
            Shop::set(
                $cacheID,
                $this->getDB()->getSingleInt(
                    'SELECT 1 AS cnt
                        FROM tartikelkategorierabatt
                        LIMIT 1',
                    'cnt',
                ) > 0
            );
        }
        if (Shop::get($cacheID)) {
            // Existiert für diese Kundengruppe ein Kategorierabatt?
            $categoryDiscount = $this->getDB()->getObjects(
                'SELECT COALESCE(tartikelkategorierabatt.kKundengruppe, :customerGroupID) AS kKundengruppe,
                        MAX(GREATEST(
                            COALESCE(category_customerdiscount.discount, 0),
                            COALESCE(tartikelkategorierabatt.fRabatt, 0))
                        ) AS fRabatt
                    FROM tkategorieartikel
                    LEFT JOIN category_customerdiscount ON category_customerdiscount.customerId = :customerID
                        AND tkategorieartikel.kKategorie = category_customerdiscount.categoryId
                    LEFT JOIN tartikelkategorierabatt ON tkategorieartikel.kArtikel = tartikelkategorierabatt.kArtikel
                        AND tkategorieartikel.kKategorie = tartikelkategorierabatt.kKategorie
                    WHERE tkategorieartikel.kArtikel = :productID
                    GROUP BY 1',
                [
                    'customerID'      => $customer->getID(),
                    'customerGroupID' => $customer->getGroupID(),
                    'productID'       => $tmpProduct->kEigenschaftKombi > 0
                        ? $tmpProduct->kVaterArtikel
                        : $tmpProduct->kArtikel,
                ]
            );
        } elseif ($customer->getID() > 0) {
            // Existiert für den Kunden ein individueller Kategorierabatt
            $categoryDiscount = $this->getDB()->getObjects(
                'SELECT :customerGroupID AS kKundengruppe, discount AS fRabatt
                    FROM tkategorieartikel
                    INNER JOIN category_customerdiscount ON category_customerdiscount.customerId = :customerID
                        AND tkategorieartikel.kKategorie = category_customerdiscount.categoryId
                    WHERE tkategorieartikel.kArtikel = :productID',
                [
                    'customerID'      => $customer->getID(),
                    'customerGroupID' => $customer->getGroupID(),
                    'productID'       => $tmpProduct->kEigenschaftKombi > 0
                        ? $this->kVaterArtikel
                        : $tmpProduct->kArtikel,
                ]
            );
        } else {
            return;
        }

        foreach ($categoryDiscount as $discount) {
            $this->categoryDiscounts[(int)$discount->kKundengruppe] = (float)$discount->fRabatt;
        }
    }

    /**
     * Get the maximum discount available for this product respecting current user group + user + category discount
     *
     * @param int $customerGroupID
     * @return float
     */
    public function getDiscount(int $customerGroupID = 0)
    {
        $customerGroupID  = $customerGroupID ?: ($this->kKundengruppe ?? $this->getCustomerGroup()->getID());
        $discounts        = [];
        $maxDiscount      = 0.0;
        $categoryDiscount = $this->categoryDiscounts[$customerGroupID] ?? 0.0;
        if ($categoryDiscount > 0) {
            $discounts[] = $categoryDiscount;
        }
        // Existiert für diese Kundengruppe ein Rabatt?
        $customerGroup = $this->getCustomerGroup()->getID() === $customerGroupID
            ? $this->getCustomerGroup()
            : new CustomerGroup($customerGroupID, $this->getDB());
        if ($customerGroup->getDiscount() != 0) {
            $discounts[] = $customerGroup->getDiscount();
        }
        // Existiert für diesen Kunden ein Rabatt?
        $customer = Frontend::getCustomer();
        if ($customer->getID() > 0 && $customer->fRabatt != 0) {
            $discounts[] = $customer->fRabatt;
        }
        // Maximalen Rabatt setzen
        if (\count($discounts) > 0) {
            $maxDiscount = (float)\max($discounts);
        }

        return $maxDiscount;
    }

    /**
     * @param int|float $taxRate
     * @return int|string
     */
    private function formatTax($taxRate)
    {
        if ($taxRate < 0) {
            return '';
        }
        $mwst2 = \number_format((float)$taxRate, 2, ',', '.');
        $mwst1 = \number_format((float)$taxRate, 1, ',', '.');
        if ($mwst2[\mb_strlen($mwst2) - 1] !== '0') {
            return $mwst2;
        }
        if ($mwst1[\mb_strlen($mwst1) - 1] !== '0') {
            return $mwst1;
        }

        return (int)$taxRate;
    }

    /**
     * @param int|bool $net
     * @return string
     */
    public function gibMwStVersandString($net): string
    {
        if (!isset($_SESSION['Kundengruppe'])) {
            $_SESSION['Kundengruppe'] = (new CustomerGroup(0, $this->getDB()))->loadDefaultGroup();
            $net                      = $this->getCustomerGroup()->isMerchant();
        }
        $customerGroupID = $this->kKundengruppe ?? $this->getCustomerGroup()->getID();
        if (!isset($_SESSION['Link_Versandseite'])) {
            Frontend::setSpecialLinks();
        }
        $net      = (bool)$net;
        $inklexkl = Shop::Lang()->get($net === true ? 'excl' : 'incl', 'productDetails');
        $mwst     = $this->formatTax(Tax::getSalesTax($this->kSteuerklasse));
        $ust      = '';
        $markup   = '';
        $langCode = Shop::getLanguageCode();
        if (!isset($_SESSION['Link_Versandseite'][$langCode])) {
            return '';
        }
        if ($this->getConfigValue('global', 'global_versandhinweis') === 'zzgl') {
            $markup    = ', ';
            $countries = $this->gibMwStVersandLaenderString(true, $customerGroupID);
            if ($countries && $this->getConfigValue('global', 'global_versandfrei_anzeigen') === 'Y') {
                if ($this->getConfigValue('global', 'global_versandkostenfrei_darstellung') === 'D') {
                    $countriesAssoc = $this->gibMwStVersandLaenderString(false, $customerGroupID);
                    $countryString  = '';
                    foreach ($countriesAssoc as $cISO => $countryName) {
                        $countryString .= '<abbr title="' . $countryName . '">' . $cISO . '</abbr> ';
                    }

                    $markup .= Shop::Lang()->get('noShippingcostsTo') . ' ' .
                        Shop::Lang()->get('noShippingCostsAtExtended', 'basket', '') .
                        \trim($countryString) . ', ' . Shop::Lang()->get('else') . ' ' .
                        Shop::Lang()->get('plus', 'basket') .
                        ' <a href="' . $_SESSION['Link_Versandseite'][$langCode] .
                        '" rel="nofollow" class="shipment">' .
                        Shop::Lang()->get('shipping', 'basket') . '</a>';
                } else {
                    $markup .= '<a href="'
                        . $_SESSION['Link_Versandseite'][$langCode]
                        . '" rel="nofollow" class="shipment" data-toggle="tooltip" data-placement="left" title="'
                        . $countries . ', ' . Shop::Lang()->get('else') . ' '
                        . Shop::Lang()->get('plus', 'basket') . ' ' . Shop::Lang()->get('shipping', 'basket') . '">'
                        . Shop::Lang()->get('noShippingcostsTo') . '</a>';
                }
            } else {
                $markup .= Shop::Lang()->get('plus', 'basket')
                    . ' <a href="' . $_SESSION['Link_Versandseite'][$langCode]
                    . '" rel="nofollow" class="shipment">'
                    . Shop::Lang()->get('shipping', 'basket') . '</a>';
            }
        } elseif ($this->getConfigValue('global', 'global_versandhinweis') === 'inkl') {
            $markup = ', ' . Shop::Lang()->get('incl', 'productDetails')
                . ' <a href="' . $_SESSION['Link_Versandseite'][$langCode]
                . '" rel="nofollow" class="shipment">'
                . Shop::Lang()->get('shipping', 'basket') . '</a>';
        }
        if ($this->getConfigValue('global', 'global_ust_auszeichnung') === 'auto') {
            $ust = $inklexkl . ' ' . $mwst . '% ' . Shop::Lang()->get('vat', 'productDetails');
        } elseif ($this->getConfigValue('global', 'global_ust_auszeichnung') === 'autoNoVat') {
            $ust = $inklexkl . ' ' . Shop::Lang()->get('vat', 'productDetails');
        } elseif ($this->getConfigValue('global', 'global_ust_auszeichnung') === 'endpreis') {
            $ust = Shop::Lang()->get('finalprice', 'productDetails');
        }
        $taxText = $this->AttributeAssoc[\ART_ATTRIBUT_STEUERTEXT] ?? false;
        if (!$taxText) {
            $taxText = $this->gibAttributWertNachName(\ART_ATTRIBUT_STEUERTEXT);
        }
        if ($taxText) {
            $ust = $taxText;
        }
        $ret = $ust . $markup;
        \executeHook(\HOOK_TOOLSGLOBAL_INC_MWSTVERSANDSTRING, ['cVersandhinweis' => &$ret, 'oArtikel' => $this]);

        return $ret;
    }

    /**
     * @param bool     $asString
     * @param int|null $customerGroupID
     * @return ($asString is true ? string : array<string, string>)
     */
    public function gibMwStVersandLaenderString(bool $asString = true, ?int $customerGroupID = null)
    {
        static $allCountries = [];

        if ($this->getConfigValue('global', 'global_versandfrei_anzeigen') !== 'Y') {
            return $asString ? '' : [];
        }
        if (!isset($_SESSION['Kundengruppe'])) {
            $_SESSION['Kundengruppe'] = (new CustomerGroup(0, $this->getDB()))->loadDefaultGroup();
        }
        $customerGroupID       = $customerGroupID ?? $this->kKundengruppe ?? Frontend::getCustomer()->getGroupID();
        $helper                = ShippingMethod::getInstance();
        $shippingFreeCountries = \is_array($this->Preise->fVK)
            ? $helper->getFreeShippingCountries($this->Preise->fVK, $customerGroupID, $this->kVersandklasse)
            : '';
        if (empty($shippingFreeCountries)) {
            return $asString ? '' : [];
        }
        $codes   = \array_filter(
            map(
                \explode(',', $shippingFreeCountries),
                static function (string $e): string {
                    return \trim($e);
                }
            )
        );
        $cacheID = 'jtl_ola_' . \md5($shippingFreeCountries) . '_' . $this->kSprache;
        if (($countries = $allCountries[$cacheID] ?? $this->getCache()->get($cacheID)) === false) {
            $countries = Shop::Container()->getCountryService()->getFilteredCountryList($codes)->mapWithKeys(
                function (Country $country) {
                    return [$country->getISO() => $country->getName($this->kSprache)];
                }
            )->toArray();

            $this->getCache()->set(
                $cacheID,
                $countries,
                [\CACHING_GROUP_CORE, \CACHING_GROUP_CATEGORY, \CACHING_GROUP_OPTION]
            );
        }
        $allCountries[$cacheID] = $countries;

        return $asString
            ? Shop::Lang()->get('noShippingCostsAtExtended', 'basket', \implode(', ', $countries))
            : $countries;
    }

    /**
     * @param string $date1
     * @param string $date2
     * @return int
     * @throws \Exception
     */
    private function calculateDaysBetween(string $date1, string $date2): int
    {
        $match = '/^\d{4}-\d{1,2}-\d{1,2}$/';
        if (!\preg_match($match, $date1) || !\preg_match($match, $date2)) {
            return 0;
        }

        $dateTime1 = new DateTime($date1);
        $dateTime2 = new DateTime($date2);
        $diff      = $dateTime2->diff($dateTime1);
        $days      = $diff->format('%a');
        if ($diff->invert === 1) {
            $days *= -1;
        }

        return (int)$days;
    }

    /**
     * @param Artikel $childProduct
     * @param bool    $isCanonical
     * @return string
     */
    public function baueVariKombiKindCanonicalURL(Artikel $childProduct, bool $isCanonical = true): string
    {
        $url = '';
        // Beachte Vater FunktionsAttribute
        if (isset($childProduct->VaterFunktionsAttribute[\FKT_ATTRIBUT_CANONICALURL_VARKOMBI])) {
            $isCanonical = match ((int)$childProduct->VaterFunktionsAttribute[\FKT_ATTRIBUT_CANONICALURL_VARKOMBI]) {
                1       => true,
                default => false,
            };
        }
        // Beachte Kind FunktionsAttribute
        if (isset($childProduct->FunktionsAttribute[\FKT_ATTRIBUT_CANONICALURL_VARKOMBI])) {
            $isCanonical = match ((int)$childProduct->FunktionsAttribute[\FKT_ATTRIBUT_CANONICALURL_VARKOMBI]) {
                1       => true,
                default => false,
            };
        }
        if ($isCanonical === true) {
            $url = $childProduct->cVaterURLFull;
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getMetaKeywords(): string
    {
        $keyWords = '';
        if (!empty($this->AttributeAssoc[\ART_ATTRIBUT_METAKEYWORDS])) {
            $keyWords = $this->AttributeAssoc[\ART_ATTRIBUT_METAKEYWORDS];
        } elseif (!empty($this->FunktionsAttribute[\ART_ATTRIBUT_METAKEYWORDS])) {
            $keyWords = $this->FunktionsAttribute[\ART_ATTRIBUT_METAKEYWORDS];
        } elseif (!empty($this->metaKeywords)) {
            $keyWords = $this->metaKeywords;
        }
        \executeHook(\HOOK_ARTIKEL_INC_METAKEYWORDS, ['keywords' => &$keyWords]);

        return $keyWords;
    }

    /**
     * @return string
     */
    public function getMetaTitle(): string
    {
        if ($this->metaTitle !== null) {
            return $this->metaTitle;
        }
        $globalMetaTitle = '';
        $title           = '';
        $price           = '';
        // append global meta title
        if ($this->getConfigValue('metaangaben', 'global_meta_title_anhaengen') === 'Y') {
            $globalMetaData = Metadata::getGlobalMetaData();
            if (!empty($globalMetaData[$this->kSprache]->Title)) {
                $globalMetaTitle = ' - ' . $globalMetaData[$this->kSprache]->Title;
            }
        }
        $idx = $this->getCustomerGroup()->getIsMerchant();
        if (
            isset($this->Preise->fVK[$idx], $this->Preise->cVKLocalized[$idx])
            && $this->Preise->fVK[$idx] > 0
            && $this->getConfigValue('metaangaben', 'global_meta_title_preis') === 'Y'
        ) {
            $price = ', ' . $this->Preise->cVKLocalized[$idx];
        }
        if (!empty($this->AttributeAssoc[\ART_ATTRIBUT_METATITLE])) {
            return Metadata::prepareMeta(
                $this->AttributeAssoc[\ART_ATTRIBUT_METATITLE] . $globalMetaTitle,
                $price,
                (int)$this->getConfigValue('metaangaben', 'global_meta_maxlaenge_title')
            );
        }
        if (!empty($this->FunktionsAttribute[\ART_ATTRIBUT_METATITLE])) {
            return Metadata::prepareMeta(
                $this->FunktionsAttribute[\ART_ATTRIBUT_METATITLE] . $globalMetaTitle,
                $price,
                (int)$this->getConfigValue('metaangaben', 'global_meta_maxlaenge_title')
            );
        }
        if (!empty($this->cName)) {
            $title = $this->cName;
        }
        $title = \str_replace('"', '', $title) . $globalMetaTitle;

        \executeHook(\HOOK_ARTIKEL_INC_METATITLE, ['cTitle' => &$title]);

        return Metadata::prepareMeta(
            $title,
            $price,
            (int)$this->getConfigValue('metaangaben', 'global_meta_maxlaenge_title')
        );
    }

    /**
     * @return string
     */
    public function setMetaDescription(): string
    {
        $description = '';
        \executeHook(\HOOK_ARTIKEL_INC_METADESCRIPTION, ['cDesc' => &$description, 'oArtikel' => &$this]);

        if (\mb_strlen($description) > 1) {
            return $description;
        }

        $globalMeta = Metadata::getGlobalMetaData();
        $prefix     = (isset($globalMeta[$this->kSprache]->Meta_Description_Praefix)
            && \mb_strlen($globalMeta[$this->kSprache]->Meta_Description_Praefix) > 0)
            ? $globalMeta[$this->kSprache]->Meta_Description_Praefix . ' '
            : '';
        // Hat der Artikel per Attribut eine MetaDescription gesetzt?
        if (!empty($this->AttributeAssoc[\ART_ATTRIBUT_METADESCRIPTION])) {
            return Metadata::truncateMetaDescription(
                $prefix . $this->AttributeAssoc[\ART_ATTRIBUT_METADESCRIPTION]
            );
        }
        // Kurzbeschreibung vorhanden? Wenn ja, nimm dies als MetaDescription
        $description = ($this->cKurzBeschreibung !== null && \mb_strlen(\strip_tags($this->cKurzBeschreibung)) > 6)
            ? $this->cKurzBeschreibung
            : '';
        // Beschreibung vorhanden? Wenn ja, nimm dies als MetaDescription
        if ($description === '' && $this->cBeschreibung !== null && \mb_strlen(\strip_tags($this->cBeschreibung)) > 6) {
            $description = $this->cBeschreibung;
        }

        if (\mb_strlen($description) > 0) {
            return Metadata::truncateMetaDescription(
                $prefix . \strip_tags(
                    \str_replace(
                        ['<br>', '<br />', '</p>', '</li>', "\n", "\r", '.'],
                        ' ',
                        $description
                    )
                )
            );
        }

        return $description;
    }

    /**
     * @param KategorieListe $categoryList
     * @return string
     */
    public function getMetaDescription(KategorieListe $categoryList): string
    {
        $description = $this->metaDescription;
        if ($description !== null && \mb_strlen($description) > 0) {
            return $description;
        }
        $globalMeta  = Metadata::getGlobalMetaData();
        $prefix      = (isset($globalMeta[$this->kSprache]->Meta_Description_Praefix)
            && \mb_strlen($globalMeta[$this->kSprache]->Meta_Description_Praefix) > 0)
            ? $globalMeta[$this->kSprache]->Meta_Description_Praefix . ' '
            : '';
        $description = ($this->cName !== null && \mb_strlen($this->cName) > 0)
            ? ($prefix . $this->cName . ' in ')
            : '';
        if (\count($categoryList->elemente) > 0) {
            $categoryNames = [];
            foreach ($categoryList->elemente as $category) {
                if ($category->getID() > 0) {
                    $categoryNames[] = $category->getName($this->kSprache);
                }
            }
            $description .= \implode(', ', $categoryNames);
        }

        return Metadata::truncateMetaDescription($description);
    }

    /**
     * @return array
     */
    public function getTierPrices(): array
    {
        $tierPrices = [];
        if (isset($this->Preise->nAnzahl_arr)) {
            foreach ($this->Preise->nAnzahl_arr as $_idx => $_nAnzahl) {
                $_v                    = [];
                $_v['nAnzahl']         = $_nAnzahl;
                $_v['fStaffelpreis']   = $this->Preise->fStaffelpreis_arr[$_idx] ?? null;
                $_v['fPreis']          = $this->Preise->fPreis_arr[$_idx] ?? null;
                $_v['cPreisLocalized'] = $this->Preise->cPreisLocalized_arr[$_idx] ?? null;
                $tierPrices[]          = $_v;
            }
        }

        return $tierPrices;
    }

    /**
     * provides data for tax/shipping cost notices
     * replaces Artikel::gibMwStVersandString()
     *
     * @return array
     */
    public function getShippingAndTaxData(): array
    {
        if (!isset($_SESSION['Kundengruppe']) || !\is_a($_SESSION['Kundengruppe'], CustomerGroup::class)) {
            $_SESSION['Kundengruppe'] = (new CustomerGroup(0, $this->getDB()))->loadDefaultGroup();
        }
        if (!isset($_SESSION['Link_Versandseite'])) {
            Frontend::setSpecialLinks();
        }
        $taxText = $this->AttributeAssoc[\ART_ATTRIBUT_STEUERTEXT] ?? false;
        if (!$taxText && $this->AttributeAssoc === null) {
            $taxText = $this->gibAttributWertNachName(\ART_ATTRIBUT_STEUERTEXT);
        }
        $countries       = $this->gibMwStVersandLaenderString(false);
        $countriesString = \count($countries) > 0
            ? Shop::Lang()->get('noShippingCostsAtExtended', 'basket', \implode(', ', $countries))
            : '';

        return [
            'net'                   => $this->getCustomerGroup()->isMerchant(),
            'text'                  => $taxText,
            'tax'                   => $this->formatTax(Tax::getSalesTax($this->kSteuerklasse)),
            'shippingFreeCountries' => $countriesString,
            'countries'             => $countries,
            'shippingClass'         => $this->cVersandklasse
        ];
    }

    /**
     * @return bool
     */
    public function showMatrix(): bool
    {
        if (
            $this->nVariationOhneFreifeldAnzahl > 0
            && !$this->kArtikelVariKombi
            && !$this->kVariKindArtikel
            && !$this->nErscheinendesProdukt
            && Request::verifyGPCDataInt('quickView') === 0
            && $this->nVariationOhneFreifeldAnzahl === \count($this->Variationen)
            && (\count($this->Variationen) <= 2
                || ($this->getConfigValue('artikeldetails', 'artikeldetails_warenkorbmatrix_anzeigeformat') === 'L'
                    && $this->nIstVater === 1)
            )
            && ($this->getConfigValue('artikeldetails', 'artikeldetails_warenkorbmatrix_anzeige') === 'Y'
                || $this->getFunctionalAttributevalue(\FKT_ATTRIBUT_WARENKORBMATRIX, true) === 1)
        ) {
            //the cart matrix cannot deal with those different kinds of variations..
            //so if we got "freifeldvariationen" in combination with normal ones, we have to disable the matrix
            $total = 1;
            foreach ($this->Variationen as $variation) {
                if ($variation->cTyp === 'FREIFELD' || $variation->cTyp === 'PFLICHT-FREIFELD') {
                    return false;
                }
                $total *= $variation->nLieferbareVariationswerte;
            }
            foreach ($this->oKonfig_arr as $_oKonfig) {
                if (isset($_oKonfig)) {
                    return false;
                }
            }

            return $total <= \ART_MATRIX_MAX;
        }

        return false;
    }

    /**
     * @param array $attributes
     * @return array
     */
    public function keyValueVariations(array $attributes): array
    {
        $keyValueVariations = [];
        foreach ($attributes as $key => $value) {
            if (\is_object($value)) {
                $key = $value->kEigenschaft;
            }
            if (!isset($keyValueVariations[$key])) {
                $keyValueVariations[$key] = [];
            }
            if (\is_object($value) && isset($value->Werte)) {
                foreach ($value->Werte as $mEigenschaftWert) {
                    $keyValueVariations[$key][] = \is_object($mEigenschaftWert)
                        ? $mEigenschaftWert->kEigenschaftWert
                        : $mEigenschaftWert;
                }
            } else {
                $valueIDs = $value;
                if (\is_object($value)) {
                    $valueIDs = [$value->kEigenschaftWert];
                } elseif (!\is_array($value)) {
                    $valueIDs = (array)$valueIDs;
                }
                $keyValueVariations[$key] = \array_merge($keyValueVariations[$key], $valueIDs);
            }
        }

        return $keyValueVariations;
    }

    /**
     * @param array $properties
     * @param array $setData
     * @return array
     */
    private function getPossibleVariationsBySelection(array $properties, array $setData): array
    {
        $possibleVariations = [];
        foreach ($properties as $propertyID => $propertyValues) {
            $i          = 2;
            $queries    = [];
            $propertyID = (int)$propertyID;
            $prepvalues = [
                'customerGroupID' => $this->kKundengruppe ?? $this->getCustomerGroup()->getID(),
                'where'           => $propertyID
            ];
            foreach ($setData as $setPropertyID => $propertyValue) {
                $setPropertyID = (int)$setPropertyID;
                $propertyValue = (int)$propertyValue;
                if ($propertyID !== $setPropertyID) {
                    $queries[] = 'INNER JOIN teigenschaftkombiwert e' . $i . '
                                    ON e1.kEigenschaftKombi = e' . $i . '.kEigenschaftKombi
                                    AND e' . $i . '.kEigenschaftWert = :kev' . $i;

                    $prepvalues['kev' . $i] = $propertyValue;
                    ++$i;
                }
            }
            $sql  = \implode(' ', $queries);
            $attr = $this->getDB()->getObjects(
                'SELECT e1.*, k.cName, k.cLagerBeachten, k.cLagerKleinerNull, k.fLagerbestand
                    FROM teigenschaftkombiwert e1
                    INNER JOIN tartikel k
                        ON e1.kEigenschaftKombi = k.kEigenschaftKombi
                    ' . $sql . '
                    LEFT JOIN tartikelsichtbarkeit
                        ON tartikelsichtbarkeit.kArtikel = k.kArtikel
                            AND tartikelsichtbarkeit.kKundengruppe = :customerGroupID
                    WHERE e1.kEigenschaft = :where
                        AND tartikelsichtbarkeit.kArtikel IS NULL',
                $prepvalues
            );
            foreach ($attr as $oEigenschaft) {
                $oEigenschaft->kEigenschaftWert = (int)$oEigenschaft->kEigenschaftWert;
                if (!isset($possibleVariations[$oEigenschaft->kEigenschaft])) {
                    $possibleVariations[$oEigenschaft->kEigenschaft] = [];
                }
                //aufLagerSichtbarkeit() betrachtet allgemein alle Artikel, hier muss zusätzlich geprüft werden
                //ob die entsprechende VarKombi verfügbar ist, auch wenn global "alle Artikel anzeigen" aktiv ist
                if (
                    $this->aufLagerSichtbarkeit($oEigenschaft)
                    && !\in_array(
                        $oEigenschaft->kEigenschaftWert,
                        $possibleVariations[$oEigenschaft->kEigenschaft],
                        true
                    )
                ) {
                    $possibleVariations[$oEigenschaft->kEigenschaft][] = $oEigenschaft->kEigenschaftWert;
                }
            }
        }

        return $possibleVariations;
    }

    /**
     * @param array $setProperties
     * @param bool  $invert
     * @return array
     */
    public function getVariationsBySelection(array $setProperties, bool $invert = false): array
    {
        $keyValueVariations             = $this->keyValueVariations($this->VariationenOhneFreifeld);
        $possibleVariationsForSelection = $this->getPossibleVariationsBySelection(
            $keyValueVariations,
            $setProperties
        );

        if (!$invert) {
            return $possibleVariationsForSelection;
        }

        $invalidVariations = [];
        foreach ($keyValueVariations as $propID => $propValues) {
            foreach ($propValues as $propValueID) {
                $propValueID = (int)$propValueID;
                if (!\in_array($propValueID, (array)$possibleVariationsForSelection[$propID], true)) {
                    if (!isset($invalidVariations[$propID]) || !\is_array($invalidVariations[$propID])) {
                        $invalidVariations[$propID] = [];
                    }
                    $invalidVariations[$propID][] = $propValueID;
                }
            }
        }

        return $invalidVariations;
    }

    /**
     * @return array
     */
    public function getChildVariations(): array
    {
        return \count($this->oVariationKombi_arr) > 0
            ? $this->keyValueVariations($this->oVariationKombi_arr)
            : [];
    }

    /**
     * @return array<string, float>
     */
    public function getDimension(): array
    {
        return [
            'length' => (float)$this->fLaenge,
            'width'  => (float)$this->fBreite,
            'height' => (float)$this->fHoehe
        ];
    }

    /**
     * @return array of string Product Dimension
     */
    public function getDimensionLocalized(): array
    {
        $values = [];
        if (($dimensions = $this->getDimension()) === null) {
            return $values;
        }
        foreach ($dimensions as $key => $val) {
            if (empty($val)) {
                continue;
            }
            $idx          = Shop::Lang()->get('dimension_' . $key, 'productDetails');
            $values[$idx] = Separator::getUnit(\JTL_SEPARATOR_LENGTH, $this->kSprache, $val);
        }

        return $values;
    }

    /**
     * @param string     $option
     * @param mixed|null $default
     * @return mixed|null
     */
    public function getOption(string $option, $default = null)
    {
        return $this->options->$option ?? $default;
    }

    /**
     * @param string $cISO
     * @return bool
     */
    public function isUsedForShippingCostCalculation(string $cISO): bool
    {
        $excludedAttributes = [\FKT_ATTRIBUT_VERSANDKOSTEN, \FKT_ATTRIBUT_VERSANDKOSTEN_GESTAFFELT];

        foreach ($excludedAttributes as $excludedAttribute) {
            if (
                isset($this->FunktionsAttribute[$excludedAttribute])
                && ($cISO === '' || \str_contains($this->FunktionsAttribute[$excludedAttribute], $cISO))
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param bool $onlyStockRelevant
     * @return object[]
     * @since 4.06.10
     */
    public function getAllDependentProducts(bool $onlyStockRelevant = false): array
    {
        $depProducts[$this->kArtikel] = (object)[
            'product'     => $this,
            'stockFactor' => 1,
        ];

        if ($this->kStueckliste > 0 && \count($this->oStueckliste_arr) === 0) {
            $this->holeStueckliste(CustomerGroup::getCurrent(), $onlyStockRelevant);
        }

        /** @var static $item */
        foreach ($this->oStueckliste_arr as $item) {
            if (!$onlyStockRelevant || ($item->cLagerBeachten === 'Y' && $item->cLagerKleinerNull !== 'Y')) {
                $depProducts[$item->kArtikel] = (object)[
                    'product'     => $item,
                    'stockFactor' => (float)$item->fAnzahl_stueckliste,
                ];
            }
        }

        return $depProducts;
    }

    /**
     * prepares a string optimized for SEO
     *
     * @param string $optStr
     * @return string - SEO optimized string
     */
    private function getSeoString(string $optStr = ''): string
    {
        $optStr = \preg_replace('/[^\\pL\d_]+/u', '-', $optStr);
        $optStr = \trim($optStr, '-');
        $optStr = \transliterator_transliterate('Latin-ASCII;', $optStr);
        $optStr = \mb_convert_case($optStr, \MB_CASE_LOWER);

        return \preg_replace('/[^-a-z\d_]+/', '', $optStr);
    }

    /**
     * @return int|null
     * @noinspection PhpHierarchyChecksInspection
     */
    public function getID()
    {
        return $this->kArtikel;
    }

    /**
     * @return array
     */
    public function getImages(): array
    {
        return $this->Bilder;
    }

    /**
     * @param string $size
     * @param int    $number
     * @return string|null
     */
    public function getImage(string $size = Image::SIZE_MD, int $number = 1): ?string
    {
        $from = $this->Bilder[$number - 1] ?? null;
        if ($from === null) {
            return null;
        }
        return match ($size) {
            Image::SIZE_XS => $from->cURLMini,
            Image::SIZE_SM => $from->cURLKlein,
            Image::SIZE_MD => $from->cURLNormal,
            Image::SIZE_LG => $from->cURLGross,
            default        => null,
        };
    }

    /**
     * @return string
     */
    public function getBackorderString(): string
    {
        $backorder = '';
        if (
            $this->cLagerBeachten === 'Y'
            && $this->fLagerbestand <= 0
            && $this->fZulauf > 0
            && $this->dZulaufDatum_de !== null
        ) {
            $backorder = \sprintf(
                Shop::Lang()->get('productInflowing', 'productDetails'),
                $this->fZulauf,
                $this->cEinheit,
                $this->dZulaufDatum_de
            );
        }

        return $backorder;
    }

    /**
     * @return int
     */
    public function getCustomerGroupID(): int
    {
        return $this->kKundengruppe;
    }

    /**
     * @param string $name
     * @param bool   $asInt
     * @return ($asInt is true ? int|null : mixed|null)
     */
    public function getFunctionalAttributevalue(string $name, bool $asInt = false)
    {
        if (!isset($this->FunktionsAttribute[$name])) {
            return null;
        }

        return $asInt ? (int)$this->FunktionsAttribute[$name] : $this->FunktionsAttribute[$name];
    }

    /**
     * @return int
     */
    private function getPrecision(): int
    {
        $precision = $this->getFunctionalAttributevalue(\FKT_ATTRIBUT_GRUNDPREISGENAUIGKEIT, true);

        return $precision === null || $precision < 1 ? 2 : $precision;
    }
}
