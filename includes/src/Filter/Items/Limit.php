<?php

declare(strict_types=1);

namespace JTL\Filter\Items;

use JTL\Filter\AbstractFilter;
use JTL\Filter\FilterInterface;
use JTL\Filter\Option;
use JTL\Filter\ProductFilter;
use JTL\Helpers\Request;
use JTL\Shop;

/**
 * Class Limit
 * @package JTL\Filter\Items
 */
class Limit extends AbstractFilter
{
    /**
     * Limit constructor.
     *
     * @param ProductFilter $productFilter
     */
    public function __construct(ProductFilter $productFilter)
    {
        parent::__construct($productFilter);
        $this->setIsCustom(false)
            ->setUrlParam(\QUERY_PARAM_PRODUCTS_PER_PAGE)
            ->setFrontendName(Shop::Lang()->get('productsPerPage', 'productOverview'))
            ->setFilterName($this->getFrontendName());
    }

    /**
     * @return int
     */
    public function getProductsPerPageLimit(): int
    {
        $extendedView = Request::getVar(\QUERY_PARAM_VIEW_MODE);
        if ($this->productFilter->getProductLimit() !== 0) {
            $limit = $this->productFilter->getProductLimit();
        } elseif (
            isset($_SESSION['ArtikelProSeite'])
            && $_SESSION['ArtikelProSeite'] !== 0
            && !$extendedView
        ) {
            $limit = $_SESSION['ArtikelProSeite'];
        } elseif (
            isset($_SESSION['oErweiterteDarstellung']->nAnzahlArtikel)
            && $_SESSION['oErweiterteDarstellung']->nAnzahlArtikel !== 0
            && !$extendedView
        ) {
            $limit = $_SESSION['oErweiterteDarstellung']->nAnzahlArtikel;
        } else {
            $type = 'artikeluebersicht_anzahl_darstellung' .
                ($extendedView
                    ?? $this->getConfig('artikeluebersicht')['artikeluebersicht_erw_darstellung_stdansicht']);

            if (($limit = $this->getConfig('artikeluebersicht')[$type]) === 0) {
                $limit = ($max = $this->getConfig('artikeluebersicht')['artikeluebersicht_artikelproseite']) !== 0
                    ? $max
                    : 20;
            }
        }

        return \min((int)$limit, \ARTICLES_PER_PAGE_HARD_LIMIT);
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
        $params           = $this->productFilter->getParams();
        $view             = $this->productFilter->getMetaData()->getExtendedView($params['nDarstellung'])->nDarstellung;
        $optionIdx        = $view === \ERWDARSTELLUNG_ANSICHT_LISTE
            ? 'products_per_page_list'
            : 'products_per_page_gallery';
        $limitOptions     = \explode(',', $this->getConfig('artikeluebersicht')[$optionIdx]);
        $activeValue      = $_SESSION['ArtikelProSeite'] ?? $this->getProductsPerPageLimit();
        foreach ($limitOptions as $i => $limitOption) {
            $limitOption = (int)\trim($limitOption);
            $name        = $limitOption > 0 ? $limitOption : Shop::Lang()->get('showAll');
            $opt         = new Option();
            $opt->setIsActive($activeValue === $limitOption);
            $opt->setURL($this->productFilter->getFilterURL()->getURL($additionalFilter->init($limitOption)));
            $opt->setType($this->getType());
            $opt->setClassName($this->getClassName());
            $opt->setParam($this->getUrlParam());
            $opt->setName((string)$name);
            $opt->setValue($limitOption);
            $opt->setSort($i);
            $options[] = $opt;
        }
        $this->options = $options;

        return $options;
    }
}
