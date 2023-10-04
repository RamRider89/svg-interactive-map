export class PositionModel {
    constructor() {
        this.setID(null);
        this.setAsignado(null);
        this.setEdificio(null);
        this.setFechaAsignado(null);
        this.setGerenteAsignado(null);
        this.setGerenteFechaAsignado(null);
        this.setGrupoAsignado(null);
        this.setIdGrupo(null);
        this.setNombreGrupo(null);
        this.setNombrePosicion(null);
        this.setNombreTipo(null);
        this.setIdEmpleado(null);

    }

    getID() { return this.id; }
    setID(id) { this.id = id; }

    getEdificio() { return this.edificio; }
    setEdificio(edificio) { this.edificio = edificio; }

    getFechaAsignado() { return this.fechaAsignado; }
    setFechaAsignado(fechaAsignado) { this.fechaAsignado = fechaAsignado; }

    getAsignado() { return this.asignado; }
    setAsignado(asignado) { this.asignado = asignado; }

    getGerenteAsignado() { return this.gerenteAsignado; }
    setGerenteAsignado(gerenteAsignado) { this.gerenteAsignado = gerenteAsignado; }

    getGerenteFechaAsignado() { return this.gerenteFechaAsignado; }
    setGerenteFechaAsignado(gerenteFechaAsignado) { this.gerenteFechaAsignado = gerenteFechaAsignado; }

    getGrupoAsignado() { return this.grupoAsignado; }
    setGrupoAsignado(grupoAsignado) { this.grupoAsignado = grupoAsignado; }

    getIdGrupo() { return this.idGrupo; }
    setIdGrupo(idGrupo) { this.idGrupo = idGrupo; }

    getNombreGrupo() { return this.nombreGrupo; }
    setNombreGrupo(nombreGrupo) { this.nombreGrupo = nombreGrupo; }

    getNombrePosicion() { return this.nombrePosicion; }
    setNombrePosicion(nombrePosicion) { this.nombrePosicion = nombrePosicion; }

    getNombreTipo() { return this.nombreTipo; }
    setNombreTipo(nombreTipo) { this.nombreTipo = nombreTipo; }

    getIdEmpleado() { return this.idEmpleado; }
    setIdEmpleado(idEmpleado) { this.idEmpleado = idEmpleado; }


    getPosition() {
        const data = {
            id: this.id,
            edificio: this.edificio,
            fechaAsignado: this.fechaAsignado,
            asignado: this.asignado,
            gerenteAsignado: this.gerenteAsignado,
            gerenteFechaAsignado: this.gerenteFechaAsignado,
            grupoAsignado: this.grupoAsignado,
            idGrupo: this.idGrupo,
            nombreGrupo: this.nombreGrupo,
            nombrePosicion: this.nombrePosicion,
            nombreTipo: this.nombreTipo,
            idEmpleado: this.idEmpleado
        }

        return data;
    }

    setPosition(data) {
        //const data = (args.data.response.length > 0) ? args.data.response[0] : null;
        if (data) {
            this.setID(parseInt(data.id));
            this.setEdificio(String(data.edificio));
            this.setFechaAsignado(new Date(data.fechaAsignado));
            this.setAsignado(Boolean(parseInt(data.asignado)));
            this.setGerenteAsignado(parseInt(data.gerenteAsignado));
            this.setGerenteFechaAsignado(new Date(data.gerenteFechaAsignado));
            this.setGrupoAsignado(Boolean(parseInt(data.grupoAsignado)));
            this.setIdGrupo(parseInt(data.idGrupo));
            this.setNombreGrupo(String(data.nombreGrupo));
            this.setNombrePosicion(String(data.nombrePosicion));
            this.setNombreTipo(String(data.nombreTipo));
            this.setIdEmpleado(parseInt(data.idEmpleado));
        } else {

            this.setID(null);
            this.setEdificio(null);
            this.setFechaAsignado(null);
            this.setAsignado(null);
            this.setGerenteAsignado(null);
            this.setGerenteFechaAsignado(null);
            this.setGrupoAsignado(null);
            this.setIdGrupo(null);
            this.setNombreGrupo(null);
            this.setNombrePosicion(null);
            this.setNombreTipo(null);
            this.setIdEmpleado(null);
        }
    }

    showInformation(format = true){
        console.info(this.getPosition());
    }

    // utils

}