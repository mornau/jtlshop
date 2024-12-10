{block name='snippets-slider-items'}
    {foreach $items as $item}
        {if $type === 'product'}
            {block name='snippets-slider-items-product'}
                <div class="product-wrapper product-wrapper-product text-center-util {if $item@first && $item@last} m-auto{elseif $item@first} ml-auto-util {elseif $item@last} mr-auto {/if}{if isset($style)} {$style}{/if}" {if $tplscope !== 'box'}{if isset($Link) && $Link->getLinkType() === $smarty.const.LINKTYP_STARTSEITE || $nSeitenTyp === $smarty.const.PAGE_ARTIKELLISTE}itemprop="about"{else}itemprop="isRelatedTo"{/if} itemscope itemtype="https://schema.org/Product"{/if}>
                    {include file='productlist/item_slider.tpl' Artikel=$item tplscope=$tplscope}
                </div>
            {/block}
        {elseif $type === 'news'}
            {block name='snippets-slider-items-news'}
                <div class="product-wrapper product-wrapper-news
                            {if $item@first && $item@last}
                                mx-auto
                            {elseif $item@first}
                                ml-auto-util
                            {elseif $item@last}
                                mr-auto
                            {/if}">
                    {include file='blog/preview.tpl' newsItem=$item}
                </div>
            {/block}
        {elseif $type === 'freegift'}
            {if $Einstellungen.sonstiges.sonstiges_gratisgeschenk_noch_nicht_verfuegbar_anzeigen === 'N'
            && $item->getStillMissingAmount() > 0}
                {continue}
            {/if}
            {$isFirstItem=$item@first}
            {$isLastItem=$item@last}
            {block name='snippets-slider-items-freegift'}
                <div class="product-wrapper product-wrapper-freegift
                    {if $isFirstItem && $isLastItem} m-auto {elseif $isFirstItem} ml-auto-util {elseif $isLastItem} mr-auto {/if}
                    freegift{if $item->getStillMissingAmount() > 0} not-available-yet{/if}">
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input"
                               type="radio"
                               id="gift{$item->productID}"
                               name="gratisgeschenk"
                               value="{$item->productID}"
                               onclick="submit();"
                               {if $item->getStillMissingAmount() > 0} disabled{/if}>
                        <label for="gift{$item->productID}"
                               class="custom-control-label {if $selectedFreegift===$item->productID}badge-check{/if}">
                            {if $selectedFreegift===$item->productID}
                                {badge class="badge-circle"}
                                    <i class="fas fa-check mx-auto"></i>
                                {/badge}
                            {/if}
                            {include file='snippets/image.tpl' item=$item->getProduct()
                                srcSize='sm'
                                alt=$item->getProduct()->cName
                                sizes='(min-width: 992px) 19vw, (min-width: 768px) 29vw, 50vw'}
                            {block name='snippets-slider-items-freegift-caption'}
                                <div class="caption">
                                    {if $item->getProduct()->cBestellwert !== null}
                                        <p class="small text-muted-util d-none">
                                            {lang key='freeGiftFrom1'} {$item->getProduct()->cBestellwert} {lang key='freeGiftFrom2'}
                                        </p>
                                    {/if}
                                    <p>{$item->getProduct()->cName}</p>
                                </div>
                                {block name='snippets-slider-items-freegift-caption-missing-amount-progress-bar'}
                                    {if $item->getStillMissingAmount() > 0}
                                        <div class="progress rounded">
                                            <div class="progress-bar" role="progressbar"
                                                 style="width: {if $item->availableFrom > 0}{(($item->availableFrom - $item->getStillMissingAmount()) * 100) / $item->availableFrom}{else}100{/if}%;"
                                                 aria-valuenow="{$item->availableFrom - $item->getStillMissingAmount()}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="{$item->availableFrom}">
                                            </div>
                                        </div>
                                        <span class="small w-100 text-center">
                                            {lang section='basket' key='freeGiftsStillMissingAmount'
                                            printf=JTL\Catalog\Product\Preise::getLocalizedPriceString($item->getStillMissingAmount())}
                                        </span>
                                    {/if}
                                {/block}
                                <p class="mt-1">
                                    {link href="{$item->getProduct()->cURLFull|cat:'?isfreegift=1'}"}
                                        {lang section='global' key='details'}
                                    {/link}
                                </p>
                            {/block}
                        </label>
                    </div>
                </div>
            {/block}
        {/if}
    {/foreach}
{/block}
