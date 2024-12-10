{block name='checkout-inc-payment-methods'}
    {radiogroup}
        {foreach $Zahlungsarten as $zahlungsart}
            {col cols=12 id=$zahlungsart->cModulId class="checkout-payment-method"}
                {radio name="Zahlungsart"
                        value=$zahlungsart->kZahlungsart
                        id="payment{$zahlungsart->kZahlungsart}"
                        checked=($AktiveZahlungsart === $zahlungsart->kZahlungsart || $Zahlungsarten|@count === 1)
                        required=($zahlungsart@first)
                }
                    {block name='checkout-inc-payment-methods-image-title'}
                        {if $zahlungsart->cBild}
                                {image src=$zahlungsart->cBild alt=$zahlungsart->angezeigterName|transByISO fluid=true class="img-sm"}
                        {else}
                            <span class="content">
                                <span class="title">{$zahlungsart->angezeigterName|transByISO}</span>
                            </span>
                        {/if}
                    {/block}
                    {if $zahlungsart->fAufpreis != 0}
                        {block name='checkout-inc-payment-methods-badge'}
                            <strong class="checkout-payment-method-badge">
                            {if $zahlungsart->cGebuehrname|has_trans}
                                <span>{$zahlungsart->cGebuehrname|transByISO} </span>
                            {/if}
                                {$zahlungsart->cPreisLocalized}
                            </strong>
                        {/block}
                    {/if}
                    {if $zahlungsart->cHinweisText|has_trans}
                        {block name='checkout-inc-payment-methods-note'}
                            <span class="checkout-payment-method-note">
                                <small>{$zahlungsart->cHinweisText|transByISO}</small>
                            </span>
                        {/block}
                    {/if}
                {/radio}
            {/col}
        {/foreach}
    {/radiogroup}
{/block}
