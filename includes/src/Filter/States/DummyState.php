<?php

declare(strict_types=1);

namespace JTL\Filter\States;

use JTL\Filter\AbstractFilter;
use JTL\Filter\FilterInterface;
use JTL\Filter\ProductFilter;

/**
 * Class DummyState
 * @package JTL\Filter\States
 */
class DummyState extends AbstractFilter
{
    /**
     * @var int|null
     */
    public ?int $dummyValue = null;

    /**
     * DummyState constructor.
     *
     * @param ProductFilter $productFilter
     */
    public function __construct(ProductFilter $productFilter)
    {
        parent::__construct($productFilter);
        $this->setIsCustom(false)
            ->setUrlParam(\QUERY_PARAM_DUMMY)
            ->setUrlParamSEO(null);
    }

    /**
     * @inheritdoc
     */
    public function setValue($value): FilterInterface
    {
        $this->dummyValue = (int)$value;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        return $this->dummyValue;
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
    public function init($value): FilterInterface
    {
        $this->isInitialized = true;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSQLJoin()
    {
        return [];
    }
}
