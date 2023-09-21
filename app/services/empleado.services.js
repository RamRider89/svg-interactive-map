import { URL_API_CAMPUS, URL_CAMPUS } from '../../environments/config.env.js';
export class EmpleadoService {
    constructor() { 
        this.setNumeroEmpleado(null);
        this.setNombreEmpleado(null);
    }

    getEmpleadoData() {
        return this.#getEmpleado(this.idEmpleado);
    }

    get getContactoEmpleado() {
        return this.#getContacto(this.nombreEmpleado);
    }

    getNumeroEmpleado() { return this.idEmpleado; }
    setNumeroEmpleado(idEmpleado) { this.idEmpleado = idEmpleado; }

    getNombreEmpleado() { return this.nombreEmpleado; }
    setNombreEmpleado(nombreEmpleado) { this.nombreEmpleado = nombreEmpleado; }

    setEmpleadoName(name) {
        this.setNombreEmpleado(name);
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
                    error: function (request, status, error) {console.warn(error.responseText);}
                }).done(function () { }),
            );
        });

        peticion.then((response) => { return 1; }).catch((err) => { console.error(err); });
        return peticion;
    }

    async #getContacto(nameEmpleado) {
        const data = {
            TIPO_QUERY: 'nombre',
            idBusqueda: 0,
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
                        console.warn(error.responseText);
                    }
                }).done(function () { }),
            );
        });
        
        peticion.then((response) => { return 0; }).catch((err) => {
            console.error(err);
        });

        return peticion;
    }

}