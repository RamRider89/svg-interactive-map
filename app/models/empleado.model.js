export class EmpleadoModel {
    constructor() {
        this.setIdEmpleado(null);
        this.setNumeroEmpresa(null);
        this.setNombreEmpresa(null);
        this.setNumeroCentro(null);
        this.setNombreCentro(null);
        this.setNumeroEmpleado(null);
        this.setNombre(null);
        this.setApellidoPaterno(null);
        this.setApellidoMaterno(null);
        this.setNombreCompleto(null);
        this.setNumeroPuesto(null);
        this.setNombrePuesto(null);
        this.setNumeroTelefono(null);
        this.setEmpleadoEmaiL(null);
        this.setFechaNacimiento(null);
        this.setFechaCumpleanos(null);
        this.setNombreGerente(null);
        this.setNumeroGerente(null);
        this.setNumeroLider(null);
        this.setTipoTrabajo(null);
        this.setFotoURL(null);
    }

    #MESES = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    getIdEmpleado() { return this.idEmpleado; }
    setIdEmpleado(id) { this.idEmpleado = id; }

    getNumeroEmpresa() { return this.numeroEmpresa; }
    setNumeroEmpresa(empresa) { this.numeroEmpresa = empresa; }

    getNombreEmpresa() { return this.nombreEmpresa; }
    setNombreEmpresa(empresa) { this.nombreEmpresa = empresa; }

    getNumeroCentro() { return this.numeroCentro; }
    setNumeroCentro(centro) { this.numeroCentro = centro; }

    getNombreCentro() { return this.nombreCentro; }
    setNombreCentro(nombreCentro) { this.nombreCentro = nombreCentro; }

    getNumeroEmpleado() { return this.numeroEmpleado; }
    setNumeroEmpleado(numeroEmpleado) { this.numeroEmpleado = numeroEmpleado; }

    getNombre() { return this.nombre; }
    setNombre(nombre) { this.nombre = nombre; }

    getApellidoPaterno() { return this.apellidoPaterno; }
    setApellidoPaterno(apellidoPaterno) { this.apellidoPaterno = apellidoPaterno; }

    getApellidoMaterno() { return this.apellidoMaterno; }
    setApellidoMaterno(apellidoMaterno) { this.apellidoMaterno = apellidoMaterno; }

    getNombreCompleto() { return this.nombreCompleto; }
    setNombreCompleto(nombreCompleto) { this.nombreCompleto = nombreCompleto; }

    getNumeroPuesto() { return this.numeroPuesto; }
    setNumeroPuesto(numeroPuesto) { this.numeroPuesto = numeroPuesto; }

    getNombrePuesto() { return this.nombrePuesto; }
    setNombrePuesto(nombrePuesto) { this.nombrePuesto = nombrePuesto; }

    getNumeroTelefono() { return this.numeroTelefono; }
    setNumeroTelefono(numeroTelefono) { this.numeroTelefono = numeroTelefono; }

    getFechaNacimiento() { return this.fechaNacimiento; }
    setFechaNacimiento(fechaNacimiento) { this.fechaNacimiento = fechaNacimiento; }

    getFechaCumpleanos() { return this.fechaCumpleanos; }
    setFechaCumpleanos(fechaCumpleanos) { this.fechaCumpleanos = fechaCumpleanos; }

    getNombreGerente() { return this.nombreGerente; }
    setNombreGerente(nombreGerente) { this.nombreGerente = nombreGerente; }

    getNumeroGerente() { return this.numeroGerente; }
    setNumeroGerente(numeroGerente) { this.numeroGerente = numeroGerente; }

    getEmpleadoEmaiL() { return this.empleadoEmaiL; }
    setEmpleadoEmaiL(email) { this.empleadoEmaiL = email; }

    getNumeroLider() { return this.numeroLider; }
    setNumeroLider(numeroLider) { this.numeroLider = numeroLider; }

    getTipoTrabajo() { return this.tipoTrabajo; }
    setTipoTrabajo(tipoTrabajo) { this.tipoTrabajo = tipoTrabajo; }

    getFotoURL() { return this.fotoURL; }
    setFotoURL(fotoURL) { this.fotoURL = fotoURL; }

    getEmpleado() {
        const data = {
            idEmpleado: this.idEmpleado,
            numeroEmpresa: this.numeroEmpresa,
            nombreEmpresa: this.nombreEmpresa,
            numeroCentro: this.numeroCentro,
            nombreCentro: this.nombreCentro,
            numeroEmpleado: this.numeroEmpleado,
            nombre: this.nombre,
            apellidoPaterno: this.apellidoPaterno,
            apellidoMaterno: this.apellidoMaterno,
            nombreCompleto: this.nombreCompleto,
            numeroPuesto: this.numeroPuesto,
            nombrePuesto: this.nombrePuesto,
            numeroTelefono: this.numeroTelefono,
            fechaNacimiento: this.fechaNacimiento,
            fechaCumpleanos: this.fechaCumpleanos,
            nombreGerente: this.nombreGerente,
            numeroGerente: this.numeroGerente,
            empleadoEmaiL: this.empleadoEmaiL,
            numeroLider: this.numeroLider,
            tipoTrabajo: this.tipoTrabajo,
            fotoURL: this.fotoURL
        }

        return data;
    }

    setEmpleado(data) {

        if (!data) { return 0;}

            this.setIdEmpleado(String(data.NumeroEmpleado).trim());
            this.setNumeroEmpresa(String(data.Empresa).trim());
            this.setNombreEmpresa(String(data.NombreEmpresa).trim());
            this.setNumeroCentro(String(data.Centro).trim());
            this.setNombreCentro(String(data.NombreCentro).trim());
            this.setNumeroEmpleado(String(data.NumeroEmpleado).trim());
            this.setNombre(this.#toTitleCase(data.Nombre));
            this.setApellidoPaterno(this.#toTitleCase(data.ApellidoPaterno));
            this.setApellidoMaterno(this.#toTitleCase(data.ApellidoMaterno));
            this.setNombreCompleto(this.#namesToTitleCase(data.Nombre, data.ApellidoPaterno, data.ApellidoMaterno));
            this.setNumeroPuesto(String(data.NumeroPuesto).trim());
            this.setNombrePuesto(this.#toTitleCase(data.NombrePuesto));
            this.setNumeroTelefono(String(data.Telefono).trim());
            this.setFechaNacimiento(String(data.FechaNacimiento).trim());
            this.setFechaCumpleanos(this.#getBirthday(String(data.FechaNacimiento).trim()));
            this.setNombreGerente(this.#namesToTitleCase(data.NombreGerente, data.ApellidoPaternoGerente, data.ApellidoMaternoGerente));
            this.setNumeroGerente(String(data.NumeroGerente).trim());
    }

    setEmpleadoComplementos(data) {
        if (!data) { return 0;}

            this.setNumeroLider(data.numeroLider);
            this.setTipoTrabajo(data.tipoTrabajo);
            this.setFotoURL(String(data.fotoURL).trim());
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