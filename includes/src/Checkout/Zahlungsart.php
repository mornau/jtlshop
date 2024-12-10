<?php

declare(strict_types=1);

namespace JTL\Checkout;

use JTL\MainModel;
use JTL\Shop;

/**
 * Class Zahlungsart
 * @package JTL\Checkout
 */
class Zahlungsart extends MainModel
{
    /**
     * @var int|null
     */
    public $kZahlungsart;

    /**
     * @var string|null
     */
    public $cName;

    /**
     * @var string|null
     */
    public $cModulId;

    /**
     * @var string|null
     */
    public $cKundengruppen;

    /**
     * @var string|null
     */
    public $cZusatzschrittTemplate;

    /**
     * @var string|null
     */
    public $cPluginTemplate;

    /**
     * @var string|null
     */
    public $cBild;

    /**
     * @var int|null
     */
    public $nSort;

    /**
     * @var int|null
     */
    public $nMailSenden;

    /**
     * @var int|null
     */
    public $nActive;

    /**
     * @var string|null
     */
    public $cAnbieter;

    /**
     * @var string|null
     */
    public $cTSCode;

    /**
     * @var int|null
     */
    public $nWaehrendBestellung;

    /**
     * @var int|null
     */
    public $nCURL;

    /**
     * @var int|null
     */
    public $nSOAP;

    /**
     * @var int|null
     */
    public $nSOCKETS;

    /**
     * @var int|null
     */
    public $nNutzbar;

    /**
     * @var string|null
     */
    public $cHinweisText;

    /**
     * @var string|null
     */
    public $cHinweisTextShop;

    /**
     * @var string|null
     */
    public $cGebuehrname;

    /**
     * @var array|null
     */
    public $einstellungen;

    /**
     * @var bool
     */
    public bool $bPayAgain = false;

    /**
     * @return int|null
     */
    public function getZahlungsart(): ?int
    {
        return $this->kZahlungsart;
    }

    /**
     * @param int|string $kZahlungsart
     * @return $this
     */
    public function setZahlungsart(int|string $kZahlungsart): self
    {
        $this->kZahlungsart = (int)$kZahlungsart;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->cName;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->cName = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getModulId(): ?string
    {
        return $this->cModulId;
    }

    /**
     * @param string $cModulId
     * @return $this
     */
    public function setModulId(string $cModulId): self
    {
        $this->cModulId = $cModulId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getKundengruppen(): ?string
    {
        return $this->cKundengruppen;
    }

    /**
     * @param string $cKundengruppen
     * @return $this
     */
    public function setKundengruppen(string $cKundengruppen): self
    {
        $this->cKundengruppen = $cKundengruppen;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getZusatzschrittTemplate(): ?string
    {
        return $this->cZusatzschrittTemplate;
    }

    /**
     * @param string|null $cZusatzschrittTemplate
     * @return $this
     */
    public function setZusatzschrittTemplate(?string $cZusatzschrittTemplate): self
    {
        $this->cZusatzschrittTemplate = $cZusatzschrittTemplate;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPluginTemplate(): ?string
    {
        return $this->cPluginTemplate;
    }

    /**
     * @param string|null $cPluginTemplate
     * @return $this
     */
    public function setPluginTemplate(?string $cPluginTemplate): self
    {
        $this->cPluginTemplate = $cPluginTemplate;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBild(): ?string
    {
        return $this->cBild;
    }

    /**
     * @param string $cBild
     * @return $this
     */
    public function setBild(string $cBild): self
    {
        $this->cBild = $cBild;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSort(): ?int
    {
        return $this->nSort;
    }

    /**
     * @param int|string $sort
     * @return $this
     */
    public function setSort(int|string $sort): self
    {
        $this->nSort = (int)$sort;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMailSenden(): ?int
    {
        return $this->nMailSenden;
    }

    /**
     * @param int|string $nMailSenden
     * @return $this
     */
    public function setMailSenden(int|string $nMailSenden): self
    {
        $this->nMailSenden = (int)$nMailSenden;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getActive(): ?int
    {
        return $this->nActive;
    }

    /**
     * @param int|string $nActive
     * @return $this
     */
    public function setActive(int|string $nActive): self
    {
        $this->nActive = $nActive;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAnbieter(): ?string
    {
        return $this->cAnbieter;
    }

    /**
     * @param string $cAnbieter
     * @return $this
     */
    public function setAnbieter(string $cAnbieter): self
    {
        $this->cAnbieter = $cAnbieter;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTSCode(): ?string
    {
        return $this->cTSCode;
    }

    /**
     * @param string $cTSCode
     * @return $this
     */
    public function setTSCode(string $cTSCode): self
    {
        $this->cTSCode = $cTSCode;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getWaehrendBestellung(): ?int
    {
        return $this->nWaehrendBestellung;
    }

    /**
     * @param int|string $nWaehrendBestellung
     * @return $this
     */
    public function setWaehrendBestellung(int|string $nWaehrendBestellung): self
    {
        $this->nWaehrendBestellung = (int)$nWaehrendBestellung;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCURL(): ?int
    {
        return $this->nCURL;
    }

    /**
     * @param int|string $nCURL
     * @return $this
     */
    public function setCURL(int|string $nCURL): self
    {
        $this->nCURL = (int)$nCURL;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSOAP(): ?int
    {
        return $this->nSOAP;
    }

    /**
     * @param int|string $nSOAP
     * @return $this
     */
    public function setSOAP(int|string $nSOAP): self
    {
        $this->nSOAP = (int)$nSOAP;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSOCKETS(): ?int
    {
        return $this->nSOCKETS;
    }

    /**
     * @param int|string $nSOCKETS
     * @return $this
     */
    public function setSOCKETS(int|string $nSOCKETS): self
    {
        $this->nSOCKETS = (int)$nSOCKETS;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getNutzbar(): ?int
    {
        return $this->nNutzbar;
    }

    /**
     * @param int|string $nNutzbar
     * @return $this
     */
    public function setNutzbar(int|string $nNutzbar): self
    {
        $this->nNutzbar = (int)$nNutzbar;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHinweisText(): ?string
    {
        return $this->cHinweisText;
    }

    /**
     * @param string $cHinweisText
     * @return $this
     */
    public function setHinweisText(string $cHinweisText): self
    {
        $this->cHinweisText = $cHinweisText;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHinweisTextShop(): ?string
    {
        return $this->cHinweisTextShop;
    }

    /**
     * @param string $cHinweisTextShop
     * @return $this
     */
    public function setHinweisTextShop(string $cHinweisTextShop): self
    {
        $this->cHinweisTextShop = $cHinweisTextShop;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGebuehrname(): ?string
    {
        return $this->cGebuehrname;
    }

    /**
     * @param string $cGebuehrname
     * @return $this
     */
    public function setGebuehrname(string $cGebuehrname): self
    {
        $this->cGebuehrname = $cGebuehrname;

        return $this;
    }

    /**
     * @param int         $id
     * @param null|object $data
     * @param null|array  $option
     * @return $this
     */
    public function load($id, $data = null, $option = null): self
    {
        $id = (int)$id;
        if ($id <= 0) {
            return $this;
        }
        $iso  = $option['iso'] ?? Shop::getLanguageCode();
        $item = Shop::Container()->getDB()->getSingleObject(
            'SELECT z.kZahlungsart, COALESCE(s.cName, z.cName) AS cName, z.cModulId, z.cKundengruppen,
                    z.cZusatzschrittTemplate, z.cPluginTemplate, z.cBild, z.nSort, z.nMailSenden, z.nActive,
                    z.cAnbieter, z.cTSCode, z.nWaehrendBestellung, z.nCURL, z.nSOAP, z.nSOCKETS, z.nNutzbar,
                    s.cISOSprache, s.cGebuehrname, s.cHinweisText, s.cHinweisTextShop
                FROM tzahlungsart AS z
                LEFT JOIN tzahlungsartsprache AS s 
                    ON s.kZahlungsart = z.kZahlungsart
                    AND s.cISOSprache = :iso
                WHERE z.kZahlungsart = :pmID
                LIMIT 1',
            [
                'iso'  => $iso,
                'pmID' => $id
            ]
        );
        if ($item !== null) {
            $this->loadObject($item);
        }

        return $this;
    }

    /**
     * @param bool        $active
     * @param string|null $iso
     * @return Zahlungsart[]
     */
    public static function loadAll(bool $active = true, string $iso = null): array
    {
        $payments = [];
        $where    = $active ? ' WHERE z.nActive = 1' : '';
        $iso      = $iso ?? Shop::getLanguageCode();
        $data     = Shop::Container()->getDB()->getObjects(
            'SELECT *
                FROM tzahlungsart AS z
                LEFT JOIN tzahlungsartsprache AS s 
                    ON s.kZahlungsart = z.kZahlungsart
                    AND s.cISOSprache = :iso' . $where,
            ['iso' => $iso]
        );
        foreach ($data as $obj) {
            $payments[] = new self(null, $obj);
        }

        return $payments;
    }
}
