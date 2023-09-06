class LiderService {
    constructor(centro) { 
        this.centro = centro;
    }

    get getLideresCentro() {
        return this.lideresCentro(this.centro);
    }

    async lideresCentro(centro) {
        // loading lideres
        const data = {
            TIPO_QUERY: 'CENTRO',
            idUsuario: null,
            idCentro: centro,
            nombreEmpleado: null
        };

        const peticion = new Promise((resolve) => {
            resolve(
                $.ajax({
                    type: 'POST',
                    url: URL_API_CAMPUS + 'api/getlidercentro',
                    dataType: 'json',
                    data: JSON.stringify(data),
                    async: true,
                    beforeSend: function () { },
                    success: function (response) {
                        if (response.meta.status != "SUCCESS") { console.warn(response.meta) }
                    },
                    error: function (request, status, error) {
                        console.warn(error.responseText);
                    }
                }).done(function () { }),
            );
        });
        
        peticion.then((response) => { return 0; }).catch((err) => {console.error(err);});
        return peticion;
    }

}