<?php

declare(strict_types=1);

namespace JTL\News;

use Illuminate\Support\Collection;
use JTL\Cache\JTLCacheInterface;
use JTL\DB\DbInterface;
use JTL\Shop;

use function Functional\group;
use function Functional\map;

/**
 * Class ItemList
 * @package JTL\News
 */
final class ItemList implements ItemListInterface
{
    /**
     * @var Collection
     */
    private Collection $items;

    /**
     * @var JTLCacheInterface
     */
    private JTLCacheInterface $cache;

    /**
     * ItemList constructor.
     * @param DbInterface            $db
     * @param JTLCacheInterface|null $cache
     */
    public function __construct(private readonly DbInterface $db, ?JTLCacheInterface $cache = null)
    {
        $this->cache = $cache ?? Shop::Container()->getCache();
        $this->items = new Collection();
    }

    /**
     * @inheritdoc
     */
    public function createItems(array $itemIDs, bool $activeOnly = true): Collection
    {
        $itemIDs = \array_map('\intval', $itemIDs);
        if (\count($itemIDs) === 0) {
            return $this->items;
        }
        $itemList      = \implode(',', $itemIDs);
        $itemLanguages = $this->db->getObjects(
            'SELECT tnewssprache.languageID,
            tnewssprache.languageCode,
            tnews.cKundengruppe, 
            tnews.kNews, 
            tnewssprache.title AS localizedTitle, 
            tnewssprache.content, 
            tnewssprache.preview, 
            tnews.cPreviewImage AS previewImage, 
            tnewssprache.metaTitle, 
            tnewssprache.metaKeywords, 
            tnewssprache.metaDescription, 
            tnews.nAktiv AS isActive, 
            tnews.dErstellt AS dateCreated, 
            tnews.dGueltigVon AS dateValidFrom, 
            tseo.cSeo AS localizedURL
                FROM tnews
                JOIN tnewssprache
                    ON tnews.kNews = tnewssprache.kNews
                JOIN tseo 
                    ON tseo.cKey = \'kNews\'
                    AND tseo.kKey = tnews.kNews
                    AND tseo.kSprache = tnewssprache.languageID
                WHERE tnews.kNews IN (' . $itemList . ')
                GROUP BY tnews.kNews, tnewssprache.languageID
                ORDER BY FIELD(tnews.kNews, ' . $itemList . ')'
        );
        $items         = map(
            group(
                $itemLanguages,
                static function (\stdClass $e): int {
                    return (int)$e->kNews;
                }
            ),
            function ($e, $newsID): Item {
                $l = new Item($this->db, $this->cache);
                $l->setID($newsID);
                $l->map($e);

                return $l;
            }
        );
        foreach ($items as $item) {
            $this->items->push($item);
        }

        return $this->items;
    }

    /**
     * @inheritdoc
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * @inheritdoc
     */
    public function setItems(Collection $items): void
    {
        $this->items = $items;
    }

    /**
     * @inheritdoc
     */
    public function addItem($item): void
    {
        $this->items->push($item);
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        $res       = \get_object_vars($this);
        $res['db'] = '*truncated*';

        return $res;
    }
}
