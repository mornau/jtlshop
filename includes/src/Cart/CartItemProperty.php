<?php

declare(strict_types=1);

namespace JTL\Cart;

use JTL\Helpers\GeneralObject;
use JTL\Shop;

/**
 * Class CartItemProperty
 * @package JTL\Cart
 */
class CartItemProperty
{
    /**
     * @var int|null
     */
    public $kWarenkorbPosEigenschaft;

    /**
     * @var int|null
     */
    public $kWarenkorbPos;

    /**
     * @var int|null
     */
    public $kEigenschaft;

    /**
     * @var int|null
     */
    public $kEigenschaftWert;

    /**
     * @var float|null
     */
    public $fAufpreis;

    /**
     * @var float|null
     */
    public $fGewichtsdifferenz;

    /**
     * @var array|string|null
     */
    public $cEigenschaftName;

    /**
     * @var array|string|null
     */
    public $cEigenschaftWertName;

    /**
     * @var string|null
     */
    public $cFreifeldWert;

    /**
     * @var string|null
     */
    public $cAufpreisLocalized;

    /**
     * @var string|null
     */
    public $cTyp;

    /**
     * CartItemProperty constructor.
     * @param int $id
     */
    public function __construct(int $id = 0)
    {
        if ($id > 0) {
            $this->loadFromDB($id);
        }
    }

    /**
     * gibt Namen der Eigenschaft zurück
     *
     * @return string - EigenschaftName
     */
    public function gibEigenschaftName(): string
    {
        $obj = Shop::Container()->getDB()->select('teigenschaft', 'kEigenschaft', $this->kEigenschaft);

        return $obj->cName ?? '';
    }

    /**
     * gibt Namen des EigenschaftWerts zurück
     *
     * @return string - EigenschaftWertName
     */
    public function gibEigenschaftWertName(): string
    {
        $obj = Shop::Container()->getDB()->select('teigenschaftwert', 'kEigenschaftWert', $this->kEigenschaftWert);

        return $obj->cName ?? '';
    }

    /**
     * @param int $kWarenkorbPosEigenschaft
     * @return $this
     */
    public function loadFromDB(int $kWarenkorbPosEigenschaft): self
    {
        $obj = Shop::Container()->getDB()->select(
            'twarenkorbposeigenschaft',
            'kWarenkorbPosEigenschaft',
            $kWarenkorbPosEigenschaft
        );
        if ($obj !== null) {
            $this->kWarenkorbPosEigenschaft = (int)$obj->kWarenkorbPosEigenschaft;
            $this->kWarenkorbPos            = (int)$obj->kWarenkorbPos;
            $this->kEigenschaft             = (int)$obj->kEigenschaft;
            $this->kEigenschaftWert         = (int)$obj->kEigenschaftWert;
            $this->cEigenschaftName         = $obj->cEigenschaftName;
            $this->cEigenschaftWertName     = $obj->cEigenschaftWertName;
            $this->cFreifeldWert            = $obj->cFreifeldWert;
            $this->fAufpreis                = $obj->fAufpreis;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function insertInDB(): self
    {
        $obj = GeneralObject::copyMembers($this);
        unset($obj->kWarenkorbPosEigenschaft, $obj->cAufpreisLocalized, $obj->fGewichtsdifferenz, $obj->cTyp);
        //sql strict mode
        if ($obj->fAufpreis === null || $obj->fAufpreis === '') {
            $obj->fAufpreis = 0;
        }
        $this->kWarenkorbPosEigenschaft = Shop::Container()->getDB()->insert('twarenkorbposeigenschaft', $obj);

        return $this;
    }

    /**
     * @return int
     */
    public function updateInDB(): int
    {
        $obj = GeneralObject::copyMembers($this);

        return Shop::Container()->getDB()->update(
            'twarenkorbposeigenschaft',
            'kWarenkorbPosEigenschaft',
            $obj->kWarenkorbPosEigenschaft,
            $obj
        );
    }
}
