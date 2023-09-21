export class MessagesModule {
    constructor() {
        this.id = null;
        this.msj = null;
        this.success = null;
        this.type = null;
        this.background = null;
        this.message = null;
        this.listMessages = null;
    }

    setNewMessage(data) {
        this.id = data.id;
        this.msj = data.msj;
        this.success = data.success;
        this.type = data.type;
        this.background = data.background;
    }

    async setMessages(){
        this.listMessages = await this.#loadMessages();
    }

    get getMessages() {
        return this.listMessages;
    }

    async #loadMessages() {

        const peticion = new Promise((resolve) => {
            resolve(
                $.ajax({
                    method: 'GET',
                    url: './assets/messages.json',
                    dataType: 'json',
                    cache: false,
                    beforeSend: function () { },
                    success: function (response) {
                        if (!response) { console.warn("Err: No messages found") }
                    },
                    error: function (request, status, error) {
                        console.warn(error.responseText);
                    }
                }).done(function () { }),
            );
        });

        peticion.then((response) => { return 0; }).catch((err) => { console.error(err); });
        return peticion;
    }

}