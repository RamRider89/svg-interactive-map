class PositionModule {
    constructor() {
        this.position = null;
        this.Position = new PositionModel();
        this.positionService = new PositionService();
        this.empleadoModule = new EmpleadoModule();
    }
    
    setCampusPosition(id) {
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
            console.warn('Error al cargar la información del lugar de trabajo');
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
                    console.warn('Complete los campos necesarios.');
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

        const confirmModal = async () => {
            //const modal = new bootstrap.Modal(document.getElementById('modalConfirmar'));

            const respuesta = new Promise((resolve, reject) => {
                $('#modalConfirmar').modal('show');
                $('#modalConfirmar .btn-confirmar').click(() => {
                    this.#showButtonLoading('.btn-confirmar');
                    resolve(true);
                });
                $('#modalConfirmar .btn-cancelar').click(() => {
                    reject(false);
                });
            }).then(async (val) => {
                //Employee.showInformation();
                //Position.showInformation();
                const asignado = await confirmarPosicion('REGISTRAR', Employee, Position);
                console.log(asignado);

                if (Boolean(parseInt(asignado.consulta[0].CONSULTA)) && Boolean(parseInt(asignado.consulta[0].REGISTRADO))) {
                    // codigo registrado  
                    $("#collapseCodigoConfirmacion").addClass("show");

                    // BOTONES MODAL CONFIRMAR
                    $('#modalConfirmar .btn-confirmar').prop("disabled", false);
                    $('#modalConfirmar .btn-confirmar').html('<i class="fa fa-check"></i> Confirmar');

                    $('#modalConfirmar .btn-confirmar').click(async () => {
                        const confirmar = await confirmarPosicion('CONFIRMAR', Employee, Position);
                        console.log(confirmar);
                        if (Boolean(parseInt(confirmar.consulta[0].CONSULTA)) && Boolean(parseInt(confirmar.consulta[0].CONFIRMADO))) {
                            console.log("Codigo confirmado");
                            $('#modalConfirmar .btn-confirmar').html('<i class="fa fa-check"></i> Confirmar');
                            $("#collapseCodigoConfirmacion").removeClass("show");
                            $('#modalConfirmar').modal('hide');

                        }else {
                            console.warn("Error al confirmar el código");
                        }
                    });
                    
                }else { 
                    // error
                    console.warn("Error al generar el código");
                }

                


            }).catch(function (err) {
                //user clicked cancel
                console.warn(err);
            });
        }

        const confirmarPosicion = async (TIPO_QUERY, Employee, Position) => {

            let code = 0;
            if (TIPO_QUERY === 'CONFIRMAR') {
                const inputElements = [...document.querySelectorAll('input.code-input')];
                code = inputElements.map(({ value }) => value).join('');
                console.log(code);
            }
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
            const positionData = (peticionCodigo.data.response) ? peticionCodigo.data.response : null;

            // return
            return positionData;
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
            (idEmpleado > 0) ? this.#getEmpleado(idEmpleado) : console.warn("El ID de empleado es incorrecto.");
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
        $('#modalConfirmar .btn-confirmar').click(() => {
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
    
}