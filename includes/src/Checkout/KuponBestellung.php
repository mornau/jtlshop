<?php

declare(strict_types=1);

namespace JTL\Checkout;

use JTL\Shop;
use stdClass;

/**
 * Class KuponBestellung
 * @package JTL\Checkout
 */
class KuponBestellung
{
    /**
     * @var int|null
     */
    public ?int $kKupon = null;

    /**
     * @var int|null
     */
    public ?int $kBestellung = null;

    /**
     * @var int|null
     */
    public ?int $kKunde = null;

    /**
     * @var string|null
     */
    public ?string $cBestellNr = null;

    /**
     * @var float|string|null
     */
    public string|null|float $fGesamtsummeBrutto = null;

    /**
     * @var float|string|null
     */
    public string|null|float $fKuponwertBrutto = null;

    /**
     * @var string|null
     */
    public ?string $cKuponTyp = null;

    /**
     * @var string|null
     */
    public ?string $dErstellt = null;

    /**
     * KuponBestellung constructor.
     *
     * @param int $couponID
     * @param int $orderID
     */
    public function __construct(int $couponID = 0, int $orderID = 0)
    {
        if ($couponID > 0 && $orderID > 0) {
            $this->loadFromDB($couponID, $orderID);
        }
    }

    /**
     * @param int $couponID
     * @param int $orderID
     * @return $this
     */
    private function loadFromDB(int $couponID = 0, int $orderID = 0): self
    {
        $item = Shop::Container()->getDB()->select(
            'tkuponbestellung',
            'kKupon',
            $couponID,
            'kBestellung',
            $orderID
        );
        if ($item === null || $item->kKupon <= 0) {
            return $this;
        }
        $this->kKupon             = (int)$item->kKupon;
        $this->kBestellung        = (int)$item->kBestellung;
        $this->kKunde             = (int)$item->kKunde;
        $this->cBestellNr         = $item->cBestellNr;
        $this->fGesamtsummeBrutto = $item->fGesamtsummeBrutto;
        $this->fKuponwertBrutto   = $item->fKuponwertBrutto;
        $this->cKuponTyp          = $item->cKuponTyp;
        $this->dErstellt          = $item->dErstellt;

        return $this;
    }

    /**
     * @param bool $primary
     * @return ($primary is true ? int|false : bool)
     */
    public function save(bool $primary = true): bool|int
    {
        $ins = new stdClass();
        foreach (\array_keys(\get_object_vars($this)) as $member) {
            $ins->$member = $this->$member;
        }
        $key = Shop::Container()->getDB()->insert('tkuponbestellung', $ins);
        if ($key < 1) {
            return false;
        }

        return $primary ? $key : true;
    }

    /**
     * @return int
     */
    public function update(): int
    {
        $_upd                      = new stdClass();
        $_upd->kKupon              = $this->kKupon;
        $_upd->kBestellung         = $this->kBestellung;
        $_upd->kKunde              = $this->kKunde;
        $_upd->cBestellNr          = $this->cBestellNr;
        $_upd->fGesammtsummeBrutto = $this->fGesamtsummeBrutto;
        $_upd->fKuponwertBrutto    = $this->fKuponwertBrutto;
        $_upd->cKuponTyp           = $this->cKuponTyp;
        $_upd->dErstellt           = $this->dErstellt;

        return Shop::Container()->getDB()->update(
            'tkuponbestellung',
            ['kKupon', 'kBestellung'],
            [$this->kKupon, $this->kBestellung],
            $_upd
        );
    }

    /**
     * @return int
     */
    public function delete(): int
    {
        return Shop::Container()->getDB()->delete(
            'tkupon',
            ['kKupon', 'kBestellung'],
            [$this->kKupon, $this->kBestellung]
        );
    }

    /**
     * @param int $kKupon
     * @return $this
     */
    public function setKupon(int $kKupon): self
    {
        $this->kKupon = $kKupon;

        return $this;
    }

    /**
     * @param int $orderID
     * @return $this
     */
    public function setBestellung(int $orderID): self
    {
        $this->kBestellung = $orderID;

        return $this;
    }

    /**
     * @param int $customerID
     * @return $this
     */
    public function setKunden(int $customerID): self
    {
        $this->kKunde = $customerID;

        return $this;
    }

    /**
     * @param string $cBestellNr
     * @return $this
     */
    public function setBestellNr(string $cBestellNr): self
    {
        $this->cBestellNr = $cBestellNr;

        return $this;
    }

    /**
     * @param float|string $fGesamtsummeBrutto
     * @return $this
     */
    public function setGesamtsummeBrutto(float|string $fGesamtsummeBrutto): self
    {
        $this->fGesamtsummeBrutto = (float)$fGesamtsummeBrutto;

        return $this;
    }

    /**
     * @param float|string $fKuponwertBrutto
     * @return $this
     */
    public function setKuponwertBrutto(float|string $fKuponwertBrutto): self
    {
        $this->fKuponwertBrutto = (float)$fKuponwertBrutto;

        return $this;
    }

    /**
     * @param string $cKuponTyp
     * @return $this
     */
    public function setKuponTyp(string $cKuponTyp): self
    {
        $this->cKuponTyp = $cKuponTyp;

        return $this;
    }

    /**
     * @param string $dErstellt
     * @return $this
     */
    public function setErstellt(string $dErstellt): self
    {
        $this->dErstellt = $dErstellt;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getKupon(): ?int
    {
        return $this->kKupon;
    }

    /**
     * @return int|null
     */
    public function getBestellung(): ?int
    {
        return $this->kBestellung;
    }

    /**
     * @return int|null
     */
    public function getKunde(): ?int
    {
        return $this->kKunde;
    }

    /**
     * @return string|null
     */
    public function getBestellNr(): ?string
    {
        return $this->cBestellNr;
    }

    /**
     * @return string|float|null
     */
    public function getGesamtsummeBrutto(): float|string|null
    {
        return $this->fGesamtsummeBrutto;
    }

    /**
     * @return string|float|null
     */
    public function getKuponwertBrutto(): float|string|null
    {
        return $this->fKuponwertBrutto;
    }

    /**
     * @return string|null
     */
    public function getKuponTyp(): ?string
    {
        return $this->cKuponTyp;
    }

    /**
     * @return string|null
     */
    public function getErstellt(): ?string
    {
        return $this->dErstellt;
    }

    /**
     * Gets used coupons from orders
     *
     * @param string $start
     * @param string $end
     * @param int    $couponID
     * @return array[]
     */
    public static function getOrdersWithUsedCoupons(string $start, string $end, int $couponID = 0): array
    {
        return Shop::Container()->getDB()->getArrays(
            'SELECT kbs.*, wkp.cName, kp.kKupon
                FROM tkuponbestellung AS kbs
                LEFT JOIN tbestellung AS bs 
                   ON kbs.kBestellung = bs.kBestellung
                LEFT JOIN twarenkorbpos AS wkp 
                    ON bs.kWarenkorb = wkp.kWarenkorb
                LEFT JOIN tkupon AS kp 
                    ON kbs.kKupon = kp.kKupon
                WHERE kbs.dErstellt BETWEEN :strt AND :nd
                    AND bs.cStatus != :stt
                    AND (wkp.nPosTyp = 3 OR wkp.nPosTyp = 7) ' .
            ($couponID > 0 ? ' AND kp.kKupon = ' . $couponID : '') . '
                ORDER BY kbs.dErstellt DESC',
            ['strt' => $start, 'nd' => $end, 'stt' => \BESTELLUNG_STATUS_STORNO]
        );
    }
}
