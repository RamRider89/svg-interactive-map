import { MESSAGES_LIST } from '../../environments/config.env.js';

export class NotificationModule {
    constructor() {
        this.id = null;
        this.title = null;
        this.message = null;
        this.background = null;
        this.animation = false;
        this.autohide = true;
        this.delay = 500;
    }

    setNewToast(data) {
        this.id = data.id;
        this.title = data.title;
        this.message = data.message;
        this.background = data.background;
        this.animation = data.animation;
        this.autohide = data.autohide;
        this.delay = data.delay;
    }

    get getToast(){
        return this.#generateToast();
    }

    #generateToast(){
        const toast = `
                <div id="` + this.id + `"class="toast ` + this.background + ` text-white fade" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="mr-auto">` + this.title + `</strong>
                        <button type="button" class="ml-2 mb-1 close" data-bs-dismiss="toast" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="toast-body">` + this.message + `</div>
                </div>`;
        
        return toast;
    }

    /**
     * $('.toast').toast({
                    animation: false,
                    delay: 3000
                });
     */

}