<script type="text/javascript">
    function changeZeitSelect(currentSelect) {ldelim}
        if (currentSelect.options[currentSelect.selectedIndex].value > 0)
            window.location.href = "{$adminURL}{$route}?tab=globalestats&nAnsicht=" + currentSelect.options[currentSelect.selectedIndex].value;
    {rdelim}
</script>

{include file='tpl_inc/seite_header.tpl' cTitel=__('kampagne') cBeschreibung=__('kampagneDesc') cDokuURL=__('kampagneURL')}
<div id="content">
    <div class="tabs">
        <nav class="tabs-nav">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {if $cTab === '' || $cTab === 'uebersicht'} active{/if}" data-toggle="tab" role="tab" href="#uebersicht">
                        {__('kampagneOverview')}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {if $cTab === 'globalestats'} active{/if}" data-toggle="tab" role="tab" href="#globalestats">
                        {__('kampagneGlobalStats')}
                    </a>
                </li>
            </ul>
        </nav>
        <div class="tab-content">
            <div id="uebersicht" class="tab-pane fade {if $cTab === '' || $cTab === 'uebersicht'} active show{/if}">
                <div>
                    <div class="subheading1">{__('kampagneIntern')}</div>
                    <hr class="mb-3">
                    {if count($oKampagne_arr) > 0}
                        <div class="table-responsive">
                            <table class="table table-striped table-align-top">
                                <thead>
                                    <tr>
                                        <th class="text-left">{__('kampagneName')}</th>
                                        <th class="text-left">{__('kampagneParam')}</th>
                                        <th class="text-left">{__('kampagneValue')}</th>
                                        <th class="th-4 text-center">{__('activated')}</th>
                                        <th class="th-5 text-center">{__('kampagnenDate')}</th>
                                        <th class="th-6"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                {foreach $oKampagne_arr as $oKampagne}
                                    {if !empty($oKampagne->nInternal)}
                                        <tr>
                                            <td>
                                                <strong><a href="{$adminURL}{$route}?kKampagne={$oKampagne->kKampagne}&detail=1&token={$smarty.session.jtl_token}">{$oKampagne->getName()}</a></strong>
                                            </td>
                                            <td>{$oKampagne->cParameter}</td>
                                            <td>
                                                {if isset($oKampagne->nDynamisch) && $oKampagne->nDynamisch == 1}
                                                    {__('dynamic')}
                                                {else}
                                                    {__('kampagneStatic')}
                                                    <br />
                                                    <strong>{__('kampagneValueStatic')}:</strong>
                                                    {$oKampagne->cWert}
                                                {/if}
                                            </td>
                                            <td class="text-center">
                                                {if isset($oKampagne->nAktiv) && $oKampagne->nAktiv == 1}
                                                    <i class="fal fa-check text-success"></i>
                                                {else}
                                                    <i class="fal fa-times text-danger"></i>
                                                {/if}
                                            </td>
                                            <td class="text-center">{$oKampagne->dErstellt_DE}</td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <a href="{$adminURL}{$route}?kKampagne={$oKampagne->kKampagne}&editieren=1&token={$smarty.session.jtl_token}"
                                                       title="{__('modify')}"
                                                       class="btn btn-link px-2"
                                                       data-toggle="tooltip">
                                                        <span class="icon-hover">
                                                            <span class="fal fa-edit"></span>
                                                            <span class="fas fa-edit"></span>
                                                        </span>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    {/if}
                                {/foreach}
                                </tbody>
                            </table>
                        </div>
                    {else}
                        <div class="alert alert-info" role="alert">{__('noDataAvailable')}</div>
                    {/if}
                </div>
                <div>
                    <div class="subheading1">{__('kampagneExtern')}</div>
                    <hr class="mb-3">
                    <form name="kampagnen" method="post" action="{$adminURL}{$route}">
                        {if empty($oKampagne->nInternal)}
                            {$jtl_token}
                            <input type="hidden" name="tab" value="uebersicht" />
                            <input type="hidden" name="delete" value="1" />
                            <div class="table-responsive">
                                <table class="table table-striped table-align-top">
                                    <thead>
                                        <tr>
                                            <th class="check"></th>
                                            <th class="text-left">{__('kampagneName')}</th>
                                            <th class="text-left">{__('kampagneParam')}</th>
                                            <th class="text-left">{__('kampagneValue')}</th>
                                            <th class="th-4 text-center">{__('activated')}</th>
                                            <th class="th-5 text-center">{__('kampagnenDate')}</th>
                                            <th class="th-6"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    {foreach $oKampagne_arr as $oKampagne}
                                        {if empty($oKampagne->nInternal)}
                                            <tr>
                                                <td class="check">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" name="kKampagne[]" type="checkbox" id="campaign-id-{$oKampagne->kKampagne}" value="{$oKampagne->kKampagne}">
                                                        <label class="custom-control-label" for="campaign-id-{$oKampagne->kKampagne}"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong><a href="{$adminURL}{$route}?kKampagne={$oKampagne->kKampagne}&detail=1&token={$smarty.session.jtl_token}">{$oKampagne->cName}</a></strong>
                                                </td>
                                                <td>{$oKampagne->cParameter}</td>
                                                <td>
                                                    {if isset($oKampagne->nDynamisch) && $oKampagne->nDynamisch == 1}
                                                        {__('dynamic')}
                                                    {else}
                                                        {__('kampagneStatic')}
                                                        <br />
                                                        <strong>{__('kampagneValueStatic')}:</strong>
                                                        {$oKampagne->cWert}
                                                    {/if}
                                                </td>
                                                <td class="text-center">
                                                    {if isset($oKampagne->nAktiv) && $oKampagne->nAktiv == 1}
                                                        <i class="fal fa-check text-success"></i>
                                                    {else}
                                                        <i class="fal fa-times text-danger"></i>
                                                    {/if}
                                                </td>
                                                <td class="text-center">{$oKampagne->dErstellt_DE}</td>
                                                <td class="text-center">
                                                    <a href="{$adminURL}{$route}?kKampagne={$oKampagne->kKampagne}&editieren=1&token={$smarty.session.jtl_token}"
                                                       class="btn btn-link px-2" title="{__('modify')}">
                                                        <i class="fal fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        {/if}
                                    {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        {else}
                            <div class="alert alert-info" role="alert">{__('noDataAvailable')}</div>
                        {/if}
                        <div class="card-footer save-wrapper">
                            <div class="row">
                                <div class="col-sm-6 col-xl-auto text-left">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" name="ALLMSGS" id="ALLMSGS" type="checkbox" onclick="AllMessages(this.form);" />
                                        <label class="custom-control-label" for="ALLMSGS">{__('globalSelectAll')}</label>
                                    </div>
                                </div>
                                <div class="ml-auto col-sm-6 col-xl-auto">
                                    <button name="submitDelete" type="submit" value="{__('delete')}" class="btn btn-danger btn-block"><i class="fas fa-trash-alt"></i> {__('deleteSelected')}</button>
                                </div>
                                <div class="col-sm-6 col-xl-auto">
                                    <a href="{$adminURL}{$route}?neu=1&token={$smarty.session.jtl_token}" class="btn btn-primary btn-block">{__('kampagneNewBTN')}</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div id="globalestats" class="tab-pane fade {if $cTab === 'globalestats'} active show{/if}">
                <div>
                    <div class="form-group form-row align-items-center">
                        <label class="col col-sm-3 col-lg-1 col-form-label text-sm-right" for="nAnsicht">{__('kampagneView')}:</label>
                        <div class="col-sm-4 col-lg-2">
                            <select id="nAnsicht" name="nAnsicht" class="custom-select combo" onchange="changeZeitSelect(this);">
                                <option value="-1"></option>
                                <option value="1"{if $smarty.session.Kampagne->nAnsicht == 1} selected{/if}>{__('monthly')}</option>
                                <option value="2"{if $smarty.session.Kampagne->nAnsicht == 2} selected{/if}>{__('weekly')}</option>
                                <option value="3"{if $smarty.session.Kampagne->nAnsicht == 3} selected{/if}>{__('daily')}</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <strong>{__('kampagnePeriod')}:</strong> {$cZeitraum}
                        </div>
                    </div>
                </div>
                {if isset($oKampagne_arr) && count($oKampagne_arr) > 0 && isset($oKampagneDef_arr) && count($oKampagneDef_arr) > 0}
                    <div class="table-responsive">
                        <table class="table table-striped table-align-top">
                            <thead>
                                <tr>
                                    <th class="th-1"></th>
                                    {foreach $oKampagneDef_arr as $oKampagneDef}
                                        <th class="th-2">
                                            <a href="{$adminURL}{$route}?tab=globalestats&nSort={$oKampagneDef->kKampagneDef}&token={$smarty.session.jtl_token}">{__($oKampagneDef->cName)}</a>
                                            {if $oKampagneDef->cName === 'Angeschaute Newsletter'}
                                                {getHelpDesc cDesc=__('kampagnenNLInfo')}
                                            {/if}
                                        </th>
                                    {/foreach}
                                </tr>
                            </thead>
                            <tbody>
                            {foreach $oKampagneStat_arr as $kKampagne => $oKampagneStatDef_arr}
                                {if $kKampagne !== 'Gesamt'}
                                    <tr>
                                        <td>
                                            <a href="{$adminURL}{$route}?detail=1&kKampagne={$oKampagne_arr[$kKampagne]->kKampagne}&cZeitParam={$cZeitraumParam}&token={$smarty.session.jtl_token}">{$oKampagne_arr[$kKampagne]->getName()}</a>
                                        </td>
                                        {foreach $oKampagneStatDef_arr as $kKampagneDef => $oKampagneStatDef}
                                            <td>
                                                <a href="{$adminURL}{$route}?kKampagne={$kKampagne}&defdetail=1&kKampagneDef={$kKampagneDef}&cZeitParam={$cZeitraumParam}&token={$smarty.session.jtl_token}">{$oKampagneStat_arr[$kKampagne][$kKampagneDef]}</a>
                                            </td>
                                        {/foreach}
                                    </tr>
                                {/if}
                            {/foreach}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>{__('kampagneOverall')}</td>
                                    {foreach $oKampagneDef_arr as $kKampagneDef => $oKampagneDef}
                                        <td>
                                            {$oKampagneStat_arr.Gesamt[$kKampagneDef]}
                                        </td>
                                    {/foreach}
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="card-footer save-wrapper">
                        <div class="row">
                            <div class="ml-auto col-sm-6 col-xl-auto">
                                <a href="{$adminURL}{$route}?tab=globalestats&nStamp=-1&token={$smarty.session.jtl_token}" class="btn btn-outline-primary btn-block">
                                    <i class="fa fa-angle-double-left"></i> {__('earlier')}
                                </a>
                            </div>
                            {if isset($bGreaterNow) && !$bGreaterNow}
                            <div class="col-sm-6 col-xl-auto">
                                <a href="{$adminURL}{$route}?tab=globalestats&nStamp=1&token={$smarty.session.jtl_token}" class="btn btn-outline-primary btn-block">
                                    <i class="fa fa-angle-double-right"></i> {__('later')}
                                </a>
                            </div>
                            {/if}
                        </div>
                    </div>
                {else}
                    <div class="alert alert-info" role="alert">{__('noDataAvailable')}</div>
                {/if}
            </div>
        </div>
    </div>
</div>
