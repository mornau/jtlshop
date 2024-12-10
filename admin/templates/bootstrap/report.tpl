{include file='tpl_inc/header.tpl'}
{include file='tpl_inc/seite_header.tpl' cTitel=__('Reports') cBeschreibung=__('reportDesc')}
<div id="content">
    <form method="post">
        <button type="submit" class="btn btn-primary" name="create" value="1">
            <i class="fa fa-share"></i> {__('Generate report')}
        </button>
        <hr>
        {if count($reports) > 0}
            <div class="card">
                <div class="card-header">{__('Reports')}</div>
                <div class="card-body">
                    <table class="table table-striped" id="reports-table">
                        <thead>
                        <tr>
                            <th>{__('ID')}</th>
                            <th>{__('File')}</th>
                            <th>{__('Remote IP')}</th>
                            <th>{__('Created')}</th>
                            <th>{__('Visited')}</th>
                            <th>{__('Valid until')}</th>
                            <th>{__('Action')}</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach $reports as $report}
                            <tr>
                                <td>{$report->id}</td>
                                <td>{$report->file}</td>
                                <td>{$report->remoteIP|default:'---'}</td>
                                <td>{$report->created}</td>
                                <td>{$report->visited|default:'---'}</td>
                                <td>{$report->validUntil|default:'---'}</td>
                                <td>
                                    <button name="download" value="{$report->id}" class="btn btn-link px-1 dl notext"
                                            title="{__('Download')}" data-toggle="tooltip" data-placement="top">
                                        <span class="icon-hover">
                                            <span class="fal fa-download"></span>
                                            <span class="fas fa-download"></span>
                                        </span>
                                    </button>
                                    <button name="share" value="{$report->id}" class="btn btn-link px-1 share notext"
                                            title="{__('Share')}" data-toggle="tooltip" data-placement="top">
                                        <span class="icon-hover">
                                            <span class="fal fa-link"></span>
                                            <span class="fas fa-link"></span>
                                        </span>
                                    </button>
                                    <button name="delete" value="{$report->id}" class="btn btn-link px-1 delete notext"
                                            title="{__('Delete')}" data-toggle="tooltip" data-placement="top">
                                        <span class="icon-hover">
                                            <span class="fal fa-trash-alt"></span>
                                            <span class="fas fa-trash-alt"></span>
                                        </span>
                                    </button>
                                </td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        {else}
            <div class="alert alert-info">{__('noDataAvailable')}</div>
        {/if}
    </form>
</div>
{include file='tpl_inc/footer.tpl'}
