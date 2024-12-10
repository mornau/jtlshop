<?php

declare(strict_types=1);

namespace JTL\Checkout;

use JTL\Helpers\Text;
use JTL\Language\LanguageHelper;
use JTL\Shop;
use stdClass;

/**
 * Class Adresse
 * @package JTL
 */
class Adresse
{
    /**
     * @var string|null
     */
    public $cAnrede;

    /**
     * @var string|null
     */
    public $cVorname;

    /**
     * @var string|null
     */
    public $cNachname;

    /**
     * @var string|null
     */
    public $cTitel;

    /**
     * @var string|null
     */
    public $cFirma;

    /**
     * @var string|null
     */
    public $cStrasse;

    /**
     * @var string|null
     */
    public $cAdressZusatz;

    /**
     * @var string|null
     */
    public $cPLZ;

    /**
     * @var string|null
     */
    public $cOrt;

    /**
     * @var string|null
     */
    public $cBundesland;

    /**
     * @var string|null
     */
    public $cLand;

    /**
     * @var string|null
     */
    public $cTel;

    /**
     * @var string|null
     */
    public $cMobil;

    /**
     * @var string|null
     */
    public $cFax;

    /**
     * @var string|null
     */
    public $cMail;

    /**
     * @var string|null
     */
    public $cHausnummer;

    /**
     * @var string|null
     */
    public $cZusatz;

    /**
     * @var string[]
     */
    protected static array $encodedProperties = [
        'cNachname',
        'cFirma',
        'cZusatz',
        'cStrasse'
    ];

    /**
     * Adresse constructor.
     */
    public function __construct()
    {
    }

    /**
     * encrypt shipping address
     *
     * @return $this
     */
    public function encrypt(): self
    {
        $cyptoService = Shop::Container()->getCryptoService();
        foreach (self::$encodedProperties as $property) {
            $this->$property = $cyptoService->encryptXTEA(\trim((string)($this->$property ?? '')));
        }

        return $this;
    }

    /**
     * decrypt shipping address
     *
     * @return $this
     */
    public function decrypt(): self
    {
        $cryptoService = Shop::Container()->getCryptoService();
        foreach (self::$encodedProperties as $property) {
            if ($this->$property !== null) {
                $this->$property = \trim($cryptoService->decryptXTEA($this->$property));
                // Workaround: nur nach Update relevant (SHOP-5956)
                // verschlüsselte Shop4-Daten sind noch Latin1 kodiert und müssen nach UTF-8 konvertiert werden
                if (!Text::is_utf8($this->$property)) {
                    $this->$property = Text::convertUTF8($this->$property);
                }
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return \get_object_vars($this);
    }

    /**
     * @return stdClass
     */
    public function toObject(): stdClass
    {
        return (object)$this->toArray();
    }

    /**
     * @param array $array
     * @return $this
     */
    public function fromArray(array $array): self
    {
        foreach ($array as $key => $value) {
            if (\property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        return $this;
    }

    /**
     * @param object $object
     * @return $this
     */
    public function fromObject($object): self
    {
        return $this->fromArray((array)$object);
    }

    /**
     * @param null|string $anrede
     * @return string
     */
    public function mappeAnrede(?string $anrede): string
    {
        return match (\mb_convert_case($anrede ?? '', \MB_CASE_LOWER)) {
            'm'     => Shop::Lang()->get('salutationM'),
            'w'     => Shop::Lang()->get('salutationW'),
            default => '',
        };
    }

    /**
     * @param string $iso
     * @return string
     */
    public static function checkISOCountryCode(string $iso): string
    {
        \preg_match('/[a-zA-Z]{2}/', $iso, $matches);
        if (\mb_strlen($matches[0]) !== \mb_strlen($iso)) {
            $o = LanguageHelper::getIsoCodeByCountryName($iso);
            if ($o !== 'noISO' && $o !== '') {
                $iso = $o;
            }
        }

        return $iso;
    }
}
