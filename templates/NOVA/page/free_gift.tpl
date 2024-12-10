{block name='page-free-gift'}
    {opcMountPoint id='opc_before_free_gift' inContainer=false}
    {container fluid=$Link->getIsFluid() class="page-freegift {if $Einstellungen.template.theme.left_sidebar === 'Y' && $boxesLeftActive}container-plus-sidebar{/if}"}
        <p>{lang key='freeGiftFromOrderValue'}</p>
        {if !empty($freeGifts)}
            {opcMountPoint id='opc_before_free_gift_list'}
            {row id="freegift"}
                {block name='page-freegift-freegifts'}
                    {foreach $freeGifts as $freeGiftProduct}
                        {$basketValue = $freeGiftProduct->availableFrom - $freeGiftProduct->getStillMissingAmount()}
                        {$isFreeGiftAvailableNow = $basketValue >= $freeGiftProduct->availableFrom}
                        {col sm=6 md=4 class="page-freegift-item freegift mb-3"}
                            {block name='page-freegift-freegift-image'}
                                {link href=$freeGiftProduct->getProduct()->cURLFull|cat:'?isfreegift=1'}
                                    {include file='snippets/image.tpl'
                                        item=$freeGiftProduct->getProduct()
                                        square=false
                                        srcSize='sm'
                                        sizes='200px'}
                                {/link}
                            {/block}
                            {block name='page-freegift-freegift-link'}
                                <p>
                                    {link href=$freeGiftProduct->getProduct()->cURLFull|cat:'?isfreegift=1'}{$freeGiftProduct->getProduct()->cName}{/link}
                                    {block name='page-freegift-freegift-info'}
                                        <span class="small text-muted-util d-block">
                                            {lang key='freeGiftFrom1'}
                                            {$freeGiftProduct->getProduct()->cBestellwert}
                                            {lang key='freeGiftFrom2'}
                                        </span>
                                    {/block}
                                </p>
                            {/block}
                        {/col}
                    {/foreach}
                {/block}
            {/row}
        {/if}
    {/container}
{/block}
