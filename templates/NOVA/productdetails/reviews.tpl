{block name='productdetails-reviews'}
    <div class="reviews">
        {block name='productdetails-reviews-content'}
        {row id='reviews-overview'}
            {block name='productdetails-reviews-overview'}
                {if $Artikel->Bewertungen->oBewertungGesamt->nAnzahl > 0}
                    {if empty($smarty.get.quickView)}
                        {$colsMD = 6}
                        {$colsLG = 4}
                    {else}
                        {$colsMD = 12}
                        {$colsLG = 12}
                    {/if}
                    {col cols=12 md=$colsMD lg=$colsLG order=1 order-md=0}
                        {card}
                            {block name='productdetails-reviews-heading'}
                                <div class="card-title">
                                    <div class="subheadline">
                                        {lang key='averageProductRating' section='product rating'}
                                    </div>
                                </div>
                            {/block}
                            {block name='productdetails-reviews-rating-dropdown'}
                            <div class="dropdown">
                                {if !empty($smarty.get.quickView)}
                                    <span id="ratingDropdown">
                                {else}
                                    <button class="btn btn-link dropdown-toggle" type="button" id="ratingDropdown"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {/if}
                                    {block name='productdetails-reviews-include-rating'}
                                        {include file='productdetails/rating.tpl' total=$Artikel->Bewertungen->oBewertungGesamt->nAnzahl}
                                        <span>({$Artikel->Bewertungen->oBewertungGesamt->nAnzahl} {lang key='Votes'})</span>
                                    {/block}
                                {if !empty($smarty.get.quickView)}
                                    </span>
                                {else}
                                    </button>
                                {/if}
                                <div class="dropdown-menu min-w-lg" aria-labelledby="ratingDropdown" data-dropdown-stay>
                                    <div class="dropdown-body">
                                        {block name='productdetails-reviews-votes'}
                                            {foreach name=sterne from=$Artikel->Bewertungen->nSterne_arr item=nSterne key=i}
                                                {assign var=int1 value=5}
                                                {math equation='x - y' x=$int1 y=$i assign='schluessel'}
                                                {assign var=int2 value=100}
                                                {math equation='(a/b)*c' a=$nSterne b=$Artikel->Bewertungen->oBewertungGesamt->nAnzahl c=$int2 assign='percent'}
                                                {row}
                                                    {col cols=4}
                                                    {if isset($bewertungSterneSelected) && $bewertungSterneSelected === $schluessel}
                                                        <strong>
                                                    {/if}
                                                    {if $nSterne > 0 && (!isset($bewertungSterneSelected) || $bewertungSterneSelected !== $schluessel)}
                                                        {link href="{$Artikel->cURLFull}?btgsterne={$schluessel}#tab-votes"}{$schluessel} {if $i == 4}{lang key='starSingular' section='product rating'}{else}{lang key='starPlural' section='product rating'}{/if}{/link}
                                                    {else}
                                                        {$schluessel} {if $i == 4}{lang key='starSingular' section='product rating'}{else}{lang key='starPlural' section='product rating'}{/if}
                                                    {/if}
                                                    {if isset($bewertungSterneSelected) && $bewertungSterneSelected === $schluessel}
                                                        </strong>
                                                    {/if}
                                                    {/col}
                                                    {col cols=6}
                                                        {progress now=$percent|round min=0 max=100}
                                                    {/col}
                                                    {col cols=2}
                                                        {if !empty($nSterne)}{$nSterne}{else}0{/if}
                                                    {/col}
                                                {/row}
                                            {/foreach}
                                            {if isset($bewertungSterneSelected) && $bewertungSterneSelected > 0}
                                                {block name='productdetails-reviews-note-all-ratings'}
                                                    <hr>
                                                    {link href="{$Artikel->cURLFull}#tab-votes" class="btn btn-outline-primary btn-sm btn-block"}
                                                        {lang key='allRatings'}
                                                    {/link}
                                                {/block}
                                            {/if}
                                        {/block}
                                    </div>
                                </div>
                            </div>
                            {/block}
                        {/card}
                    {/col}
                {/if}
            {/block}
            {block name='productdetails-reviews-quickview'}
                {if empty($smarty.get.quickView)}
                {col cols=12 md=6 lg=8  order=0 order-md=1}
                    {form method="post" action="{get_static_route id='bewertung.php'}#tab-votes" id="article_rating" slide=true}
                        <div class="subheadline">
                            {if $Artikel->Bewertungen->oBewertungGesamt->nAnzahl == 0}
                                {lang key='firstReview'}
                            {else}
                                {lang key='shareYourExperience' section='product rating'}
                            {/if}
                        </div>
                        {input type="hidden" name="bfa" value="1"}
                        {input type="hidden" name="a" value=$Artikel->kArtikel}
                        {button type="submit" name="bewerten" value="1" variant="outline-primary"}
                            {if $bereitsBewertet === false}
                                {lang key='productAssess' section='product rating'}
                            {else}
                                {lang key='edit' section='product rating'}
                            {/if}
                        {/button}
                    {/form}
                {/col}
                {/if}
            {/block}
        {/row}
        {/block}

        {block name='productdetails-reviews-reviews-in-lang'}
            {if $ratingPagination->getPageItemCount() > 0 || isset($Artikel->HilfreichsteBewertung->oBewertung_arr[0]->nHilfreich) &&
            $Artikel->HilfreichsteBewertung->oBewertung_arr[0]->nHilfreich > 0}
                <p>
                    {if $Einstellungen.bewertung.bewertung_alle_sprachen === 'Y'}
                        {lang key='reviewsInAllLang' section='product rating'}
                    {else}
                        {lang key='reviewsInCurrLang' section='product rating'}
                    {/if}
                </p>
            {else}
                <p>
                    {if $Einstellungen.bewertung.bewertung_alle_sprachen === 'Y'}
                        {lang key='noReviewsInAllLang' section='product rating'}
                    {else}
                        {lang key='noReviewsInCurrLang' section='product rating'}
                    {/if}
                </p>
            {/if}
        {/block}
        {if (isset($Artikel->HilfreichsteBewertung->oBewertung_arr[0]->nHilfreich)
            && $Artikel->HilfreichsteBewertung->oBewertung_arr[0]->nHilfreich > 0 )
            || $ratingPagination->getPageItemCount() > 0
        }
            {form id="reviews-list" method="post" action="{get_static_route id='bewertung.php'}#tab-votes" class="reviews-list" slide=true}
                {input type="hidden" name="bhjn" value="1"}
                {input type="hidden" name="a" value=$Artikel->kArtikel}
                {input type="hidden" name="btgsterne" value=$BlaetterNavi->nSterne}
                {input type="hidden" name="btgseite" value=$BlaetterNavi->nAktuelleSeite}

                {if isset($Artikel->HilfreichsteBewertung->oBewertung_arr[0]->nHilfreich) &&
                    $Artikel->HilfreichsteBewertung->oBewertung_arr[0]->nHilfreich > 0
                }
                    {block name='productdetails-reviews-form-most-useful'}
                        {card class="reviews-mosthelpful" no-body=true}
                            {block name='productdetails-reviews-most-helpful-header'}
                                {cardheader}
                                    <span class="h3">
                                        {lang key='theMostUsefulRating' section='product rating'}
                                    </span>
                                {/cardheader}
                            {/block}
                            {block name='productdetails-reviews-most-helpful'}
                                {block name='productdetails-reviews-most-helpful-body'}
                                    {cardbody class="review"}
                                        {foreach $Artikel->HilfreichsteBewertung->oBewertung_arr as $oBewertung}
                                            {block name='productdetails-reviews-form-most-useful-include-review-item'}
                                                {include file='productdetails/review_item.tpl' oBewertung=$oBewertung bMostUseful=true}
                                            {/block}
                                        {/foreach}
                                    {/cardbody}
                                {/block}
                            {/block}
                        {/card}
                    {/block}
                {/if}

                {if $ratingPagination->getPageItemCount() > 0}
                    {block name='productdetails-reviews-verified-purchase-notice'}
                        <a class="btn btn-outline-secondary verified-purchase-notice"
                           tabindex="0" role="button"
                           data-toggle="popover"
                           data-trigger="focus"
                           data-placement="top"
                           data-content="{{lang key='verifiedPurchaseNotice' section='product rating'}|escape:'html'}">
                            {lang key='reviewsHowTo' section='product rating'}
                        </a>
                    {/block}
                    {block name='productdetails-reviews-include-pagination-top'}
                        {if empty($smarty.get.quickView)}
                        {include file='snippets/pagination.tpl' oPagination=$ratingPagination cThisUrl=$Artikel->cURLFull cParam_arr=['btgsterne'=>$bewertungSterneSelected] cAnchor='tab-votes'}
                        {/if}
                    {/block}
                    {block name='productdetails-reviews-form'}
                        {foreach $ratingPagination->getPageItems() as $oBewertung}
                            {block name='productdetails-reviews-form-include-review-item'}
                                {card class="review {if $oBewertung@last}last{/if}"}
                                    {include file='productdetails/review_item.tpl' oBewertung=$oBewertung}
                                {/card}
                            {/block}
                        {/foreach}
                    {/block}
                    {block name='productdetails-reviews-include-pagination-bottom'}
                        {if empty($smarty.get.quickView)}
                        {include file='snippets/pagination.tpl' oPagination=$ratingPagination cThisUrl=$Artikel->cURLFull cParam_arr=['btgsterne'=>$bewertungSterneSelected] cAnchor='tab-votes' showFilter=false}
                        {/if}
                    {/block}
                {/if}
            {/form}
        {/if}
    </div>
{/block}
