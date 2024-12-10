{include file='tpl_inc/header.tpl'}
{$loggedIn = $account !== false && $smarty.session.loginIsValid|default:false === true}
{if !$loggedIn}
    <div id="content_wrapper">
{/if}
    <h1>{__('permissionDenied')}</h1>
    <div class="alert alert-danger clear">
        <p>{__('noPermissionForPage')}</p>
        <p>{__('contactAdminForPermission')}</p>
    </div>
{if !$loggedIn}
    </div>
{/if}
{include file='tpl_inc/footer.tpl'}
