<?php

declare(strict_types=1);

namespace JTL\Checkout;

use JTL\Cart\Cart;
use JTL\Cart\CartItem;
use JTL\Catalog\Product\Artikel;
use JTL\Catalog\Product\EigenschaftWert;
use JTL\Customer\Customer;
use JTL\DB\DbInterface;
use JTL\Helpers\Product;
use JTL\Helpers\Tax;
use JTL\Session\Frontend;
use JTL\Shop;
use stdClass;

/**
 * Class StockUpdater
 * @package JTL\Helpers
 */
class StockUpdater
{
    /**
     * @var int
     */
    private int $languageID;

    /**
     * @param DbInterface $db
     * @param Customer    $customer
     * @param Cart        $cart
     */
    public function __construct(
        private readonly DbInterface $db,
        private readonly Customer $customer,
        private readonly Cart $cart
    ) {
        $this->languageID = Shop::getLanguageID();
    }

    /**
     * @param Bestellung $order
     * @former KuponVerwendungen()
     * @since 5.2.0
     */
    public function updateCouponUsages(Bestellung $order): void
    {
        $couponID    = 0;
        $couponType  = '';
        $couponGross = 0;
        if (isset($_SESSION['VersandKupon']->kKupon) && $_SESSION['VersandKupon']->kKupon > 0) {
            $couponID    = (int)$_SESSION['VersandKupon']->kKupon;
            $couponType  = Kupon::TYPE_SHIPPING;
            $couponGross = $_SESSION['Versandart']->fPreis;
        }
        if (isset($_SESSION['NeukundenKupon']->kKupon) && $_SESSION['NeukundenKupon']->kKupon > 0) {
            $couponID   = (int)$_SESSION['NeukundenKupon']->kKupon;
            $couponType = Kupon::TYPE_NEWCUSTOMER;
        }
        if (isset($_SESSION['Kupon']->kKupon) && $_SESSION['Kupon']->kKupon > 0) {
            $couponID   = (int)$_SESSION['Kupon']->kKupon;
            $couponType = Kupon::TYPE_STANDARD;
        }
        foreach ($this->cart->PositionenArr as $item) {
            $item->nPosTyp = (int)$item->nPosTyp;
            if (
                !isset($_SESSION['VersandKupon'])
                && ($item->nPosTyp === \C_WARENKORBPOS_TYP_KUPON
                    || $item->nPosTyp === \C_WARENKORBPOS_TYP_NEUKUNDENKUPON)
            ) {
                $couponGross = Tax::getGross($item->fPreisEinzelNetto, CartItem::getTaxRate($item)) * (-1);
            }
        }
        if ($couponID <= 0) {
            return;
        }
        $this->db->queryPrepared(
            'UPDATE tkupon
                SET nVerwendungenBisher = nVerwendungenBisher + 1
                WHERE kKupon = :couponID',
            ['couponID' => $couponID]
        );

        $this->db->queryPrepared(
            'INSERT INTO `tkuponkunde` (kKupon, cMail, dErstellt, nVerwendungen)
                 VALUES (:couponID, :email, NOW(), :used)
                 ON DUPLICATE KEY UPDATE
              nVerwendungen = nVerwendungen + 1',
            [
                'couponID' => $couponID,
                'email'    => Kupon::hash($this->customer->cMail),
                'used'     => 1
            ]
        );

        $this->db->insert(
            'tkuponflag',
            (object)[
                'cKuponTyp'  => $couponType,
                'cEmailHash' => Kupon::hash($this->customer->cMail),
                'dErstellt'  => 'NOW()'
            ]
        );

        $couponOrder                     = new KuponBestellung();
        $couponOrder->kKupon             = $couponID;
        $couponOrder->kBestellung        = $order->kBestellung;
        $couponOrder->kKunde             = $this->cart->kKunde;
        $couponOrder->cBestellNr         = $order->cBestellNr;
        $couponOrder->fGesamtsummeBrutto = $order->fGesamtsumme;
        $couponOrder->fKuponwertBrutto   = $couponGross;
        $couponOrder->cKuponTyp          = $couponType;
        $couponOrder->dErstellt          = 'NOW()';

        $couponOrder->save();
    }

    /**
     * @param int   $productID
     * @param float $stockLevel
     * @param bool  $allowNegativeStock
     * @former aktualisiereKomponenteLagerbestand()
     * @since 5.2.0
     */
    public function updateBOMStock(int $productID, float $stockLevel, bool $allowNegativeStock): void
    {
        $boms = $this->db->getObjects(
            "SELECT tstueckliste.kStueckliste, tstueckliste.fAnzahl,
                tartikel.kArtikel, tartikel.fLagerbestand, tartikel.cLagerKleinerNull
            FROM tstueckliste
            JOIN tartikel
                ON tartikel.kStueckliste = tstueckliste.kStueckliste
            WHERE tstueckliste.kArtikel = :cid
                AND tartikel.cLagerBeachten = 'Y'",
            ['cid' => $productID]
        );
        foreach ($boms as $bom) {
            // Ist der aktuelle Bestand der Stückliste größer als dies mit dem Bestand der Komponente möglich wäre?
            $max = \floor($stockLevel / $bom->fAnzahl);
            if ($max < (float)$bom->fLagerbestand && (!$allowNegativeStock || $bom->cLagerKleinerNull === 'Y')) {
                // wenn ja, dann den Bestand der Stückliste entsprechend verringern, aber nur wenn die Komponente nicht
                // überberkaufbar ist oder die gesamte Stückliste Überverkäufe zulässt
                $this->db->update(
                    'tartikel',
                    'kArtikel',
                    (int)$bom->kArtikel,
                    (object)['fLagerbestand' => $max]
                );
            }
        }
    }

    /**
     * @param Artikel   $product
     * @param int|float $amount
     * @param array     $attributeValues
     * @param int       $productFilter
     * @return int|float - neuer Lagerbestand
     * @former aktualisiereLagerbestand()
     * @since 5.2.0
     */
    public function updateStock(Artikel $product, $amount, array $attributeValues, int $productFilter = 1)
    {
        $stockLevel = (float)$product->fLagerbestand;
        if ($amount <= 0 || $product->cLagerBeachten !== 'Y') {
            return $stockLevel;
        }
        if ($product->cLagerVariation === 'Y' && \is_array($attributeValues) && \count($attributeValues) > 0) {
            foreach ($attributeValues as $value) {
                $EigenschaftWert = new EigenschaftWert((int)$value->kEigenschaftWert, $this->db);
                if ($EigenschaftWert->fPackeinheit == 0) {
                    $EigenschaftWert->fPackeinheit = 1;
                }
                $this->db->queryPrepared(
                    'UPDATE teigenschaftwert
                        SET fLagerbestand = fLagerbestand - :inv
                        WHERE kEigenschaftWert = :aid',
                    [
                        'aid' => (int)$value->kEigenschaftWert,
                        'inv' => $amount * $EigenschaftWert->fPackeinheit
                    ]
                );
            }
            $this->updateProductStockLevel($product->kArtikel, $amount, $product->fPackeinheit);
        } elseif ($product->fPackeinheit > 0) {
            if ($product->kStueckliste > 0) {
                $stockLevel = $this->updateBOMStockLevel($product, $amount);
            } else {
                $this->updateProductStockLevel($product->kArtikel, $amount, $product->fPackeinheit);
                $tmpProduct = $this->db->select(
                    'tartikel',
                    'kArtikel',
                    (int)$product->kArtikel,
                    null,
                    null,
                    null,
                    null,
                    false,
                    'fLagerbestand'
                );
                if ($tmpProduct !== null) {
                    $stockLevel = (float)$tmpProduct->fLagerbestand;
                }
                // Stücklisten Komponente
                if (Product::isStuecklisteKomponente($product->kArtikel)) {
                    $this->updateBOMStock(
                        $product->kArtikel,
                        $stockLevel,
                        $product->cLagerKleinerNull === 'Y'
                    );
                }
            }
            // Aktualisiere Merkmale in tartikelmerkmal vom Vaterartikel
            if ($product->kVaterArtikel > 0) {
                Artikel::beachteVarikombiMerkmalLagerbestand($product->kVaterArtikel, $productFilter);
                $this->updateProductStockLevel($product->kVaterArtikel, $amount, $product->fPackeinheit);
            }
        }

        return $stockLevel;
    }

    /**
     * @param int       $productID
     * @param float|int $amount
     * @param float|int $unit
     * @former updateStock()
     * @since 5.2.0
     */
    public function updateProductStockLevel(int $productID, $amount, $unit): void
    {
        $this->db->queryPrepared(
            'UPDATE tartikel
                SET fLagerbestand = GREATEST(fLagerbestand - :amountSubstract, 0)
                WHERE kArtikel = :productID',
            [
                'amountSubstract' => $amount * $unit,
                'productID'       => $productID
            ]
        );
    }

    /**
     * @param Artikel   $bomProduct
     * @param int|float $amount
     * @return int|float - neuer Lagerbestand
     * @former aktualisiereStuecklistenLagerbestand()
     * @since 5.2.0
     */
    public function updateBOMStockLevel(Artikel $bomProduct, $amount)
    {
        $amount        = (float)$amount;
        $bomID         = (int)$bomProduct->kStueckliste;
        $oldStockLevel = (float)$bomProduct->fLagerbestand;
        $newStockLevel = $oldStockLevel;
        $negStockLevel = $oldStockLevel;
        if ($amount <= 0) {
            return $newStockLevel;
        }
        // Gibt es lagerrelevante Komponenten in der Stückliste?
        $boms = $this->db->getObjects(
            "SELECT tstueckliste.kArtikel, tstueckliste.fAnzahl
                FROM tstueckliste
                JOIN tartikel
                  ON tartikel.kArtikel = tstueckliste.kArtikel
                WHERE tstueckliste.kStueckliste = :slid
                    AND tartikel.cLagerBeachten = 'Y'",
            ['slid' => $bomID]
        );

        if (\count($boms) > 0) {
            // wenn ja, dann wird für diese auch der Bestand aktualisiert
            $currency                            = Frontend::getCurrency();
            $customerGroup                       = Frontend::getCustomerGroup();
            $customerGroupID                     = $this->customer->getGroupID();
            $languageID                          = $this->languageID;
            $options                             = Artikel::getDefaultOptions();
            $options->nKeineSichtbarkeitBeachten = 1;
            foreach ($boms as $component) {
                $tmpArtikel = new Artikel($this->db, $customerGroup, $currency);
                $tmpArtikel->fuelleArtikel((int)$component->kArtikel, $options, $customerGroupID, $languageID);
                $compStockLevel = \floor(
                    $this->updateStock(
                        $tmpArtikel,
                        $amount * $component->fAnzahl,
                        []
                    ) / $component->fAnzahl
                );

                if ($compStockLevel < $newStockLevel && $tmpArtikel->cLagerKleinerNull !== 'Y') {
                    // Neuer Bestand ist der Kleinste Komponententbestand aller Artikel ohne Überverkauf
                    $newStockLevel = $compStockLevel;
                } elseif ($compStockLevel < $negStockLevel) {
                    // Für Komponenten mit Überverkauf wird der kleinste Bestand ermittelt.
                    $negStockLevel = $compStockLevel;
                }
            }
        }

        // Ist der alte gleich dem neuen Bestand?
        if ($oldStockLevel === $newStockLevel) {
            // Es sind keine lagerrelevanten Komponenten vorhanden, die den Bestand der Stückliste herabsetzen.
            if ($negStockLevel === $newStockLevel) {
                // Es gibt auch keine Komponenten mit Überverkäufen, die den Bestand verringern, deshalb wird
                // der Bestand des Stücklistenartikels anhand des Verkaufs verringert
                $newStockLevel -= $amount * $bomProduct->fPackeinheit;
            } else {
                // Da keine lagerrelevanten Komponenten vorhanden sind, wird der kleinste Bestand der
                // Komponentent mit Überverkauf verwendet.
                $newStockLevel = $negStockLevel;
            }

            $this->db->update(
                'tartikel',
                'kArtikel',
                (int)$bomProduct->kArtikel,
                (object)['fLagerbestand' => $newStockLevel]
            );
        }
        // Kein Lagerbestands-Update für die Stückliste notwendig! Dies erfolgte bereits über die Komponentenabfrage und
        // die dortige Lagerbestandsaktualisierung!

        return $newStockLevel;
    }

    /**
     * @param int $productID
     * @param int $xsellID
     * @former aktualisiereXselling()
     * @since 5.2.0
     */
    public function updateXSelling(int $productID, int $xsellID): void
    {
        if (!$productID || !$xsellID) {
            return;
        }
        $obj = $this->db->select('txsellkauf', 'kArtikel', $productID, 'kXSellArtikel', $xsellID);
        if ($obj !== null && $obj->nAnzahl > 0) {
            $this->db->queryPrepared(
                'UPDATE txsellkauf
                     SET nAnzahl = nAnzahl + 1
                     WHERE kArtikel = :pid
                         AND kXSellArtikel = :xs',
                ['pid' => $productID, 'xs' => $xsellID]
            );
        } else {
            $xs                = new stdClass();
            $xs->kArtikel      = $productID;
            $xs->kXSellArtikel = $xsellID;
            $xs->nAnzahl       = 1;
            $this->db->insert('txsellkauf', $xs);
        }
    }

    /**
     * @param int       $productID
     * @param int|float $amount
     * @former aktualisiereBestseller()
     * @since 5.2.0
     */
    public function updateBestsellers(int $productID, $amount): void
    {
        if (!$productID || !$amount) {
            return;
        }
        $data = $this->db->select('tbestseller', 'kArtikel', $productID);
        if ($data !== null && $data->kArtikel > 0) {
            $this->db->queryPrepared(
                'UPDATE tbestseller SET fAnzahl = fAnzahl + :mnt WHERE kArtikel = :aid',
                ['mnt' => $amount, 'aid' => $productID]
            );
        } else {
            $bestseller           = new stdClass();
            $bestseller->kArtikel = $productID;
            $bestseller->fAnzahl  = $amount;
            $this->db->insert('tbestseller', $bestseller);
        }
        if (Product::isVariCombiChild($productID)) {
            $this->updateBestsellers(Product::getParent($productID), $amount);
        }
    }
}
