{include file='tpl_inc/header.tpl'}
{include file='tpl_inc/seite_header.tpl' cTitel=__('dbcheck') cBeschreibung=__('dbcheckDesc') cDokuURL=__('dbcheckURL')}
<div id="content">
    {if $maintenanceResult !== null}
        {if is_array($maintenanceResult)}
            <ul class="list-group mb-3">
                {foreach $maintenanceResult as $result}
                    <li class="list-group-item">
                        <strong>{$result->Op} {$result->Table}:</strong> {$result->Msg_text}
                    </li>
                {/foreach}
            </ul>
        {else}
            <div class="alert alert-info">{__('errorDoAction')}</div>
        {/if}
    {/if}
    <div id="pageCheck">
        {if count($cDBFileStruct_arr) > 0}
            {if isset($engineUpdate)}
                {include file='tpl_inc/dbcheck_engineupdate.tpl'}
            {else}
                <div class="alert alert-info"><strong>{__('countTables')}:</strong> {count($cDBFileStruct_arr)}<br /><strong>{__('showModifiedTables')}:</strong> {count($cDBError_arr)}</div>
            {/if}
            <form action="{$adminURL}{$route}" method="post">
                {$jtl_token}
                <div id="contentCheck" class="card">
                    <div class="card-header">
                        <div class="subheading1">{__('databaseStructure')}</div>
                    </div>
                    <table class="table table-striped req">
                        <thead>
                        <tr>
                            <th>{__('table')}</th>
                            <th>{__('engine')}</th>
                            <th>{__('rowformat')}</th>
                            <th>{__('collation')}</th>
                            <th class="centered">{__('rows')}</th>
                            <th class="centered">{__('data')}</th>
                            <th>{__('status')}</th>
                            <th class="centered">{__('action')}</th>
                        </tr>
                        </thead>
                        {foreach $cDBFileStruct_arr as $cTable => $oDatei}
                            {assign var=hasError value=array_key_exists($cTable, $cDBError_arr)}
                            <tr class="filestate {if !array_key_exists($cTable, $cDBError_arr)}unmodified{else}modified{/if}">
                                <td>
                                    {if $hasError}
                                        {$cTable}
                                    {else}
                                        <label for="check-{$oDatei@iteration}">{$cTable}</label>
                                    {/if}
                                </td>
                                <td>
                                    {if array_key_exists($cTable, $cDBStruct_arr)}
                                        <span class="badge alert-{if $cDBStruct_arr.$cTable->ENGINE === 'InnoDB'}info{else}warning{/if}">{$cDBStruct_arr.$cTable->ENGINE}</span>
                                    {/if}
                                </td>
                                <td>
                                    {if array_key_exists($cTable, $cDBStruct_arr)}
                                        <span class="badge alert-{if $cDBStruct_arr.$cTable->ROW_FORMAT === 'Dynamic'}info{else}warning{/if}">{$cDBStruct_arr.$cTable->ROW_FORMAT}</span>
                                    {/if}
                                </td>
                                <td>
                                    {if array_key_exists($cTable, $cDBStruct_arr)}
                                        <span class="badge alert-{if strpos($cDBStruct_arr.$cTable->TABLE_COLLATION, 'utf8mb4') === 0}info{else}warning{/if}">{$cDBStruct_arr.$cTable->TABLE_COLLATION}</span>
                                    {/if}
                                </td>
                                <td class="centered">
                                    {if array_key_exists($cTable, $cDBStruct_arr)}{$cDBStruct_arr.$cTable->TABLE_ROWS|number_format:0:',':'.'}{/if}
                                </td>
                                <td class="centered">
                                    {if array_key_exists($cTable, $cDBStruct_arr)}{$cDBStruct_arr.$cTable->DATA_SIZE|formatByteSize:'%.0f'|upper|strip:'&nbsp;'}{/if}
                                </td>
                                <td>
                                    {if $hasError}
                                        <span class="badge red text-white">{$cDBError_arr[$cTable]->errMsg}</span>
                                    {else}
                                        <span class="badge green text-white">{__('ok')}</span>
                                    {/if}
                                </td>
                                <td class="centered">
                                    {if isset($cDBStruct_arr.$cTable)}
                                        {if $cDBStruct_arr.$cTable->Locked}
                                            <span title="Tabelle in Benutzung"><i class="fa fa-cog fa-spin fa-2x fa-fw"></i></span>
                                        {elseif (($cDBStruct_arr.$cTable->Migration & JTL\DB\Migration\Check::MIGRATE_TABLE) !== JTL\DB\Migration\Check::MIGRATE_NONE) && $DB_Version->hasUTF8Support() && $DB_Version->hasInnoDBSupport()}
                                            <a href="#" class="btn btn-default btn-migrate" data-action="migrate" data-table="{$cTable}" data-step="1"><i class="fa fa-cogs"></i></a>
                                        {elseif (($cDBStruct_arr.$cTable->Migration & JTL\DB\Migration\Check::MIGRATE_COLUMN) !== JTL\DB\Migration\Check::MIGRATE_NONE) && $DB_Version->hasUTF8Support() && $DB_Version->hasInnoDBSupport()}
                                            <a href="#" class="btn btn-default btn-migrate" data-action="migrate" data-table="{$cTable}" data-step="2"><i class="fa fa-cogs"></i></a>
                                        {/if}
                                        <div class="custom-control custom-checkbox{if $hasError} d-none{/if}">
                                            <input class="custom-control-input" id="check-{$oDatei@iteration}" type="checkbox" name="check[]" value="{$cTable}" />
                                            <label class="custom-control-label" for="check-{$oDatei@iteration}"></label>
                                        </div>
                                    {/if}
                                </td>
                            </tr>
                        {/foreach}
                    </table>
                </div>
                <div class="save-wrapper">
                    <div class="row">
                        <div class="col-sm-6 col-xl-auto text-left">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" name="ALL_MSG" id="ALLMSGS" onclick="AllMessages(this.form);"/>
                                <label class="custom-control-label" for="ALLMSGS">{__('markAll')}</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-auto">
                            <select name="action" class="custom-select">
                                <option value="">{__('action')}</option>
                                <option value="optimize">{__('optimize')}</option>
                                <option value="repair">{__('repair')}</option>
                                <option value="analyze">{__('analyse')}</option>
                                <option value="check">{__('check')}</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-xl-auto">
                            <button type="submit" class="btn btn-primary">{__('send')}</button>
                        </div>
                        {if count($cDBError_arr) > 0}
                        <div class="col-sm-6 col-xl-auto ml-auto">
                            <button id="viewAll" name="viewAll" type="button" class="btn btn-primary fade" value="{__('showAll')}">
                                <i class="fa fa-share"></i> {__('showAll')}
                            </button>
                            <button id="viewModified" name="viewModified" type="button" class="btn btn-danger viewModified fade show" value="{__('showModified')}">
                                <i class="fal fa-exclamation-triangle"></i> {__('showModified')}
                            </button>
                        </div>
                        {/if}
                    </div>
                </div>
            </form>
        {/if}
    </div>
</div>
<div id="modalWait" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2><span>&nbsp;</span> <img src="{$templateBaseURL}gfx/widgets/ajax-loader.gif"></h2>
            </div>
            <div class="modal-body">
                <div class="progress" data-notify="progressbar">
                    <div class="progress-bar progress-bar-{ldelim}0{rdelim}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="ml-auto col-sm-6 col-xl-auto">
                        <button id="cancelWait" class="btn btn-danger btn-block">
                            <i class="fa fa-close"></i>&nbsp;{__('migrationCancel')}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    {literal}
    $(document).ready(function () {
        $('#viewAll').on('click', function () {
            $('#viewAll').removeClass('show').hide();
            $('#viewModified').addClass('show').show();
            $('.unmodified').show();
            $('.modified').show();
            colorLines();
        });

        $('#viewModified').on('click', function () {
            $('#viewAll').addClass('show').show();
            $('#viewModified').removeClass('show').hide();
            $('.unmodified').hide();
            $('.modified').show();
            colorLines();
        });

        $('*[data-action="migrate"]').on('click', function (e) {
            var $this = $(this);

            e.preventDefault();
            showModalWait('', parseInt($this.data('step')) === 1 ? 2 : 1);
            doSingleMigration($this.data('table'), $this.data('step'), $this.closest('tr'));
        });

        $('#cancelWait').on('click', function (e) {
            cancelWait(true);
            e.preventDefault();
            window.setTimeout(closeModalWait, 1000);
        });

        function colorLines() {
            var mod = 1;
            $('.req li:not(:hidden)').each(function () {
                if (mod === 1) {
                    $(this).removeClass('mod0');
                    $(this).removeClass('mod1');
                    $(this).addClass('mod1');
                    mod = 0;
                } else {
                    $(this).removeClass('mod1');
                    $(this).removeClass('mod0');
                    $(this).addClass('mod0');
                    mod = 1;
                }
            });
        }
    });
    function showModalWait(msg, maxMigrationTables) {
        var $modalWait = $("#modalWait");

        if (msg) {
            $('h4 > span', $modalWait).text(msg);
        }
        if (typeof maxMigrationTables === 'undefined') {
            maxMigrationTables = 1;
        }
        cancelWait(false);

        $modalWait.modal({
            backdrop: 'static'
        });
        $('.progress-bar', $modalWait).attr('aria-valuenow', 0);
        $('.progress-bar', $modalWait).attr('aria-valuemax', maxMigrationTables);
        $('.progress-bar', $modalWait).css('width', 0);

        return $modalWait;
    }
    function updateModalWait(msg, step) {
        var $modalWait = $("#modalWait");

        if (typeof msg !== 'undefined' && msg !== null && msg !== '') {
            $('h2 > span', $modalWait).text(msg);
        }
        if (typeof step !== 'undefined' && step !== null && step > 0) {
            var progressMax     = $('.progress-bar', $modalWait).attr('aria-valuemax');
            var progressNow     = parseInt($('.progress-bar', $modalWait).attr('aria-valuenow')) + step;
            var progressPercent = progressNow > progressMax ? 100 : progressNow / progressMax * 100;
            $('.progress-bar', $modalWait).attr('aria-valuenow', progressNow > progressMax ? progressMax : progressNow);
            $('.progress-bar', $modalWait).css('width', progressPercent + '%');
        }
    }
    function closeModalWait() {
        $("#modalWait").modal("hide");
    }
    function cancelWait(cancel) {
        var $cancelWait = $('#cancelWait');

        if (typeof cancel === 'undefined') {
            return $cancelWait.data('canceled');
        }

        $cancelWait.prop('disabled', cancel);
        $cancelWait.data('canceled', cancel);
    }
    function doSingleMigration(table, step, $row) {
        if (cancelWait()) {
            closeModalWait();
            return;
        }
        if (typeof step === 'undefined' || step === 0) {
            step = 1;
        }
        if (typeof table !== 'undefined' && table !== '') {
            updateModalWait(sprintf('{/literal}{__('migrationOf')}{literal}', table, step));
        }
        ioCall('migrateToInnoDB_utf8', ['migrate_single', table, step],
            function (data, context) {
                if (data && typeof data.status !== 'undefined' && data.status !== 'failure') {
                    if (data.status === 'migrate' && data.nextStep === 2) {
                        updateModalWait(null, 1);
                        doSingleMigration(table, 2, $row);
                    } else {
                        updateModalWait(null, 1);
                        updateRow($row, table, data);
                        window.setTimeout(closeModalWait, 1000);
                    }
                } else {
                    window.alert(sprintf('{/literal}{__('errorMigrationTable')}{literal}', table));
                    window.location.reload(true);
                }
            },
            function (responseJSON) {
                window.alert(sprintf('{/literal}{__('errorMigrationTable')}{literal}', table));
                window.location.reload(true);
            },
            {}
        );
    }
    function updateRow($row, table, data) {
        var $cols = $('td', $row);
        if ($cols.length > 0) {
            $($cols[1]).html('<span class="badge alert-' + (data.table && data.table.ENGINE === 'InnoDB' ? 'info' : 'warning') + '">' + data.table.ENGINE + '</span>');
            $($cols[2]).html('<span class="badge alert-' + (data.table && data.table.ROW_FORMAT === 'Dynamic' ? 'info' : 'warning') + '">' + data.table.ROW_FORMAT + '</span>');
            $($cols[3]).html('<span class="badge alert-' + (data.table && data.table.TABLE_COLLATION.startsWith('utf8mb4') ? 'info' : 'warning') + '">' + data.table.TABLE_COLLATION + '</span>');
            $($cols[6]).html('<span class="badge ' + (data.table.Status === '' ? 'green' : 'red') + ' text-white">' + (data.table.Status === '' ? '{/literal}{__('ok')}{literal}' : data.table.Status) + '</span>');
            if (data.table.Status === '') {
                $($cols[7]).find('.btn-migrate').remove();
                $($cols[7]).find('.d-none').removeClass('d-none');
            }
        }
    }
    {/literal}
</script>
{include file='tpl_inc/footer.tpl'}
