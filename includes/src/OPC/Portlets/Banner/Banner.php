<?php

declare(strict_types=1);

namespace JTL\OPC\Portlets\Banner;

use JTL\Catalog\Product\Artikel;
use JTL\OPC\InputType;
use JTL\OPC\Portlet;

/**
 * Class Banner
 * @package JTL\OPC\Portlets
 */
class Banner extends Portlet
{
    /**
     * @param int $productID
     * @return Artikel|null
     */
    public function getProduct(int $productID): ?Artikel
    {
        return (new Artikel())->fuelleArtikel($productID, Artikel::getDefaultOptions());
    }

    /**
     * @return string
     */
    public function getPlaceholderImgUrl(): string
    {
        return $this->getBaseUrl() . 'preview.banner.jpg';
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getPropertyDesc(): array
    {
        return [
            'src'   => [
                'type'  => InputType::IMAGE,
                'label' => \__('Image'),
                'thumb' => true,
            ],
            'alt'   => [
                'label' => \__('alternativeText'),
                'desc'  => \__('altTextDesc'),
            ],
            'zones' => [
                'type'    => InputType::ZONES,
                'label'   => \__('bannerAreas'),
                'srcProp' => 'src',
                'default' => [],
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getPropertyTabs(): array
    {
        return [
            \__('Styles')    => 'styles',
            \__('Animation') => 'animations',
        ];
    }
}
