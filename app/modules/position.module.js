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
                    resolve(true);
                });
                $('#modalConfirmar .btn-cancelar').click(() => {
                    reject(false);
                });
            }).then(async (val) => {
                Employee.showInformation();
                Position.showInformation();

                const asignado = await confirmarPosicion(Position.id);

                console.log("Lugar asignado: " + asignado);


            }).catch(function (err) {
                //user clicked cancel
                console.warn(err)
            });
        }

        const confirmarPosicion = async (id) => {
            this.positionService.setPosition(parseInt(id));
            const peticionPosicion = await this.positionService.getPosition();
            const positionData = (peticionPosicion.data.response.length > 0) ? peticionPosicion.data.response[0] : null;
            return (Boolean(parseInt(positionData.asignado)));
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
    }
    
}