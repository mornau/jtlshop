<?php

declare(strict_types=1);

namespace JTL\Catalog;

use DateTime;
use Exception;
use JTL\Catalog\Product\Artikel;
use JTL\MainModel;
use JTL\Shop;
use stdClass;

/**
 * Class Warehouse
 * @package JTL\Catalog
 */
class Warehouse extends MainModel
{
    /**
     * @var int
     */
    public int $kWarenlager = 0;

    /**
     * @var string
     */
    public string $cName = '';

    /**
     * @var string
     */
    public string $cKuerzel = '';

    /**
     * @var string
     */
    public string $cLagerTyp = '';

    /**
     * @var string
     */
    public string $cBeschreibung = '';

    /**
     * @var string
     */
    public string $cStrasse = '';

    /**
     * @var string
     */
    public string $cPLZ = '';

    /**
     * @var string
     */
    public string $cOrt = '';

    /**
     * @var string
     */
    public string $cLand = '';

    /**
     * @var int
     */
    public int $nFulfillment = 0;

    /**
     * @var int
     */
    public int $nAktiv = 0;

    /**
     * @var stdClass|null
     */
    public ?stdClass $oLageranzeige = null;

    /**
     * @var array|null
     */
    public ?array $cSpracheAssoc_arr = null;

    /**
     * @var float|string|null
     */
    public $fBestand;

    /**
     * @var float|string|null
     */
    public $fZulauf;

    /**
     * @var string|null
     */
    public ?string $dZulaufDatum = null;

    /**
     * @var string|null
     */
    public ?string $dZulaufDatum_de = null;

    /**
     * @return stdClass|null
     */
    public function getOLageranzeige(): ?stdClass
    {
        return $this->oLageranzeige;
    }

    /**
     * @param stdClass $oLageranzeige
     * @return $this
     */
    public function setOLageranzeige(stdClass $oLageranzeige): self
    {
        $this->oLageranzeige = $oLageranzeige;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getLanguages(): ?array
    {
        return $this->cSpracheAssoc_arr;
    }

    /**
     * @param array $languages
     * @return $this
     */
    public function setLanguages(array $languages): self
    {
        $this->cSpracheAssoc_arr = $languages;

        return $this;
    }

    /**
     * @return float
     */
    public function getStock()
    {
        return $this->fBestand;
    }

    /**
     * @param float|string $fBestand
     * @return $this
     */
    public function setStock($fBestand): self
    {
        $this->fBestand = $fBestand;

        return $this;
    }

    /**
     * @return float|string|null
     */
    public function getBackorder()
    {
        return $this->fZulauf;
    }

    /**
     * @param float|string $fZulauf
     * @return $this
     */
    public function setBackorder($fZulauf): self
    {
        $this->fZulauf = $fZulauf;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBackorderDate(): ?string
    {
        return $this->dZulaufDatum;
    }

    /**
     * @param string $dZulaufDatum
     * @return $this
     */
    public function setBackorderDate(string $dZulaufDatum): self
    {
        $this->dZulaufDatum = $dZulaufDatum;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBackorderDateDE(): ?string
    {
        return $this->dZulaufDatum_de;
    }

    /**
     * @param string $dZulaufDatum_de
     * @return $this
     */
    public function setBackorderDateDE(string $dZulaufDatum_de): self
    {
        $this->dZulaufDatum_de = $dZulaufDatum_de;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getID(): ?int
    {
        return $this->kWarenlager;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setID(int $id): self
    {
        $this->kWarenlager = $id;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getWarenlager(): ?int
    {
        return $this->getID();
    }

    /**
     * @param int|string $kWarenlager
     * @return $this
     */
    public function setWarenlager(int|string $kWarenlager): self
    {
        return $this->setID((int)$kWarenlager);
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
    public function getKuerzel(): ?string
    {
        return $this->cKuerzel;
    }

    /**
     * @param string $cKuerzel
     * @return $this
     */
    public function setKuerzel(string $cKuerzel): self
    {
        $this->cKuerzel = $cKuerzel;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLagerTyp(): ?string
    {
        return $this->cLagerTyp;
    }

    /**
     * @param string $cLagerTyp
     * @return $this
     */
    public function setLagerTyp(string $cLagerTyp): self
    {
        $this->cLagerTyp = $cLagerTyp;

        return $this;
    }

    /**
     * @return string
     */
    public function getBeschreibung(): string
    {
        return $this->cBeschreibung;
    }

    /**
     * @param string $cBeschreibung
     * @return $this
     */
    public function setBeschreibung(string $cBeschreibung): self
    {
        $this->cBeschreibung = $cBeschreibung;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStrasse(): ?string
    {
        return $this->cStrasse;
    }

    /**
     * @param string $cStrasse
     * @return $this
     */
    public function setStrasse(string $cStrasse): self
    {
        $this->cStrasse = $cStrasse;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPLZ(): ?string
    {
        return $this->cPLZ;
    }

    /**
     * @param string $cPLZ
     * @return $this
     */
    public function setPLZ(string $cPLZ): self
    {
        $this->cPLZ = $cPLZ;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOrt(): ?string
    {
        return $this->cOrt;
    }

    /**
     * @param string $cOrt
     * @return $this
     */
    public function setOrt(string $cOrt): self
    {
        $this->cOrt = $cOrt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLand(): ?string
    {
        return $this->cLand;
    }

    /**
     * @param string $cLand
     * @return $this
     */
    public function setLand(string $cLand): self
    {
        $this->cLand = $cLand;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFulfillment(): ?int
    {
        return $this->nFulfillment;
    }

    /**
     * @param int|string $nFulfillment
     * @return $this
     */
    public function setFulfillment(int|string $nFulfillment): self
    {
        $this->nFulfillment = (int)$nFulfillment;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAktiv(): ?int
    {
        return $this->nAktiv;
    }

    /**
     * @param int|string $nAktiv
     * @return $this
     */
    public function setAktiv(int|string $nAktiv): self
    {
        $this->nAktiv = (int)$nAktiv;

        return $this;
    }

    /**
     * @param int         $id
     * @param null|object $data
     * @param int|null    $option
     */
    public function load($id, $data = null, $option = null): void
    {
        if ($id !== null) {
            $id = (int)$id;
            if ($id > 0) {
                $select = '';
                $join   = '';
                if ($option !== null && (int)$option > 0) {
                    $option = (int)$option;
                    $select = ', IF (twarenlagersprache.cName IS NOT NULL, 
                    twarenlagersprache.cName, twarenlager.cName) AS cName';
                    $join   = ' LEFT JOIN twarenlagersprache 
                                    ON twarenlagersprache.kWarenlager = twarenlager.kWarenlager
                                    AND twarenlagersprache.kSprache = ' . $option;
                }

                $data = Shop::Container()->getDB()->getSingleObject(
                    'SELECT twarenlager.* ' . $select . '
                         FROM twarenlager' . $join . '
                         WHERE twarenlager.kWarenlager = ' . $id
                );
            }
        }
        if (isset($data->kWarenlager) && $data->kWarenlager > 0) {
            $this->loadObject($data);
        }
    }

    /**
     * @param bool $primary
     * @return bool|int
     * @throws Exception
     */
    public function save(bool $primary = true)
    {
        $data = new stdClass();
        foreach (\array_keys(\get_object_vars($this)) as $cMember) {
            $data->$cMember = $this->$cMember;
        }
        if ($this->getWarenlager() === null) {
            $key = Shop::Container()->getDB()->insert('twarenlager', $data);
            if ($key > 0) {
                return $primary ? $key : true;
            }
        } else {
            $result = $this->update();
            if ($result) {
                return $primary ? -1 : true;
            }
        }

        return false;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function update(): int
    {
        $members = \array_keys(\get_object_vars($this));
        if (!\is_array($members) || \count($members) === 0) {
            throw new Exception('ERROR: Object has no members!');
        }
        $upd = new stdClass();
        foreach ($members as $member) {
            $method = 'get' . \mb_substr($member, 1);
            if (\method_exists($this, $method)) {
                $upd->$member = $this->$method() ?? '_DBNULL_';
            }
        }
        unset($upd->oLageranzeige);

        return Shop::Container()->getDB()->update('twarenlager', 'kWarenlager', $this->kWarenlager, $upd);
    }

    /**
     * @return int
     */
    public function delete(): int
    {
        return Shop::Container()->getDB()->getAffectedRows(
            'DELETE twarenlager, twarenlagersprache
                FROM twarenlager
                LEFT JOIN twarenlagersprache 
                    ON twarenlagersprache.kWarenlager = twarenlager.kWarenlager
                WHERE twarenlager.kWarenlager = :lid',
            ['lid' => (int)$this->kWarenlager]
        );
    }

    /**
     * @return bool
     */
    public function loadLanguages(): bool
    {
        if ($this->getWarenlager() <= 0) {
            return false;
        }
        $data = Shop::Container()->getDB()->selectAll('twarenlagersprache', 'kWarenlager', $this->getWarenlager());
        if (\count($data) === 0) {
            return false;
        }
        $this->cSpracheAssoc_arr = [];
        foreach ($data as $item) {
            $this->cSpracheAssoc_arr[(int)$item->kSprache] = $item->cName;
        }

        return true;
    }

    /**
     * @param bool $activeOnly
     * @param bool $loadLanguages
     * @return Warehouse[]
     */
    public static function getAll(bool $activeOnly = true, bool $loadLanguages = false): array
    {
        $warehouses = [];
        $sql        = $activeOnly ? ' WHERE nAktiv = 1' : '';
        foreach (Shop::Container()->getDB()->getObjects('SELECT * FROM twarenlager' . $sql) as $item) {
            $warehouse = new self(null, $item);
            if ($loadLanguages) {
                $warehouse->loadLanguages();
            }
            $warehouses[] = $warehouse;
        }

        return $warehouses;
    }

    /**
     * @param int        $productID
     * @param int|null   $langID
     * @param null|array $config
     * @param bool       $active
     * @return Warehouse[]
     */
    public static function getByProduct(
        int $productID,
        int $langID = null,
        $config = null,
        bool $active = true
    ): array {
        $warehouses = [];
        if ($productID > 0) {
            $sql  = $active ? ' AND twarenlager.nAktiv = 1' : '';
            $data = Shop::Container()->getDB()->getObjects(
                'SELECT tartikelwarenlager.*
                    FROM tartikelwarenlager
                    JOIN twarenlager 
                        ON twarenlager.kWarenlager = tartikelwarenlager.kWarenlager' . $sql . '
                    WHERE tartikelwarenlager.kArtikel = :productID',
                ['productID' => $productID]
            );
            foreach ($data as $item) {
                $warehouse               = new self($item->kWarenlager, null, $langID);
                $warehouse->fBestand     = $item->fBestand;
                $warehouse->fZulauf      = $item->fZulauf;
                $warehouse->dZulaufDatum = $item->dZulaufDatum;
                if ($warehouse->dZulaufDatum !== null && \mb_strlen($warehouse->dZulaufDatum) > 1) {
                    try {
                        $warehouse->dZulaufDatum_de = (new DateTime($item->dZulaufDatum))->format('d.m.Y');
                    } catch (Exception) {
                        $warehouse->dZulaufDatum_de = '00.00.0000';
                    }
                }
                if (\is_array($config)) {
                    $warehouse->buildWarehouseInfo($warehouse->fBestand, $config);
                }
                $warehouses[] = $warehouse;
            }
        }

        return $warehouses;
    }

    /**
     * @param float $stock
     * @param array $config
     * @return $this
     */
    public function buildWarehouseInfo($stock, array $config): self
    {
        $this->oLageranzeige                = new stdClass();
        $this->oLageranzeige->cLagerhinweis = [];
        $conf                               = Shop::getSettings([\CONF_GLOBAL, \CONF_ARTIKELDETAILS]);
        if ($config['cLagerBeachten'] === 'Y') {
            if ($stock > 0) {
                $this->oLageranzeige->cLagerhinweis['genau']          = $stock . ' '
                    . (!empty($config['cEinheit']) ? ($config['cEinheit'] . ' ') : '')
                    . Shop::Lang()->get('inStock');
                $this->oLageranzeige->cLagerhinweis['verfuegbarkeit'] = Shop::Lang()->get('productAvailable');
                if (
                    isset($conf['artikeldetails']['artikel_lagerbestandsanzeige'])
                    && $conf['artikeldetails']['artikel_lagerbestandsanzeige'] === 'verfuegbarkeit'
                ) {
                    $this->oLageranzeige->cLagerhinweis['verfuegbarkeit'] = Shop::Lang()->get('ampelGruen');
                }
            } elseif ($config['cLagerKleinerNull'] === 'Y') {
                $this->oLageranzeige->cLagerhinweis['genau']          = Shop::Lang()->get('ampelGruen');
                $this->oLageranzeige->cLagerhinweis['verfuegbarkeit'] = Shop::Lang()->get('ampelGruen');
            } else {
                $this->oLageranzeige->cLagerhinweis['genau']          = Shop::Lang()->get('productNotAvailable');
                $this->oLageranzeige->cLagerhinweis['verfuegbarkeit'] = Shop::Lang()->get('productNotAvailable');
            }
        } else {
            $this->oLageranzeige->cLagerhinweis['genau']          = Shop::Lang()->get('ampelGruen');
            $this->oLageranzeige->cLagerhinweis['verfuegbarkeit'] = Shop::Lang()->get('ampelGruen');
        }
        if ($config['cLagerBeachten'] === 'Y') {
            $this->oLageranzeige->nStatus   = 1;
            $this->oLageranzeige->AmpelText = $config['attribut_ampeltext_gelb'];
            if ($stock <= (int)$conf['global']['artikel_lagerampel_rot']) {
                $this->oLageranzeige->nStatus   = 0;
                $this->oLageranzeige->AmpelText = $config['attribut_ampeltext_rot'];
            }
            if (
                $stock >= (int)$conf['global']['artikel_lagerampel_gruen']
                || ($config['cLagerKleinerNull'] === 'Y' && $conf['global']['artikel_ampel_lagernull_gruen'] === 'Y')
            ) {
                $this->oLageranzeige->nStatus   = 2;
                $this->oLageranzeige->AmpelText = $config['attribut_ampeltext_gruen'];
            }
        } else {
            $this->oLageranzeige->nStatus = (int)$conf['global']['artikel_lagerampel_keinlager'];

            switch ($this->oLageranzeige->nStatus) {
                case 1:
                    $this->oLageranzeige->AmpelText = $config['attribut_ampeltext_gelb'];
                    break;
                case 0:
                    $this->oLageranzeige->AmpelText = $config['attribut_ampeltext_rot'];
                    break;
                default:
                    $this->oLageranzeige->nStatus   = 2;
                    $this->oLageranzeige->AmpelText = $config['attribut_ampeltext_gruen'];
                    break;
            }
        }

        return $this;
    }

    /**
     * @param Artikel $item
     * @return string
     */
    public function getBackorderString(Artikel $item): string
    {
        $backorder = '';
        if (
            $item->cLagerBeachten === 'Y'
            && $this->getStock() <= 0
            && $this->getBackorder() > 0
            && $this->getBackorderDate() !== null
        ) {
            $backorder = \sprintf(
                Shop::Lang()->get('productInflowing', 'productDetails'),
                $this->getBackorder(),
                $item->cEinheit,
                $this->getBackorderDateDE()
            );
        }

        return $backorder;
    }
}
