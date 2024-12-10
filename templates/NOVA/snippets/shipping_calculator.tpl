{block name='snippets-shipping-calculator'}
    {block name='snippets-shipping-calculator-form'}
        <div id="shipping-estimate-form" class="shipping-calculator-main">
            {block name='snippets-shipping-calculator-form-content'}
                {block name='snippets-shipping-calculator-estimate'}
                    <div class="h3 shipping-calculator-main-heading">{lang key='estimateShippingCostsTo' section='checkout'}:</div>
                {/block}
                {block name='snippets-shipping-calculator-estimate-main'}
                    <div class="form-row">
                        {block name='snippets-shipping-calculator-countries'}
                            {col cols=12 md=5 class="shipping-calculator-main-country"}
                                {formgroup}
                                    {select name="land" id="country" class='custom-select' placeholder="" aria=["label"=>"{lang key='country' section='account data'}"]}
                                        {foreach $countries as $country}
                                            {if $country->isShippingAvailable()}
                                                <option value="{$country->getISO()}" {if $shippingCountry === $country->getISO()}selected{/if}>
                                                    {$country->getName()}
                                                </option>
                                            {/if}
                                        {/foreach}
                                    {/select}
                                {/formgroup}
                            {/col}
                        {/block}
                        {block name='snippets-shipping-calculator-submit'}
                            {col cols=12 md=3}
                                {$selectedISO = trim("
                                    {if isset($VersandPLZ)}
                                        {$VersandPLZ}
                                    {elseif JTL\Session\Frontend::getCustomer()->cPLZ !== null}
                                        {JTL\Session\Frontend::getCustomer()->cPLZ}
                                    {/if}")}
                                {formgroup label-for="plz" label="{lang key='plz' section='forgot password'}"}
                                    {input type="text"
                                        id="plz"
                                        name="plz"
                                        size="8"
                                        maxlength="8"
                                        value=$selectedISO
                                        placeholder=" "
                                        aria=["label"=>"{lang key='plz' section='account data'}"]
                                    }
                                {/formgroup}
                            {/col}
                            {col cols=12 md=4}
                                {button block=true name='versandrechnerBTN' type='submit' variant='outline-primary'}
                                    {lang key='estimateShipping' section='checkout'}
                                {/button}
                            {/col}
                        {/block}
                    </div>
                {/block}
            {/block}
        </div>
    {/block}
    {if !empty($Versandland) && !empty($VersandPLZ)}
        <div id="shipping-estimated" class="table-responsive">
            {block name='snippets-shipping-calculator-content'}
                {if !empty($ArtikelabhaengigeVersandarten)}
                    {block name='snippets-shipping-calculator-table-artikelabhaengig'}
                        <table class="table table-striped">
                            {block name='snippets-shipping-calculator-table-artikelabhaengig-caption'}
                                <caption>{lang key='productShippingDesc' section='checkout'}</caption>
                            {/block}
                            <tbody>
                                {block name='snippets-shipping-calculator-table-artikelabhaengig-body'}
                                    {foreach $ArtikelabhaengigeVersandarten as $artikelversand}
                                        <tr>
                                            <td>{$artikelversand->cName|transByISO}</td>
                                            <td class="text-right-util text-nowrap-util">
                                                <strong>{$artikelversand->cPreisLocalized}</strong>
                                            </td>
                                        </tr>
                                    {/foreach}
                                {/block}
                            </tbody>
                        </table>
                    {/block}
                {/if}
                {if !empty($Versandarten)}
                    {block name='snippets-shipping-calculator-shipping-methods'}
                        <table class="table table-striped">
                            {block name='snippets-shipping-calculator-shipping-methods-caption'}
                                <caption>{lang key='shippingMethods'}</caption>
                            {/block}
                            <tbody>
                                {block name='snippets-shipping-calculator-shipping-methods-body'}
                                    {foreach $Versandarten as $versandart}
                                        <tr id="shipment_{$versandart->kVersandart}">
                                            <td>
                                                {if $versandart->cBild}
                                                    {image src=$versandart->cBild alt="{$versandart->angezeigterName|transByISO}" fluid=true}
                                                {else}
                                                    {$versandart->angezeigterName|transByISO}
                                                {/if}
                                                {if $versandart->angezeigterHinweistext|transByISO}
                                                    <p class="small">
                                                        {$versandart->angezeigterHinweistext|transByISO}
                                                    </p>
                                                {/if}
                                                {if isset($versandart->Zuschlag) && $versandart->Zuschlag->fZuschlag != 0}
                                                    <p class="small">
                                                        {$versandart->Zuschlag->angezeigterName|transByISO}
                                                            (+{$versandart->Zuschlag->cPreisLocalized})
                                                    </p>
                                                {/if}
                                                {if $versandart->cLieferdauer|transByISO && $Einstellungen.global.global_versandermittlung_lieferdauer_anzeigen === 'Y'}
                                                    <p class="small">
                                                        {lang key='shippingTimeLP'}: {$versandart->cLieferdauer|transByISO}
                                                    </p>
                                                {/if}
                                            </td>
                                            <td class="text-right-util text-nowrap-util">
                                                <strong>
                                                    {$versandart->cPreisLocalized}
                                                </strong>
                                            </td>
                                        </tr>
                                    {/foreach}
                                {/block}
                            </tbody>
                        </table>
                    {/block}
                    {block name='snippets-shipping-calculator-link'}
                        {if isset($checkout) && $checkout}
                            {get_static_route id='warenkorb.php' assign='link'}
                        {else}
                            {$link = $ShopURL|cat:'/?s='|cat:$Link->getID()}
                        {/if}
                    {/block}
                {else}
                    {block name='snippets-shipping-calculator-no-shipping-available'}
                        {row}
                            {col}
                                {lang key='noShippingAvailable' section='checkout'}
                            {/col}
                        {/row}
                    {/block}
                {/if}
            {/block}
        </div>
    {/if}
    {block name='snippets-shipping-calculator-hr-end'}
        {if $hrAtEnd|default:true}
            <hr class="shipping-calculator-hr">
        {/if}
    {/block}
{/block}
