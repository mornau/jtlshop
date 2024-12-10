<?php

declare(strict_types=1);

namespace JTL\FreeGift\Repositories;

use JTL\Abstracts\AbstractDBRepository;
use JTL\DataObjects\DomainObjectInterface;

/**
 * Class FreeGiftsRepository
 * @package JTL\FreeGift\Repositories
 * @since 5.4.0
 * @description This is a layer between the FreeGift Service and the database.
 */
class FreeGiftRepository extends AbstractDBRepository
{
    /**
     * @var array<string, string>
     */
    private array $mapping = [
        'kGratisGeschenk' => 'id',
        'kArtikel'        => 'productID',
        'kWarenkorb'      => 'basketID',
        'nAnzahl'         => 'quantity'
    ];

    /**
     * @inheritdoc
     */
    public function getTableName(): string
    {
        return 'tgratisgeschenk';
    }

    /**
     * @inheritdoc
     */
    public function getKeyName(): string
    {
        return $this->mapColumn('id');
    }

    public function mapColumn(string $identifier): ?string
    {
        return $this->mapping[$identifier] ?? \array_flip($this->mapping)[$identifier] ?? null;
    }

    /**
     * @param int $id
     * @param int $customerGroupID
     * @return object{productID: int, productValue: float}|null
     */
    public function getByProductID(int $id, int $customerGroupID = 0): ?object
    {
        return $this->db->getCollection(
            'SELECT tartikel.kArtikel AS productID, tartikelattribut.cWert AS productValue
                FROM tartikel
                JOIN tartikelattribut
                    ON tartikelattribut.kArtikel = tartikel.kArtikel
                LEFT JOIN tartikelsichtbarkeit 
                    ON tartikel.kArtikel = tartikelsichtbarkeit.kArtikel
                    AND tartikelsichtbarkeit.kKundengruppe = :cgid
                WHERE tartikel.kArtikel = :id
                AND tartikelsichtbarkeit.kArtikel IS NULL
                AND tartikel.nIstVater = 0
                AND tartikelattribut.cName = :attr
                AND (tartikel.fLagerbestand > 0
                    OR tartikel.cLagerBeachten = :no
                    OR tartikel.cLagerKleinerNull = :yes)',
            [
                'id'   => $id,
                'attr' => \FKT_ATTRIBUT_GRATISGESCHENK,
                'no'   => 'N',
                'yes'  => 'Y',
                'cgid' => $customerGroupID,
            ],
        )->map(function ($item) {
            return (object)[
                'productID'    => (int)$item->productID,
                'productValue' => (float)$item->productValue
            ];
        })->first();
    }

    /**
     * @param float $basketValue
     * @param int   $customerGroupID
     * @return object{productID: int, productValue: float}[]
     */
    public function getNextAvailable(float $basketValue, int $customerGroupID = 0): array
    {
        return $this->db->getCollection(
            'SELECT tartikel.kArtikel AS productID, tartikelattribut.cWert AS productValue
                FROM tartikel
                JOIN tartikelattribut
                    ON tartikelattribut.kArtikel = tartikel.kArtikel
                           AND tartikelattribut.cWert >= ' . $basketValue . '
                LEFT JOIN tartikelsichtbarkeit 
                    ON tartikel.kArtikel = tartikelsichtbarkeit.kArtikel
                    AND tartikelsichtbarkeit.kKundengruppe = :cgid
                WHERE tartikelsichtbarkeit.kArtikel IS NULL
                AND tartikel.nIstVater = 0
                AND tartikelattribut.cName = :attr
                AND (tartikel.fLagerbestand > 0
                    OR tartikel.cLagerBeachten = :no
                    OR tartikel.cLagerKleinerNull = :yes)',
            [
                'attr' => \FKT_ATTRIBUT_GRATISGESCHENK,
                'no'   => 'N',
                'yes'  => 'Y',
                'cgid' => $customerGroupID,
            ],
        )->map(function ($item) {
            return (object)[
                'productID'    => (int)$item->productID,
                'productValue' => (float)$item->productValue
            ];
        })->all();
    }

    /**
     * @param string $limit
     * @param string $sortBy
     * @param string $sortDirection
     * @param int    $customerGroupID
     * @return object{productID: int, productValue: float}[]
     */
    public function getFreeGiftProducts(
        string $limit = '',
        string $sortBy = 'ORDER BY CAST(tartikelattribut.cWert AS DECIMAL)',
        string $sortDirection = 'DESC',
        int $customerGroupID = 0,
    ): array {
        return $this->db->getCollection(
            'SELECT tartikel.kArtikel AS productID, tartikelattribut.cWert AS productValue
                FROM tartikel
                JOIN tartikelattribut
                    ON tartikelattribut.kArtikel = tartikel.kArtikel
                LEFT JOIN tartikelsichtbarkeit 
                    ON tartikel.kArtikel = tartikelsichtbarkeit.kArtikel
                    AND tartikelsichtbarkeit.kKundengruppe = :cgid
                LEFT JOIN teigenschaft
                    ON teigenschaft.kArtikel = tartikel.kArtikel
                WHERE teigenschaft.kArtikel IS NULL
                AND tartikelsichtbarkeit.kArtikel IS NULL
                AND tartikel.nIstVater = 0
                AND tartikelattribut.cName = :attr
                AND (tartikel.fLagerbestand > 0
                    OR tartikel.cLagerBeachten = :no
                    OR tartikel.cLagerKleinerNull = :yes)'
            . ' ' . $sortBy . ' ' . $sortDirection . ' ' . $limit,
            [
                'attr' => \FKT_ATTRIBUT_GRATISGESCHENK,
                'no'   => 'N',
                'yes'  => 'Y',
                'cgid' => $customerGroupID,
            ]
        )->map(function ($item) {
            return (object)[
                'productID'    => (int)$item->productID,
                'productValue' => (float)$item->productValue
            ];
        })->all();
    }

    /**
     * @param string $limitSQL
     * @return array{productID: int, quantity: int, lastOrdered: string, avgOrderValue: float}
     */
    public function getCommonFreeGifts(string $limitSQL): array
    {
        return $this->db->getCollection(
            'SELECT tgratisgeschenk.kArtikel AS productID, COUNT(*) AS quantity, 
                MAX(tbestellung.dErstellt) AS lastOrdered, AVG(tbestellung.fGesamtsumme) AS avgOrderValue
                FROM tgratisgeschenk
                INNER JOIN tbestellung
                    ON tbestellung.kWarenkorb = tgratisgeschenk.kWarenkorb
                INNER JOIN tartikel
                    ON tartikel.kArtikel = tgratisgeschenk.kArtikel
                        AND tartikel.nIstVater = 0
                LEFT JOIN teigenschaft
                    ON teigenschaft.kArtikel = tartikel.kArtikel
                WHERE teigenschaft.kArtikel IS NULL
                GROUP BY tgratisgeschenk.kArtikel
                ORDER BY quantity DESC, lastOrdered DESC ' . $limitSQL
        )->map(function (object $item): object {
            return (object)[
                'productID'     => (int)$item->productID,
                'quantity'      => (int)$item->quantity,
                'lastOrdered'   => $item->lastOrdered,
                'avgOrderValue' => (float)$item->avgOrderValue
            ];
        })->all();
    }

    /**
     * @return int
     */
    public function getCommonFreeGiftsCount(): int
    {
        return $this->db->getSingleInt(
            'SELECT COUNT(DISTINCT(tgratisgeschenk.kArtikel)) AS cnt
                FROM tgratisgeschenk
                INNER JOIN tbestellung
                    ON tbestellung.kWarenkorb = tgratisgeschenk.kWarenkorb
                INNER JOIN tartikel
                    ON tartikel.kArtikel = tgratisgeschenk.kArtikel
                           AND tartikel.nIstVater = 0
                LEFT JOIN teigenschaft
                    ON teigenschaft.kArtikel = tartikel.kArtikel
                WHERE teigenschaft.kArtikel IS NULL',
            'cnt',
        );
    }

    /**
     * @param string $limitSQL
     * @return int[]
     */
    public function getActiveFreeGiftIDs(string $limitSQL): array
    {
        return $this->db->getInts(
            'SELECT tartikelattribut.kArtikel AS productID
                FROM tartikelattribut
                INNER JOIN tartikel
                    ON tartikel.kArtikel = tartikelattribut.kArtikel
                           AND tartikel.nIstVater = 0
                LEFT JOIN teigenschaft
                    ON teigenschaft.kArtikel = tartikel.kArtikel
                WHERE teigenschaft.kArtikel IS NULL
                AND tartikelattribut.cName = :atr
                ORDER BY CAST(cWert AS SIGNED) DESC ' . $limitSQL,
            'productID',
            ['atr' => \FKT_ATTRIBUT_GRATISGESCHENK]
        );
    }

    public function getActiveFreeGiftsCount(): int
    {
        return $this->db->getSingleInt(
            'SELECT COUNT(*) AS cnt
                FROM tartikelattribut
                INNER JOIN tartikel
                    ON tartikel.kArtikel = tartikelattribut.kArtikel
                           AND tartikel.nIstVater = 0
                LEFT JOIN teigenschaft
                    ON teigenschaft.kArtikel = tartikel.kArtikel
                WHERE teigenschaft.kArtikel IS NULL
                AND tartikelattribut.cName = :nm',
            'cnt',
            ['nm' => \FKT_ATTRIBUT_GRATISGESCHENK]
        );
    }

    /**
     * @param string $limitSQL
     * @return array{productID: int, quantity: int, orderCreated: string, totalOrderValue: float}
     */
    public function getRecentFreeGifts(string $limitSQL): array
    {
        return $this->db->getCollection(
            'SELECT tgratisgeschenk.kArtikel AS productID, tgratisgeschenk.nAnzahl AS quantity,
                tbestellung.dErstellt AS orderCreated, tbestellung.fGesamtsumme AS totalOrderValue
                FROM tgratisgeschenk
                INNER JOIN tbestellung
                      ON tbestellung.kWarenkorb = tgratisgeschenk.kWarenkorb
                INNER JOIN tartikel
                    ON tartikel.kArtikel = tgratisgeschenk.kArtikel
                           AND tartikel.nIstVater = 0
                LEFT JOIN teigenschaft
                    ON teigenschaft.kArtikel = tartikel.kArtikel
                WHERE teigenschaft.kArtikel IS NULL
                ORDER BY orderCreated DESC ' . $limitSQL
        )->map(function (object $item): object {
            return (object)[
                'productID'       => (int)$item->productID,
                'quantity'        => (int)$item->quantity,
                'orderCreated'    => $item->orderCreated,
                'totalOrderValue' => (float)$item->totalOrderValue
            ];
        })->all();
    }

    public function getRecentFreeGiftsCount(): int
    {
        return $this->db->getSingleInt(
            'SELECT COUNT(*) AS cnt
                FROM twarenkorbpos
                INNER JOIN tgratisgeschenk
                    ON tgratisgeschenk.kWarenkorb = twarenkorbpos.kWarenkorb
                INNER JOIN tartikel
                    ON tartikel.kArtikel = tgratisgeschenk.kArtikel
                           AND tartikel.nIstVater = 0
                LEFT JOIN teigenschaft
                    ON teigenschaft.kArtikel = tartikel.kArtikel
                WHERE teigenschaft.kArtikel IS NULL
                AND nPosTyp = :tp
                LIMIT 100',
            'cnt',
            ['tp' => \C_WARENKORBPOS_TYP_GRATISGESCHENK]
        );
    }

    /**
     * @inheritdoc
     */
    public function insert(DomainObjectInterface $domainObject): int
    {
        if (isset($domainObject->modifiedKeys) && \count($domainObject->modifiedKeys) > 0) {
            throw new \InvalidArgumentException(
                'DomainObject has been modified. The last modified keys are '
                . \print_r($domainObject->modifiedKeys, true)
                . '. The DomainObject looks like this: '
                . \print_r($domainObject->toArray(true), true)
            );
        }

        // Map old column names with new ones
        $array = $domainObject->toArray();
        $obj   = new \stdClass();
        foreach (\array_keys($array) as $key) {
            $obj->{$this->mapColumn($key)} = $array[$key];
        }

        foreach ($obj as &$value) {
            if ($value === null) {
                $value = '_DBNULL_';
            }
        }

        return $this->db->insertRow($this->getTableName(), $obj);
    }
}
