{block name='account-order-item'}
    <div class="order-items card-table">
        {block name='account-order-item-items'}
        {foreach $Bestellung->Positionen as $oPosition}
            {if !(is_string($oPosition->cUnique) && !empty($oPosition->cUnique) && (int)$oPosition->kKonfigitem > 0)} {*!istKonfigKind()*}
                {row class="type-{$oPosition->nPosTyp} order-item"}
                    {block name='account-order-item-items-data'}
                    {col cols=12 md="{if $Einstellungen.kaufabwicklung.bestellvorgang_einzelpreise_anzeigen === 'Y'}6{else}8{/if}"}
                        {row}
                            {col cols=3 md=4 class='order-item-image-wrapper'}
                                {if !empty($oPosition->Artikel->cVorschaubildURL)}
                                    {block name='account-order-item-image'}
                                        {link href=$oPosition->Artikel->cURLFull title=$oPosition->cName|transByISO|escape:'html'}
                                            {include file='snippets/image.tpl'
                                                item=$oPosition->Artikel
                                                sizes='(min-width: 992px) 10vw, (min-width: 768px) 17vw, 25vw'
                                                lazy=!$oPosition@first
                                                square=false
                                                alt=$oPosition->cName|transByISO|escape:'html'
                                            }
                                        {/link}
                                    {/block}
                                {/if}
                            {/col}
                            {col md=8}
                            {if $oPosition->nPosTyp == $smarty.const.C_WARENKORBPOS_TYP_ARTIKEL}
                                {block name='account-order-item-details'}
                                    {block name='account-order-item-link'}
                                        {link href=$oPosition->Artikel->cURLFull title=$oPosition->cName|transByISO|escape:'html'}{$oPosition->cName|transByISO}{/link}
                                    {/block}
                                    <ul class="list-unstyled text-muted-util small item-detail-list">
                                        {block name='account-order-item-sku'}
                                            <li class="sku">{lang key='productNo' section='global'}: {$oPosition->Artikel->cArtNr}</li>
                                        {/block}
                                        {if $Einstellungen.artikeldetails.show_shelf_life_expiration_date === 'Y'
                                        && isset($oPosition->Artikel->dMHD, $oPosition->Artikel->dMHD_de)}
                                            {block name='account-order-item-mhd'}
                                                <li title="{lang key='productMHDTool' section='global'}" class="best-before">
                                                    {lang key='productMHD' section='global'}:{$oPosition->Artikel->dMHD_de}
                                                </li>
                                            {/block}
                                        {/if}
                                        {if $Einstellungen.kaufabwicklung.warenkorb_varianten_varikombi_anzeigen === 'Y' && isset($oPosition->WarenkorbPosEigenschaftArr) && !empty($oPosition->WarenkorbPosEigenschaftArr)}
                                            {block name='account-order-item-variations'}
                                                {foreach $oPosition->WarenkorbPosEigenschaftArr as $Variation}
                                                    <li class="variation">
                                                        {$Variation->cEigenschaftName|transByISO}: {$Variation->cEigenschaftWertName|transByISO} {if !empty($Variation->cAufpreisLocalized[$NettoPreise])}&raquo;
                                                            {if substr($Variation->cAufpreisLocalized[$NettoPreise], 0, 1) !== '-'}+{/if}{$Variation->cAufpreisLocalized[$NettoPreise]} {/if}
                                                    </li>
                                                {/foreach}
                                            {/block}
                                        {/if}
                                        {if !empty($oPosition->cHinweis)}
                                            {block name='account-order-item-notice'}
                                                <li class="text-info notice">{$oPosition->cHinweis}</li>
                                            {/block}
                                        {/if}

                                        {* Buttonloesung eindeutige Merkmale *}
                                        {if $oPosition->Artikel->cHersteller && $Einstellungen.artikeldetails.artikeldetails_hersteller_anzeigen != "N"}
                                            {block name='account-order-item-manufacturer'}
                                                <li class="manufacturer">
                                                    {lang key='manufacturer' section='productDetails'}:
                                                    <span class="values">
                                                       {$oPosition->Artikel->cHersteller}
                                                    </span>
                                                </li>
                                            {/block}
                                        {/if}

                                        {if $Einstellungen.kaufabwicklung.bestellvorgang_artikelmerkmale == 'Y' && !empty($oPosition->Artikel->oMerkmale_arr)}
                                            {block name='account-order-item-characteristics'}
                                                {foreach $oPosition->Artikel->oMerkmale_arr as $characteristic}
                                                    <li class="characteristic">
                                                        {$characteristic->getName()|escape:'html'}:
                                                        <span class="values">
                                                            {foreach $characteristic->getCharacteristicValues() as $characteristicValue}
                                                                {if !$characteristicValue@first}, {/if}
                                                                {$characteristicValue->getValue()}
                                                            {/foreach}
                                                        </span>
                                                    </li>
                                                {/foreach}
                                            {/block}
                                        {/if}

                                        {if $Einstellungen.kaufabwicklung.bestellvorgang_artikelattribute == 'Y' && !empty($oPosition->Artikel->Attribute)}
                                            {block name='account-order-item-attributes'}
                                                {foreach $oPosition->Artikel->Attribute as $oAttribute_arr}
                                                    <li class="attribute">
                                                        {$oAttribute_arr->cName}:
                                                        <span class="values">
                                                            {$oAttribute_arr->cWert}
                                                        </span>
                                                    </li>
                                                {/foreach}
                                            {/block}
                                        {/if}

                                        {if $Einstellungen.kaufabwicklung.bestellvorgang_artikelkurzbeschreibung == 'Y'
                                            && $oPosition->Artikel->cKurzBeschreibung !== null
                                            && $oPosition->Artikel->cKurzBeschreibung|strlen > 0}
                                            {block name='account-order-item-short-desc'}
                                                <li class="shortdescription">{$oPosition->Artikel->cKurzBeschreibung}</li>
                                            {/block}
                                        {/if}
                                        {block name='account-order-item-delivery-status'}
                                            <li class="delivery-status">{lang key='orderStatus' section='login'}:
                                                <strong>
                                                {if $oPosition->bAusgeliefert}
                                                    {lang key='statusShipped' section='order'}
                                                {elseif $oPosition->nAusgeliefert > 0}
                                                    {if $oPosition->cUnique|strlen == 0}{lang key='statusShipped' section='order'}: {$oPosition->nAusgeliefertGesamt}{else}{lang key='statusPartialShipped' section='order'}{/if}
                                                {else}
                                                    {lang key='notShippedYet' section='login'}
                                                {/if}
                                                </strong>
                                            </li>
                                        {/block}
                                    </ul>
                                {/block}
                            {else}
                                {block name='account-order-item-details-not-product'}
                                    {$oPosition->cName|transByISO}{if isset($oPosition->discountForArticle)}{$oPosition->discountForArticle|transByISO}{/if}
                                    {if isset($oPosition->cArticleNameAffix)}
                                        {if is_array($oPosition->cArticleNameAffix)}
                                            <ul class="small text-muted-util">
                                                {foreach $oPosition->cArticleNameAffix as $cArticleNameAffix}
                                                    <li>{$cArticleNameAffix|transByISO}</li>
                                                {/foreach}
                                            </ul>
                                        {else}
                                            <ul class="small text-muted-util">
                                                <li>{$oPosition->cArticleNameAffix|transByISO}</li>
                                            </ul>
                                        {/if}
                                    {/if}
                                    {if !empty($oPosition->cHinweis)}
                                        <small class="text-info notice">{$oPosition->cHinweis}</small>
                                    {/if}
                                {/block}
                            {/if}

                            {if is_string($oPosition->cUnique) && !empty($oPosition->cUnique) && (int)$oPosition->kKonfigitem === 0} {*istKonfigVater()*}
                                {block name='account-order-item-config-items'}
                                    <ul class="config-items text-muted-util small">
                                        {foreach $Bestellung->Positionen as $KonfigPos}
                                            {if $oPosition->cUnique == $KonfigPos->cUnique && $KonfigPos->kKonfigitem > 0}
                                                <li>
                                                    <span class="qty">{if !(is_string($oPosition->cUnique) && !empty($oPosition->cUnique) && (int)$oPosition->kKonfigitem === 0)}{$KonfigPos->nAnzahlEinzel}{else}1{/if}x</span>
                                                    {$KonfigPos->cName|transByISO} &raquo;<br/>
                                                    <span class="price_value">
                                                        {if substr($KonfigPos->cEinzelpreisLocalized[$NettoPreise], 0, 1) !== '-'}+{/if}{$KonfigPos->cEinzelpreisLocalized[$NettoPreise]}
                                                        {lang key='pricePerUnit' section='checkout'}
                                                    </span>
                                                </li>
                                            {/if}
                                        {/foreach}
                                    </ul>
                                {/block}
                            {/if}
                            {/col}
                        {/row}
                    {/col}
                    {/block}

                    {block name='account-order-item-price'}
                        {block name='account-order-item-price-qty'}
                            {col class='qty-col text-right-util' md=2 cols=6}
                                {$oPosition->nAnzahl|replace_delim} {if !empty($oPosition->Artikel->cEinheit)}{if preg_match("/(\d)/", $oPosition->Artikel->cEinheit)} x{/if} {$oPosition->Artikel->cEinheit} {/if}
                            {/col}
                        {/block}
                        {if $Einstellungen.kaufabwicklung.bestellvorgang_einzelpreise_anzeigen === 'Y'}
                            {block name='account-order-item-price-single-price'}
                                {col class='price-col hidden-xs text-nowrap-util' md=2 cols=3}
                                    {if $oPosition->nPosTyp == $smarty.const.C_WARENKORBPOS_TYP_ARTIKEL}
                                        {if !(is_string($oPosition->cUnique) && !empty($oPosition->cUnique) && (int)$oPosition->kKonfigitem === 0)} {*!istKonfigVater()*}
                                            {$oPosition->cEinzelpreisLocalized[$NettoPreise]}
                                        {else}
                                            {$oPosition->cKonfigeinzelpreisLocalized[$NettoPreise]}
                                        {/if}
                                    {/if}
                                {/col}
                            {/block}
                        {/if}
                        {block name='account-order-item-price-overall'}
                            {col class='price-col text-nowrap-util' md=2 cols=3}
                                <strong class="price_overall">
                                    {if is_string($oPosition->cUnique) && !empty($oPosition->cUnique) && (int)$oPosition->kKonfigitem === 0}
                                        {$oPosition->cKonfigpreisLocalized[$NettoPreise]}
                                    {else}
                                        {$oPosition->cGesamtpreisLocalized[$NettoPreise]}
                                    {/if}
                                </strong>
                            {/col}
                        {/block}
                    {/block}
                {/row}
                {block name='account-order-item-last-hr'}
                    <hr>
                {/block}
            {/if}
        {/foreach}
        {/block}
        {block name='account-order-items-total-wrapper'}
        {row}
            {col xl=5 md=6 class='order-items-total'}
                {block name='account-order-items-total'}
                    {if $NettoPreise}
                        {block name='account-order-items-total-price-net'}
                            {row class="total-net"}
                                {col }
                                    <span class="price_label"><strong>{lang key='totalSum'} ({lang key='net'}):</strong></span>
                                {/col}
                                {col class="col-auto price-col"}
                                    <strong class="price total-sum">{$Bestellung->WarensummeLocalized[1]}</strong>
                                {/col}
                            {/row}
                        {/block}
                    {/if}
                    {if $Bestellung->GuthabenNutzen == 1}
                        {block name='account-order-items-total-customer-credit'}
                            {row class="customer-credit"}
                                {col}
                                    {lang key='useCredit' section='account data'}
                                {/col}
                                {col class="col-auto ml-auto-util text-right-util"}
                                    {$Bestellung->GutscheinLocalized}
                                {/col}
                            {/row}
                        {/block}
                    {/if}
                    {if $Einstellungen.global.global_steuerpos_anzeigen !== 'N'}
                        {block name='account-order-items-total-tax'}
                            {foreach $Bestellung->Steuerpositionen as $taxPosition}
                                {row class="tax"}
                                    {col}
                                        <span class="tax_label">{$taxPosition->cName}:</span>
                                    {/col}
                                    {col class="col-auto price-col"}
                                        <span class="tax_label">{$taxPosition->cPreisLocalized}</span>
                                    {/col}
                                {/row}
                            {/foreach}
                        {/block}
                    {/if}
                    {block name='account-order-items-total-total'}
                        <hr>
                        {row}
                            {col}
                                <span class="price_label"><strong>{lang key='totalSum'} {if $NettoPreise}{lang key='gross' section='global'}{/if}:</strong></span>
                            {/col}
                            {col class="col-auto price-col"}
                                <strong class="price total-sum">{$Bestellung->WarensummeLocalized[0]}</strong>
                            {/col}
                        {/row}
                    {/block}
                    {if !empty($Bestellung->OrderAttributes)}
                        {block name='account-order-items-total-order-attributes'}
                            {foreach $Bestellung->OrderAttributes as $attribute}
                                {if $attribute->cName === 'Finanzierungskosten'}
                                    {row class="type-{$smarty.const.C_WARENKORBPOS_TYP_ZINSAUFSCHLAG}"}
                                        {block name='account-order-items-finance-costs'}
                                            {col}
                                                {lang key='financeCosts' section='order'}
                                            {/col}
                                        {/block}
                                        {block name='account-order-items-finance-costs-value'}
                                            {col class="col-auto price-col"}
                                                <strong class="price_overall">
                                                    {$attribute->cValue}
                                                </strong>
                                            {/col}
                                        {/block}
                                    {/row}
                                {/if}
                            {/foreach}
                        {/block}
                    {/if}
                {/block}
            {/col}
        {/row}
        {/block}
    </div>
{/block}
