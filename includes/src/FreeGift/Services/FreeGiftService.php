<?php

declare(strict_types=1);

namespace JTL\FreeGift\Services;

use JTL\Abstracts\AbstractService;
use JTL\Cart\Cart;
use JTL\Catalog\Product\Artikel;
use JTL\Catalog\Product\Preise;
use JTL\FreeGift\DomainObjects\FreeGiftDomainObject;
use JTL\FreeGift\DomainObjects\FreeGiftProductDomainObject;
use JTL\FreeGift\Helper\FreeGiftProductsArray;
use JTL\FreeGift\Repositories\FreeGiftRepository;
use JTL\Shop;
use JTL\Shopsetting;

/**
 * Class FreeGiftsService
 * @package JTL\FreeGift
 * @since 5.4.0
 */
class FreeGiftService extends AbstractService
{
    /**
     * @var FreeGiftRepository|null
     */
    public ?FreeGiftRepository $freeGiftsRepository;

    /**
     * @param FreeGiftRepository|null $freeGiftsRepository
     * @since 5.4.0
     */
    public function __construct(
        ?FreeGiftRepository $freeGiftsRepository = null
    ) {
        $this->freeGiftsRepository = $freeGiftsRepository ?? new FreeGiftRepository();
    }

    /**
     * @return FreeGiftRepository
     */
    protected function getRepository(): FreeGiftRepository
    {
        return $this->freeGiftsRepository;
    }

    /**
     * @param array|null $conf
     * @param int        $customerGroupID
     * @return FreeGiftProductsArray
     * @since 5.4.0
     */
    public function getFreeGifts(?array $conf = null, int $customerGroupID = 0): FreeGiftProductsArray
    {
        $freeGiftProducts = new FreeGiftProductsArray();
        $conf             = $conf ?? Shopsetting::getInstance()->getSettings([\CONF_SONSTIGES]);
        if ($conf['sonstiges']['sonstiges_gratisgeschenk_nutzen'] !== 'Y') {
            return $freeGiftProducts;
        }

        $sortBy  = 'ORDER BY CAST(tartikelattribut.cWert AS DECIMAL)';
        $sortDir = 'ASC';
        if ($conf['sonstiges']['sonstiges_gratisgeschenk_sortierung'] === 'N') {
            $sortBy = 'ORDER BY tartikel.cName';
        } elseif ($conf['sonstiges']['sonstiges_gratisgeschenk_sortierung'] === 'L') {
            $sortBy  = 'ORDER BY tartikel.fLagerbestand';
            $sortDir = 'DESC';
        }

        foreach (
            $this->getRepository()->getFreeGiftProducts(
                limit: (int)$conf['sonstiges']['sonstiges_gratisgeschenk_anzahl'] > 0
                    ? 'LIMIT ' . $conf['sonstiges']['sonstiges_gratisgeschenk_anzahl']
                    : '',
                sortBy: $sortBy,
                sortDirection: $sortDir,
                customerGroupID: $customerGroupID,
            ) as $freeGiftProduct
        ) {
            $freeGiftProducts->append(
                $this->initFreeGiftProductDomainObject($freeGiftProduct)
            );
        }

        return $freeGiftProducts;
    }

    /**
     * @param int   $productID
     * @param float $basketSum
     * @param int   $customerGroupID
     * @return FreeGiftProductDomainObject|null
     * @since 5.4.0
     */
    public function getFreeGiftProduct(
        int $productID,
        float $basketSum,
        int $customerGroupID = 0
    ): ?FreeGiftProductDomainObject {
        $freeGiftProduct = $this->getRepository()->getByProductID(
            $productID,
            $customerGroupID
        );
        if ($freeGiftProduct === null) {
            return null;
        }

        if ($freeGiftProduct->productValue < $basketSum) {
            return $this->initFreeGiftProductDomainObject($freeGiftProduct);
        }

        return null;
    }

    /**
     * @param float $basketValue
     * @param int   $customerGroupID
     * @return float
     * @since 5.4.0
     */
    public function getNextAvailableMissingAmount(
        float $basketValue = 0.00,
        int $customerGroupID = 0
    ): float {
        $nextFreeGiftAmount = $this->getRepository()->getNextAvailable(
            $basketValue,
            $customerGroupID,
        )[0]->productValue ?? 0.00;

        return $nextFreeGiftAmount > 0
            ? $nextFreeGiftAmount - $basketValue
            : 0.00;
    }

    /**
     * @param int $productID
     * @param int $basketID
     * @param int $quantity
     * @return int
     * @since 5.4.0
     */
    public function saveFreeGift(
        int $productID,
        int $basketID,
        int $quantity
    ): int {
        return $this->getRepository()->insert(
            new FreeGiftDomainObject(
                productID: $productID,
                basketID : $basketID,
                quantity : $quantity,
            )
        );
    }

    /**
     * @param Cart $cart
     * @return bool
     * @since 5.4.0
     * @comment Used in smarty tpl (basket/freegift_hint.tpl)
     */
    public function basketHoldsFreeGift(Cart $cart): bool
    {
        return $cart->posTypEnthalten(\C_WARENKORBPOS_TYP_GRATISGESCHENK);
    }

    /**
     * @param string $limitSQL
     * @return array
     * @since 5.4.0
     */
    public function getCommonFreeGifts(string $limitSQL): array
    {
        return $this->getRepository()->getCommonFreeGifts($limitSQL);
    }

    /**
     * @return int
     * @since 5.4.0
     */
    public function getCommonFreeGiftsCount(): int
    {
        return $this->getRepository()->getCommonFreeGiftsCount();
    }

    /**
     * @param string $limitSQL
     * @return int[]
     * @since 5.4.0
     */
    public function getActiveFreeGiftIDs(string $limitSQL): array
    {
        return $this->getRepository()->getActiveFreeGiftIDs($limitSQL);
    }

    /**
     * @return int
     * @since 5.4.0
     */
    public function getActiveFreeGiftsCount(): int
    {
        return $this->getRepository()->getActiveFreeGiftsCount();
    }

    /**
     * @param string $limitSQL
     * @return array{productID: int, quantity: int, orderCreated: string, totalOrderValue: float}
     * @since 5.4.0
     */
    public function getRecentFreeGifts(string $limitSQL): array
    {
        return $this->getRepository()->getRecentFreeGifts($limitSQL);
    }

    /**
     * @return int
     * @since 5.4.0
     */
    public function getRecentFreeGiftsCount(): int
    {
        return $this->getRepository()->getRecentFreeGiftsCount();
    }

    /**
     * @param object $freeGiftProduct
     * @return FreeGiftProductDomainObject|null
     */
    private function initFreeGiftProductDomainObject(
        object $freeGiftProduct,
    ): ?FreeGiftProductDomainObject {
        $product = new Artikel();
        try {
            $product->fuelleArtikel($freeGiftProduct->productID, Artikel::getDefaultOptions());
        } catch (\Exception $e) {
            Shop::Container()->getLogService()->error(
                'Error while loading product property in FreeGiftService::appendToFreeGiftProductArray(): '
                . $e->getMessage(),
            );
        }

        if (
            $product->kArtikel > 0
            && ($product->kEigenschaftKombi > 0
                || \count($product->Variationen) === 0)
        ) {
            $product->cBestellwert = Preise::getLocalizedPriceString($freeGiftProduct->productValue);
            return new FreeGiftProductDomainObject(
                productID    : $freeGiftProduct->productID,
                availableFrom: $freeGiftProduct->productValue,
                product      : $product,
            );
        }

        return null;
    }
}
