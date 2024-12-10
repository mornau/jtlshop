<?php

declare(strict_types=1);

namespace JTL\Router;

use JTL\Catalog\Wishlist\Wishlist;
use JTL\Filter\ProductFilter;
use JTL\Helpers\Product;
use JTL\Helpers\Request;
use JTL\Helpers\Text;

/**
 * Class State
 * @package JTL\Router
 */
class State
{
    /**
     * @var bool
     */
    public bool $is404 = false;

    /**
     * @var int
     */
    public int $pageType = \PAGE_UNBEKANNT;

    /**
     * @var int
     */
    public int $itemID = 0;

    /**
     * @var string
     */
    public string $type = '';

    /**
     * @var int
     */
    public int $languageID = 0;

    /**
     * @var string
     */
    public string $slug = '';

    /**
     * @var int
     */
    public int $pageID = 0;

    /**
     * @var array<string, int>
     */
    public array $customFilters = [];

    /**
     * @var int[]
     */
    public array $characteristicFilterIDs = [];

    /**
     * @var int[]
     */
    public array $searchFilterIDs = [];

    /**
     * @var int
     */
    public int $manufacturerFilterID = 0;

    /**
     * @var int[]
     */
    public array $manufacturerFilterIDs = [];

    /**
     * @var int
     */
    public int $categoryFilterID = 0;

    /**
     * @var int[]
     */
    public array $categoryFilterIDs = [];

    /**
     * @var array<string, string>
     */
    public array $caseMismatches = [];

    /**
     * @var int[]
     */
    public array $manufacturers = [];

    /**
     * @var bool
     */
    public bool $categoryFilterNotFound = false;

    /**
     * @var bool
     */
    public bool $manufacturerFilterNotFound = false;

    /**
     * @var bool
     */
    public bool $characteristicNotFound = false;

    /**
     * @var int
     */
    public int $configItemID = 0;

    /**
     * @var int
     */
    public int $categoryID = 0;

    /**
     * @var int
     */
    public int $productID = 0;

    /**
     * @var int
     */
    public int $childProductID = 0;

    /**
     * @var int
     */
    public int $linkID = 0;

    /**
     * @var int
     */
    public int $manufacturerID = 0;

    /**
     * @var int
     */
    public int $searchQueryID = 0;

    /**
     * @var int
     */
    public int $characteristicID = 0;

    /**
     * @var int
     */
    public int $searchSpecialID = 0;

    /**
     * @var int
     */
    public int $newsItemID = 0;

    /**
     * @var int
     */
    public int $newsOverviewID = 0;

    /**
     * @var int
     */
    public int $newsCategoryID = 0;

    /**
     * @var int
     */
    public int $ratingFilterID = 0;

    /**
     * @var int
     */
    public int $searchFilterID = 0;

    /**
     * @var int
     */
    public int $searchSpecialFilterID = 0;

    /**
     * @var int[]
     */
    public array $searchSpecialFilterIDs = [];

    /**
     * @var int
     */
    public int $viewMode = 0;

    /**
     * @var int
     */
    public int $sortID = 0;

    /**
     * @var int
     */
    public int $show = 0;

    /**
     * @var int
     */
    public int $compareListID = 0;

    /**
     * @var int
     */
    public int $linkType = 0;

    /**
     * @var int
     */
    public int $stars = 0;

    /**
     * @var int
     */
    public int $wishlistID = 0;

    /**
     * @var int
     */
    public int $count = 0;
    /**
     * @var int
     */
    public int $productsPerPage = 0;

    /**
     * @var string
     */
    public string $priceRangeFilter = '';

    /**
     * @var string
     */
    public string $canonicalURL = '';

    /**
     * @var string
     */
    public string $date = '';

    /**
     * @var string
     */
    public string $optinCode = '';

    /**
     * @var string
     */
    public string $searchQuery = '';

    /**
     * @var string
     */
    public string $fileName = '';

    /**
     * @var string|null
     */
    public ?string $currentRouteName = null;

    /**
     * @var array{id: int, slug:string}|null
     */
    public ?array $routeData = null;

    /**
     * @var array<string, string>
     */
    private static array $mapping = [
        'kKonfigPos'             => 'configItemID',
        'kKategorie'             => 'categoryID',
        'kArtikel'               => 'productID',
        'kVariKindArtikel'       => 'childProductID',
        'kSeite'                 => 'pageID',
        'kLink'                  => 'linkID',
        'kHersteller'            => 'manufacturerID',
        'kSuchanfrage'           => 'searchQueryID',
        'kMerkmalWert'           => 'characteristicID',
        'kSuchspecial'           => 'searchSpecialID',
        'suchspecial'            => 'searchSpecialID',
        'kNews'                  => 'newsItemID',
        'kNewsMonatsUebersicht'  => 'newsOverviewID',
        'kNewsKategorie'         => 'newsCategoryID',
        'nBewertungSterneFilter' => 'ratingFilterID',
        'cPreisspannenFilter'    => 'priceRangeFilter',
        'manufacturerFilters'    => 'manufacturerFilterIDs',
        'kHerstellerFilter'      => 'manufacturerFilterID',
        'categoryFilters'        => 'categoryFilterIDs',
        'MerkmalFilter_arr'      => 'characteristicFilterIDs',
        'kKategorieFilter'       => 'categoryFilterID',
        'searchSpecialFilters'   => 'searchSpecialFilterIDs',
        'kSuchFilter'            => 'searchFilterID',
        'kSuchspecialFilter'     => 'searchSpecialFilterID',
        'SuchFilter_arr'         => 'searchFilterIDs',
        'nDarstellung'           => 'viewMode',
        'nSort'                  => 'sortID',
        'nSortierung'            => 'sortID',
        'show'                   => 'show',
        'vergleichsliste'        => 'compareListID',
        'bFileNotFound'          => 'is404',
        'is404'                  => 'is404',
        'cCanonicalURL'          => 'canonicalURL',
        'nLinkart'               => 'linkType',
        'nSterne'                => 'stars',
        'kWunschliste'           => 'wishlistID',
        'nNewsKat'               => 'newsCategoryID',
        'cDatum'                 => 'date',
        'nAnzahl'                => 'count',
        'optinCode'              => 'optinCode',
        'cSuche'                 => 'searchQuery',
        'nArtikelProSeite'       => 'productsPerPage',
    ];

    /**
     * @return string[]
     */
    public function getMapping(): array
    {
        return self::$mapping;
    }

    public function initFromRequest(): void
    {
        $this->configItemID          = Request::verifyGPCDataInt(\QUERY_PARAM_CONFIG_ITEM);
        $this->categoryID            = Request::verifyGPCDataInt(\QUERY_PARAM_CATEGORY);
        $this->productID             = Request::verifyGPCDataInt(\QUERY_PARAM_PRODUCT);
        $this->childProductID        = Request::verifyGPCDataInt(\QUERY_PARAM_CHILD_PRODUCT);
        $this->pageID                = Request::verifyGPCDataInt(\QUERY_PARAM_PAGE);
        $this->linkID                = Request::verifyGPCDataInt(\QUERY_PARAM_LINK);
        $this->manufacturerID        = Request::verifyGPCDataInt(\QUERY_PARAM_MANUFACTURER);
        $this->searchQueryID         = Request::verifyGPCDataInt(\QUERY_PARAM_SEARCH_QUERY_ID);
        $this->characteristicID      = Request::verifyGPCDataInt(\QUERY_PARAM_CHARACTERISTIC_VALUE);
        $this->searchSpecialID       = Request::verifyGPCDataInt(\QUERY_PARAM_SEARCH_SPECIAL);
        $this->newsItemID            = Request::verifyGPCDataInt(\QUERY_PARAM_NEWS_ITEM);
        $this->newsOverviewID        = Request::verifyGPCDataInt(\QUERY_PARAM_NEWS_OVERVIEW);
        $this->newsCategoryID        = Request::verifyGPCDataInt(\QUERY_PARAM_NEWS_CATEGORY);
        $this->ratingFilterID        = Request::verifyGPCDataInt(\QUERY_PARAM_RATING_FILTER);
        $this->priceRangeFilter      = Request::verifyGPDataString(\QUERY_PARAM_PRICE_FILTER);
        $this->manufacturerFilterIDs = Request::verifyGPDataIntegerArray(\QUERY_PARAM_MANUFACTURER_FILTER);
        $this->manufacturerFilterID  = \count($this->manufacturerFilterIDs) > 0
            ? $this->manufacturerFilterIDs[0]
            : 0;

        $this->categoryFilterIDs      = Request::verifyGPDataIntegerArray(\QUERY_PARAM_CATEGORY_FILTER);
        $this->categoryFilterID       = \count($this->categoryFilterIDs) > 0
            ? $this->categoryFilterIDs[0]
            : 0;
        $this->searchSpecialFilterIDs = Request::verifyGPDataIntegerArray(\QUERY_PARAM_SEARCH_SPECIAL_FILTER);
        $this->searchFilterID         = Request::verifyGPCDataInt(\QUERY_PARAM_SEARCH_FILTER);
        $this->searchSpecialFilterID  = \count($this->searchSpecialFilterIDs) > 0
            ? $this->searchSpecialFilterIDs[0]
            : 0;
        $this->viewMode               = Request::verifyGPCDataInt(\QUERY_PARAM_VIEW_MODE);
        $this->sortID                 = Request::verifyGPCDataInt(\QUERY_PARAM_SORT);
        $this->show                   = Request::verifyGPCDataInt(\QUERY_PARAM_SHOW);
        $this->compareListID          = Request::verifyGPCDataInt(\QUERY_PARAM_COMPARELIST);
        $this->stars                  = Request::verifyGPCDataInt(\QUERY_PARAM_STARS);
        $this->wishlistID             = Wishlist::checkeParameters();
        if ($this->newsCategoryID === 0) {
            $this->newsCategoryID = Request::verifyGPCDataInt(\QUERY_PARAM_NEWS_CATEGORY);
        }
        $this->date      = Request::verifyGPDataString(\QUERY_PARAM_DATE);
        $this->count     = Request::verifyGPCDataInt(\QUERY_PARAM_QTY);
        $this->optinCode = Request::verifyGPDataString(\QUERY_PARAM_OPTIN_CODE);
        $this->linkID    = Request::verifyGPCDataInt(\QUERY_PARAM_LINK);
        if (($query = Request::verifyGPDataString(\QUERY_PARAM_SEARCH_QUERY)) !== '') {
            $this->searchQuery = Text::xssClean($query);
        } elseif (($term = Request::verifyGPDataString(\QUERY_PARAM_SEARCH_TERM)) !== '') {
            $this->searchQuery = Text::xssClean($term);
        } else {
            $this->searchQuery = Text::xssClean(Request::verifyGPDataString(\QUERY_PARAM_SEARCH));
        }
        $this->productsPerPage = Request::verifyGPCDataInt(\QUERY_PARAM_PRODUCTS_PER_PAGE);
        if ($this->productID > 0) {
            $this->type = 'kArtikel';
            if (Product::isVariChild($this->productID)) {
                $this->childProductID = $this->productID;
                $this->productID      = Product::getParent($this->productID);
            }
            $this->itemID = $this->productID;
        } elseif ($this->categoryID > 0) {
            $this->type   = 'kKategorie';
            $this->itemID = $this->categoryID;
        } elseif ($this->manufacturerID > 0) {
            $this->type   = 'kHersteller';
            $this->itemID = $this->manufacturerID;
        } elseif ($this->linkID > 0) {
            $this->type   = 'kLink';
            $this->itemID = $this->linkID;
        } elseif ($this->characteristicID > 0) {
            $this->type   = 'kMerkmalWert';
            $this->itemID = $this->characteristicID;
        } elseif ($this->newsItemID > 0) {
            $this->type   = 'kNews';
            $this->itemID = $this->newsItemID;
        } elseif ($this->newsCategoryID > 0) {
            $this->type   = 'kNewsKategorie';
            $this->itemID = $this->newsCategoryID;
        } elseif ($this->newsOverviewID > 0) {
            $this->type   = 'kNewsMonatsUebersicht';
            $this->itemID = $this->newsOverviewID;
        } elseif ($this->searchQueryID > 0) {
            $this->type   = 'kSuchanfrage';
            $this->itemID = $this->searchQueryID;
        } elseif ($this->searchSpecialID > 0) {
            $this->type   = 'suchspecial';
            $this->itemID = $this->searchSpecialID;
        }
        $this->characteristicFilterIDs = ProductFilter::initCharacteristicFilter();
        $this->searchFilterIDs         = ProductFilter::initSearchFilter();
        $this->categoryFilterIDs       = ProductFilter::initCategoryFilter();
    }

    /**
     * @return array{kKonfigPos: int, kKategorie: int, kArtikel: int, kVariKindArtikel: int, kSeite: int, kLink: int,
     *     kHersteller: int, kSuchanfrage: int, kMerkmalWert: int, kSuchspecial: int, suchspecial: int, kNews: int,
     *     kNewsMonatsUebersicht: int, kNewsKategorie: int, nBewertungSterneFilter: int, cPreisspannenFilter: string,
     *     manufacturerFilters: int[], kHerstellerFilter: int, categoryFilters: int[], MerkmalFilter_arr: int[],
     *     kKategorieFilter: int, searchSpecialFilters: int[], kSuchFilter: int, kSuchspecialFilter: int,
     *     SuchFilter_arr: int[], nDarstellung: int, nSort: int, nSortierung: int, show: int, vergleichsliste: int,
     *     bFileNotFound: bool, is404: bool, cCanonicalURL: string, nLinkart: int, nSterne: int, kWunschliste: int,
     *     nAnzahl: int, optinCode: string, cSuche: string, nArtikelProSeite: int}
     */
    public function getAsParams(): array
    {
        $params = [];
        foreach ($this->getMapping() as $old => $new) {
            $params[$old] = $this->{$new};
        }

        return $params;
    }

    public function filtersValid(): bool
    {
        return $this->characteristicNotFound === false
            && $this->manufacturerFilterNotFound === false
            && $this->categoryFilterNotFound === false;
    }
}
