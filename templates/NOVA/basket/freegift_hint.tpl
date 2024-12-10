{block name='basket-freegift-hint'}
    {$freeGiftService = JTL\Shop::Container()->getFreeGiftService()}
    {if $Einstellungen.sonstiges.sonstiges_gratisgeschenk_wk_hinweis_anzeigen === 'Y'
        && $Einstellungen.sonstiges.sonstiges_gratisgeschenk_nutzen === 'Y'
        && $freeGiftService->getFreeGifts()->count() > 0}
        {if $freeGiftService->basketHoldsFreeGift(JTL\Session\Frontend::getCart()) === false}
            <hr>
            <div class="font-weight-bold">
                {if !empty($oSpezialseiten_arr) && isset($oSpezialseiten_arr[$smarty.const.LINKTYP_GRATISGESCHENK])}
                    <a href="{$oSpezialseiten_arr[$smarty.const.LINKTYP_GRATISGESCHENK]->getURL()}"
                       title="{lang key='freeGiftsSeeAll' section='basket'}"><i class="fas fa-gifts text-dark mr-1"></i></a>
                {else}
                    <i class="fas fa-gifts text-dark mr-1"></i>
                {/if}
                <span>{lang key='freeGiftsAvailable' section='basket'}</span>
            </div>

            <span class="d-block">{lang section='basket' key='freeGiftsAvailableText'}</span>
            <a href="{get_static_route id='warenkorb.php'}#freeGiftsHeading" class="btn btn-link p-0">
                <u>{lang section='basket' key='chooseFreeGiftNow'}</u>
            </a>

            {block name='basket-freegift-hint-still-missing-amount'}
                {if $Einstellungen.sonstiges.sonstiges_gratisgeschenk_noch_nicht_verfuegbar_anzeigen === 'Y'
                && !empty($nextFreeGiftMissingAmount)}
                    <span class="d-block">{lang section='basket' key='freeGiftsStillMissingAmountForNextFreeGift'
                    printf=JTL\Catalog\Product\Preise::getLocalizedPriceString($nextFreeGiftMissingAmount)}</span>
                {/if}
            {/block}
        {/if}
    {/if}
{/block}
