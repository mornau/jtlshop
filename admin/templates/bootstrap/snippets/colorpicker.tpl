<div class="input-group" id="{$cpID}-group">
    <input type="text" class="form-control colorpicker-input"
           name="{$cpName}" value="{$cpValue}" id="{$cpID}"
           autocomplete="off">
    <span class="input-group-append">
        <span class="input-group-text colorpicker-input-addon">
            <i></i>
        </span>
    </span>
</div>
<script>
    $('#{$cpID}-group').colorpicker({ldelim}
        format: 'rgba',
        fallbackColor: 'rgb(255, 255, 255)',
        autoInputFallback: true,
        useAlpha: {if $useAlpha|default:false}true{else}false{/if}
    {rdelim});
</script>
