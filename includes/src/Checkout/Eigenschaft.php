<?php

declare(strict_types=1);

namespace JTL\Checkout;

use JTL\DB\DbInterface;
use JTL\Helpers\GeneralObject;
use JTL\Shop;

/**
 * Class Eigenschaft
 * @package JTL\Checkout
 */
class Eigenschaft
{
    /**
     * @var int|null
     */
    public ?int $kEigenschaft = null;

    /**
     * @var int|null
     */
    public ?int $kArtikel = null;

    /**
     * @var string|null
     */
    public ?string $cName = null;

    /**
     * string - 'Y'/'N'
     */
    public string $cWaehlbar = 'N';

    /**
     * @var string|null
     */
    public ?string $cTyp;

    /**
     * @var int
     */
    public int $nSort = 0;

    /**
     * @param int              $id
     * @param DbInterface|null $db
     */
    public function __construct(int $id = 0, private ?DbInterface $db = null)
    {
        $this->db = $this->db ?? Shop::Container()->getDB();
        if ($id > 0) {
            $this->loadFromDB($id);
        }
    }

    /**
     * @param int $id
     * @return $this
     */
    public function loadFromDB(int $id): self
    {
        $obj = $this->db->select('teigenschaft', 'kEigenschaft', $id);
        if ($obj !== null) {
            $this->kEigenschaft = (int)$obj->kEigenschaft;
            $this->kArtikel     = (int)$obj->kArtikel;
            $this->cName        = $obj->cName;
            $this->cWaehlbar    = $obj->cWaehlbar;
            $this->cTyp         = $obj->cTyp;
            $this->nSort        = (int)$obj->nSort;
        }
        \executeHook(\HOOK_EIGENSCHAFT_CLASS_LOADFROMDB);

        return $this;
    }

    /**
     * @return int
     */
    public function insertInDB(): int
    {
        return $this->db->insert('teigenschaft', GeneralObject::copyMembers($this));
    }

    /**
     * @return int
     */
    public function updateInDB(): int
    {
        $obj = GeneralObject::copyMembers($this);

        return $this->db->update('teigenschaft', 'kEigenschaft', $obj->kEigenschaft, $obj);
    }
}
