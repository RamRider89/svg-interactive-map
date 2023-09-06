class EmpleadoModel {
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
            nombreEmpresa: this.nombreEmpresa,
            numeroCentro: this.numeroCentro,
            nombreCentro: this.nombreCentro,
            numeroEmpleado: this.numeroEmpleado,
            nombreEmpleado: this.nombreEmpleado,
            nombrePuesto: this.nombrePuesto,
            numeroTelefono: this.numeroTelefono,
            fechaNacimiento: this.fechaNacimiento,
            nombreGerente: this.nombreGerente,
            numeroGerente: this.numeroGerente,
            empleadoEmaiL: this.empleadoEmaiL
        }

        return data;
    }

    setPosicionEmpleado(data) {
        //const data = (args.data.response.length > 0) ? args.data.response[0] : null;
        if (data) {
            this.setIdEmpleado(String(data.NumeroEmpleado).trim());
            this.setNombreEmpresa(String(data.NombreEmpresa).trim());
            this.setNumeroCentro(String(data.Centro).trim());
            this.setNombreCentro(String(data.NombreCentro).trim());
            this.setNumeroEmpleado(String(data.NumeroEmpleado).trim());
            this.setNombreEmpleado(this.#namesToTitleCase(data.Nombre, data.ApellidoPaterno, data.ApellidoMaterno));
            this.setNombrePuesto(this.#toTitleCase(data.NombrePuesto));
            this.setNumeroTelefono(String(data.Telefono).trim());
            this.setFechaNacimiento(this.#getBirthday(String(data.FechaNacimiento).trim()));
            this.setNombreGerente(this.#namesToTitleCase(data.NombreGerente, data.ApellidoPaternoGerente, data.ApellidoMaternoGerente));
            this.setNumeroGerente(String(data.NumeroGerente).trim());
        } else {

        }
    }

    showInformation(format = true){
        console.info(this.getEmpleado());
    }

    // utils
    #toTitleCase(texto) {
        const str = String(texto).trim().toLowerCase();
        return str.replace(/(?:^|\s)\w/g, function (match) {
            return match.toUpperCase();
        });
    }

    #namesToTitleCase(name, apellPat, apellMat) {
        const nameCompleto = String(String(name).trim() + ' ' + String(apellPat).trim() + ' ' + String(apellMat).trim()).toLowerCase();
        return nameCompleto.replace(/(?:^|\s)\w/g, function (match) {
            return match.toUpperCase();
        });
    }

    #getBirthday(fecha) {
        const nacimiento = new Date(fecha.trim());
        const day = nacimiento.getDate();
        const month = nacimiento.getMonth();
        return String(day + ' de ' + this.#MESES[month]);
    }

}