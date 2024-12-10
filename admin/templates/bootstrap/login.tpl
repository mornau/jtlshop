{include file='tpl_inc/header.tpl'}

<script type="text/javascript">
    {literal}
    $(document).ready(function () {
        $("input.field:first").focus();
    });
    {/literal}
</script>
<div class="vertical-center">
    <div class="container">
        <div id="login_wrapper">
            <div id="login_outer" class="card">
                <div class="card-body">
                    <p class="text-center mb-4">
                        <a href="{$adminURL}/">
                            <img class="brand-logo" width="120" height="38" src="{$templateBaseURL}gfx/JTL-Shop-Logo-rgb.png" alt="JTL-Shop">
                            <img class="brand-logo brand-logo-white" width="120" height="38" src="{$templateBaseURL}gfx/JTL-Shop-Logo-rgb-white.png" alt="JTL-Shop">
                        </a>
                    </p>
                    {if $alertError}
                        {include file='snippets/alert_list.tpl'}
                        <script type="text/javascript">
                            {literal}
                            $(document).ready(function () {
                                $("#login_wrapper").effect("shake", {times: 2}, 50);
                            });
                            {/literal}
                        </script>
                    {elseif isset($pw_updated) && $pw_updated === true}
                        <div class="alert alert-success" role="alert"><i class="fal fa-info-circle"></i> {__('successPasswordChange')}</div>
                    {/if}

                    {form method="post" action="{$adminURL}/" class="form-horizontal" role="form"}
                        <input id="benutzer" type="hidden" name="adminlogin" value="1" />
                        {if isset($uri) && $uri|strlen > 0}
                            <input type="hidden" name="uri" value="{$uri}" />
                        {/if}
                        {if isset($smarty.session.AdminAccount->TwoFA_active)
                        && true === $smarty.session.AdminAccount->TwoFA_active }  {* added for 2FA *}
                            <input type="hidden" name="benutzer" value="">
                            <input type="hidden" name="passwort" value="">
                            <p class="text-muted">{__('TwoFALogin')}</p>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text p-0 d-block overflow-hidden" id="codeIsStillValid">
                                        <div data-seconds="50%"></div>
                                    </span>
                                </div>
                                <input class="form-control" type="text" placeholder="2fa-code" name="TwoFA_code"
                                       id="inputTwoFA" value="" size="20" tabindex="10" />
                            </div>

                            {literal}
                                <script>
                                    $(document).ready(function () {
                                        let date = new Date(),
                                            seconds = date.getSeconds();

                                        if (seconds > 30) {
                                            seconds = seconds - 30;
                                        }

                                        document.body.style.setProperty(
                                            "--secondsCodeIsStillValid",
                                            "-" + seconds + "s"
                                        );
                                    });
                            {/literal}
                                function switchUser() {
                                    window.location.href = '{$adminURL}/logout?token=' + $("[name$=jtl_token]").val();
                                }
                            </script>
                        {else}
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-user"></i></span></div>
                                <input class="form-control" type="text" placeholder="{__('username')}" name="benutzer" id="user_login" value="" size="20" tabindex="10" autocomplete="username" />
                            </div>
                            <div class="input-group mt-2">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-lock"></i></span></div>
                                <input class="form-control" type="password" placeholder="{__('password')}" name="passwort" id="user_pass" value="" size="20" tabindex="20" />
                            </div>
                            {if isset($code_adminlogin) && $code_adminlogin}
                                {captchaMarkup getBody=true}
                            {/if}
                        {/if}
                        <div id="collapseExtended" class="collapse input-group mt-2 form-group form-row align-items-center{if $plgSafeMode === true} show{/if}" aria-labelledby="headingExtended">
                            <input id="safemode" class="col col-sm-auto ml-2" type="checkbox" name="safemode" value="on"{if $plgSafeMode === true} checked="checked"{/if}>
                            <label for="safemode" class="col col-sm-auto col-form-label text-sm-right" title="{__('Safe mode enabled.')}" data-toggle="tooltip">{__('Safe mode')}</label>
                        </div>
                        <button type="submit" value="Anmelden" tabindex="100" class="btn btn-primary btn-block mt-3">{__('login')}</button>
                        {if isset($smarty.session.AdminAccount->TwoFA_active) && true === $smarty.session.AdminAccount->TwoFA_active }
                            <button type="button" tabindex="110" class="btn btn-default btn-block btn-md" onclick="switchUser();">{__('changerUser')}</button>
                        {/if}
                        {if $plgSafeMode !== true}
                            <div id="headingExtended" class="mt-3 text-right small">
                                <a href="#" data-toggle="collapse" data-target="#collapseExtended" aria-expanded="false" aria-controls="collapseExtended">{__('extended')}</a>
                            </div>
                        {/if}
                    {/form}
                </div>
            </div>
            <p class="forgot-pw-wrap text-center">
                <small><a href="{$adminURL}/pass" title="{__('forgotPassword')}"><i class="fa fa-lock"></i> {__('forgotPassword')}</a></small>
            </p>
        </div>
    </div>
</div>
{include file='tpl_inc/footer.tpl'}
