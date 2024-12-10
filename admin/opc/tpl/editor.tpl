<!DOCTYPE html>
<html lang="{$opc->getAdminLangTag()}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{__('onPageComposer')}</title>

    <link rel="stylesheet" href="{$adminUrl}/opc/css/dependencies.bundle.css">
    <link rel="stylesheet" href="{$templateUrl}css/typeaheadjs.css">
    <link rel="stylesheet" href="{$templateUrl}css/tempusdominus-bootstrap-4.min.css">
    <link rel="stylesheet" href="{$adminUrl}/opc/css/editor.css">

    <script src="{$adminUrl}/opc/js/dependencies.bundle.js"></script>
    <script src="{$templateUrl}js/searchpicker.js"></script>
    <script src="{$templateUrl}js/moment-with-locales.js"></script>
    <script src="{$templateUrl}js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="{$shopUrl}/includes/libs/tinymce/js/tinymce/tinymce.min.js"></script>

    <script type="module">
        import { OPC } from "{$adminUrl}/opc/js/OPC.js";

        window.opc = new OPC({
            jtlToken:    '{$smarty.session.jtl_token}',
            shopUrl:     '{$shopUrl}',
            adminUrl:    '{$adminUrl}',
            pageKey:     {$pageKey},
            error:       {json_encode($error)},
            messages:    {json_encode($opc->getEditorMessages())},
        });

        opc.init();
    </script>

    {foreach $opc->getPortletInitScriptUrls() as $scriptUrl}
        <script src="{$scriptUrl}"></script>
    {/foreach}
</head>
<body>
    <div id="opc">
        {include file="./sidebar.tpl" pageName=$page->getName()}

        <div id="resizer"></div>

        <div id="iframePanel">
            <iframe id="iframe"></iframe>
        </div>

        <div id="previewPanel" style="display: none">
            <iframe id="previewFrame" name="previewFrame"></iframe>
            <form action="" target="previewFrame" method="post" id="previewForm">
                <input type="hidden" name="opcPreviewMode" value="yes">
                <input type="hidden" name="pageData" value="" id="previewPageDataInput">
            </form>
        </div>

        {include file="./modals/publish.tpl"}
        {include file="./modals/loader.tpl"}
        {include file="./modals/error.tpl"}
        {include file="./modals/config.tpl"}
        {include file="./modals/blueprint.tpl"}
        {include file="./modals/blueprint_delete.tpl"}
        {include file="./modals/tour.tpl"}
        {include file="./modals/restore_unsaved.tpl"}
        {include file="./modals/messagebox.tpl"}

        <div id="portletToolbar" class="opc-portlet-toolbar" style="display:none">
            <button type="button" class="opc-toolbar-btn opc-label" id="portletLabel"></button>
            <button type="button" class="opc-toolbar-btn" id="btnConfig" title="{__('editSettings')}">
                <i class="fas fa-pen"></i>
            </button>
            <button type="button" class="opc-toolbar-btn" id="btnClone" title="{__('copySelect')}">
                <i class="far fa-clone"></i>
            </button>
            <button type="button" class="opc-toolbar-btn" id="btnBlueprint" title="{__('saveTemplate')}">
                <i class="far fa-star"></i>
            </button>
            <button type="button" class="opc-toolbar-btn" id="btnParent" title="{__('goUp')}">
                <i class="fas fa-level-up-alt"></i>
            </button>
            <button type="button" class="opc-toolbar-btn" id="btnTrash" title="{__('deleteSelect')}">
                <i class="fas fa-trash"></i>
            </button>
        </div>

        <div id="portletPreviewLabel" class="opc-label" style="display:none"></div>

        <div id="dropTargetBlueprint" class="opc-droptarget" style="display:none">
            <div class="opc-droptarget-hover">
                <img src="{$shopUrl}/{$smarty.const.PFAD_ADMIN}opc/gfx/icon-drop-target.svg"
                     class="opc-droptarget-icon" alt="Drop Target">
                <span>{__('dropPortletHere')}</span>
                <i class="opc-droptarget-info fas fa-info-circle" data-toggle="tooltip" data-placement="left"></i>
            </div>
        </div>

        {include file="./tutorials.tpl"}
    </div>
</body>
</html>
