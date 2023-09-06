class PositionService {
    constructor() { 
        this.position = null;
    }

    setPositionName(position) {
        this.position = position;
    }

    setPosition(id) {
        this.id = id;
    } 

    getPositionByName() {
        return this.#getPositionByName(this.position);
    }

    getPosition() {
        return this.#getPosition(this.id);
    }

    async #getPositionByName(position) {
        const data = { nombrePosicion: String(position) };
        const peticion = new Promise((resolve) => {
            resolve(
                // OBTENER LA INFORMACION DE LA POSICION
                
                $.ajax({
                    type: 'POST',
                    url: URL_API_CAMPUS + 'api/getposicionbyname',
                    dataType: 'json',
                    data: JSON.stringify(data),
                    async: true,
                    beforeSend: function () { },
                    success: function (response) {
                        return (response.meta.status == "SUCCESS") ? response.data.response : null;
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

    async #getPosition(id) {
        const peticion = new Promise((resolve) => {
            resolve(
                // OBTENER LA INFORMACION DE LA POSICION

                $.ajax({
                    type: 'GET',
                    url: URL_API_CAMPUS + 'api/getposicion/' + id,
                    dataType: 'json',
                    async: true,
                    beforeSend: function () { },
                    success: function (response) {
                        return (response.meta.status == "SUCCESS") ? response.data.response : null;
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