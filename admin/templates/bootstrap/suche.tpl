{if $standalonePage}
    {include file='tpl_inc/header.tpl'}
    {$cTitel = sprintf(__('searchResultsFor'), $query)}
    {include file='tpl_inc/seite_header.tpl' cTitel=$cTitel}
    <div class="card">
        <div class="card-body search-page">
{/if}

{if count($adminMenuItems)}
    <div class="dropdown-header">{__('pagesMenu')}</div>
    <ul class="backend-search-section">
        {foreach $adminMenuItems as $item}
            <li class="has-icon" tabindex="-1">
                <a class="dropdown-item" href="{$item->link}">
                    <span class="title">
                        <span class="icon-wrapper">{include file="img/icons/{$item->icon}.svg"}</span>
                        {$item->path}
                    </span>
                </a>
            </li>
        {/foreach}
    </ul>
    <div class="dropdown-divider dropdown-divider-light"></div>
{/if}
{if count($settings)}
    <div class="dropdown-header">{__('content')}</div>
    <ul>
        {foreach $settings as $section}
            {foreach $section->getSubsections() as $sub}
                {if $sub->show() === true}
                    <li>
                        <a class="dropdown-item" href="{$sub->getURL()}">
                            <span class="title">{__($sub->getHighlightedName())}</span>
                            <span class="path">{$sub->getPath()}</span>
                        </a>
                        <ul>
                        {foreach $sub->getItems() as $setting}
                            <li tabindex="-1">
                                <a class="dropdown-item value" href="{$setting->getURL()}">
                                    <span class="title">{$setting->getHighlightedName()}</span>
                                    <span class="path"><code>{$setting->getValueName()}</code></span>
                                </a>
                            </li>
                        {/foreach}
                        </ul>
                    </li>
                {/if}
            {/foreach}
        {/foreach}
    </ul>
{/if}
{if isset($shippings)}
    <div class="dropdown-divider dropdown-divider-light"></div>
    <div class="dropdown-header"><a href="{$adminURL}/shippingmethods" class="value">{__('shippingTypesOverview')}</a></div>
    <ul>
        {foreach $shippings as $shipping}
            <li class="dropdown-item is-form-submit" tabindex="-1">
                <form method="post" action="{$adminURL}/shippingmethods">
                    {$jtl_token}
                    <input type="hidden" name="edit" value="{$shipping->kVersandart}">
                    <button type="submit" class="btn btn-link p-0">{$shipping->cName}</button>
                </form>
            </li>
        {/foreach}
    </ul>
{/if}
{if isset($paymentMethods)}
    <div class="dropdown-divider dropdown-divider-light"></div>
    <div class="dropdown-header"><a href="{$adminURL}/paymentmethods" class="value">{__('paymentTypesOverview')}</a></div>
    <ul>
        {foreach $paymentMethods as $paymentMethod}
            <li>
                <a href="{$adminURL}/paymentmethods?kZahlungsart={$paymentMethod->kZahlungsart}&token={$smarty.session.jtl_token}" class="dropdown-item value">
                    {$paymentMethod->cName}
                </a>
            </li>
        {/foreach}
    </ul>
{/if}
{if $plugins->isNotEmpty()}
    <div class="dropdown-divider dropdown-divider-light"></div>
    <div class="dropdown-header">
        <a href="{$adminURL}/pluginmanager" class="value">{__('Plug-in manager')}</a>
    </div>
    <ul>
        {foreach $plugins as $plugin}
            <li>
                <a href="{$adminURL}/{JTL\Router\Route::PLUGIN}/{$plugin->id}?token={$smarty.session.jtl_token}" class="dropdown-item value">
                    <span class="title">
                        {$plugin->highlightedName}
                    </span>
                </a>
                {if $plugin->matchingOptions->isNotEmpty()}
                    <ul>
                        {foreach $plugin->matchingOptions as $option}
                            <li>
                                <a href="{$option->url}" class="dropdown-item">{$option->name}</a>
                            </li>
                        {/foreach}
                    </ul>
                {/if}
            </li>
        {/foreach}
    </ul>
{/if}

{if count($tplSettings)}
    <div class="dropdown-divider dropdown-divider-light"></div>
    <div class="dropdown-header">
        {__('Template-Einstellungen')}
    </div>
    <ul>
        {foreach $tplSettings as $tplSetting}
            <li>
                <a href="{$adminURL}/template?action=config&dir={$tplSetting->dir}&token={$smarty.session.jtl_token}#{$tplSetting->key}"
                   class="dropdown-item">
                    <span class="title">
                        {$tplSetting->name}
                    </span>
                </a>
            </li>
        {/foreach}
    </ul>
{/if}

{if empty($adminMenuItems) && empty($settings) && empty($shippings) && empty($paymentMethods) && $plugins->isEmpty()
        && empty($tplSettings)}
    <span class="{if !$standalonePage}ml-3{/if}">{__('noSearchResult')}</span>
{/if}
{if $standalonePage}
        </div>
    </div>
    {include file='tpl_inc/footer.tpl'}
{/if}
