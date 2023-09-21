import { MapaService } from '../services/mapa.services.js';
import { TiposService } from '../services/tipos.services.js';
import { MapaModule } from './mapa.module.js';
import { PositionModule } from './position.module.js';

$("body").ready(function () {

    const mapaService = new MapaService();
    const tiposService = new TiposService();
    const mapaModule = new MapaModule();

    const MESES = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    loadMapaCampus();
    loadDataCampusGeneral();

    async function loadMapaCampus() {
        // getMapa
        const peticionMapa = await mapaService.mapaCampus;
        const mapaCampus = (peticionMapa.rootElement) ? peticionMapa.rootElement.outerHTML : null;
        
        //PETICION MAPA
        await drawSVG(mapaCampus)
            .then((xml) => { console.log(xml) })
            .catch((err) => { console.error(err) })
            .finally(() => {
                console.log('-> Draw completed.');
                mapaModule.setMapaCampusControls(0);
            });
        document.getElementById("page-top").style.cursor = "default";
    }

    async function drawSVG(svg) {
        return new Promise((resolve, reject) => {
            (Promise.any($("#imageContainer").html(svg))) ? resolve('-> SVG OK') : reject(new Error('Failed to draw'));
        });
    }


    // load generales
    async function loadDataCampusGeneral() {
        // getTiposTrabajo
        const peticiontTipoTrabajo = await tiposService.tipoTrabajo;
        const tiposTrabajo = (peticiontTipoTrabajo.data.response.length > 0) ? peticiontTipoTrabajo.data.response : null;

        // getTipoGruposTrabajo
        const peticiontTipoGruposTrabajo = await tiposService.tipoGruposTrabajo;
        const tipoGruposTrabajo = (peticiontTipoGruposTrabajo.data.response.length > 0) ? peticiontTipoGruposTrabajo.data.response : null;

        localStorage.setItem("TIPOSTRABAJO", JSON.stringify(tiposTrabajo));
        localStorage.setItem("TIPOSGRUPOTRABAJO", JSON.stringify(tipoGruposTrabajo));

        console.log(tiposTrabajo);
        console.log(tipoGruposTrabajo);
    }

    // LOAD POSITION DATA
    async function getPositionData(position) {
        console.log('Loading position: ' + position);
        const positionService = new PositionService(String(position));
        const peticiontPosicion = await positionService.positionData;
        const positionData = (peticiontPosicion.data.response.length > 0) ? peticiontPosicion.data.response[0] : null;
        loadPositionData(positionData);
    }

    function loadPositionData(data) {
        const Position = new PositionModel();
        Position.setPosition(data);
        Position.showInformation();
        
        if (Position) {
            (Position.asignado) ? 0 : showModalAsignarPosicion(Position);
        }else {
            console.warn('Error al cargar la información del lugar de trabajo');
        }

        // LOAD ASIGNAR O CAMBIAR

        function showModalAsignarPosicion(Position) {

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

                    loadGrupoData(Position);
                    loadValidateEvents();

                    modalAsignar.show();
                },
                error: (status, error) => { console.warn(status + ' ' + error.responseText); }
            }).done(function () { });
        }

        function loadGrupoData(Position) {
            if (Position.id) {
                $("#infoTipoLugarTrabajo").text(Position.nombreTipo);
                $("#infoGrupoLugarTrabajo").text(Position.nombreGrupo);
                $("#infoPosicionLugarTrabajo").text(Position.nombrePosicion);
                $("#infoEdificioTrabajo").text(Position.edificio);
            }else{
                $(".grupoData").text('');
            }
        }

        function loadValidateEvents() {
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
                //console.log($("#idEmpleado").val());
            });

            $("#btnGetEmpleado").on('click', () => {
                const idEmpleado = parseInt($("#idEmpleado").val());
                (idEmpleado > 0) ? getEmpleado(idEmpleado) : console.warn("El ID de empleado es incorrecto.");
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

        // GET EMPLEADO
        async function getEmpleado(idEmpleado) {
            // OBTENER AL EMPLEADO
            console.info('Loading data from employee: ' + idEmpleado);

            showButtonLoading("#btnGetEmpleado");
            setProgresBarLoad('#progresBar', 30);

            // GET EMPLEADO DATA
            const empleadoService = new EmpleadoService(parseInt(idEmpleado));
            const peticionEmpleado = await empleadoService.getEpleadoData;
            const empleadoData = (peticionEmpleado.data.response.length > 0) ? peticionEmpleado.data.response[0] : null;

            // EMPLEADO MODEL
            const Empleado = new EmpleadoModel();
            Empleado.setEmpleado(empleadoData);
            setProgresBarLoad('#progresBar', 60);

            // GET EMAIL
            empleadoService.setEmpleadoName(Empleado.nombreEmpleado);
            const peticionContacto = await empleadoService.getContactoEpleado;
            const contactoData = (peticionContacto.data.response.length > 0) ? peticionContacto.data.response[0] : null;
            const empleadoEmail = (contactoData) ? contactoData.email : null;
            Empleado.setEmpleadoEmaiL(empleadoEmail);
            setProgresBarLoad('#progresBar', 80);

            loadEmpleadoData(Empleado, contactoData);

        }

        function loadEmpleadoData(employee, contacto) {
            if (employee) {
                // cargando datos en el modal
                $("#infoEmpresa").text(employee.nombreEmpresa);
                $("#infoCentro").text(employee.numeroCentro);
                $("#infoCentroName").text(employee.nombreCentro);
                $("#infoEmpleadoNombre").text(employee.nombreEmpleado);
                $("#infoEmpleadoPuesto").text(employee.nombrePuesto);
                $("#infoEmpleadoEmail").text(employee.empleadoEmaiL);
                $("#infoEmpleadoTelefono").text(employee.numeroTelefono);
                $("#infoEmpleadoCumple").text(employee.fechaNacimiento);
                $("#infoEmpleadoNomGerente").text(employee.nombreGerente);
                $("#infoEmpleadoNumGerente").text(employee.numeroGerente);

                // GET LIDERES 
                getLideresCentro(employee.numeroCentro);
                
                saveDataEmployee(employee, contacto);

                // $("#idLider")
                loadAsignarLugar(employee);

            } else {
                console.warn('El empleado no existe');
                resetModalInfoEmpleado();
                return null;
            }
            showButtonSearch("#btnGetEmpleado");
        }

        function saveDataEmployee(employee, contacto) {
            setProgresBarLoad('#progresBar', 100);
            localStorage.setItem("EMPLEADODATA", JSON.stringify(employee));
            localStorage.setItem("CONTACTO", JSON.stringify(contacto));
            employee.showInformation();
            console.log(contacto);
        }


        // POST | GET EMAIL EMPLEADO
        async function getEmailEmpleado(name) {


        }

        // POST | GET LIDER CENTRO
        async function getLideresCentro(centro) {
            const liderService = new LiderService(parseInt(centro));
            const peticionLideresCentro = await liderService.getLideresCentro;
            const lideresCentro = loadLideresCentro((peticionLideresCentro.data.response.length > 0) ? peticionLideresCentro.data.response : null, centro);
            
            console.log(lideresCentro);
            setProgresBarLoad('#progresBar', 90);
        }

        function loadLideresCentro(lideresCentro, idCentro) {
            
            resetSelect('#idLiderTrabajo');
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
                nameLider = namesToTitleCase(item.Nombre, item.ApellidoPaterno, item.ApellidoMaterno);
                
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

        function loadAsignarLugar(employee) {
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
                    }else {
                        event.preventDefault();
                        event.stopPropagation();
                        console.log("FORM OK");
                        confirmModal();

                        //(confirmar) ? console.log("guardar") : console.log("cencelo");
                    }
                }, false);
            })();

            const confirmModal = (function () {
                //const modal = new bootstrap.Modal(document.getElementById('modalConfirmar'));
                const confirm = new Promise(function (resolve, reject) {
                    $('#modalConfirmar').modal('show');
                    $('#modalConfirmar .btn-confirmar').click(function () {
                        resolve(true);
                    });
                    $('#modalConfirmar .btn-cancelar').click(function () {
                        reject(false);
                    });
                }).then(function (val) {
                    //val is your returned value. argument called with resolve.
                    console.log(val);
                    asignarLugarEmpleado(employee, Position);
                }).catch(function (err) {
                    //user clicked cancel
                    console.log('Cancelado')
                });
            });

            const asignarLugarEmpleado = (function (employee, Position) {
                employee.showInformation();
                Position.showInformation();
            });

        }



        // VISUAL ANIMATIONS
        function resetModalInfoEmpleado() {
            $(".empleadoData").text('');
            $("#infoTipoTrabajo").val('');
        }

        function showButtonLoading(button) {
            $(button).prop("disabled", true);
            $(button).html('<span class="spinner-border spinner-border-sm"></span> Cargando...');
        }

        function showButtonSearch(button) {
            $(button).prop("disabled", false);
            $(button).html('<i class="fa fa-search"></i> Buscar');  
        }

        function setProgresBarLoad(progresbar, load){
            (load <= 50) ? $(String(progresbar) + ' > .progress-bar').show() : 0

            $(String(progresbar) + ' > .progress-bar').css('width', String(load) + '%');
            
            (load >= 100) ? hideProgresBar(progresbar) : 0
        }

        function hideProgresBar(progresbar) {
            setTimeout(() => {
                $(String(progresbar) + ' > .progress-bar').css("width", "0%").hide();
            }, 1000);
        }

        function resetSelect(select) {
            $(String(select)).html('').append($('<option>', {
                value: '',
                text: 'Selecciona..'
            }));
        }

        // UTILS
        function toTitleCase(texto) {
            const str = String(texto).trim().toLowerCase();
            return str.replace(/(?:^|\s)\w/g, function (match) {
                return match.toUpperCase();
            });
        }

        function namesToTitleCase(name, apellPat, apellMat) {
            const nameCompleto = String(String(name).trim() + ' ' + String(apellPat).trim() + ' ' + String(apellMat).trim()).toLowerCase();
            return nameCompleto.replace(/(?:^|\s)\w/g, function (match) {
                return match.toUpperCase();
            });
        }

        function getBirthday(fecha) {
            const nacimiento = new Date(fecha.trim());
            const day = nacimiento.getDate();
            const month = nacimiento.getMonth();
            return String(day + ' de ' + MESES[month]);
        }
    }

});