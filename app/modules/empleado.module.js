import { EmpleadoService } from '../services/empleado.services.js';
import { LiderService } from '../services/lider.services.js';
import { EmpleadoModel } from '../models/empleado.model.js';

export class EmpleadoModule {
    constructor() {
        this.id = null;
        this.empleadoService = new EmpleadoService();
        this.Empleado = new EmpleadoModel();
    }

    setEmpleadoCampus(id) {
        this.id = id;
    }

    getEmpleadoCampus() {
        this.#getEmpleado(this.id);
    }

    get getEmpleado(){
        return this.Empleado;
    }


     // GET EMPLEADO
    async #getEmpleado(idEmpleado) {
        // OBTENER AL EMPLEADO
        console.info('Loading data from employee: ' + idEmpleado);

        this.#showButtonLoading("#btnGetEmpleado");
        this.#setProgresBarLoad('#progresBar', 30);

        // GET EMPLEADO DATA
        this.empleadoService.setNumeroEmpleado(parseInt(idEmpleado));
        const peticionEmpleado = await this.empleadoService.getEmpleadoData();
        const empleadoData = (peticionEmpleado.data.response.length > 0) ? peticionEmpleado.data.response[0] : null;

        // EMPLEADO MODEL
        this.Empleado.setEmpleado(empleadoData);
        this.#setProgresBarLoad('#progresBar', 60);

        // GET EMAIL
        this.empleadoService.setEmpleadoName(this.Empleado.getNombreCompleto());
        const peticionContacto = await this.empleadoService.getContactoEmpleado;
        const contactoData = (peticionContacto.data.response.length > 0) ? peticionContacto.data.response[0] : null;
        const empleadoEmail = (contactoData) ? contactoData.email : null;
        this.Empleado.setEmpleadoEmaiL(empleadoEmail);
        this.#setProgresBarLoad('#progresBar', 80);

        this.loadEmpleadoData(this.Empleado, contactoData);

    }

    loadEmpleadoData(employee, contacto) {
        if (employee) {
            // cargando datos en el modal
            $("#infoEmpresa").text(employee.nombreEmpresa);
            $("#infoCentro").text(employee.numeroCentro);
            $("#infoCentroName").text(employee.nombreCentro);
            $("#infoEmpleadoNombre").text(employee.nombreEmpleado);
            $("#infoEmpleadoPuesto").text(employee.nombrePuesto);
            $("#infoEmpleadoEmail").text(employee.empleadoEmaiL);
            $("#infoEmpleadoTelefono").text(employee.numeroTelefono);
            $("#infoEmpleadoCumple").text(employee.fechaCumpleanos);
            $("#infoEmpleadoNomGerente").text(employee.nombreGerente);
            $("#infoEmpleadoNumGerente").text(employee.numeroGerente);

            // GET LIDERES 
            this.#getLideresCentro(employee.numeroCentro);

            this.#saveDataEmployee(employee, contacto);

            // // $("#idLider")
            // loadAsignarLugar(employee);

        } else {
            console.warn('El empleado no existe');
            this.#resetModalInfoEmpleado();
            return null;
        }
        this.#showButtonSearch("#btnGetEmpleado");
    }

    #saveDataEmployee(employee, contacto) {
        this.#setProgresBarLoad('#progresBar', 100);
        localStorage.setItem("EMPLEADODATA", JSON.stringify(employee));
        localStorage.setItem("CONTACTO", JSON.stringify(contacto));
        employee.showInformation();
        console.log(contacto);
    }


    // POST | GET EMAIL EMPLEADO
    async getEmailEmpleado(name) {


    }

    // POST | GET LIDER CENTRO
    async #getLideresCentro(centro) {
        const liderService = new LiderService(parseInt(centro));
        const peticionLideresCentro = await liderService.getLideresCentro;
        const lideresCentro = this.#loadLideresCentro((peticionLideresCentro.data.response.length > 0) ? peticionLideresCentro.data.response : null, centro);

        console.log(lideresCentro);
        this.#setProgresBarLoad('#progresBar', 90);
    }

    #loadLideresCentro(lideresCentro, idCentro) {

        this.#resetSelect('#idLiderTrabajo');
        if (!lideresCentro) {
            $("#idLiderTrabajo").append($('<option>', {
                value: 0,
                text: 'Sin lider asignado'
            }));
            return null;
        }

        let listaLideres = []
        let idLider, nameLider;
        lideresCentro.forEach((item) => {
            idLider = parseInt(String(item.NumeroEmpleado).trim());
            nameLider = this.#namesToTitleCase(item.Nombre, item.ApellidoPaterno, item.ApellidoMaterno);

            $("#idLiderTrabajo").append($('<option>', {
                value: idLider,
                text: nameLider
            }));

            listaLideres.push({
                numeroCentro: idCentro,
                numeroEmpleado: idLider,
                nombreEmpleado: nameLider
            });
        });

        return listaLideres;
    }

    //UTILS
    // VISUAL ANIMATIONS
    #resetModalInfoEmpleado() {
        $(".empleadoData").text('');
        $("#infoTipoTrabajo").val('');
    }

    #showButtonLoading(button) {
        $(button).prop("disabled", true);
        $(button).html('<span class="spinner-border spinner-border-sm"></span> Cargando...');
    }

    #showButtonSearch(button) {
        $(button).prop("disabled", false);
        $(button).html('<i class="fa fa-search"></i> Buscar');
    }

    #setProgresBarLoad(progresbar, load) {
        (load <= 50) ? $(String(progresbar) + ' > .progress-bar').show() : 0

        $(String(progresbar) + ' > .progress-bar').css('width', String(load) + '%');

        (load >= 100) ? this.#hideProgresBar(progresbar) : 0
    }

    #hideProgresBar(progresbar) {
        setTimeout(() => {
            $(String(progresbar) + ' > .progress-bar').css("width", "0%").hide();
        }, 1000);
    }

    #resetSelect(select) {
        $(String(select)).html('').append($('<option>', {
            value: '',
            text: 'Selecciona..'
        }));
    }

    #namesToTitleCase(name, apellPat, apellMat) {
        const nameCompleto = String(String(name).trim() + ' ' + String(apellPat).trim() + ' ' + String(apellMat).trim()).toLowerCase();
        return nameCompleto.replace(/(?:^|\s)\w/g, function (match) {
            return match.toUpperCase();
        });
    }
}