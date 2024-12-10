<?php

declare(strict_types=1);

namespace JTL\Filter\Items;

use Illuminate\Support\Collection;
use JTL\Filter\AbstractFilter;
use JTL\Filter\FilterInterface;
use JTL\Filter\Option;
use JTL\Filter\ProductFilter;
use JTL\Filter\SortingOptions\Factory;
use JTL\Filter\SortingOptions\SortingOptionInterface;
use JTL\Mapper\SortingType;
use JTL\Shop;

/**
 * Class Sort
 * @package JTL\Filter\Items
 */
class Sort extends AbstractFilter
{
    /**
     * @var Factory|null
     */
    private ?Factory $factory = null;

    /**
     * @var Collection
     */
    private Collection $sortingOptions;

    /**
     * @var int
     */
    protected int $activeSortingType;

    /**
     * Sort constructor.
     *
     * @param ProductFilter $productFilter
     */
    public function __construct(ProductFilter $productFilter)
    {
        $this->sortingOptions = new Collection();
        parent::__construct($productFilter);
        $this->setIsCustom(false)
            ->setUrlParam('Sortierung')
            ->setFrontendName(Shop::Lang()->get('sorting', 'productOverview'))
            ->setFilterName($this->getFrontendName());
        $this->activeSortingType = (int)$this->getConfig('artikeluebersicht')['artikeluebersicht_artikelsortierung'];
        if (isset($_SESSION['Usersortierung'])) {
            $mapper                  = new SortingType();
            $this->activeSortingType = $mapper->mapUserSorting($_SESSION['Usersortierung']);
        }
        $_SESSION['Usersortierung'] = $this->activeSortingType;
        if ($_SESSION['Usersortierung'] === \SEARCH_SORT_STANDARD && $this->productFilter->getSort() > 0) {
            $this->activeSortingType = $this->productFilter->getSort();
        }
    }

    /**
     * @return SortingOptionInterface
     */
    public function getActiveSorting(): SortingOptionInterface
    {
        return $this->factory->getSortingOption($this->activeSortingType);
    }

    /**
     * @return Factory
     */
    public function getFactory(): Factory
    {
        return $this->factory;
    }

    /**
     * @param Factory $factory
     */
    public function setFactory(Factory $factory): void
    {
        $this->factory = $factory;
    }

    /**
     * @return Collection
     */
    public function getSortingOptions(): Collection
    {
        return $this->sortingOptions;
    }

    /**
     * @param Collection $sortingOptions
     */
    public function setSortingOptions(Collection $sortingOptions): void
    {
        $this->sortingOptions = $sortingOptions;
    }

    /**
     * @return int
     */
    public function getActiveSortingType(): int
    {
        return $this->activeSortingType;
    }

    /**
     * @param int $activeSortingType
     */
    public function setActiveSortingType(int $activeSortingType): void
    {
        $this->activeSortingType = $activeSortingType;
    }

    /**
     * @throws \LogicException
     */
    public function registerSortingOptions(): void
    {
        if ($this->factory === null) {
            throw new \LogicException('Factory has to be set first.');
        }
        $this->sortingOptions = $this->factory->getAll()->sortByDesc(static function (SortingOptionInterface $i): int {
            return $i->getPriority();
        });
    }

    /**
     * @inheritdoc
     */
    public function setSeo(array $languages): FilterInterface
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSQLJoin()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getOptions($mixed = null): array
    {
        if ($this->options !== null) {
            return $this->options;
        }
        $options          = [];
        $additionalFilter = new self($this->productFilter);
        $activeSortType   = (int)($_SESSION['Usersortierung'] ?? -1);
        /** @var SortingOptionInterface $sortingOption */
        foreach ($this->sortingOptions as $i => $sortingOption) {
            $value = $sortingOption->getValue();
            $opt   = new Option();
            $opt->setIsActive($activeSortType === $value);
            $opt->setURL($this->productFilter->getFilterURL()->getURL($additionalFilter->init($value)));
            $opt->setType($this->getType());
            $opt->setClassName($this->getClassName());
            $opt->setParam($this->getUrlParam());
            $opt->setName($sortingOption->getName());
            $opt->setValue($value);
            $opt->setSort($i);
            $options[] = $opt;
        }
        $this->options = $options;

        return $options;
    }
}
