{block name='two-fa-login-form'}
    <fieldset>
        {if !empty($oRedirect->cURL)}
            {foreach $oRedirect->oParameter_arr as $oParameter}
                {input type="hidden" name=$oParameter->Name value=$oParameter->Wert}
            {/foreach}
            {input type="hidden" name="r" value=$oRedirect->nRedirect}
            {input type="hidden" name="cURL" value=$oRedirect->cURL}
        {/if}
        {formgroup label-for='inputTwoFA'}
            <input
                    type="text"
                    id="inputTwoFA"
                    name="TwoFA_code"
                    class="form-control form-control-20"
                    placeholder=""
                    required="required"
                    autocomplete="off"
                    maxlength="16"
                    autofocus />
            <label for="inputTwoFA" class="col-form-label pt-0">
                {lang key='twoFactorAuthCode' section='account data'}
            </label>
        {/formgroup}
        <div class="mb-3 two-fa-tooltip">
            <a class="tooltip-link small"
               data-toggle="tooltip"
               data-placement="top"
               title="{lang key='twoFAAuthCodeTooltipDescription' section='account data'}">
                {lang key='twoFAAuthCodeTooltip' section='account data'}
            </a>
        </div>

        {block name='account-twofa-form-submit'}
            {formgroup class="twofa-form-submit"}
            {input type="hidden" name="twofa" value="1"}
            {block name='account-twofa-form-submit-button'}
                {button type="submit" value="1" block=true variant="primary"}
                {lang key='login' section='checkout'}
                {/button}
            {/block}
            {/formgroup}
        {/block}
    </fieldset>
{/block}
