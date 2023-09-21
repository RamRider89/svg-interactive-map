export class UsersPosition {
    constructor() {

        /**
         * TABLA [CampusUsersPositions]
         * COLUMNAS
            [idEmpleado] -> INT
            [nombre] -> VARCHAR
            [apellidoPaterno] -> VARCHAR
            [apellidoMaterno] -> VARCHAR
            [puesto] -> INT
            [centro] -> INT
            [correo] -> VARCHAR
            [telefono] -> INT
            [lider] -> INT
            [gerente] -> INT
            [empresa] -> INT
            [tipoTrabajo] -> INT
            [cumpleanos] -> DATE
            [fotoUrl] -> VARCHAR
         */
        this.setID(null);
        this.setIdEmpleado(null);
        this.setNombre(null);
        this.setApellidoPaterno(null);
        this.setApellidoMaterno(null);
        this.setPuesto(null);
        this.setCentro(null);
        this.setCorreo(null);
        this.setTelefono(null);
        this.setLider(null);
        this.setGerente(null);
        this.setEmpresa(null);
        this.setTipoTrabajo(null);
        this.setCumpleanos(null);
        this.setFotoURL(null);
    }

    #MESES = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    getID() { return this.id; }
    setID(id) { this.id = id; }

    getIdEmpleado() { return this.idEmpleado; }
    setIdEmpleado(id) { this.idEmpleado = id; }

    getNombre() { return this.nombre; }
    setNombre(nombre) { this.nombre = nombre; }

    getApellidoPaterno() { return this.apellidoPaterno; }
    setApellidoPaterno(apellidoPaterno) { this.apellidoPaterno = apellidoPaterno; }

    getApellidoMaterno() { return this.apellidoMaterno; }
    setApellidoMaterno(apellidoMaterno) { this.apellidoMaterno = apellidoMaterno; }

    getPuesto() { return this.puesto; }
    setPuesto(puesto) { this.puesto = puesto; }

    getCentro() { return this.centro; }
    setCentro(centro) { this.centro = centro; }

    getCorreo() { return this.correo; }
    setCorreo(correo) { this.correo = correo; }

    getTelefono() { return this.telefono; }
    setTelefono(telefono) { this.telefono = telefono; }

    getLider() { return this.lider; }
    setLider(lider) { this.lider = lider; }

    getGerente() { return this.gerente; }
    setGerente(gerente) { this.gerente = gerente; }

    getEmpresa() { return this.empresa; }
    setEmpresa(empresa) { this.empresa = empresa; }

    getTipoTrabajo() { return this.tipoTrabajo; }
    setTipoTrabajo(tipoTrabajo) { this.tipoTrabajo = tipoTrabajo; }

    getCumpleanos() { return this.cumpleanos; }
    setCumpleanos(cumpleanos) { this.cumpleanos = cumpleanos; }

    getFotoURL() { return this.fotoURL; }
    setFotoURL(fotoURL) { this.fotoURL = fotoURL; }


    getPosicionEmpleado() {
        const data = {
            idEmpleado: this.idEmpleado,
            nombre: this.nombre,
            apellidoPaterno: this.apellidoPaterno,
            apellidoMaterno: this.apellidoMaterno,
            puesto: this.puesto,
            correo: this.correo,
            telefono: this.telefono,
            lider: this.lider,
            gerente: this.gerente,
            empresa: this.empresa,
            tipoTrabajo: this.tipoTrabajo,
            cumpleanos: this.cumpleanos,
            fotoURL: this.fotoURL
        }

        return data;
    }

    setPosicionEmpleado(data) {
        //const data = (args.data.response.length > 0) ? args.data.response[0] : null;
        if (data) {
            this.setIdEmpleado(data.idEmpleado);
            this.setNombre(data.nombre);
            this.setApellidoPaterno(data.apellidoPaterno);
            this.setApellidoMaterno(data.apellidoMaterno);
            this.setPuesto(data.numeroPuesto);
            this.setCentro(data.numeroCentro);
            this.setCorreo(data.empleadoEmaiL);
            this.setTelefono(data.numeroTelefono);

            this.setLider(data.nombre);              // -----

            this.setGerente(data.numeroGerente);
            this.setEmpresa(data.numeroEmpresa);

            this.setTipoTrabajo(data.nombre);         // -----   

            this.setCumpleanos(data.fechaNacimiento);

            this.setFotoURL(data.nombre);              // -----



//            numeroEmpleado: this.numeroEmpleado,
  //          nombreEmpleado: this.nombreEmpleado,


        } else {

        }
    }

    showInformation(format = true){
        console.info(this.getPosicionEmpleado());
    }

}