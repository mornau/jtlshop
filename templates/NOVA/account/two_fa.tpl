<style>
    div.qrcode {
        margin: 5px
    }
    div.qrcode > p {
        margin: 0;
        padding: 0;
        height: 5px;
    }
    div.qrcode > p > b,
    div.qrcode > p > i {
        display: inline-block;
        width: 5px;
        height: 5px;
    }
    div.qrcode > p > b {
        background-color: #000;
    }
    div.qrcode > p > i {
        background-color: #fff;
    }
</style>

{block name='account-manage-twofa'}
    {block name='account-manage-twofa-heading'}
        <h1>{lang key='manageTwoFA' section='account data'}</h1>
    {/block}
    {block name='account-manage-twofa-manage-twofa-form'}
        {block name='account-manage-twofa-alert'}
            {alert variant="info"}
                {lang key='manageTwoFADesc' section='account data'}
                <div id="twofa-app-warning-wrapper" class="collapse{if $Kunde->has2FA() === true} show{/if}">
                    {lang key='twoFAAppWarning' section='account data'}
                </div>
            {/alert}
        {/block}
        {row}
        {col md=7 lg=6}
        {block name='account-manage-twofa-form'}
            {form id="manage-twofa"
            action="{get_static_route id='jtl.php'}"
            method="post"
            class="jtl-validate"
            slide=true}
            <input type="hidden" name="twoFACustomerID" id="twoFACustomerID" value="{$Kunde->getID()}">
            {block name='account-manage-twofa-form-content'}
                {lang key='enableTwoFA' section='account data' assign=lbl}
                {formgroup label-for='b2FAauth' label=$lbl}
                    {select id='b2FAauth' name='b2FAauth'}
                        <option value="0"{if $Kunde->has2FA() === false} selected="selected"{/if}>{lang key='no'}</option>
                        <option value="1"{if $Kunde->has2FA() === true} selected="selected"{/if}>{lang key='yes'}</option>
                    {/select}
                {/formgroup}
                <div id="twofa-warning-wrapper" class="collapse{if $Kunde->has2FA() === false} show{/if}">
                    <div class="alert alert-warning">{lang key='twoFAEnableWarning' section='account data'}</div>
                </div>
                <div id="TwoFAwrapper"
                     class="collapse form-group{if isset($cError_arr.c2FAsecret)} error{/if}{if $Kunde->has2FA() === true} show{/if}"
                     style="border:1px solid {if isset($cError_arr.c2FAsecret)}red{else}lightgrey{/if};padding:10px;">
                    <div id="QRcodeCanvas" style="display:{if $QRcodeString !== ''}block{else}none{/if}">
                        <h4>{lang key='twoFAtutorialTitle' section='account data'}</h4>
                        <ol id="twofa-app-tutorial">
                            <li>{lang key='twoFAtutorialStep1' section='account data'}</li>
                            <li>{lang key='twoFAtutorialStep2' section='account data'}</li>
                            <li>{lang key='twoFAtutorialStep3' section='account data'}</li>
                        </ol>
                        <div id="QRcode" class="qrcode">{$QRcodeString}</div>
                        <br>
                        <input type="hidden" id="c2FAsecret" name="c2FAsecret" value="{$cKnownSecret}">
                        <br>
                    </div>
                    <div class="modal fade" id="EmergencyCodeModal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="modal-title">{lang key='emergencyCodes' section='account data'}</h2>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div id="EmergencyCodes">
                                        <div class="alert alert-info">
                                            {lang key='twoFAEmergencyCodesNotice' section='account data'}
                                        </div>
                                        <div class="iframewrapper">
                                            <iframe src="" id="printframe" name="printframe" frameborder="0"
                                                    width="100%" height="300" align="middle"></iframe>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div class="row">
                                        <div class="ml-auto col-sm-6 col-xl-auto mb-2">
                                            <button class="btn btn-outline-primary btn-block" type="button"
                                                    data-dismiss="modal">
                                                {lang key='close' section='account data'}
                                            </button>
                                        </div>
                                        <div class="col-sm-6 col-xl-auto mb-2">
                                            <button class="btn btn-outline-primary btn-block" type="button"
                                                    onclick="printframe.print();">
                                                {lang key='print' section='account data'}
                                            </button>
                                        </div>
                                        <div class="col-sm-6 col-xl-auto">
                                            <button class="btn btn-danger btn-block" type="button"
                                                    onclick="showEmergencyCodes('forceReload');">
                                                {lang key='codeCreateAgain' section='account data'}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-12 col-sm-6 mb-3">
                            <button class="btn btn-primary btn-block" type="button" onclick="createNewSecret();">
                                {lang key='codeCreate' section='account data'}
                            </button>
                        </div>
                        <div class="col-12 col-sm-6">
                            <button class="btn btn-warning btn-block" type="button" onclick="showEmergencyCodes();">
                                {lang key='emergencyCodeCreate' section='account data'}
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6">
                        </div>
                        <div class="col-12 col-sm-6">
                            <a class="tooltip-link small"
                               data-toggle="tooltip"
                               data-placement="top"
                               title="{lang key='twoFAEmergencyCodeDescription' section='account data'}">
                                {lang key='twoFAEmergencyCodeTooltip' section='account data'}
                            </a>
                        </div>
                    </div>
                </div>
                {block name='account-manage-twofa-form-submit'}
                    {row class="btn-row"}
                        {col cols=12 class='col-md-3'}
                            {link class='btn btn-outline-primary btn-back' href="{get_static_route id='jtl.php'}"}
                                {lang key='back'}
                            {/link}
                        {/col}
                        {col class='col-md-4 col-xl-3'}
                            {input type='hidden' name='manage_two_fa' value='1'}
                            {button type='submit' value='1' block=true variant='primary' id='twofa-save-button'}
                               {lang key='save' section='account data'}
                            {/button}
                        {/col}
                    {/row}
                {/block}
            {/block}
            {/form}
        {/block}
        {/col}
        {/row}
    {/block}
{/block}

<script>
    $(document).ready(function () {
        $('#b2FAauth').on('change', function (e) {
            e.stopImmediatePropagation(); // stop this event during page-load
            let $wrapper = $('#TwoFAwrapper');
            if ('none' === $wrapper.css('display')) {
                $wrapper.slideDown();
            } else {
                $wrapper.slideUp();
            }
            $('#twofa-warning-wrapper').collapse('toggle');
            $('#twofa-app-warning-wrapper').collapse('toggle');
        });
        $('#twofa-save-button').on('click', function (e) {
            let userHad2FA = {(int)$Kunde->has2FA()}
            if (userHad2FA === 1 || $('#b2FAauth').val() === '0') {
                return;
            }
            eModal.addLabel(
                '{lang key='yes' section='global' addslashes=true}',
                '{lang key='no' section='global' addslashes=true}'
            );
            var options = {
                message: '{lang key='twoFAEnableConfirmMessage' section='account data' addslashes=true}',
                label: '{lang key='yes' section='global' addslashes=true}',
                title: '{lang key='twoFAEnableConfirmTitle' section='account data' addslashes=true}'
            };
            eModal.confirm(options).then(
                function () {
                    $('#manage-twofa').submit();
                },
                function () {
                    return false;
                }
            );
            return false;
        })
    });

    function createNewSecret() {
        let currentSecret = $('#c2FAsecret').val();
        if (
            currentSecret === ''
            || confirm('{lang key='warningAuthSecretOverwrite' section='account data'}')
        ) {
            let userID = parseInt($('#twoFACustomerID').val());
            let that = this;
            $.evo.io().call('getNewTwoFA', [userID], that, function (error, data) {
                $.evo.io().call('genTwoFAEmergencyCodes', [userID], that, function (error, data) {
                    showEmergencyCodes();
                });
                $('#QRcode').html(data.response.szQRcode);
                $('#c2FAsecret').val(data.response.szSecret);
                if ($('#QRcodeCanvas').css('display') === 'none') {
                    $('#QRcodeCanvas').css('display', 'block');
                }
            });
        }
    }

    function showEmergencyCodes(action) {
        let userID = parseInt($('#twoFACustomerID').val());
        let that = this;
        $.evo.io().call('genTwoFAEmergencyCodes', [userID], that, function (error, data) {
            var iframeHtml = '';

            iframeHtml += '<h4>{lang key='shopEmergencyCodes' section='account data'}</h4>';
            iframeHtml += '{lang key='account' section='account data'}: <b>'
                + data.response.loginName
                + '</b><br>';
            iframeHtml += '{lang key='shop' section='account data'}: <b>'
                + data.response.shopName
                + '</b><br><br>';
            iframeHtml += '<pre>';

            data.response.vCodes.forEach(function (code, i) {
                iframeHtml += code + ' ';
                if (i % 2 === 1) {
                    iframeHtml += '\n';
                }
            });

            iframeHtml += '</pre>';
            $('#printframe').contents().find('body')[0].innerHTML = iframeHtml;
            $('#EmergencyCodeModal').modal('show');
        });
    }
</script>
