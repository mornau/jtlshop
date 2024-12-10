<?php

declare(strict_types=1);

namespace JTL\Checkout;

use JTL\Helpers\GeneralObject;
use JTL\Shop;

/**
 * Class ZahlungsInfo
 * @package JTL\Checkout
 */
class ZahlungsInfo
{
    /**
     * @var int|null
     */
    public $kZahlungsInfo;

    /**
     * @var int|null
     */
    public $kBestellung;

    /**
     * @var int|null
     */
    public $kKunde;

    /**
     * @var string|null
     */
    public $cBankName;

    /**
     * @var string|null
     */
    public $cBLZ;

    /**
     * @var string|null
     */
    public $cBIC;

    /**
     * @var string|null
     */
    public $cIBAN;

    /**
     * @var string|null
     */
    public $cKontoNr;

    /**
     * @var string|null
     */
    public $cKartenNr;

    /**
     * @var string|null
     */
    public $cGueltigkeit;

    /**
     * @var string|null
     */
    public $cCVV;

    /**
     * @var string|null
     */
    public $cKartenTyp;

    /**
     * @var string|null
     */
    public $cInhaber;

    /**
     * @var string|null
     */
    public $cVerwendungszweck;

    /**
     * @var string|null
     */
    public $cAbgeholt;

    /**
     * @param int $id
     * @param int $orderID
     */
    public function __construct(int $id = 0, int $orderID = 0)
    {
        if ($id > 0 || $orderID > 0) {
            $this->loadFromDB($id, $orderID);
        }
    }

    /**
     * @param int $id
     * @param int $orderID
     * @return $this
     */
    public function loadFromDB(int $id, int $orderID): self
    {
        $obj = null;
        if ($id > 0) {
            $obj = Shop::Container()->getDB()->select('tzahlungsinfo', 'kZahlungsInfo', $id);
        } elseif ($orderID > 0) {
            $obj = Shop::Container()->getDB()->select('tzahlungsinfo', 'kBestellung', $orderID);
        }
        if ($obj === null) {
            return $this;
        }
        $obj->kZahlungsInfo = (int)$obj->kZahlungsInfo;
        $obj->kBestellung   = (int)$obj->kBestellung;
        $obj->kKunde        = (int)$obj->kKunde;
        foreach (\array_keys(\get_object_vars($obj)) as $member) {
            $this->$member = $obj->$member;
        }
        if ($this->kZahlungsInfo > 0) {
            $this->entschluesselZahlungsinfo();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function verschluesselZahlungsinfo(): self
    {
        $cryptoService = Shop::Container()->getCryptoService();

        $this->cBankName         = $cryptoService->encryptXTEA(\trim($this->cBankName ?? ''));
        $this->cKartenNr         = $cryptoService->encryptXTEA(\trim($this->cKartenNr ?? ''));
        $this->cCVV              = $cryptoService->encryptXTEA(\trim($this->cCVV ?? ''));
        $this->cKontoNr          = $cryptoService->encryptXTEA(\trim($this->cKontoNr ?? ''));
        $this->cBLZ              = $cryptoService->encryptXTEA(\trim($this->cBLZ ?? ''));
        $this->cIBAN             = $cryptoService->encryptXTEA(\trim($this->cIBAN ?? ''));
        $this->cBIC              = $cryptoService->encryptXTEA(\trim($this->cBIC ?? ''));
        $this->cInhaber          = $cryptoService->encryptXTEA(\trim($this->cInhaber ?? ''));
        $this->cVerwendungszweck = $cryptoService->encryptXTEA(\trim($this->cVerwendungszweck ?? ''));

        return $this;
    }

    /**
     * @return $this
     */
    public function entschluesselZahlungsinfo(): self
    {
        $cryptoService = Shop::Container()->getCryptoService();

        $this->cBankName         = \trim($cryptoService->decryptXTEA($this->cBankName ?? ''));
        $this->cKartenNr         = \trim($cryptoService->decryptXTEA($this->cKartenNr ?? ''));
        $this->cCVV              = \trim($cryptoService->decryptXTEA($this->cCVV ?? ''));
        $this->cKontoNr          = \trim($cryptoService->decryptXTEA($this->cKontoNr ?? ''));
        $this->cBLZ              = \trim($cryptoService->decryptXTEA($this->cBLZ ?? ''));
        $this->cIBAN             = \trim($cryptoService->decryptXTEA($this->cIBAN ?? ''));
        $this->cBIC              = \trim($cryptoService->decryptXTEA($this->cBIC ?? ''));
        $this->cInhaber          = \trim($cryptoService->decryptXTEA($this->cInhaber ?? ''));
        $this->cVerwendungszweck = \trim($cryptoService->decryptXTEA($this->cVerwendungszweck ?? ''));

        return $this;
    }

    /**
     * @return int
     */
    public function insertInDB(): int
    {
        $this->cAbgeholt = 'N';
        $this->verschluesselZahlungsinfo();
        $obj = GeneralObject::copyMembers($this);
        unset($obj->kZahlungsInfo);
        $this->kZahlungsInfo = Shop::Container()->getDB()->insert('tzahlungsinfo', $obj);
        $this->entschluesselZahlungsinfo();

        return $this->kZahlungsInfo;
    }

    /**
     * @return int
     */
    public function updateInDB(): int
    {
        $this->verschluesselZahlungsinfo();
        $obj = GeneralObject::copyMembers($this);
        $res = Shop::Container()->getDB()->update('tzahlungsinfo', 'kZahlungsInfo', $obj->kZahlungsInfo, $obj);
        $this->entschluesselZahlungsinfo();

        return $res;
    }
}
