import { URL_API_CAMPUS, URL_CAMPUS } from '../../environments/config.env.js';

export class PositionService {
    constructor() { 
        this.position = null;
    }

    setPositionName(position) {
        this.position = position;
    }

    setPosition(id) {
        this.id = id;
    }

    genNewCode(data){

        this.TIPO_QUERY = data.TIPO_QUERY;
        this.idEmpleado = data.idEmpleado;
        this.emailEmpleado = data.emailEmpleado;
        this.idCentro = data.idCentro;
        this.tipoMovimiento = data.tipoMovimiento;
        this.idPositionNew = data.idPositionNew;
        this.codigo = data.codigo;
    }

    getPositionByName() {
        return this.#getPositionByName(this.position);
    }

    getPosition() {
        return this.#getPosition(this.id);
    }

    getAllPositions(status){
        return this.#getAllPositions(status);
    }

    setCodigoConfirmacion() {
        return this.#setCodigoConfirmacion(this.id);
    }

    setEmailConfirmacion(args) {
        return this.#setEmailConfirmacion(args);
    }

    setPositionAsignada(mov) {
        return this.#setPosition(this.id, mov);
    }

    setUserPosition(employee) {
        return this.#setUserPosition(employee);
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
                        console.warn(error);
                    }
                }).done(function () { }),
            );
        });

        peticion.then((response) => { return 0; }).catch((err) => { console.error(err.responseText); });
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
                        console.warn(error);
                    }
                }).done(function () { }),
            );
        });

        peticion.then((response) => { return 0; }).catch((err) => { console.error(err.responseText); });
        return peticion;
    }

    async #getAllPositions(status) {
        const peticion = new Promise((resolve) => {
            resolve(
                // OBTENER LA INFORMACION DE LAS POSICIONES
                $.ajax({
                    type: 'POST',
                    url: URL_API_CAMPUS + 'api/getposiciones/',
                    dataType: 'json',
                    data: JSON.stringify(status),
                    async: true,
                    beforeSend: function () { },
                    success: function (response) {
                        return (response.meta.status == "SUCCESS") ? response.data.response : null;
                    },
                    error: function (request, status, error) {
                        console.warn(error);
                    }
                }).done(function () { }),
            );
        });

        peticion.then((response) => { return 0; }).catch((err) => { console.error(err.responseText); });
        return peticion;
    }


    async #setCodigoConfirmacion(id) {
        const data = {
            TIPO_QUERY: this.TIPO_QUERY,
            idEmpleado: this.idEmpleado,
            emailEmpleado: this.emailEmpleado,
            idCentro: this.idCentro,
            idPosition: id,
            tipoMovimiento: this.tipoMovimiento,
            idPositionNew: this.idPositionNew,
            codigo: this.codigo
        };

        const peticion = new Promise((resolve) => {
            resolve(
                // OBTENER LA INFORMACION DE LA POSICION

                $.ajax({
                    type: 'POST',
                    url: URL_API_CAMPUS + 'api/codigoconfirmacion/',
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

        peticion.then((response) => { return response; }).catch((err) => { console.error(err); });
        return peticion;
    }

    async #setEmailConfirmacion(data) {

        const peticion = new Promise((resolve) => {
            resolve(
                // ENVIAR EMAIL DE CONFIRMACION
                $.ajax({
                    type: 'POST',
                    url: URL_API_CAMPUS + 'api/emailconfirmacion/',
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

        peticion.then((response) => { return response; }).catch((err) => { console.error(err); });
        return peticion;
    }

    async #setPosition(id, mov) {
        const data = {
            TIPO_MOV: mov,
            idPosition: id,
            idEmpleado: this.idEmpleado
        };

        const peticion = new Promise((resolve) => {
            resolve(
                // ASIGNANDO LA POSICION
                $.ajax({
                    type: 'POST',
                    url: URL_API_CAMPUS + 'api/setposition/',
                    dataType: 'json',
                    data: JSON.stringify(data),
                    async: true,
                    beforeSend: function () { },
                    success: function (response) {
                        return (response.meta.status == "SUCCESS") ? response.data.response : null;
                    },
                    error: function (request, status, error) {
                        console.warn(error);
                    }
                }).done(function () { }),
            );
        });

        peticion.then((response) => { return 0; }).catch((err) => { console.error(err.responseText); });
        return peticion;
    }

    // relacionar posicion con datos de usuario
    async #setUserPosition(employee) {
        const peticion = new Promise((resolve) => {
            resolve(
                // ASIGNANDO LA POSICION
                $.ajax({
                    type: 'POST',
                    url: URL_API_CAMPUS + 'api/setuserposition/',
                    dataType: 'json',
                    data: JSON.stringify(employee),
                    async: true,
                    beforeSend: function () { },
                    success: function (response) {
                        return (response.meta.status == "SUCCESS") ? response.data.response : null;
                    },
                    error: function (request, status, error) {
                        console.warn(error);
                    }
                }).done(function () { }),
            );
        });

        peticion.then((response) => { return 0; }).catch((err) => { console.error(err.responseText); });
        return peticion;
    }

}