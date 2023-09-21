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
        this.bodyToaster = null;
        this.backgroundToaster = null;

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
            (this.Position.asignado) ? 0 : this.showModalAsignarPosicion();
        } else {
            console.warn(MESSAGES_LIST.position.errorCargarInfo.msj);
        }
    }

    // LOAD ASIGNAR O CAMBIAR
    showModalAsignarPosicion() {
        $.ajax({
            type: 'GET',
            url: './assets/modales/modalAsignarPosicion.html',
            dataType: 'html',
            async: true,
            cache: false,
            success: (modalHtml) => {
                $("#modalInformation").html(modalHtml);
                const modalAsignar = new bootstrap.Modal(document.getElementById('modalAsignarPosicion'), {
                    keyboard: false,
                    backdrop: 'static'
                });

                const tiposTrabajo = JSON.parse(localStorage.getItem('TIPOSTRABAJO'));
                tiposTrabajo.forEach((item) => {
                    $("#infoTipoTrabajo").append($('<option>', {
                        value: item.id,
                        text: item.name
                    }));
                });

                this.#loadGrupoData(this.Position);
                this.#loadValidateEvents();

                modalAsignar.show();
            },
            error: (status, error) => { console.warn(status + ' ' + error.responseText); }
        }).done(function () { });
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

    #getEmpleado(id){
        this.empleadoModule.setEmpleadoCampus(id);
        this.empleadoModule.getEmpleadoCampus();
        const employee = this.empleadoModule.getEmpleado;
        this.#loadAsignarLugar(employee, this.Position);
    }

    async #loadAsignarLugar(Employee, Position){
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function () {
            'use strict'
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            const form = document.querySelector('#formAsignarLugar');

            form.addEventListener('submit', function (event) {
                form.classList.add('was-validated');

                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    console.warn(MESSAGES_LIST.position.camposIncompletos.msj);
                    return;
                } else {
                    event.preventDefault();
                    event.stopPropagation();
                    console.log("FORM OK");
                    confirmModal();

                    //(confirmar) ? console.log("guardar") : console.log("cencelo");
                }
            }, false);
        })();

        const btnConfirmar = document.getElementById("btn-confirmar-position");

        const confirmModal = async () => {
            resetTimerVerification();
            $('#modalConfirmar').modal('show');
        }

        // enviar codigo al email
        const enviarCodigoEmail = async () => {
            this.#showButtonLoading('#btn-confirmar-position');
            const codigo = await confirmarPosicion('REGISTRAR', Employee, Position);
            console.log(codigo);

            if (Boolean(parseInt(codigo.consulta[0].CONSULTA)) && Boolean(parseInt(codigo.consulta[0].REGISTRADO))) {
                // codigo registrado  
                // togle visual user
                toogleConfirmMessages(true);    // mostrar inputs codigo
                resetButtonPosition();

                // 15 minutos para verificar
                timerVerification();
                btnConfirmar.removeEventListener("click", enviarCodigoEmail, false);
                btnConfirmar.addEventListener("click", confirmarCodigoEmail, false);

                this.bodyToaster = MESSAGES_LIST.codigo.codigoEnviado.msj;
                this.backgroundToaster = MESSAGES_LIST.codigo.codigoEnviado.bg;

            } else {
                // error
                console.warn("Error al generar el código");
                this.bodyToaster = MESSAGES_LIST.codigo.errorGenerarCodigo.msj;
                this.backgroundToaster = MESSAGES_LIST.codigo.errorGenerarCodigo.bg;
            }

            // notification
            let data = {
                message: this.bodyToaster,
                bg: this.backgroundToaster
            };
            this.#generateToast(data);
            resetButtonPosition();
        }

        // confirmar codigo recibido
        const confirmarCodigoEmail = async () => {

            this.#showButtonLoading('#btn-confirmar-position');
            const confirmar = await confirmarPosicion('CONFIRMAR', Employee, Position);
            console.log(confirmar);
            resetButtonPosition();
            if (confirmar) {
                if (Boolean(parseInt(confirmar.consulta[0].CONSULTA)) && Boolean(parseInt(confirmar.consulta[0].CONFIRMADO))) {

                    // CODIGO CONFIRMADO
                    this.bodyToaster = MESSAGES_LIST.codigo.codigoconfirmado.msj;
                    this.backgroundToaster = MESSAGES_LIST.codigo.codigoconfirmado.bg;
                    console.log(MESSAGES_LIST.codigo.codigoconfirmado.msj);

                    // GUARDANDO EL LUGAR
                    loadAsignarLugarTrabajo();

                    // HIDE MODAL
                    toogleConfirmMessages(false);
                    $('#modalConfirmar').modal('hide');

                } else {
                    console.warn(MESSAGES_LIST.codigo.errorConfirmarCodigo.msj);

                    this.bodyToaster = MESSAGES_LIST.codigo.errorConfirmarCodigo.msj;
                    this.backgroundToaster = MESSAGES_LIST.codigo.errorConfirmarCodigo.bg;
                }
            }else {
                return 0;
            }

            // notification
            let data = {
                message: this.bodyToaster,
                bg: this.backgroundToaster
            };
            this.#generateToast(data);
        }

        // confirmar posicion
        const confirmarPosicion = async (TIPO_QUERY, Employee, Position) => {

            let code = 0;
            if (TIPO_QUERY === 'CONFIRMAR') {
                const inputElements = [...document.querySelectorAll('input.code-input')];
                code = inputElements.map(({ value }) => value).join('');
                console.log(code);
            }

            if (TIPO_QUERY === 'CONFIRMAR' && !code){ return 0;}

            const data = {
                TIPO_QUERY: TIPO_QUERY,
                idEmpleado: Employee.idEmpleado,
                emailEmpleado: Employee.empleadoEmaiL,
                idCentro: Employee.numeroCentro,
                idPosition: Position.id,
                tipoMovimiento: "A",
                idPositionNew: 0,
                codigo: code
            };

            this.positionService.setPosition(parseInt(Position.id));
            this.positionService.genNewCode(data);
            const peticionCodigo = await this.positionService.setCodigoConfirmacion();
            return (peticionCodigo.data.response) ? peticionCodigo.data.response : null;
        }

        // GUARDANDO EL LUGAR
        const asignarPosition = async () => {
            const asignarPosicion = await this.positionService.setPositionAsignada();
            return (asignarPosicion.data.response) ? asignarPosicion.data.response : null;
        }

        // RELACIONANDO EL USUARIO CON EL LUGAR
        const asignarUserPosition = async () => {
            const asignarUserPosicion = await this.positionService.setPositionAsignada();
            return (asignarUserPosicion.data.response) ? asignarUserPosicion.data.response : null;
        }

        const loadAsignarLugarTrabajo = async () => {
            // completando datos del empleado
            // ENVIAR PARAMETROS QUE FALTAN


            const asignar = await asignarPosition();
            const consulta = Boolean(parseInt(asignar.setPosicion[0].ASIGNADO));

            // notification
            let data = {
                message: MESSAGES_LIST.position.positionAsignada[consulta].msj,
                bg: MESSAGES_LIST.position.positionAsignada[consulta].bg
            };
            this.#generateToast(data);

            (consulta) ? loadRelacionarUsuarioPosicion(asignar) : 0;
        }

        const loadRelacionarUsuarioPosicion = async (response) => {
            // const relacionar = await asignarUserPosition();
            // const consulta = Boolean(parseInt(relacionar.setPosicion[0].ASIGNADO));

            // // notification
            // let data = {
            //     message: MESSAGES_LIST.position.positionAsignada[consulta].msj,
            //     bg: MESSAGES_LIST.position.positionAsignada[consulta].bg
            // };
            // this.#generateToast(data);
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
                minutosRestantes = 1 - minutosCorriendo;
                segundosRestantes = 60 - segundosCorriendo;

                $("#minutosRestantes").text(minutosRestantes);
                $("#secondsRestantes").text(segundosRestantes);
                if (minutosCorriendo >= 2 && segundosCorriendo >= 0) {
                    
                    // notification
                    let data = {
                        message: MESSAGES_LIST.codigo.tiempoExcedidoConfirmarCodigo.msj,
                        bg: MESSAGES_LIST.codigo.tiempoExcedidoConfirmarCodigo.bg
                    };
                    this.#generateToast(data);
                    console.warn(MESSAGES_LIST.codigo.tiempoExcedidoConfirmarCodigo.msj);

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

    #generateToast(data){
        let idToaster;
        idToaster = 'toast' + Math.floor(Math.random() * 1000);
        // notification
        let args = {
            id: idToaster,
            title: 'Notificación',
            message: data.message,
            background: data.bg,
            animation: false,
            autohide: true,
            delay: 4000
        }
        this.notificationCenter.setNewToast(args);
        const toast = this.notificationCenter.getToast;

        $("#notificationCenter").append(toast);

        $('#' + idToaster).toast({
            animation: args.animation,
            delay: args.delay
        });

        $('#' + idToaster).toast('show');
    }
    
}