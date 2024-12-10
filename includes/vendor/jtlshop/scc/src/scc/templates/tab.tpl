{$id = $params.id->getValue()|default:uniqid()}

<{$params.tag} class="nav-item {$params.class->getValue()}"
    {if $params.id->hasValue()}id="label-{$id}"{/if}
    {if $params.style->hasValue()}style="{$params.style->getValue()}"{/if}
    {if $params.itemprop->hasValue()}itemprop="{$params.itemprop->getValue()}"{/if}
    {if $params.itemscope->getValue() === true}itemscope {/if}
    {if $params.itemtype->hasValue()}itemtype="{$params.itemtype->getValue()}"{/if}
    {if $params.itemid->hasValue()}itemid="{$params.itemid->getValue()}"{/if}
    {if $params.title->hasValue()} title="{$params.title->getValue()}"{/if}
    role="{if $params.role->hasValue()}{$params.role->getValue()}{else}presentation{/if}"
    {if $params.aria->hasValue()}{foreach $params.aria->getValue() as $ariaKey => $ariaVal}aria-{$ariaKey}="{$ariaVal}" {/foreach}{/if}
    {if $params.data->hasValue()}{foreach $params.data->getValue() as $dataKey => $dataVal}data-{$dataKey}="{$dataVal}" {/foreach}{/if}
    {if $params.attribs->hasValue()}
        {foreach $params.attribs->getValue() as $key => $val} {$key}="{$val}" {/foreach}
    {/if}
>
   {* {if $params.swipeable->getValue() === true}
        <div class="nav-link{if $params.active->getValue() === true} active{/if}{if $params.disabled->getValue() === true} disabled{/if}"
           id="tab-link-{$id}">
            {$params.title->getValue()}
        </div>
    {else}*}
        <a class="nav-link{if $params.active->getValue() === true} active{/if}{if $params.disabled->getValue() === true} disabled{/if}"
           href="#tab-{$id}"
           data-toggle="tab"
           role="tab"
           aria-controls="tab-{$id}"
           id="tab-link-{$id}"
        >
            {$params.title->getValue()}
        </a>
    {*{/if}*}
</{$params.tag}>

{$tmp = $parentSmarty->getTemplateVars('tabContents')|default:[]}
{$tmp[] = $blockContent}
{$x = $parentSmarty->assign('tabContents', $tmp)}
{$tmp = $parentSmarty->getTemplateVars('tabIDs')|default:[]}
{$tmp[] = $id}
{$x = $parentSmarty->assign('tabIDs', $tmp)}
{if $params.active->getValue() === true}
    {$x = $parentSmarty->assign('activeTabID', $id)}
{/if}
