import { URL_API_CAMPUS, URL_CAMPUS } from '../../environments/config.env.js';
export class EmpleadoService {
    constructor() { 
        this.setNumeroEmpleado(null);
        this.setNombreEmpleado(null);
    }

    getNumeroEmpleado() { return this.idEmpleado; }
    setNumeroEmpleado(idEmpleado) { this.idEmpleado = idEmpleado; }

    getNombreEmpleado() { return this.nombreEmpleado; }
    setNombreEmpleado(nombreEmpleado) { this.nombreEmpleado = nombreEmpleado; }

    setEmpleadoName(name) {
        this.setNombreEmpleado(name);
    }

    getEmpleadoData() {
        return this.#getEmpleado(this.idEmpleado);
    }

    getEmpleadoCampus() {
        return this.#getEmpleadoCampus(this.idEmpleado);
    }

    get getContactoEmpleado() {
        let contacto = this.#getContacto('IDCOPPEL', this.nombreEmpleado);
        (!contacto) ? contacto = this.#getContacto('NOMBRE', this.nombreEmpleado) : false;
        return contacto;
    }

    #getEmpleadoData() {
        const data = {
            numeroEmpleado: this.getNumeroEmpleado,
            nombreEmpleado: this.getNombreEmpleado,
        }
        return data;
    }

    showInformation(format = true) {
        console.info(`${this.#getEmpleadoData()}`);
    }


    async #getEmpleado(idEmpleado) {
        // loading empleado
        const data = { idEmpleado: idEmpleado };
        const peticion = new Promise((resolve) => {
            resolve(
                // OBTENER LA INFORMACION DEL EMPLEADO
                $.ajax({
                    type: 'POST',
                    url: URL_API_CAMPUS + 'api/getempleado',
                    dataType: 'json',
                    data: JSON.stringify(data),
                    async: true,
                    beforeSend: function () {},
                    success: function (response) {
                        //(response.meta.status == "SUCCESS") ? this.#setEmpleadoData(response.data.response) : null;
                        (response.meta.status != "SUCCESS") ? console.warn(response.meta) : 0;
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

    async #getEmpleadoCampus(idEmpleado) {
        // loading empleado
        const data = { idEmpleado: idEmpleado };
        const peticion = new Promise((resolve) => {
            resolve(
                // OBTENER LA INFORMACION DEL EMPLEADO
                $.ajax({
                    type: 'POST',
                    url: URL_API_CAMPUS + 'api/getempleadocampus',
                    dataType: 'json',
                    data: JSON.stringify(data),
                    async: true,
                    beforeSend: function () { },
                    success: function (response) {
                        //(response.meta.status == "SUCCESS") ? this.#setEmpleadoData(response.data.response) : null;
                        (response.meta.status != "SUCCESS") ? console.warn(response.meta) : 0;
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

    async #getContacto(query, nameEmpleado) {
        const data = {
            TIPO_QUERY: query,
            idBusqueda: this.idEmpleado,
            strBusqueda: nameEmpleado
        };

        const peticion = new Promise((resolve) => {
            resolve(
                $.ajax({
                    type: 'POST',
                    url: URL_API_CAMPUS + 'api/getcontactos',
                    dataType: 'json',
                    data: JSON.stringify(data),
                    async: true,
                    beforeSend: function () { },
                    success: function (response) {
                        (response.meta.status != "SUCCESS") ? console.warn(response.meta) : 0;
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