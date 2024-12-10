import {IO} from "./IO.js";
import {Page} from "./Page.js";
import {GUI} from "./GUI.js";
import {Iframe} from "./Iframe.js";
import {Tutorial} from "./Tutorial.js";
import {PageTree} from "./PageTree.js";
import {PreviewFrame} from "./PreviewFrame.js";
import {installJqueryFixes, Emitter} from "./utils.js";

export class OPC extends Emitter
{
    constructor(env)
    {
        super();

        installJqueryFixes();

        this.messages     = env.messages;
        this.error        = env.error;
        this.shopUrl      = env.shopUrl;
        this.io           = new IO(env.jtlToken, env.adminUrl);
        this.page         = new Page(this.io, env.shopUrl, env.pageKey);
        this.gui          = new GUI(this.io, this.page, env.messages, env.adminUrl, env.jtlToken);
        this.iframe       = new Iframe(this.io, this.gui, this.page, env.shopUrl, env.adminUrl);
        this.tutorial     = new Tutorial(this.iframe);
        this.pagetree     = new PageTree(this.page, this.iframe, this.gui);
        this.previewFrame = new PreviewFrame();
    }

    async init()
    {
        await this.io.init();
        this.gui.init(this.iframe, this.previewFrame, this.tutorial, this.error);
        this.tutorial.init();
        this.pagetree.init();
        this.previewFrame.init();

        await this.page.lock(er => {
            if(er === 1) {
                this.gui.showError(this.messages.opcPageLocked);
            } else if(er === 2) {
                this.gui.showError(this.messages.dbUpdateNeeded);
            }
        });

        await this.page.loadDraft();
        await this.iframe.init(this.pagetree);
        this.gui.updateRevisionList();
        this.gui.hideLoader();
        this.pagetree.render();

        if(this.page.hasUnsavedContent()) {
            this.gui.showRestoreUnsaved();
            $(window.unsavedRevision).show();
        } else {
            $(window.unsavedRevision).hide();
        }
    }

    selectImageProp(propName)
    {
        this.gui.selectImageProp(propName);
    }

    selectVideoProp(propName)
    {
        this.gui.selectVideoProp(propName);
    }

    setImageSelectCallback(callback)
    {
        this.gui.setImageSelectCallback(callback);
    }

    enableTypeahead(...args)
    {
        this.gui.enableTypeahead(...args);
    }
}
