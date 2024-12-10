{include file='tpl_inc/header.tpl'}
{include file='tpl_inc/seite_header.tpl' cTitel=__('redirect') cBeschreibung=__('redirectDesc') cDokuURL=__('redirectURL')}
{include file='tpl_inc/sortcontrols.tpl'}

{assign var=cTab value=$cTab|default:'redirects'}

<script>
    $(function () {
        {foreach $redirects as $redirect}
        var $stateChecking    = $('#input-group-{$redirect->kRedirect} .state-checking'),
            $stateAvailable   = $('#input-group-{$redirect->kRedirect} .state-available'),
            $stateUnavailable = $('#input-group-{$redirect->kRedirect} .state-unavailable');

        {if $redirect->cAvailable === 'y'}
        $stateChecking.hide();
        $stateAvailable.show();
        {elseif $redirect->cAvailable === 'n'}
        $stateChecking.hide();
        $stateUnavailable.show();
        {else}
        checkUrl({$redirect->kRedirect}, true);
        {/if}
        {/foreach}
    });

    function checkUrl(kRedirect, doUpdate) {
        doUpdate = doUpdate || false;

        var $stateChecking    = $('#input-group-' + kRedirect + ' .state-checking'),
            $stateAvailable   = $('#input-group-' + kRedirect + ' .state-available'),
            $stateUnavailable = $('#input-group-' + kRedirect + ' .state-unavailable');

        $stateChecking.show();
        $stateAvailable.hide();
        $stateUnavailable.hide();

        function checkUrlCallback(result) {
            $stateChecking.hide();
            $stateAvailable.hide();
            $stateUnavailable.hide();

            if (result === true) {
                $stateAvailable.show();
            } else {
                $stateUnavailable.show();
            }
        }

        if (doUpdate) {
            ioCall('updateRedirectState', [kRedirect], checkUrlCallback);
        } else {
            ioCall('redirectCheckAvailability', [$('#cToUrl-' + kRedirect).val()], checkUrlCallback);
        }
    }

    function redirectTypeahedDisplay(item) {
        return '/' + item.cSeo;
    }

    function redirectTypeahedSuggestion(item) {
        var type = '';
        switch(item.cKey) {
            case 'kLink': type = 'Seite'; break;
            case 'kNews': type = 'News'; break;
            case 'kNewsKategorie': type = 'News-Kategorie'; break;
            case 'kNewsMonatsUebersicht': type = 'News-Montasübersicht'; break;
            case 'kArtikel': type = 'Artikel'; break;
            case 'kKategorie': type = 'Kategorie'; break;
            case 'kHersteller': type = 'Hersteller'; break;
            case 'kMerkmalWert': type = 'Merkmal-Wert'; break;
            case 'suchspecial': type = 'Suchspecial'; break;
            default: type = 'Anderes'; break;
        }
        return '<span>/' + item.cSeo +
            ' <small class="text-muted">- ' + type + '</small></span>';
    }

    function toggleReferer(kRedirect) {
        var $refTr  = $('#referer-tr-' + kRedirect),
            $refDiv = $('#referer-div-' + kRedirect);

        if (!$refTr.is(':visible')) {
            $refTr.show();
            $refDiv.slideDown();
        } else {
            $refDiv.slideUp(500, $refTr.hide.bind($refTr));
        }
    }
</script>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-6 col-xl-auto">
                {include file='tpl_inc/csv_export_btn.tpl' exporterId='redirects'}
            </div>
            <div class="col-sm-6 col-xl-auto">
                {include file='tpl_inc/csv_import_btn.tpl' importerId='redirects'}
            </div>
        </div>
    </div>
</div>

<div class="tabs">
    <nav class="tabs-nav">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link {if $cTab === 'redirects'} active{/if}" data-toggle="tab" role="tab" href="#redirects">
                    {__('overview')}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {if $cTab === 'new_redirect'} active{/if}" data-toggle="tab" role="tab" href="#new_redirect">
                    {__('create/edit')}
                </a>
            </li>
        </ul>
    </nav>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade{if $cTab === 'redirects'} active show{/if}" id="redirects">
            {include file='tpl_inc/filtertools.tpl' oFilter=$oFilter}
            {include file='tpl_inc/pagination.tpl' pagination=$pagination cAnchor='redirects'}
            {$types = [
            JTL\Redirect::TYPE_IMPORT => __('Import'),
            JTL\Redirect::TYPE_MANUAL => __('Manual'),
            JTL\Redirect::TYPE_WAWI => __('Wawi sync'),
            JTL\Redirect::TYPE_404 => __('Not found'),
            JTL\Redirect::TYPE_UNKNOWN => __('Unknown')
            ]}
            <div>
                <form method="post">
                    {$jtl_token}
                    {if count($redirects) > 0}
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>{__('redirectFrom')} {call sortControls pagination=$pagination nSortBy=0}</th>
                                    <th class="min-w">{__('redirectTo')} {call sortControls pagination=$pagination nSortBy=1}</th>
                                    <th class="text-center">{__('calls')} {call sortControls pagination=$pagination nSortBy=2}</th>
                                    <th class="text-center">{__('Type')} {call sortControls pagination=$pagination nSortBy=3}</th>
                                    <th class="text-center"></th>
                                </tr>
                                </thead>
                                <tbody>
                                {foreach $redirects as $redirect}
                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" type="checkbox" name="redirects[{$redirect->kRedirect}][enabled]" value="1"
                                                       id="check-{$redirect->kRedirect}">
                                                <label class="custom-control-label" for="check-{$redirect->kRedirect}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <label for="check-{$redirect->kRedirect}">
                                                <a href="{$redirect->cFromUrl}" target="_blank"
                                                   {if $redirect->cFromUrl|strlen > 50}data-toggle="tooltip"
                                                   data-placement="bottom" title="{$redirect->cFromUrl}"{/if}>
                                                    {$redirect->cFromUrl|truncate:50}
                                                </a>
                                                <button type="button" class="btn btn-link px-2"
                                                        title="
                                                            {if $redirect->paramHandling === 0}
                                                                {__('exact match')}
                                                            {elseif $redirect->paramHandling === 1}
                                                                {__('ignore GET parameters')}
                                                            {else}{__('append GET parameters')}{/if}"
                                                        data-toggle="tooltip">
                                                    <span class="fas fa-info-circle font-size-lg"></span>
                                                </button>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="form-group form-row align-items-center" id="input-group-{$redirect->kRedirect}">
                                                    <span class="col col-lg-auto col-form-label text-info state-checking">
                                                        <i class="fa fa-spinner fa-pulse"></i>
                                                    </span>
                                                <span class="col col-lg-auto col-form-label text-success state-available" style="display:none;">
                                                        <i class="fal fa-check"></i>
                                                    </span>
                                                <span class="col col-lg-auto col-form-label text-danger state-unavailable" style="display:none;">
                                                        <i class="fal fa-exclamation-triangle"></i>
                                                    </span>
                                                <div class="col col-md-10">
                                                    <input class="form-control min-w-sm" name="redirects[{$redirect->kRedirect}][cToUrl]"
                                                           value="{$redirect->cToUrl}" id="cToUrl-{$redirect->kRedirect}"
                                                           onblur="checkUrl({$redirect->kRedirect})">
                                                </div>
                                                <script>
                                                    enableTypeahead(
                                                        '#cToUrl-{$redirect->kRedirect}', 'getSeos',
                                                        redirectTypeahedDisplay, redirectTypeahedSuggestion,
                                                        checkUrl.bind(null, {$redirect->kRedirect}, false)
                                                    );
                                                </script>

                                            </div>
                                        </td>
                                        <td class="text-center">
                                                <span class="badge badge-primary font-weight-bold font-size-sm">
                                                    {$redirect->nCount}
                                                </span>
                                        </td>
                                        <td class="text-center">
                                                <span class="badge badge-secondary font-weight-bold font-size-sm">
                                                    {$types[$redirect->type]}
                                                </span>
                                            <p class="small">
                                                <span>{$redirect->dateCreated}</span>
                                            </p>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                {if $redirect->nCount > 0}
                                                    <button type="button" class="btn btn-link px-2"
                                                            title="{__('details')}"
                                                            onclick="toggleReferer({$redirect->kRedirect});"
                                                            data-toggle="tooltip">
                                                        <span class="fal fa-chevron-circle-down rotate-180 font-size-lg"></span>
                                                    </button>
                                                {/if}
                                                <a href="{$adminURL}{$route}?action=edit&id={$redirect->kRedirect}&token={$smarty.session.jtl_token}"
                                                   class="btn-prg btn btn-link px-2"
                                                   title="{__('modify')}"
                                                   data-toggle="tooltip">
                                                        <span class="icon-hover">
                                                            <span class="fal fa-edit"></span>
                                                            <span class="fas fa-edit"></span>
                                                        </span>
                                                </a>

                                            </div>
                                        </td>
                                    </tr>
                                    {if $redirect->nCount > 0}
                                        <tr id="referer-tr-{$redirect->kRedirect}" style="display:none;">
                                            <td></td>
                                            <td colspan="5">
                                                <div id="referer-div-{$redirect->kRedirect}" style="display:none;">
                                                    <table class="table">
                                                        <thead>
                                                        <tr>
                                                            <th>{__('redirectReferer')}</th>
                                                            <th>{__('date')}</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        {foreach $redirect->oRedirectReferer_arr as $redirectReferer}
                                                            <tr>
                                                                <td>
                                                                    {if $redirectReferer->kBesucherBot > 0}
                                                                        {if $redirectReferer->cBesucherBotName|strlen > 0}
                                                                            {$redirectReferer->cBesucherBotName}
                                                                        {else}
                                                                            {$redirectReferer->cBesucherBotAgent}
                                                                        {/if}
                                                                        (Bot)
                                                                    {elseif $redirectReferer->cRefererUrl|strlen > 0}
                                                                        <a href="{$redirectReferer->cRefererUrl}" target="_blank">
                                                                            {$redirectReferer->cRefererUrl}
                                                                        </a>
                                                                    {else}
                                                                        <i>{__('redirectRefererDirect')}</i>
                                                                    {/if}
                                                                </td>
                                                                <td>
                                                                    {$redirectReferer->dDate|date_format:'d.m.Y - H:m:i'}
                                                                </td>
                                                            </tr>
                                                        {/foreach}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    {/if}
                                {/foreach}
                                </tbody>
                            </table>
                        </div>
                    {elseif $totalRedirectCount > 0}
                        <div class="alert alert-info" role="alert">{__('noFilterResults')}</div>
                    {else}
                        <div class="alert alert-info" role="alert">{__('noDataAvailable')}</div>
                    {/if}
                    <div class="save-wrapper">
                        <div class="row">
                            <div class="col-sm-6 col-xl-auto text-left">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" name="ALLMSGS" id="ALLMSGS" onclick="AllMessages(this.form);">
                                    <label class="custom-control-label" for="ALLMSGS">{__('globalSelectAll')}</label>
                                </div>
                            </div>
                            {if count($redirects) > 0}
                                <div class="ml-auto col-sm-6 col-xl-auto">
                                    <button name="action" value="delete" class="btn btn-danger btn-block">
                                        <i class="fas fa-trash-alt"></i> {__('deleteSelected')}
                                    </button>
                                </div>
                                <div class="col-sm-6 col-xl-auto">
                                    <button name="action" value="delete_all" class="btn btn-warning btn-block">
                                        {__('redirectDelUnassigned')}
                                    </button>
                                </div>
                            {/if}
                            <div class="ol-sm-6 col-xl-auto">
                                <button name="action" value="save" class="btn btn-primary btn-block">
                                    {__('saveWithIcon')}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                {include file='tpl_inc/pagination.tpl' pagination=$pagination cAnchor='redirects' isBottom=true}
            </div>
        </div>
        <div role="tabpanel" class="tab-pane fade{if $cTab === 'new_redirect'} active show{/if}" id="new_redirect">
            <form method="post">
                {$jtl_token}
                <div class="settings">
                    <div class="subheading1">{__('redirectNew')}</div>
                    <hr class="mb-3">
                    <div>
                        <div class="form-group form-row align-items-center">
                            <label class="col col-sm-4 col-form-label text-sm-right" for="cFromUrl">{__('redirectFrom')}:</label>
                            <div class="col-sm pl-sm-3 pr-sm-5 order-last order-sm-2">
                                <input class="form-control" id="cFromUrl" name="cFromUrl" required
                                       {if !empty($cFromUrl)}value="{$cFromUrl}"{/if}>
                            </div>
                        </div>
                        <div class="form-group form-row align-items-center" id="input-group-0">
                            <label class="col col-sm-4 col-form-label text-sm-right" for="cToUrl-0">{__('redirectTo')}:</label>
                            <div class="col-sm pl-sm-3 pr-sm-5 order-last order-sm-2">
                                <input class="form-control" id="cToUrl-0" name="cToUrl" required
                                       onblur="checkUrl(0)" {if !empty($cToUrl)}value="{$cToUrl}"{/if}>
                            </div>
                            <div class="col-auto ml-sm-n4 order-2 order-sm-3" style="display:none;">
                                <i class="fa fa-spinner fa-pulse text-info state-checking"></i>
                            </div>
                            <div class="col-auto ml-sm-n4 order-2 order-sm-3" style="display:none;">
                                <i class="fal fa-check text-success text-success state-available"></i>
                            </div>
                            <div class="col-auto ml-sm-n4 order-2 order-sm-3">
                                <i class="fal fa-exclamation-triangle text-danger state-unavailable"></i>
                            </div>
                            <script>
                                enableTypeahead(
                                    '#cToUrl-0', 'getSeos', redirectTypeahedDisplay, redirectTypeahedSuggestion,
                                    checkUrl.bind(null, 0, false)
                                )
                            </script>
                        </div>
                        <div class="form-group form-row align-items-center">
                            {assign var=paramHandling value=$paramHandling|default:0}
                            <label class="col col-sm-4 col-form-label text-sm-right" for="paramHandline">
                                {__('Parameter handling')}:
                            </label>
                            <div class="col-sm pl-sm-3 pr-sm-5 order-last order-sm-2">
                                <select class="custom-select" id="paramHandling" name="paramHandling">
                                    <option value="0"{if $paramHandling === 0} selected{/if}>{__('exact match')}</option>
                                    <option value="1"{if $paramHandling === 1} selected{/if}>{__('ignore GET parameters')}</option>
                                    <option value="2"{if $paramHandling === 2} selected{/if}>{__('append GET parameters')}</option>
                                </select>
                            </div>
                            <div class="col-auto ml-sm-n4 order-2 order-sm-3">{getHelpDesc cDesc=__('parameterHandlingDesc')}</div>
                        </div>
                    </div>
                    <div class="save-wrapper">
                        <div class="row first-ml-auto">
                            {if !empty($redirectID)}
                                <div class="col-sm-6 col-xl-auto">
                                    <a class="btn btn-outline-primary btn-block" id="go-back" href="{$adminURL}{$route}">
                                        {__('cancelWithIcon')}
                                    </a>
                                </div>
                                <div class="col-sm-6 col-xl-auto">
                                    <button name="action" value="new" class="btn btn-primary btn-block">
                                        <i class="fa fa-save"></i> {__('save')}
                                    </button>
                                </div>
                                <input type="hidden" name="redirect-id" value="{$redirectID}">
                            {else}
                                <div class="col-sm-6 col-xl-auto">
                                    <button name="action" value="new" class="btn btn-primary btn-block">
                                        <i class="fa fa-save"></i> {__('create')}
                                    </button>
                                </div>
                            {/if}
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{include file='tpl_inc/footer.tpl'}
