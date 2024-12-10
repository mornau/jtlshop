<?php

declare(strict_types=1);

namespace JTL\Router\Controller\Backend;

use JTL\Backend\Permissions;
use JTL\Catalog\Product\Artikel;
use JTL\FreeGift\Services\FreeGiftService;
use JTL\Helpers\Request;
use JTL\Pagination\Pagination;
use JTL\Session\Frontend;
use JTL\Shop;
use JTL\Smarty\JTLSmarty;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class GiftsController
 * @package JTL\Router\Controller\Backend
 */
class GiftsController extends AbstractBackendController
{
    /**
     * @var FreeGiftService
     */
    protected FreeGiftService $freeGiftService;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        $this->freeGiftService = Shop::Container()->getFreeGiftService();
    }

    /**
     * @inheritdoc
     */
    public function getResponse(ServerRequestInterface $request, array $args, JTLSmarty $smarty): ResponseInterface
    {
        $this->smarty = $smarty;
        $this->checkPermissions(Permissions::MODULE_GIFT_VIEW);
        $this->getText->loadAdminLocale('pages/gratisgeschenk');

        $settingsIDs = [
            'configgroup_10_gifts',
            'sonstiges_gratisgeschenk_nutzen',
            'sonstiges_gratisgeschenk_anzahl',
            'sonstiges_gratisgeschenk_sortierung',
            'sonstiges_gratisgeschenk_noch_nicht_verfuegbar_anzeigen',
            'sonstiges_gratisgeschenk_wk_hinweis_anzeigen'
        ];

        if (Request::verifyGPCDataInt('einstellungen') === 1) {
            $this->alertService->addSuccess(
                $this->saveAdminSettings($settingsIDs, $_POST, [\CACHING_GROUP_OPTION], true),
                'saveSettings'
            );
        }
        $paginationActive  = (new Pagination('aktiv'))
            ->setItemCount($this->getActiveCount())
            ->assemble();
        $paginationCommon  = (new Pagination('haeufig'))
            ->setItemCount($this->getCommonCount())
            ->assemble();
        $paginationLast100 = (new Pagination('letzte100'))
            ->setItemCount($this->getRecentCount())
            ->assemble();
        $this->getAdminSectionSettings($settingsIDs, true);

        return $smarty
            ->assign('oPagiAktiv', $paginationActive)
            ->assign('oPagiHaeufig', $paginationCommon)
            ->assign('oPagiLetzte100', $paginationLast100)
            ->assign('route', $this->route)
            ->assign(
                'oAktiveGeschenk_arr',
                $this->getActive(' LIMIT ' . $paginationActive->getLimitSQL())
            )
            ->assign(
                'oHaeufigGeschenk_arr',
                $this->getCommon(' LIMIT ' . $paginationCommon->getLimitSQL())
            )
            ->assign(
                'oLetzten100Geschenk_arr',
                $this->getRecent100(' LIMIT ' . $paginationLast100->getLimitSQL())
            )
            ->getResponse('gratisgeschenk.tpl');
    }

    /**
     * @param string $sql
     * @return Artikel[]
     * @former holeAktiveGeschenke()
     */
    private function getActive(string $sql): array
    {
        $res = [];
        if ($sql === '') {
            return $res;
        }

        $options                            = Artikel::getDefaultOptions();
        $options->nKeinLagerbestandBeachten = 1;
        $customerGroup                      = Frontend::getCustomerGroup();
        $currency                           = Frontend::getCurrency();
        $freeGiftService                    = $this->freeGiftService;
        foreach ($freeGiftService->getActiveFreeGiftIDs($sql) as $productID) {
            $product = new Artikel($this->db, $customerGroup, $currency, $this->cache);
            $product->fuelleArtikel($productID, $options, 0, 0, true);
            if ($product->kArtikel > 0) {
                $res[] = $product;
            }
        }

        return $res;
    }

    /**
     * @param string $sql
     * @return array<object{artikel: Artikel, lastOrdered: string, avgOrderValue: string}>
     * @former holeHaeufigeGeschenke()
     */
    private function getCommon(string $sql): array
    {
        $res = [];
        if ($sql === '') {
            return $res;
        }

        $options                            = Artikel::getDefaultOptions();
        $options->nKeinLagerbestandBeachten = 1;
        $customerGroup                      = Frontend::getCustomerGroup();
        $currency                           = Frontend::getCurrency();
        $freeGiftService                    = $this->freeGiftService;
        foreach ($freeGiftService->getCommonFreeGifts($sql) as $item) {
            $product = new Artikel($this->db, $customerGroup, $currency, $this->cache);
            $product->fuelleArtikel($item->productID, $options, 0, 0, true);
            if ($product->kArtikel > 0) {
                $product->nGGAnzahl = $item->quantity;
                $res[]              = (object)[
                    'artikel'       => $product,
                    'lastOrdered'   => \date_format(\date_create($item->lastOrdered), 'd.m.Y H:i:s'),
                    'avgOrderValue' => $item->avgOrderValue
                ];
            }
        }

        return $res;
    }

    /**
     * @param string $sql
     * @return array<object{artikel: Artikel, orderCreated: string, orderValue: string}>
     * @former holeLetzten100Geschenke()
     */
    private function getRecent100(string $sql): array
    {
        $res = [];
        if ($sql === '') {
            return $res;
        }

        $options                            = Artikel::getDefaultOptions();
        $options->nKeinLagerbestandBeachten = 1;
        $customerGroup                      = Frontend::getCustomerGroup();
        $currency                           = Frontend::getCurrency();
        $freeGiftService                    = $this->freeGiftService;
        foreach ($freeGiftService->getRecentFreeGifts($sql) as $item) {
            $product = new Artikel($this->db, $customerGroup, $currency, $this->cache);
            $product->fuelleArtikel($item->productID, $options, 0, 0, true);
            if ($product->kArtikel > 0) {
                $product->nGGAnzahl = $item->quantity;
                $res[]              = (object)[
                    'artikel'      => $product,
                    'orderCreated' => \date_format(\date_create($item->orderCreated), 'd.m.Y H:i:s'),
                    'orderValue'   => $item->totalOrderValue
                ];
            }
        }

        return $res;
    }

    /**
     * @return int
     * @former gibAnzahlAktiverGeschenke()
     */
    private function getActiveCount(): int
    {
        return $this->freeGiftService->getActiveFreeGiftsCount();
    }

    /**
     * @return int
     * @former gibAnzahlHaeufigGekaufteGeschenke()
     */
    private function getCommonCount(): int
    {
        return $this->freeGiftService->getCommonFreeGiftsCount();
    }

    /**
     * @return int
     * @former gibAnzahlLetzten100Geschenke()
     */
    private function getRecentCount(): int
    {
        return $this->freeGiftService->getRecentFreeGiftsCount();
    }
}
