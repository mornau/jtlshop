{$presentation = $instance->getProperty('presentation')}
{$uid = $instance->getUid()}

{$displayCountSM = $instance->getProperty('displayCountSM')}
{$displayCountMD = $instance->getProperty('displayCountMD')}
{$displayCountLG = $instance->getProperty('displayCountLG')}
{$displayCountXL = $instance->getProperty('displayCountXL')}

{if $isPreview}
    <div class="opc-item-slider" style="{$instance->getStyleString()}">
        {image alt='ManufacturerStream' src=$portlet->getBaseUrl()|cat:'preview.'|cat:$presentation|cat:'.jpg'}
    </div>
{else}
    {$items = $portlet->getFilteredItems($instance)}
    {if $inContainer === false}
        <div class="container-fluid">
    {/if}
    {$noCaptionSlider = $presentation === 'justImages'}
    <div class="opc-item-slider {$instance->getStyleClasses()} opc-item-slider-{$presentation}"
         style="{$instance->getStyleString()}">
        <div class="slick-slider-other">
            {block name='snippets-product-slider-other-products'}
                {row class="slick-lazy slick-smooth-loading carousel carousel-arrows-inside slick-type-product {if $items|count < 3}slider-no-preview{/if}"
                    data=[
                        "slick-type" => "product-slider",
                        "display-counts" => "$displayCountSM,$displayCountMD,$displayCountLG,$displayCountXL"
                    ]
                    style="--display-count-sm:$displayCountSM;--display-count-md:$displayCountMD;--display-count-lg:$displayCountLG;--display-count-xl:$displayCountXL"
                }
                    {foreach $items as $item}
                        <div class="product-wrapper product-wrapper-product text-center-util
                                {if $item@first && $item@last}m-auto{elseif $item@first}ml-auto-util{elseif $item@last}mr-auto{/if}
                                {if isset($style)}{$style}{/if}">
                            {link href = $item->getURL()}
                                <div class="item-slider productbox-image square square-image">
                                    <div class="inner">
                                        {include file='snippets/image.tpl' item=$item alt=$item->getName()|escape:'html'
                                            square=false srcSize='sm' class='product-image'
                                            sizes='(min-width: 1300px) 15vw, (min-width: 992px) 20vw, (min-width: 768px) 34vw, 50vw'
                                        }
                                        <meta itemprop="image" content="{$item->getImage(\JTL\Media\Image::SIZE_MD)}">
                                        <meta itemprop="url" content="{$item->getURL()}">
                                    </div>
                                </div>
                            {/link}
                            {if empty($noCaptionSlider)}
                                {link href = $item->getURL()}
                                    <span class="item-slider-desc text-clamp-2">
                                        <span itemprop="name">{$item->getName()}</span>
                                    </span>
                                {/link}
                            {/if}
                        </div>
                    {/foreach}
                {/row}
            {/block}
        </div>
    </div>
    {if $inContainer === false}
        </div>
    {/if}
{/if}
