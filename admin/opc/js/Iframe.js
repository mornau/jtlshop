import {initDragStart, noop} from "./utils.js";

export class Iframe
{
    constructor(io, gui, page, shopUrl, adminUrl)
    {
        this.io          = io;
        this.gui         = gui;
        this.page        = page;
        this.shopUrl     = shopUrl;
        this.adminUrl    = adminUrl;

        this.draggedElm         = null;
        this.hoveredElm         = null;
        this.selectedElm        = null;
        this.dropTarget         = null;
        this.dragNewPortletCls  = null;
        this.dragNewBlueprintId = 0;
        this.loadedStylesheets  = [];
    }

    async init(pagetree)
    {
        $(window.btnConfig).on('click', () => this.onBtnConfig());
        $(window.btnClone).on('click', () => this.onBtnClone());
        $(window.btnBlueprint).on('click', () => this.onBtnBlueprint());
        $(window.btnParent).on('click', () => this.onBtnParent());
        $(window.btnTrash).on('click', () => this.onBtnTrash());

        this.pagetree = pagetree;

        await new Promise(res => {
            window.iframe.src = this.getIframePageUrl();
            window.iframe.onload = res;
        });

        this.ctx  = window.iframe.contentWindow;
        this.jq   = this.ctx.$;
        this.head = this.jq('head');
        this.body = this.jq('body');

        this.ctx.opc = opc;

        this.jq('[data-opc-portlet-css-link=true]').each((e, elm) => {
            this.loadedStylesheets.push(elm.href);
        });

        this.loadStylesheet(this.adminUrl + '/opc/css/iframe.css');
        this.loadStylesheet(this.shopUrl + '/templates/NOVA/themes/base/fontawesome/css/all.min.css');

        this.disableLinks();
        $(window.portletPreviewLabel).appendTo(this.body);
        $(window.portletToolbar).appendTo(this.body);

        this.loadScript(
            this.shopUrl + '/templates/NOVA/js/popper.min.js',
            () => {
                this.toolbarPopper      = this.makePopper($(this.ctx.portletToolbar));
                this.previewLabelPopper = this.makePopper($(this.ctx.portletPreviewLabel));
            }
        );

        try {
            await this.page.initIframe(this.jq);
        } catch (er) {
            return await this.gui.showError('Error while loading draft preview: ' + er.toString());
        }

        this.onPageLoad();
        this.gui.updatePagetreeBtn();
        opc.emit('iframe.init', this);
    }

    disableLinks()
    {
        // disable links and buttons that could change the current iframe page
        this.jq('a:not(.opc-no-disable), button:not(.opc-no-disable)')
            .off('click')
            .removeAttr('onclick')
            .on('click', e => e.preventDefault());
        this.jq('.variations select').off('change');
    }

    getIframePageUrl()
    {
        let pageUrlLink = document.createElement('a');

        pageUrlLink.href = this.page.fullUrl;

        if(pageUrlLink.search !== '') {
            pageUrlLink.search += '&opcEditMode=yes';
        } else {
            pageUrlLink.search = '?opcEditMode=yes';
        }

        pageUrlLink.search += '&opcEditedPageKey=' + this.page.key;

        return pageUrlLink.href.toString();
    }

    makePopper(elm)
    {
        return new this.ctx.Popper(
            document.body,
            elm[0],
            {
                placement: 'top-start',
                modifiers: {computeStyle: {gpuAcceleration: false }, offset: {offset:"8,0"}},
                onUpdate: data => {
                    $(data.instance.popper).css('top', data.styles.top + 1 + "px");
                },
            }
        );
    }

    onPageLoad()
    {
        this.enableEditingEvents();
        this.updateDropTargets();
        this.pagetree.render();
        this.gui.hideLoader();
        this.disableLinks();
    }

    updateDropTargets()
    {
        this.stripDropTargets();

        this.areas().each((i, area) => {
            area = this.jq(area);
            let droptarget = $(window.dropTargetBlueprint).clone().attr('id', '').show();
            droptarget.find('.opc-droptarget-info').attr('title', area.data('title') || area.data('area-id'));
            area.append(droptarget.clone());
            area.children('[data-portlet]').before(droptarget.clone());
        });

        this.areas().find('.opc-droptarget-info').tooltip();
    }

    stripDropTargets()
    {
        this.dropTargets().remove();
    }

    areas()
    {
        return this.jq('.opc-area');
    }

    portlets()
    {
        return this.jq('[data-portlet]');
    }

    dropTargets()
    {
        return this.jq('.opc-droptarget');
    }

    async loadStylesheet(url)
    {
        await new Promise(resolve => {
            if (this.loadedStylesheets.indexOf(url) === -1) {
                this.loadedStylesheets.push(url);
                this
                    .jq('<link rel="stylesheet" href="' + url + '">')
                    .on('load', resolve)
                    .appendTo(this.head);
            } else {
                resolve();
            }
        })
    }

    async loadPortletPreviewCss(portletCls)
    {
        let portletBtn = this.gui.portletButtons.filter("[data-portlet-class='" + portletCls + "']");

        if (portletBtn) {
            let css = portletBtn.data('portlet-css');

            if (css) {
                await this.loadStylesheet(css);

                if (portletCls === 'Flipcard') {
                    this.page.updateFlipcards();
                }
            }
        }
    }

    loadMissingPortletPreviewStyles()
    {
        this.portlets().each((i, elm) => {
            this.loadPortletPreviewCss($(elm).data('portlet').class);
        });
    }

    loadScript(url, callback)
    {
        let script = this.ctx.document.createElement('script');

        script.src = url;
        script.addEventListener('load', callback || noop);

        this.head[0].appendChild(script);
    }

    enableEditingEvents()
    {
        this.disableEditingEvents();

        this.page.rootAreas
            .on('mouseover', e => this.onPortletMouseOver(e))
            .on('click', e => this.onPortletClick(e))
            .on('dblclick', e => this.onBtnConfig(e))
            .on('dragstart', e => this.onPortletDragStart(e))
            .on('dragend', e => this.onPortletDragEnd(e))
            .on('dragover', e => this.onPortletDragOver(e))
            .on('drop', e => this.onPortletDrop(e));

        this.jq(this.ctx.document)
            .on('keydown', e => this.onKeyDown(e));
    }

    disableEditingEvents()
    {
        this.page.rootAreas
            .off('mouseover')
            .off('click')
            .off('dblclick')
            .off('dragstart')
            .off('dragend')
            .off('dragover')
            .off('drop');

        this.jq(this.ctx.document)
            .off('keydown');
    }

    onPortletMouseOver(e)
    {
        this.setHovered(this.findSelectableParent(this.jq(e.target)));
    }

    onPortletClick(e)
    {
        this.setSelected(this.findSelectableParent(this.jq(e.target)));
    }

    onPortletDragStart(e)
    {
        initDragStart(e);
        this.setDragged(this.findSelectableParent(this.jq(e.target)));
    }

    findSelectableParent(elm)
    {
        while(!this.isSelectable(elm) && !elm.is(this.page.rootAreas)) {
            elm = elm.parent();
        }

        return this.isSelectable(elm) ? elm : undefined;
    }

    onPortletDragEnd()
    {
        this.cleanUpDrag();
    }

    cleanUpDrag()
    {
        this.setDragged();
        this.setDropTarget();
        this.toolbarPopper.update();
        this.previewLabelPopper.update();
    }

    onPortletDragOver(e)
    {
        var elm = this.jq(e.target);

        if(elm.parent().hasClass('opc-droptarget')) {
            elm = elm.parent();
        } else if(elm.parent().parent().hasClass('opc-droptarget')) {
            elm = elm.parent().parent();
        }

        if(elm.hasClass('opc-droptarget') && !this.isDescendant(elm, this.draggedElm)) {
            this.setDropTarget(elm);
        }
        else {
            this.setDropTarget();
        }

        e.preventDefault();
    }

    async onPortletDrop()
    {
        if(this.dropTarget !== null) {
            let oldArea = this.draggedElm.parent();

            this.dropTarget.replaceWith(this.draggedElm);
            this.updateDropTargets();
            this.setSelected(this.draggedElm);

            if(this.dragNewPortletCls) {
                this.newPortletDropTarget = this.draggedElm;
                this.setSelected();
                let html = null;

                try {
                    html = await this.io.createPortlet(this.dragNewPortletCls)
                } catch(er) {
                    this.newPortletDropTarget.remove();
                    return await this.gui.showError(er.error.message);
                }

                this.onNewPortletCreated(html);

                if (this.dragNewPortletGroup && this.dragNewPortletGroup === 'content') {
                    this.gui.openConfigurator(this.selectedElm);
                }
            } else if(this.dragNewBlueprintId > 0) {
                this.newPortletDropTarget = this.draggedElm;
                this.setSelected();
                this.onNewPortletCreated(await this.io.getBlueprintPreview(this.dragNewBlueprintId));
            } else {
                this.pagetree.updateArea(oldArea);
                this.pagetree.updateArea(this.draggedElm.parent());
                this.setSelected(this.draggedElm);
                this.gui.setUnsaved(true, true);
            }
        }

        this.page.updateFlipcards();
    }

    onNewPortletCreated(html)
    {
        let newPortlet = this.createPortletElm(html);
        this.newPortletDropTarget.replaceWith(newPortlet);
        let newArea = newPortlet.parent();
        this.pagetree.updateArea(newArea);
        this.setSelected(newPortlet);
        this.updateDropTargets();
        this.gui.setUnsaved(true, true);
        this.loadMissingPortletPreviewStyles();
        this.page.updateFlipcards();
        this.disableLinks();
    }

    createPortletElm(html)
    {
        let container = document.createElement('div');
        container.innerHTML = html;

        if (container.firstElementChild) {
            return this.jq(container.firstElementChild);
        }
    }

    setDragged(elm)
    {
        elm = elm || null;

        if(this.draggedElm !== null) {
            this.draggedElm.removeClass('opc-dragged');
        }

        if(elm !== null) {
            elm.addClass('opc-dragged');
        }

        this.draggedElm = elm;
    }

    setHovered(elm)
    {
        elm = elm || null;

        if(this.hoveredElm !== null) {
            this.hoveredElm.removeClass('opc-hovered');
            this.hoveredElm.attr('draggable', 'false');
            $(this.ctx.portletPreviewLabel).hide();
        }

        if(elm !== null) {
            elm.addClass('opc-hovered');
            elm.attr('draggable', 'true');
            this.ctx.portletPreviewLabel.innerText = elm.data('portlet').title;
            $(this.ctx.portletPreviewLabel).show();
            this.previewLabelPopper.reference = elm[0];
            this.previewLabelPopper.update();
        }

        this.hoveredElm = elm;
    }

    setSelected(elm, scrollIntoView = false)
    {
        elm = elm || null;

        opc.emit('iframe.setSelected', elm);

        if(elm === null || !elm.is(this.selectedElm)) {
            if(this.selectedElm !== null) {
                this.selectedElm.removeClass('opc-selected');
                $(this.ctx.portletToolbar).hide();
            }

            if(elm !== null) {
                var portletData = elm.data('portlet');
                elm.addClass('opc-selected');
                this.ctx.portletLabel.innerText = portletData ? portletData.title : '';
                $(this.ctx.portletToolbar).show();
                this.toolbarPopper.reference = elm[0];
                this.toolbarPopper.update();

                if(scrollIntoView) {
                    this.scrollIntoView(elm);
                }
            }

            this.selectedElm = elm;
        }

        this.pagetree.setSelected(this.selectedElm);
    }

    scrollIntoView(elm)
    {
        var offsTop    = elm.offset().top;
        var viewTop    = this.jq(this.ctx).scrollTop();
        var diffTop    = offsTop - 128 - viewTop;
        var viewBottom = viewTop + $(this.ctx).height();
        var diffBottom = offsTop + 128 + elm.height() - viewBottom;

        if(diffTop < 0) {
            this.ctx.scrollBy(0, diffTop);
        }

        if(diffBottom > 0) {
            this.ctx.scrollBy(0, diffBottom);
        }
    }

    setDropTarget(elm)
    {
        elm = elm || null;

        if(this.dropTarget !== null) {
            this.dropTarget.removeClass('opc-active-droptarget');
        }

        if(elm !== null) {
            elm.addClass('opc-active-droptarget');
        }

        this.dropTarget = elm;
    }

    dragNewPortlet(cls, group)
    {
        this.dragNewPortletCls   = cls || null;

        if (group) {
            this.dragNewPortletGroup = group;
        }

        this.setDragged(this.jq('<i class="fas fa-spinner fa-pulse"></i>'));
    }

    dragNewBlueprint(id)
    {
        this.dragNewBlueprintId = id || 0;
        this.setDragged(this.jq('<i class="fas fa-spinner fa-pulse"></i>'));
    }

    onBtnConfig()
    {
        if(this.selectedElm) {
            this.gui.openConfigurator(this.selectedElm);
        }
    }

    replaceSelectedPortletHtml(html)
    {
        let newPortlet = this.createPortletElm(html);
        this.selectedElm.replaceWith(newPortlet);
        let area = newPortlet.parent();
        this.pagetree.updateArea(area);
        this.setSelected(newPortlet);
        this.updateDropTargets();
        this.gui.setUnsaved(true, true);
    }

    async onBtnClone()
    {
        if(this.selectedElm !== null) {
            let data = this.page.portletToJSON(this.selectedElm);
            opc.emit('iframe.clonePortlet', data);
            let html = await this.io.getPortletPreviewHtml(data);
            let copiedElm = this.createPortletElm(html);
            copiedElm.insertAfter(this.selectedElm);
            let area = copiedElm.parent();
            this.pagetree.updateArea(area);
            this.setSelected(copiedElm);
            this.updateDropTargets();
            this.gui.setUnsaved(true, true);
        }
    }

    onBtnBlueprint()
    {
        if(this.selectedElm !== null) {
            $(window.blueprintModal).modal('show');
        }
    }

    onBtnParent()
    {
        if(this.selectedElm !== null) {
            var elm = this.findSelectableParent(this.selectedElm.parent());

            if (this.isSelectable(elm)) {
                this.setSelected(elm);
            }
        }
    }

    onBtnTrash()
    {
        if(this.selectedElm !== null) {
            let area = this.selectedElm.parent();
            this.selectedElm.remove();
            this.pagetree.updateArea(area);
            this.setSelected();
            this.updateDropTargets();
            this.gui.setUnsaved(true, true);
            this.page.updateFlipcards();
        }
    }

    onKeyDown(e)
    {
        if(e.key === 'Delete' && this.selectedElm !== null) {
            this.onBtnTrash();
        }
    }

    isSelectable(elm)
    {
        return elm && elm.is('[data-portlet]');
    }

    isDescendant(descendant, tree)
    {
        return tree && tree.has(descendant).length > 0;
    }
}
