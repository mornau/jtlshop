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
 * Class CategoryList
 * @package JTL\News
 */
final class CategoryList implements ItemListInterface
{
    /**
     * @var Collection
     */
    private Collection $items;

    /**
     * CategoryList constructor.
     * @param DbInterface            $db
     * @param JTLCacheInterface|null $cache
     */
    public function __construct(private readonly DbInterface $db, private ?JTLCacheInterface $cache = null)
    {
        $this->items = new Collection();
        $this->cache = $cache ?? Shop::Container()->getCache();
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
        $itemLanguages = $this->db->getObjects(
            'SELECT *
                FROM tnewskategoriesprache
                JOIN tnewskategorie
                    ON tnewskategoriesprache.kNewsKategorie = tnewskategorie.kNewsKategorie
                JOIN tseo
                    ON tseo.cKey = \'kNewsKategorie\'
                    AND tseo.kKey = tnewskategorie.kNewsKategorie
                WHERE tnewskategorie.kNewsKategorie  IN (' . \implode(',', $itemIDs) . ')
                GROUP BY tnewskategoriesprache.kNewsKategorie,tnewskategoriesprache.languageID
                ORDER BY tnewskategorie.lft'
        );
        $items         = map(
            group(
                $itemLanguages,
                static function ($e): int {
                    return (int)$e->kNewsKategorie;
                }
            ),
            function ($e, $newsID) use ($activeOnly): Category {
                $c = new Category($this->db, $this->cache);
                $c->setID($newsID);
                $c->map($e, $activeOnly);

                return $c;
            }
        );
        foreach ($items as $item) {
            $this->items->push($item);
        }

        return $this->items;
    }

    /**
     * @param Collection<Category> $tree
     * @param int                  $id
     * @return Category|null
     */
    private function findParentCategory(Collection $tree, int $id): ?Category
    {
        /** @var Category|null $found */
        $found = $tree->first(static function (Category $e) use ($id): bool {
            return $e->getID() === $id;
        });
        if ($found !== null) {
            return $found;
        }
        /** @var Category $item */
        foreach ($tree as $item) {
            $found = $this->findParentCategory($item->getChildren(), $id);

            if ($found !== null) {
                return $found;
            }
        }

        return null;
    }

    /**
     * @return Collection<Category>
     */
    public function generateTree(): Collection
    {
        $tree = new Collection();
        foreach ($this->items as $item) {
            /** @var Category $item */
            if ($item->getParentID() === 0) {
                $tree->push($item);
                continue;
            }
            $parentID = $item->getParentID();
            $found    = $this->findParentCategory($tree, $parentID);
            $found?->addChild($item);
        }

        return $tree;
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
        $res          = \get_object_vars($this);
        $res['db']    = '*truncated*';
        $res['cache'] = '*truncated*';

        return $res;
    }
}
