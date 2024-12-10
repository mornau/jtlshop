<?php

declare(strict_types=1);

namespace JTL\Router\Controller;

use JTL\Alert\Alert;
use JTL\Catalog\Category\Kategorie;
use JTL\Catalog\Category\KategorieListe;
use JTL\Catalog\Product\Artikel;
use JTL\Catalog\Product\Preise;
use JTL\Catalog\Product\Preisverlauf;
use JTL\Extensions\Upload\Upload;
use JTL\Helpers\Form;
use JTL\Helpers\Product as ProductHelper;
use JTL\Helpers\Request;
use JTL\Helpers\Tax;
use JTL\Helpers\Text;
use JTL\Pagination\Pagination;
use JTL\Router\Middleware\VisibilityMiddleware;
use JTL\Router\State;
use JTL\Session\Frontend;
use JTL\Shop;
use JTL\Smarty\JTLSmarty;
use League\Route\RouteGroup;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ProductController
 * @package JTL\Router\Controller
 */
class ProductController extends AbstractController
{
    /**
     * @var string
     */
    protected string $tseoSelector = 'kArtikel';

    /**
     * @inheritdoc
     */
    public function init(): bool
    {
        if (
            $this->currentProduct !== null
            && $this->currentProduct->kArtikel > 0
            && $this->currentProduct->kArtikel === $this->state->productID
        ) {
            return true;
        }
        parent::init();
        $this->currentProduct = new Artikel();
        $this->currentProduct->fuelleArtikel(
            $this->state->productID,
            Artikel::getDetailOptions(),
            $this->customerGroupID,
            $this->state->languageID
        );
        if ($this->state->childProductID > 0) {
            $child = (new Artikel())->fuelleArtikel(
                $this->state->childProductID,
                Artikel::getDetailOptions(),
                $this->customerGroupID,
                $this->state->languageID
            );
            if ($child === null || $child->kArtikel <= 0) {
                return false;
            }
        }

        return $this->currentProduct->kArtikel > 0 && $this->currentProduct->kArtikel === $this->state->productID;
    }

    /**
     * @param string[] $messages
     * @return string[]
     */
    public function checkAndSendAvailabilityMessage(array $messages): array
    {
        if (
            Frontend::get('lastAvailabilityMessage') === null ||
            (int)\date_diff(\date_create(), Frontend::get('lastAvailabilityMessage'))->format('%i') >=
            $this->config['artikeldetails']['benachrichtigung_sperre_minuten']
        ) {
            $messages = ProductHelper::checkAvailabilityMessage($messages, $this->config['artikeldetails']);
            Frontend::set('lastAvailabilityMessage', \date_create());

            return $messages;
        }

        $messages[] = Shop::Lang()->get('notificationNotPossible', 'messages');

        return $messages;
    }

    /**
     * @inheritdoc
     */
    protected function handleSeoError(int $id, int $languageID): State
    {
        if ($id > 0) {
            $exists = $this->db->getSingleObject(
                'SELECT kArtikel
                    FROM tartikel
                    WHERE kArtikel = :pid',
                ['pid' => $id]
            );
            if ($exists !== null) {
                $seo = (object)[
                    'cSeo'     => '',
                    'cKey'     => $this->tseoSelector,
                    'kKey'     => $id,
                    'kSprache' => $languageID
                ];

                return $this->updateState($seo, $seo->cSeo);
            }
        }
        $this->state->is404 = true;

        return $this->updateProductFilter();
    }

    /**
     * @inheritdoc
     */
    public function register(RouteGroup $route, string $dynName): void
    {
        $name                 = \SLUG_ALLOW_SLASHES ? 'name:.+' : 'name';
        $visibilityMiddleware = new VisibilityMiddleware();
        $route->get('/' . \ROUTE_PREFIX_PRODUCTS . '/id/{id:\d+}', $this->getResponse(...))
            ->setName('ROUTE_PRODUCT_BY_ID' . $dynName)
            ->middleware($visibilityMiddleware);
        $route->get('/' . \ROUTE_PREFIX_PRODUCTS . '/{' . $name . '}', $this->getResponse(...))
            ->setName('ROUTE_PRODUCT_BY_NAME' . $dynName)
            ->middleware($visibilityMiddleware);
        $route->post('/' . \ROUTE_PREFIX_PRODUCTS . '/id/{id:\d+}', $this->getResponse(...))
            ->setName('ROUTE_PRODUCT_BY_ID' . $dynName . 'POST')
            ->middleware($visibilityMiddleware);
        $route->post('/' . \ROUTE_PREFIX_PRODUCTS . '/{' . $name . '}', $this->getResponse(...))
            ->setName('ROUTE_PRODUCT_BY_NAME' . $dynName . 'POST')
            ->middleware($visibilityMiddleware);
    }

    /**
     * @inheritdoc
     */
    public function getResponse(ServerRequestInterface $request, array $args, JTLSmarty $smarty): ResponseInterface
    {
        if (isset($args['id']) || isset($args['name'])) {
            $this->getStateFromSlug($args);
            if (!$this->init()) {
                return $this->notFoundResponse($request, $args, $smarty);
            }
        }
        $this->smarty = $smarty;
        Shop::setPageType(\PAGE_ARTIKEL);
        global $AktuellerArtikel;
        $priceHistory = null;
        $rated        = false;
        $valid        = Form::validateToken();
        if (
            $productNote = ProductHelper::mapErrorCode(
                Request::verifyGPDataString('cHinweis'),
                ((float)Request::getVar('fB', 0) > 0) ? (float)$_GET['fB'] : 0.0
            )
        ) {
            $this->alertService->addNotice($productNote, 'productNote', ['showInAlertListTemplate' => false]);
        }
        if (Request::verifyGPCDataInt('isfreegift') === 1) {
            $this->alertService->addNotice(
                Shop::Lang()->get('productAvailableAsFreeGift', 'productDetails'),
                'productNote',
                ['showInAlertListTemplate' => false],
            );
        }
        if ($productError = ProductHelper::mapErrorCode(Request::verifyGPDataString('cFehler'))) {
            $this->alertService->addError($productError, 'productError');
        }
        if (
            $valid
            && isset($_POST['a'])
            && Request::verifyGPCDataInt('addproductbundle') === 1
            && ProductHelper::addProductBundleToCart(Request::verifyGPCDataInt('a'))
        ) {
            $this->alertService->addNotice(Shop::Lang()->get('basketAllAdded', 'messages'), 'allAdded');
            $this->state->productID = Request::pInt('aBundle');
        }
        // Warenkorbmatrix Anzeigen auf Artikel Attribut pruefen und falls vorhanden setzen
        if (
            isset($this->currentProduct->FunktionsAttribute['warenkorbmatrixanzeigen'])
            && \mb_strlen($this->currentProduct->FunktionsAttribute['warenkorbmatrixanzeigen']) > 0
        ) {
            $this->config['artikeldetails']['artikeldetails_warenkorbmatrix_anzeige'] =
                $this->currentProduct->FunktionsAttribute['warenkorbmatrixanzeigen'];
        }
        // Warenkorbmatrix Anzeigeformat auf Artikel Attribut pruefen und falls vorhanden setzen
        if (
            isset($this->currentProduct->FunktionsAttribute['warenkorbmatrixanzeigeformat'])
            && \mb_strlen($this->currentProduct->FunktionsAttribute['warenkorbmatrixanzeigeformat']) > 0
        ) {
            $this->config['artikeldetails']['artikeldetails_warenkorbmatrix_anzeigeformat'] =
                $this->currentProduct->FunktionsAttribute['warenkorbmatrixanzeigeformat'];
        }
        $similarProducts = (int)$this->config['artikeldetails']['artikeldetails_aehnlicheartikel_anzahl'] > 0
            ? $this->currentProduct->holeAehnlicheArtikel()
            : [];
        if ($this->state->childProductID > 0) {
            $child = new Artikel();
            $child->fuelleArtikel(
                $this->state->childProductID,
                Artikel::getDetailOptions(),
                $this->customerGroupID,
                $this->languageID
            );
            $child->verfuegbarkeitsBenachrichtigung = ProductHelper::showAvailabilityForm(
                $child,
                $this->config['artikeldetails']['benachrichtigung_nutzen']
            );

            $this->currentProduct = ProductHelper::combineParentAndChild($this->currentProduct, $child);
            $this->canonicalURL   = $this->currentProduct->baueVariKombiKindCanonicalURL(
                $this->currentProduct,
                $this->config['artikeldetails']['artikeldetails_canonicalurl_varkombikind'] !== 'N'
            );
        }
        if (
            $this->config['preisverlauf']['preisverlauf_anzeigen'] === 'Y'
            && Frontend::getCustomerGroup()->mayViewPrices()
        ) {
            $this->state->productID = $this->state->childProductID > 0
                ? $this->state->childProductID
                : $this->currentProduct->kArtikel;
            $priceHistory           = new Preisverlauf(0, $this->db, $this->cache);
            $priceHistory           = $priceHistory->gibPreisverlauf(
                $this->state->productID,
                $this->currentProduct->Preise->kKundengruppe,
                (int)$this->config['preisverlauf']['preisverlauf_anzahl_monate']
            );
        }
        // Canonical bei non SEO Shops oder wenn SEO kein Ergebnis geliefert hat
        if (empty($this->canonicalURL)) {
            $this->canonicalURL = $this->currentProduct->cURLFull;
        }
        $this->currentProduct->berechneSieSparenX((int)$this->config['artikeldetails']['sie_sparen_x_anzeigen']);

        $messages = ProductHelper::getProductMessages(product: $this->currentProduct);
        if ($this->config['artikeldetails']['artikeldetails_fragezumprodukt_anzeigen'] !== 'N') {
            $this->smarty->assign('Anfrage', ProductHelper::getProductQuestionFormDefaults());
        }
        if ($this->config['artikeldetails']['benachrichtigung_nutzen'] !== 'N') {
            $this->smarty->assign('Benachrichtigung', ProductHelper::getAvailabilityFormDefaults());
        }
        if ($valid && Request::pInt('fragezumprodukt') === 1) {
            $messages = ProductHelper::checkProductQuestion(
                $messages,
                $this->config['artikeldetails'],
                $this->currentProduct
            );
        } elseif ($valid && Request::pInt('benachrichtigung_verfuegbarkeit') === 1) {
            $messages = $this->checkAndSendAvailabilityMessage($messages);
        }
        foreach ($messages as $productNoticeKey => $productNotice) {
            $this->alertService->addDanger($productNotice, 'productNotice' . $productNoticeKey);
        }
        $this->currentCategory    = new Kategorie(
            $this->currentProduct->gibKategorie($this->customerGroupID),
            $this->languageID,
            $this->customerGroupID,
            false,
            $this->db
        );
        $this->expandedCategories = new KategorieListe();
        $this->expandedCategories->getOpenCategories($this->currentCategory);
        $ratingPage   = Request::verifyGPCDataInt('btgseite');
        $ratingStars  = Request::verifyGPCDataInt('btgsterne');
        $sorting      = Request::verifyGPCDataInt('sortierreihenfolge');
        $showRatings  = Request::verifyGPCDataInt('bewertung_anzeigen');
        $allLanguages = Request::verifyGPCDataInt('moreRating');
        if ($ratingPage === 0) {
            $ratingPage = 1;
        }
        if ($this->currentProduct->Bewertungen === null || $ratingStars > 0) {
            $this->currentProduct->holeBewertung(
                -1,
                $ratingPage,
                $ratingStars,
                $this->config['bewertung']['bewertung_freischalten'],
                $sorting,
                $this->config['bewertung']['bewertung_alle_sprachen'] === 'Y'
            );
            $this->currentProduct->holehilfreichsteBewertung();
        }

        if (Frontend::getCustomer()->getID() > 0) {
            $rated = ProductHelper::getRatedByCurrentCustomer(
                $this->currentProduct->kArtikel,
                $this->currentProduct->kVaterArtikel
            );
        }

        $this->currentProduct->Bewertungen->Sortierung = $sorting;

        $ratingsCount = $ratingStars === 0
            ? $this->currentProduct->Bewertungen->nAnzahlSprache
            : $this->currentProduct->Bewertungen->nSterne_arr[5 - $ratingStars];
        $ratingNav    = ProductHelper::getRatingNavigation(
            $ratingPage,
            $ratingStars,
            $ratingsCount,
            $this->config['bewertung']['bewertung_anzahlseite']
        );
        if (
            Request::hasGPCData(\QUERY_PARAM_CONFIG_ITEM)
            && isset(Frontend::getCart()->PositionenArr[Request::verifyGPCDataInt(\QUERY_PARAM_CONFIG_ITEM)])
        ) {
            ProductHelper::getEditConfigMode(Request::verifyGPCDataInt(\QUERY_PARAM_CONFIG_ITEM), $this->smarty);
            $this->smarty->assign(
                'voucherPrice',
                Tax::getGross(
                    Frontend::getCart()->PositionenArr[Request::verifyGPCDataInt(\QUERY_PARAM_CONFIG_ITEM)]->fPreis,
                    Tax::getSalesTax($this->currentProduct->kSteuerklasse)
                )
            );
        }
        $child  = $this->currentProduct->kVariKindArtikel ?? 0;
        $parent = $this->currentProduct->kArtikel;
        $nav    = $this->config['artikeldetails']['artikeldetails_navi_blaettern'] === 'Y'
            ? ProductHelper::getProductNavigation($parent, $this->currentCategory->getID())
            : null;
        $this->assignUploadData();
        $this->smarty->assign('showMatrix', $this->currentProduct->showMatrix())
            ->assign('arNichtErlaubteEigenschaftswerte', $this->currentProduct->nonAllowedVariationValues)
            ->assign('oAehnlicheArtikel_arr', $similarProducts)
            ->assign('UVPlocalized', $this->currentProduct->cUVPLocalized)
            ->assign('UVPBruttolocalized', Preise::getLocalizedPriceString($this->currentProduct->fUVPBrutto))
            ->assign('Artikel', $this->currentProduct)
            ->assign(
                'Xselling',
                $child > 0
                    ? ProductHelper::getXSelling($child, null, $this->config['artikeldetails'])
                    : ProductHelper::buildXSellersFromIDs($this->currentProduct->similarProducts, $parent)
            )
            ->assign('Artikelhinweise', $messages)
            ->assign(
                'verfuegbarkeitsBenachrichtigung',
                ProductHelper::showAvailabilityForm(
                    $this->currentProduct,
                    $this->config['artikeldetails']['benachrichtigung_nutzen']
                )
            )
            ->assign('BlaetterNavi', $ratingNav)
            ->assign(
                'BewertungsTabAnzeigen',
                (int)($ratingPage > 0
                    || $ratingStars > 0
                    || $showRatings > 0
                    || $allLanguages > 0)
            )
            ->assign('alertNote', $this->alertService->alertTypeExists(Alert::TYPE_NOTE))
            ->assign('bewertungSterneSelected', $ratingStars)
            ->assign('bPreisverlauf', \is_array($priceHistory) && \count($priceHistory) > 1)
            ->assign('preisverlaufData', $priceHistory)
            ->assign('NavigationBlaettern', $nav)
            ->assign('bereitsBewertet', $rated);

        $this->assignPagination();
        $AktuellerArtikel = $this->currentProduct;
        $this->preRender();

        \executeHook(\HOOK_ARTIKEL_PAGE, ['oArtikel' => $this->currentProduct]);

        if (Request::isAjaxRequest()) {
            $this->smarty->assign('listStyle', Text::filterXSS($_GET['isListStyle'] ?? ''));
        }

        return $this->smarty->getResponse('productdetails/index.tpl');
    }

    /**
     * @return void
     */
    protected function assignPagination(): void
    {
        $ratings = $this->currentProduct->Bewertungen->oBewertung_arr;
        if ((int)($this->currentProduct->HilfreichsteBewertung->oBewertung_arr[0]->nHilfreich ?? 0) > 0) {
            $ratings = \array_filter(
                $this->currentProduct->Bewertungen->oBewertung_arr,
                function ($rating): bool {
                    return (int)$this->currentProduct->HilfreichsteBewertung->oBewertung_arr[0]->kBewertung
                        !== (int)$rating->kBewertung;
                }
            );
        }
        $pagination = new Pagination('ratings');
        $pagination->setItemArray($ratings)
            ->setItemsPerPageOptions([(int)$this->config['bewertung']['bewertung_anzahlseite']])
            ->setDefaultItemsPerPage($this->config['bewertung']['bewertung_anzahlseite'])
            ->setSortByOptions([
                ['dDatum', Shop::Lang()->get('paginationOrderByDate')],
                ['nSterne', Shop::Lang()->get('paginationOrderByRating')],
                ['nHilfreich', Shop::Lang()->get('paginationOrderUsefulness')]
            ])
            ->setDefaultSortByDir((int)$this->config['bewertung']['bewertung_sortierung'])
            ->setSortFunction(function ($a, $b) use ($pagination): int {
                $sortBy  = $pagination->getSortByCol();
                $sortDir = $pagination->getSortDirSQL() === 0 ? +1 : -1;
                $valueA  = \is_string($a->$sortBy) ? \mb_convert_case($a->$sortBy, \MB_CASE_LOWER) : $a->$sortBy;
                $valueB  = \is_string($b->$sortBy) ? \mb_convert_case($b->$sortBy, \MB_CASE_LOWER) : $b->$sortBy;

                if ($b->kSprache === $this->languageID && $a->kSprache !== $this->languageID) {
                    return +1;
                }
                if ($a->kSprache === $this->languageID && $b->kSprache !== $this->languageID) {
                    return -1;
                }
                if ($valueA === $valueB) {
                    return 0;
                }
                if ($valueA < $valueB) {
                    return -$sortDir;
                }
                return +$sortDir;
            })
            ->assemble();

        $this->smarty->assign('ratingPagination', $pagination);
    }

    private function assignUploadData(): void
    {
        if (
            $this->currentProduct->hasUploads === false
            || $this->currentProduct->nIstVater !== 0
            || ($this->currentProduct->kVariKindArtikel ?? 0) !== 0
            || !Upload::checkLicense()
        ) {
            return;
        }
        $maxSize = Upload::uploadMax();
        $this->smarty->assign('nMaxUploadSize', $maxSize)
            ->assign('cMaxUploadSize', Upload::formatGroesse($maxSize))
            ->assign('oUploadSchema_arr', Upload::gibArtikelUploads($this->currentProduct->kArtikel));
    }
}
