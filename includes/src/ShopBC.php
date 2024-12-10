<?php

declare(strict_types=1);

namespace JTL;

/**
 * Class ShopBC
 * @package JTL
 */
class ShopBC
{
    /**
     * @var int
     */
    public static int $kKonfigPos = 0;

    /**
     * @var int
     */
    public static int $kKategorie = 0;

    /**
     * @var int
     */
    public static int $kArtikel = 0;

    /**
     * @var int
     */
    public static int $kVariKindArtikel = 0;

    /**
     * @var int
     */
    public static int $kSeite = 0;

    /**
     * @var int
     */
    public static int $kLink = 0;

    /**
     * @var int
     */
    public static int $nLinkart = 0;

    /**
     * @var int
     */
    public static int $kHersteller = 0;

    /**
     * @var int
     */
    public static int $kSuchanfrage = 0;

    /**
     * @var int
     */
    public static int $kMerkmalWert = 0;

    /**
     * @var int
     */
    public static int $kSuchspecial = 0;

    /**
     * @var int
     */
    public static int $kNews = 0;

    /**
     * @var int
     */
    public static int $kNewsMonatsUebersicht = 0;

    /**
     * @var int
     */
    public static int $kNewsKategorie = 0;

    /**
     * @var int
     */
    public static int $nBewertungSterneFilter = 0;

    /**
     * @var string
     */
    public static string $cPreisspannenFilter = '';

    /**
     * @var int
     */
    public static int $kHerstellerFilter = 0;

    /**
     * @var int[]
     */
    public static array $manufacturerFilterIDs = [];

    /**
     * @var int[]
     */
    public static array $categoryFilterIDs = [];

    /**
     * @var int
     */
    public static int $kKategorieFilter = 0;

    /**
     * @var int
     */
    public static int $kSuchspecialFilter = 0;

    /**
     * @var int[]
     */
    public static array $searchSpecialFilterIDs = [];

    /**
     * @var int
     */
    public static int $kSuchFilter = 0;

    /**
     * @var int
     */
    public static int $nDarstellung = 0;

    /**
     * @var int
     */
    public static int $nSortierung = 0;

    /**
     * @var int
     */
    public static int $nSort = 0;

    /**
     * @var int
     */
    public static int $show = 0;

    /**
     * @var int
     */
    public static int $vergleichsliste = 0;

    /**
     * @var bool
     */
    public static bool $bFileNotFound = false;

    /**
     * @var string
     */
    public static string $cCanonicalURL = '';

    /**
     * @var bool
     */
    public static bool $is404 = false;

    /**
     * @var int[]
     */
    public static array $MerkmalFilter = [];

    /**
     * @var int[]
     */
    public static array $SuchFilter = [];

    /**
     * @var int
     */
    public static int $kWunschliste = 0;

    /**
     * @var bool
     */
    public static bool $bSEOMerkmalNotFound = false;

    /**
     * @var bool
     */
    public static bool $bKatFilterNotFound = false;

    /**
     * @var bool
     */
    public static bool $bHerstellerFilterNotFound = false;

    /**
     * @var string|null
     */
    public static ?string $fileName = null;

    /**
     * @var string
     */
    public static string $AktuelleSeite;

    /**
     * @var int
     */
    public static int $pageType = \PAGE_UNBEKANNT;

    /**
     * @var bool
     */
    public static bool $directEntry = true;

    /**
     * @var bool
     */
    public static bool $bSeo = false;

    /**
     * @var bool
     */
    public static bool $isInitialized = false;

    /**
     * @var int
     */
    public static int $nArtikelProSeite = 0;

    /**
     * @var string
     */
    public static string $cSuche = '';

    /**
     * @var int
     */
    public static int $seite = 0;

    /**
     * @var int
     */
    public static int $nSterne = 0;

    /**
     * @var int
     */
    public static int $nNewsKat = 0;

    /**
     * @var string
     */
    public static string $cDatum = '';

    /**
     * @var int
     */
    public static int $nAnzahl = 0;

    /**
     * @var array<string, int>
     */
    public static array $customFilters = [];

    /**
     * @var string
     */
    protected static string $optinCode = '';
}
