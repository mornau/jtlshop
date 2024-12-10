<?php

declare(strict_types=1);

namespace JTL\Checkout;

use Illuminate\Support\Collection;
use JTL\Country\Country;
use JTL\Helpers\GeneralObject;
use JTL\MagicCompatibilityTrait;
use JTL\Shop;
use stdClass;

/**
 * Class Versandart
 * @package JTL\Checkout
 */
class Versandart
{
    use MagicCompatibilityTrait;

    /**
     * @var int|null
     */
    public $kVersandart;

    /**
     * @var int|null
     */
    public $kVersandberechnung;

    /**
     * @var string|null
     */
    public $cVersandklassen;

    /**
     * @var string|null
     */
    public $cName;

    /**
     * @var string|null
     */
    public $cLaender;

    /**
     * @var string|null
     */
    public $cAnzeigen;

    /**
     * @var string|null
     */
    public $cKundengruppen;

    /**
     * @var string|null
     */
    public $cBild;

    /**
     * @var string|null
     */
    public $cNurAbhaengigeVersandart;

    /**
     * @var int|null
     */
    public $nSort;

    /**
     * @var float|null
     */
    public $fPreis;

    /**
     * @var float|null
     */
    public $fVersandkostenfreiAbX;

    /**
     * @var float|null
     */
    public $fDeckelung;

    /**
     * @var array|null
     */
    public $oVersandartSprache_arr;

    /**
     * @var array|null
     */
    public $oVersandartStaffel_arr;

    /**
     * @var string|null
     */
    public $cSendConfirmationMail;

    /**
     * @var string|null
     */
    public $cIgnoreShippingProposal;

    /**
     * @var int|null
     */
    public $nMinLiefertage;

    /**
     * @var int|null
     */
    public $nMaxLiefertage;

    /**
     * @var string|null
     */
    public $eSteuer;

    /**
     * @var Country|null
     */
    public $country;

    /**
     * @var array|null
     */
    public $cPriceLocalized;

    /**
     * @var Collection|null
     */
    public $shippingSurcharges;

    /**
     * @var array<string, string>
     */
    public static array $mapping = [
        'cCountryCode' => 'CountryCode'
    ];

    /**
     * Versandart constructor.
     * @param int $id
     */
    public function __construct(int $id = 0)
    {
        if ($id > 0) {
            $this->load($id);
        }
    }

    /**
     * @param int $id
     * @return void
     */
    private function load(int $id): void
    {
        $cache   = Shop::Container()->getCache();
        $cacheID = 'shippingmethod_' . $id;
        if (($method = $cache->get($cacheID)) === false) {
            $this->loadFromDB($id);
            $cache->set($cacheID, $this, [\CACHING_GROUP_OPTION]);
        } else {
            foreach (\get_object_vars($method) as $idx => $value) {
                $this->$idx = $value;
            }
        }
    }

    /**
     * @param int $id
     * @return int
     */
    public function loadFromDB(int $id): int
    {
        $db  = Shop::Container()->getDB();
        $obj = $db->select('tversandart', 'kVersandart', $id);
        if ($obj === null || !$obj->kVersandart) {
            return 0;
        }
        $this->kVersandart              = (int)$obj->kVersandart;
        $this->nSort                    = (int)$obj->nSort;
        $this->kVersandberechnung       = (int)$obj->kVersandberechnung;
        $this->nMinLiefertage           = (int)$obj->nMinLiefertage;
        $this->nMaxLiefertage           = (int)$obj->nMaxLiefertage;
        $this->cVersandklassen          = $obj->cVersandklassen;
        $this->cName                    = $obj->cName;
        $this->cLaender                 = $obj->cLaender;
        $this->cAnzeigen                = $obj->cAnzeigen;
        $this->cKundengruppen           = $obj->cKundengruppen;
        $this->cBild                    = $obj->cBild;
        $this->eSteuer                  = $obj->eSteuer;
        $this->fPreis                   = $obj->fPreis;
        $this->fVersandkostenfreiAbX    = $obj->fVersandkostenfreiAbX;
        $this->fDeckelung               = $obj->fDeckelung;
        $this->cNurAbhaengigeVersandart = $obj->cNurAbhaengigeVersandart;
        $this->cSendConfirmationMail    = $obj->cSendConfirmationMail;
        $this->cIgnoreShippingProposal  = $obj->cIgnoreShippingProposal;

        $localized = $db->selectAll(
            'tversandartsprache',
            'kVersandart',
            $this->kVersandart
        );
        foreach ($localized as $translation) {
            $translation->kVersandart = (int)$translation->kVersandart;

            $this->oVersandartSprache_arr[$translation->cISOSprache] = $translation;
        }
        // Versandstaffel
        $this->oVersandartStaffel_arr = $db->selectAll(
            'tversandartstaffel',
            'kVersandart',
            (int)$this->kVersandart
        );
        foreach ($this->oVersandartStaffel_arr as $item) {
            $item->kVersandartStaffel = (int)$item->kVersandartStaffel;
            $item->kVersandart        = (int)$item->kVersandart;
        }

        $this->loadShippingSurcharges();

        return 1;
    }

    /**
     * @return int
     */
    public function insertInDB(): int
    {
        $obj = GeneralObject::copyMembers($this);
        unset(
            $obj->oVersandartSprache_arr,
            $obj->oVersandartStaffel_arr,
            $obj->nMinLiefertage,
            $obj->nMaxLiefertage
        );
        $this->kVersandart = Shop::Container()->getDB()->insert('tversandart', $obj);

        return $this->kVersandart;
    }

    /**
     * @return int
     */
    public function updateInDB(): int
    {
        $obj = GeneralObject::copyMembers($this);
        unset(
            $obj->oVersandartSprache_arr,
            $obj->oVersandartStaffel_arr,
            $obj->nMinLiefertage,
            $obj->nMaxLiefertage
        );

        return Shop::Container()->getDB()->update('tversandart', 'kVersandart', $obj->kVersandart, $obj);
    }

    /**
     * @param int $id
     * @return bool
     */
    public static function deleteInDB(int $id): bool
    {
        if ($id <= 0) {
            return false;
        }
        $db = Shop::Container()->getDB();
        $db->delete('tversandart', 'kVersandart', $id);
        $db->delete('tversandartsprache', 'kVersandart', $id);
        $db->delete('tversandartzahlungsart', 'kVersandart', $id);
        $db->delete('tversandartstaffel', 'kVersandart', $id);
        $db->queryPrepared(
            'DELETE tversandzuschlag, tversandzuschlagplz, tversandzuschlagsprache
                FROM tversandzuschlag
                LEFT JOIN tversandzuschlagplz 
                    ON tversandzuschlagplz.kVersandzuschlag = tversandzuschlag.kVersandzuschlag
                LEFT JOIN tversandzuschlagsprache 
                    ON tversandzuschlagsprache.kVersandzuschlag = tversandzuschlag.kVersandzuschlag
                WHERE tversandzuschlag.kVersandart = :fid',
            ['fid' => $id]
        );

        return true;
    }

    /**
     * @param int $id
     * @return bool
     */
    public static function cloneShipping(int $id): bool
    {
        $sections = [
            'tversandartsprache'     => 'kVersandart',
            'tversandartstaffel'     => 'kVersandartStaffel',
            'tversandartzahlungsart' => 'kVersandartZahlungsart',
            'tversandzuschlag'       => 'kVersandzuschlag'
        ];

        $method = Shop::Container()->getDB()->select('tversandart', 'kVersandart', $id);
        if ($method !== null && $method->kVersandart > 0) {
            unset($method->kVersandart);
            $kVersandartNew = Shop::Container()->getDB()->insert('tversandart', $method);
            if ($kVersandartNew > 0) {
                foreach ($sections as $name => $key) {
                    $items = self::getShippingSection($name, 'kVersandart', $id);
                    self::cloneShippingSection($items, $name, 'kVersandart', $kVersandartNew, $key);
                }

                return true;
            }
        }

        return false;
    }

    /**
     * @param string $table
     * @param string $key
     * @param int    $value
     * @return stdClass[]
     */
    private static function getShippingSection(string $table, string $key, int $value): array
    {
        if ($value > 0 && \mb_strlen($table) > 0 && \mb_strlen($key) > 0) {
            return Shop::Container()->getDB()->selectAll($table, $key, $value);
        }

        return [];
    }

    /**
     * @param array       $objects
     * @param string      $table
     * @param string      $key
     * @param int         $value
     * @param null|string $unsetKey
     */
    private static function cloneShippingSection(array $objects, $table, $key, int $value, $unsetKey = null): void
    {
        if ($value <= 0 || \count($objects) === 0 || \mb_strlen($key) === 0) {
            return;
        }
        $db = Shop::Container()->getDB();
        foreach ($objects as $item) {
            $primary = $item->$unsetKey;
            if ($unsetKey !== null) {
                unset($item->$unsetKey);
            }
            $item->$key = $value;
            if ($table === 'tversandartzahlungsart' && empty($item->fAufpreis)) {
                $item->fAufpreis = 0;
            }
            $id = $db->insert($table, $item);
            if ($id > 0 && $table === 'tversandzuschlag') {
                self::cloneShippingSectionSpecial($primary, $id);
            }
        }
    }

    /**
     * @param int $oldKey
     * @param int $newKey
     */
    private static function cloneShippingSectionSpecial(int $oldKey, int $newKey): void
    {
        if ($oldKey <= 0 || $newKey <= 0) {
            return;
        }
        $sections = [
            'tversandzuschlagplz'     => 'kVersandzuschlagPlz',
            'tversandzuschlagsprache' => 'kVersandzuschlag'
        ];
        foreach ($sections as $section => $subKey) {
            $subSections = self::getShippingSection($section, 'kVersandzuschlag', $oldKey);

            self::cloneShippingSection($subSections, $section, 'kVersandzuschlag', $newKey, $subKey);
        }
    }

    /**
     * load zip surcharges for shipping method
     */
    public function loadShippingSurcharges(): void
    {
        $this->setShippingSurcharges(
            Shop::Container()->getDB()->getCollection(
                'SELECT kVersandzuschlag
                    FROM tversandzuschlag
                    WHERE kVersandart = :kVersandart
                    ORDER BY kVersandzuschlag DESC',
                ['kVersandart' => $this->kVersandart]
            )->map(static function (stdClass $surcharge): ShippingSurcharge {
                return new ShippingSurcharge((int)$surcharge->kVersandzuschlag);
            })
        );
    }

    /**
     * @param string $iso
     * @return Collection
     */
    public function getShippingSurchargesForCountry(string $iso): Collection
    {
        return $this->getShippingSurcharges()->filter(static function (ShippingSurcharge $surcharge) use ($iso): bool {
            return $surcharge->getISO() === $iso;
        });
    }

    /**
     * @param string $zip
     * @param string $iso
     * @return ShippingSurcharge|null
     */
    public function getShippingSurchargeForZip(string $zip, string $iso): ?ShippingSurcharge
    {
        return $this->getShippingSurchargesForCountry($iso)
            ->first(static function (ShippingSurcharge $surcharge) use ($zip): bool {
                return $surcharge->hasZIPCode($zip);
            });
    }

    /**
     * @return Collection
     */
    public function getShippingSurcharges(): Collection
    {
        return $this->shippingSurcharges;
    }

    /**
     * @param Collection $shippingSurcharges
     * @return Versandart
     */
    private function setShippingSurcharges(Collection $shippingSurcharges): self
    {
        $this->shippingSurcharges = $shippingSurcharges;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->country !== null ? $this->country->getISO() : '';
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode(string $countryCode): void
    {
        $this->country = Shop::Container()->getCountryService()->getCountry($countryCode);
    }
}
