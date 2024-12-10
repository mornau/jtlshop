{$postfix = $postfix|default:''}
{$prefix = $prefix|default:''}
{$isChild = $isChild|default:false}
{$addChild = $addChild|default:false}

{foreach $item->getAttributes() as $attr}
    {if $attr->isDynamic() === true}
        {continue}
    {/if}
    {$name = $attr->getName()}
    {$inputName = $name}
    {if $isChild}
        {$inputName = $prefix|cat:'['|cat:$inputName|cat:'][]'}
    {/if}
    {$type = $attr->getDataType()}
    {$inputConfig = $attr->getInputConfig()}
    {$value = $item->getAttribValue($name)}
    {$inputType = $inputConfig->getInputType()}
    {if $inputType === 'date'}
        {$value = $value|date_format:'d.m.Y H:i:s'}
    {/if}

    {if $inputConfig->isHidden() === true}
        {input type='hidden' value=$value name=$inputName id=$name|cat:$postfix}
        {continue}
    {/if}
    {if strpos($type, "\\") !== false && class_exists($type)}
        {$cnt = 0}
        {if $item->$name !== null}
            {$cnt = $item->$name->count()}
        {/if}
        {if $cnt === 0 && $addChild === false}
            {continue}
        {/if}
        <div class="subheading1">{__('childHeading')}</div>
        <hr>
        {foreach $item->$name as $childItem}
            {include file='tpl_inc/model_item.tpl' isChild=true postfix=$childItem->getId() item=$childItem prefix=$name}
            <hr>
        {/foreach}
        {if $addChild !== false && $childModel !== null}
            {include file='tpl_inc/model_item.tpl' isChild=true item=$childModel addChild=false postfix=$postfix prefix=$name assign=cmdata}
            <div id="childmodelappend"></div>
            <script>
                $(document).ready(function () {
                    $('#add-child-model-item').on('click', function () {
                        $('#childmodelappend').append(`{trim($cmdata)}`);
                    });
                });
            </script>

            <button type="button" value="1" class="btn btn-default" id="add-child-model-item">
                <i class="fas fa-share"></i> {__('add')}
            </button>
        {/if}
        {continue}
    {/if}

    <div class="form-group form-row align-items-center">
        {if $inputType === JTL\Plugin\Admin\InputType::SELECT}
            <label class="col col-sm-4 col-form-label text-sm-right" for="{$name}{$postfix}">{__($name)}:</label>
            <div class="col-sm pl-sm-3 pr-sm-5 order-last order-sm-2">
                <select class="custom-select" id="{$name}{$postfix}" name="{$inputName}">
                    {foreach $inputConfig->getAllowedValues() as $k => $v}
                        <option value="{$k}"{if $value === $k} selected{/if}>{__($v)}</option>
                    {/foreach}
                </select>
                <span id="specialLinkType-error" class="hidden-soft error"> <i title="{__('isDuplicateSpecialLink')}" class="fal fa-exclamation-triangle error"></i></span>
            </div>
        {elseif $inputType === JTL\Plugin\Admin\InputType::TEXTAREA}
            <label class="col col-sm-4 col-form-label text-sm-right" for="{$name}{$postfix}">{__($name)}:</label>
            <div class="col-sm pl-sm-3 pr-sm-5 order-last order-sm-2">
                <textarea class="form-control tinymce" id="{$name}{$postfix}" name="{$inputName}" rows="10" cols="40">{$value}</textarea>
            </div>
        {elseif $inputType === JTL\Plugin\Admin\InputType::CHECKBOX}
            <span class="col col-sm-4 col-form-label text-sm-right">{__($name)}:</span>
            <div class="col-sm pl-sm-3 pr-sm-5 order-last order-sm-2">
                {foreach $inputConfig->getAllowedValues() as $k => $v}
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="{$name}{$postfix}{$k}" value="{$k}"
                        name="{$inputName}[]"{if strpos($value, $k) !== false} checked{/if}>
                        <label class="custom-control-label" for="{$name}{$postfix}{$k}">{__($v)}</label>
                    </div>
                {/foreach}
             </div>
        {else}
            <label class="col col-sm-4 col-form-label text-sm-right" for="{$name}{$postfix}">
                {__({$name})}{if $name === 'languageID'}
                    {foreach $availableLanguages as $availableLanguage}
                        {if $availableLanguage->id === (int)$value}
                            ({$availableLanguage->localizedName})
                        {/if}
                    {/foreach}
                {/if}:
            </label>
            <div class="col-sm pl-sm-3 pr-sm-5 order-last order-sm-2">
                {input readonly=!$inputConfig->isModifyable() type=$inputType value=$value name=$inputName id=$name|cat:$postfix}
            </div>
        {/if}
    </div>
{/foreach}
