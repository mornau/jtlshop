<span data-html="true"
        data-toggle="tooltip"
        data-placement="{$placement}"
        title="{if $description !== null}{$description}{/if}{if $cID !== null && $description !== null}<hr>{/if}{if $cID !== null}<p><strong>{__('settingValueName')}: </strong><code>{$cID}</code></p>{/if}">
    {if $iconQuestion}
        <span class="fas fa-question-circle fa-fw"></span>
    {else}
        <span class="fas fa-info-circle fa-fw"></span>
    {/if}
</span>
