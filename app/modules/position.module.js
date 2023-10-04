import { PositionModel } from '../models/position.model.js';
import { PositionService } from '../services/position.services.js';
import { EmpleadoModule } from './empleado.module.js';
import { NotificationModule } from './notifications.module.js';
import { MESSAGES_LIST } from '../../environments/config.env.js';

export class PositionModule {
    constructor() {
        this.position = null;
        this.Position = new PositionModel();
        this.positionService = new PositionService();
        this.empleadoModule = new EmpleadoModule();
        this.notificationCenter = new NotificationModule();
        this.timerValidateCodigo = null; // timerGlobal
        this.modalAsignar = null;
        this.modalEditar = null;
        this.showConsoleMessage = (success, msj) => { (success) ? console.log(msj) : console.warn(msj); }
    }
    
    async setCampusPosition(id) {
        this.position = id;
        this.getPositionData(this.position);
    }

    // LOAD POSITION DATA
    async getPositionData(position) {
        console.log('Loading position: ' + position);
        this.positionService.setPositionName(String(position));
        const peticionPosicion = await this.positionService.getPositionByName();
        const positionData = (peticionPosicion.data.response.length > 0) ? peticionPosicion.data.response[0] : null;
        this.loadPositionData(positionData);
    }

    loadPositionData(data) {
        this.Position.setPosition(data);
        this.Position.showInformation();

        if (this.Position) {
            this.showModalAsignarPosicion(this.Position.asignado);
        } else {
            console.warn(MESSAGES_LIST.position.errorCargarInfo.msj);
        }
    }

    // LOAD ASIGNAR || ACTUALIZAR || CANCELAR
    showModalAsignarPosicion(asignado) {
        const idEmpleado = this.Position.idEmpleado;

        $.ajax({
            type: 'GET',
            url: './assets/modales/modalAsignarPosicion.html',
            dataType: 'html',
            async: true,
            cache: false,
            success: (modalHtml) => {
                $("#modalInformation").html(modalHtml);
                const formBusqueda = document.querySelector('#formAsignarLugar');
                this.modalAsignar = new bootstrap.Modal(document.getElementById('modalAsignarPosicion'), {
                    keyboard: false,
                    backdrop: 'static'
                });

                const tiposTrabajo = JSON.parse(localStorage.getItem('TIPOSTRABAJO'));

                formBusqueda.addEventListener('submit', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                }, false);
                
                tiposTrabajo.forEach((item) => {
                    $("#infoTipoTrabajo").append($('<option>', {
                        value: item.id,
                        text: item.name
                    }));
                });

                this.#loadGrupoData(this.Position);
                this.#loadValidateEvents();

                if (idEmpleado > 0 && asignado) { 
                    this.#loadEmpleadoAsignado(idEmpleado);
                    $('.visibleSinAsignar').addClass('hidden');
                }else {
                    $('.visibleAsignado').addClass('hidden');
                }
                $('#modalAsignarPosicion > div > div > div.modal-header').addClass(MESSAGES_LIST.modal.alta[asignado].bg);
                $('#modalAsignarPosicionLabel').text(MESSAGES_LIST.modal.alta[asignado].title);

                this.modalAsignar.show();
            },
            error: (status, error) => { console.warn(status + ' ' + error.responseText); }
        }).done(function () { });
    }

    #loadEmpleadoAsignado(idEmpleado) {
        (idEmpleado > 0) ? this.#getEmpleado(idEmpleado) : console.warn(MESSAGES_LIST.empleado.idIncorrecto.msj);
    }


    #loadGrupoData(Position) {
        if (Position.id) {
            $("#infoTipoLugarTrabajo").text(Position.nombreTipo);
            $("#infoGrupoLugarTrabajo").text(Position.nombreGrupo);
            $("#infoPosicionLugarTrabajo").text(Position.nombrePosicion);
            $("#infoEdificioTrabajo").text(Position.edificio);
        } else {
            $(".grupoData").text('');
        }
    }

    async #getEmpleado(id){
        this.empleadoModule.setEmpleadoCampus(id);
        await this.empleadoModule.getEmpleadoCampus();
        const employee = this.empleadoModule.getEmpleado;
        const Pos = this.Position;
        console.log(employee);
        if (!employee.idEmpleado) {
            this.resetSearchEmployee('noExiste');
            return 0;
        }
        if (!employee.empleadoEmaiL) {
            this.resetSearchEmployee('noExisteEmail');
        }

        // Bootstrap validation styles to
        const formBusqueda = document.querySelector('#formAsignarLugar');
        formBusqueda.addEventListener('submit', function (event) {
            formBusqueda.classList.add('was-validated');
            event.preventDefault();
            event.stopPropagation();

            if (!Pos.id) {
                console.warn(MESSAGES_LIST.position.errorCargarInfo.msj);
                return 0;
            } else if (!formBusqueda.checkValidity()) {
                console.warn(MESSAGES_LIST.position.camposIncompletos.msj);
                return 0;
            } else {
                formConstrols(employee, Pos, false);
            }
        });

        const formConstrols = (Employee, Position, MOV) => {
            (!Employee) ? console.warn("Error en datos de empleado") : this.#loadAsignarLugar(Employee, Position, MOV);
        }

        const btnCancelarPosition = document.getElementById("btnCancelarLugar");
        const loadCancelarPosition = () => { formConstrols(employee, Pos, true) };

        (employee.idEmpleado > 0 && Pos.asignado) ? btnCancelarPosition.addEventListener("click", loadCancelarPosition, false) : btnCancelarPosition.removeEventListener("click", loadCancelarPosition, false);
    }

    resetSearchEmployee(action){
        // notification
        let data = {
            success: false,
            message: MESSAGES_LIST.empleado[action].msj,
            bg: MESSAGES_LIST.empleado[action].bg
        };
        this.#notificationCenter(data);
    }
    
    async #loadAsignarLugar(Employee, Position, CANCEL_POS){
        'use strict';

        if (!Employee.empleadoEmaiL) {
            this.resetSearchEmployee('noExisteEmail');
            return 0;
        }

        const btnConfirmar = document.getElementById("btn-confirmar-position");
        const modalConfirmar = new bootstrap.Modal(document.getElementById('modalConfirmar'), {
            keyboard: false,
            backdrop: 'static'
        });

        // enviar codigo al email
        const enviarCodigoEmail = async () => {
            this.#showButtonLoading('#btn-confirmar-position');
            const codigo = await confirmarPosicion('REGISTRAR', Employee, Position);
            console.log(codigo);
            // convertQueryJSON
            const response = convertQueryJSON(codigo, 'consulta');
            if (response.CONSULTA && response.REGISTRADO) {
                // codigo registrado  
                // togle visual user
                toogleConfirmMessages(true);    // mostrar inputs codigo
                resetButtonPosition();
                // 15 minutos para verificar
                timerVerification();
                btnConfirmar.removeEventListener("click", enviarCodigoEmail, false);
                btnConfirmar.addEventListener("click", confirmarCodigoEmail, false);
            }

            // notification
            let data = {
                success: response.REGISTRADO,
                message: MESSAGES_LIST.codigo.enviado[response.REGISTRADO].msj,
                bg: MESSAGES_LIST.codigo.enviado[response.REGISTRADO].bg
            };
            this.#notificationCenter(data);
            resetButtonPosition();
        }

        // confirmar posicion
        const confirmarPosicion = async (TIPO_QUERY, Employee, Position) => {

            let code = 0;
            if (TIPO_QUERY === 'CONFIRMAR') {
                const inputElements = [...document.querySelectorAll('input.code-input')];
                code = inputElements.map(({ value }) => value).join('');
                console.log(code);
            }

            if (TIPO_QUERY === 'CONFIRMAR' && !code) { return 0; }

            const tipoMovimiento = ['A', 'B', 'C'];

            const data = {
                TIPO_QUERY: TIPO_QUERY,
                idEmpleado: Employee.idEmpleado,
                emailEmpleado: Employee.empleadoEmaiL,
                idCentro: Employee.numeroCentro,
                idPosition: Position.id,
                tipoMovimiento: tipoMovimiento[+ Position.asignado + CANCEL_POS],
                idPositionNew: 0,
                codigo: code
            };

            console.log(data);

            this.positionService.setPosition(parseInt(Position.id));
            this.positionService.genNewCode(data);
            const peticionCodigo = await this.positionService.setCodigoConfirmacion();
            return (peticionCodigo.data.response) ? peticionCodigo.data.response : null;
        }

        // confirmar codigo recibido
        const confirmarCodigoEmail = async () => {

            this.#showButtonLoading('#btn-confirmar-position');
            const confirmar = await confirmarPosicion('CONFIRMAR', Employee, Position);
            console.log(confirmar);
            resetButtonPosition();

            if (!confirmar) { return 0;}
            
            // convertQueryJSON
            const response = convertQueryJSON(confirmar, 'consulta');
            if (response.CONSULTA && response.CONFIRMADO) {
                // GUARDANDO EL LUGAR
                loadAsignarLugarTrabajo();
                // HIDE MODAL
                resetTimerVerification();
                //employeeExtraData(false);
                $('#modalConfirmar').modal('hide');
            }
            // notification
            let data = {
                success: response.CONFIRMADO,
                message: MESSAGES_LIST.codigo.confirmado[response.CONFIRMADO].msj,
                bg: MESSAGES_LIST.codigo.confirmado[response.CONFIRMADO].bg
            };
            this.#notificationCenter(data);
        }

        // GUARDANDO EL LUGAR
        const asignarPosition = async () => {
            const tipoMovimiento = [1, 1, 0];
            const mov = parseInt(tipoMovimiento[+ Position.asignado + CANCEL_POS]);
            const asignarPosicion = await this.positionService.setPositionAsignada(mov);// opuesto al booleano
            return (asignarPosicion.data.response) ? asignarPosicion.data.response : null;
        }

        // RELACIONANDO EL USUARIO CON EL LUGAR
        const asignarUserPosition = async (employee) => {
            const asignarUserPosicion = await this.positionService.setUserPosition(employee);
            return (asignarUserPosicion.data.response) ? asignarUserPosicion.data.response : null;
        }

        const loadAsignarLugarTrabajo = async () => {
            const asignar = await asignarPosition();
            const response = convertQueryJSON(asignar, 'setPosicion');
            const type = [false, true, 'cancel'];
            const mov = type[+ response.ASIGNADO + CANCEL_POS]

            // notification
            let data = {
                success: response.ASIGNADO,
                message: MESSAGES_LIST.position.positionAsignada[mov].msj,
                bg: MESSAGES_LIST.position.positionAsignada[mov].bg
            };
            this.#notificationCenter(data);

            if (CANCEL_POS) { loadAsignacionFinal(true); return 0; }
            else if (response.ASIGNADO) { loadRelacionarUsuarioPosicion(response); }
        }

        const loadRelacionarUsuarioPosicion = async (status) => {

            const employee = this.empleadoModule.getEmpleado;
            const relacionar = await asignarUserPosition(employee);
            const response = convertQueryJSON(relacionar, 'setUserPosition');

            //let response = { ASIGNADO : true};
            // notification
            let data = {
                success: response.ASIGNADO,
                message: MESSAGES_LIST.position.userPositionAsignada[response.ASIGNADO].msj,
                bg: MESSAGES_LIST.position.userPositionAsignada[response.ASIGNADO].bg
            };
            this.#notificationCenter(data);

            (response.ASIGNADO) ? loadAsignacionFinal(response) : 0;
        }

        const loadAsignacionFinal = (status) => {
            modalConfirmar.hide();
            this.modalAsignar.hide();
            console.log(status);

            setColorPosition(Position.getNombrePosicion());
            sendNotificationUserAsignado(status);
        }

        const setColorPosition = (pos) => {
            if (CANCEL_POS) {
                $("#" + pos + " > .asiento").removeClass('asientoOcupado');
                $("#" + pos + " > .respaldo").removeClass('respaldoOcupado');
            }else {
                $("#" + pos + " > .asiento").addClass('asientoOcupado');
                $("#" + pos + " > .respaldo").addClass('respaldoOcupado');
            }
        }

        const sendNotificationUserAsignado = async (status) => {
            const employee = this.empleadoModule.getEmpleado;

            const asunto = MESSAGES_LIST.position.userPositionConfirmar[CANCEL_POS].asunto;
            let body = MESSAGES_LIST.position.userPositionConfirmar[CANCEL_POS].msj;
            const mensaje = body.replace('%NOMBRE%', employee.getNombreCompleto());

            let extra = MESSAGES_LIST.position.userPositionConfirmar[CANCEL_POS].adicional;
            const adicional = extra.replace('%POSITION%', Position.getNombrePosicion()).replace('%GRUPO% ', Position.getNombreGrupo()).replace('%EDIFICIO% ', Position.getEdificio());

            const data = {
                asunto: asunto,
                mensaje: mensaje,
                mensajeAdicional: adicional,
                emailEmpleado: employee.getEmpleadoEmaiL()
            };

            (status) ? await this.positionService.setEmailConfirmacion(data) : false;
        }

        // timer 15 minutos para verificar
        const timerVerification = () => {
            const dateInicio = new Date();
            let minutosCorriendo = 0, segundosCorriendo = 0;
            let minutosRestantes, segundosRestantes, now;

            // INTERVALO DE TIEMPO CADA SEGUNDO HASTA AGOTAR 15 MINUTOS DE ESPERA
            this.timerValidateCodigo = setInterval(() => {

                now = new Date();
                minutosCorriendo = Math.floor(((now - dateInicio) % (1000 * 60 * 60)) / (1000 * 60));
                segundosCorriendo = Math.floor(((now - dateInicio) % ((1000 * 60)) / 1000));
                minutosRestantes = 14 - minutosCorriendo;
                segundosRestantes = 60 - segundosCorriendo;

                $("#minutosRestantes").text(minutosRestantes);
                $("#secondsRestantes").text(segundosRestantes);
                if (minutosCorriendo >= 15 && segundosCorriendo >= 0) {
                    
                    // notification
                    let data = {
                        message: MESSAGES_LIST.codigo.confirmado['tiempoExcedido'].msj,
                        bg: MESSAGES_LIST.codigo.confirmado['tiempoExcedido'].bg
                    };
                    this.#notificationCenter(data);
                    console.warn(MESSAGES_LIST.codigo.confirmado['tiempoExcedido'].msj);

                    resetTimerVerification();
                    return;
                }
            }, 1000);
        }

        const resetTimerVerification = () => {
            clearInterval(this.timerValidateCodigo);
            btnConfirmar.removeEventListener("click", confirmarCodigoEmail, false);
            btnConfirmar.addEventListener("click", enviarCodigoEmail, false);

            $(".textTime").text('00'); // reset time
            $('.code-input').val('');
            toogleConfirmMessages(false);
        }

        // UITILS - VISUAL
        const resetButtonPosition = () => {
            $('#btn-confirmar-position').prop("disabled", false);
            $('#btn-confirmar-position').html('<i class="fa fa-check"></i> Confirmar');
        }

        const toogleConfirmMessages = (status) => {
            if (status) {
                $('#modalConfirmar p.alert').hide();
                $('#CodigoConfirmacionInfo').removeClass("hidden"); //show card
            } else {
                $('#modalConfirmar p.alert').show();
                $('#CodigoConfirmacionInfo').addClass("hidden"); // hidde card
            }
        }

        // SCRIPT para recorrer el resultado de un query y obtener los datos booleanos
        const convertQueryJSON = (response, consulta) => {
            let result = [];
            let row = new Object();
            $.each(response[consulta][0], function (rowName, rowValue) {
                row[rowName] = convertStrtoBool(rowValue);
            });
            result.push(row);
            return Object.freeze(row);
        }

        // convert bool values
        const convertStrtoBool = (response) => { return Boolean(parseInt(response)); }
        const convertInttoBool = (response) => { return Boolean(response); }

        const employeeExtraData = (action) => {
            $("#idEmpleado").prop("disabled", action);
            $("#idLiderTrabajo").prop("disabled", action);
            $("#infoTipoTrabajo").prop("disabled", action);

            const lider = (action) ? $("#idLiderTrabajo").val() : null;
            const tipotrabajo = (action) ? $("#infoTipoTrabajo").val() : null;
            this.empleadoModule.setEmpleadoExtraDataCampus(lider, tipotrabajo);
        }

        const confirmModal = async () => {
            // alta || cambio || baja
            const type = ["alta", "cambio", "baja"];
            let mov = type[+ Position.asignado + CANCEL_POS];

            $("#modalConfirmarLabel").text(MESSAGES_LIST.modal.confirmar[mov].title);
            $("#msjConfirmarModal").addClass(MESSAGES_LIST.modal.confirmar[mov].bg);
            $("#msjConfirmarModal").text(MESSAGES_LIST.modal.confirmar[mov].msj);
            $("#btn-confirmar-position").addClass(MESSAGES_LIST.modal.confirmar[mov].btn);

            employeeExtraData(true);
            resetTimerVerification();
            modalConfirmar.show();

            $('#modalConfirmar').on('hidden.bs.modal', function (e) {
                employeeExtraData(false);
                $("#modalConfirmarLabel").text('');
                $("#msjConfirmarModal").removeClass().addClass('alert');
                $("#msjConfirmarModal").text('');
                $("#btn-confirmar-position").removeClass().addClass('btn btn-sm');
            });
        }
        confirmModal();
    }



    #loadValidateEvents() {
        const numeros = /[0-9]/;
        const letrasMinusculas = /[a-z]/;
        const letrasMayusculas = /[A-Z]/;

        // validacion de campos
        (function (a) {
            a.fn.validCaracteresEspeciales = function (b) {
                a(this).on({
                    keypress: function (a) {
                        let c = a.which,
                            d = a.keyCode,
                            e = String.fromCharCode(c).toLowerCase(),
                            f = b;
                        if (event.keyCode == 32) {
                            event.preventDefault();
                        }
                        if (String.fromCharCode(c).toLowerCase() != '¡') { (-1 != f.indexOf(e) || 9 == d || 37 != c && 37 == d || 39 == d && 39 != c || 8 == d || 46 == d && 46 != c) && 161 != c || a.preventDefault() }
                    }
                })
            }
        }(jQuery));
        // ("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~@#$%&/*-+\\|/_=[]{}',.;:<>¿?!\¡");
        $("#idEmpleado").validCaracteresEspeciales("0123456789");
        $("#idLider").validCaracteresEspeciales("0123456789");

        // evento
        $("#idEmpleado").blur(() => {
            console.log($("#idEmpleado").val());
        });

        $("#btnGetEmpleado").on('click', () => {
            const idEmpleado = parseInt($("#idEmpleado").val());
            (idEmpleado > 0) ? this.#getEmpleado(idEmpleado) : console.warn(MESSAGES_LIST.empleado.idIncorrecto.msj);
        });

        // eliminando la opcion de pegar en el input
        $(".inputData").on('paste', (event) => {
            event.preventDefault();
            return false;
        });

        $(".inputData").on('drop', (event) => {
            event.preventDefault();
            return false;
        });

        // BOTONES MODAL CONFIRMAR
        $('.btn-confirmar-position').click(() => {
            // ...
        });
        $('#modalConfirmar .btn-cancelar').click(() => {
            $("#collapseCodigoConfirmacion").removeClass("show");
        });


        // INPUTS CODIGO CONFIRMACION
        const inputElements = [...document.querySelectorAll('input.code-input')];

        inputElements.forEach((ele, index) => {
            ele.addEventListener('keydown', (e) => {
                // if the keycode is backspace & the current field is empty
                // focus the input before the current. Then the event happens
                // which will clear the "before" input box.
                (e.keyCode === 8 && e.target.value === '') ? inputElements[Math.max(0, index - 1)].focus() : 0;
            });

            $(ele).validCaracteresEspeciales("0123456789");

            ele.addEventListener('input', (e) => {
                // take the first character of the input
                // this actually breaks if you input an emoji like ....
                // but I'm willing to overlook insane security code practices.
                const [first, ...rest] = e.target.value;
                e.target.value = first ?? ''; // first will be undefined when backspace was entered, so set the input to ""
                const lastInputBox = index === inputElements.length - 1;
                const didInsertContent = first !== undefined;
                if (didInsertContent && !lastInputBox) {
                    // continue to input the rest of the string
                    inputElements[index + 1].focus();
                    inputElements[index + 1].value = rest.join('');
                    inputElements[index + 1].dispatchEvent(new Event('input'));
                }
            });
        });

    }

    #showButtonLoading(button) {
        $(button).prop("disabled", true);
        $(button).html('<span class="spinner-border spinner-border-sm"></span> Cargando...');
    }

    #notificationCenter(data){
        let idToaster;
        idToaster = 'toast' + Math.floor(Math.random() * 1000);
        // notification
        let args = {
            id: idToaster,
            title: 'Notificación',
            message: data.message,
            background: data.bg,
            animation: true,
            autohide: true,
            delay: 5000
        }
        this.notificationCenter.setNewToast(args);
        const toast = this.notificationCenter.getToast;

        $("#notificationCenter").append(toast);

        $('#' + idToaster).toast({
            animation: args.animation,
            delay: args.delay
        });

        $('#' + idToaster).toast('show');

        this.showConsoleMessage(data.success, data.message);

    }
    
}