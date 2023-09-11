class TiposService {
    constructor() {}

    get tipoTrabajo() {
        return this.getTipoTrabajo();
    }

    get tipoGruposTrabajo() {
        return this.getTipoGruposTrabajo();
    }

    async getTipoTrabajo() {
        // loading tipos de trabajo
        const peticion = new Promise((resolve) => {
            resolve( 
                $.ajax(
                {
                type: 'GET',
                url: URL_API_CAMPUS + 'api/gettipotrabajo/0',
                dataType: 'json',
                async: true,
                success: function (response) {
                    return (response.meta.status == "SUCCESS") ? response.data.response : null;
                },
                error: function (request, status, error) { console.warn(error.responseText); }
                }).done(function () { }),
            );
        });

        peticion.then((response) => { return 0; }).catch((err) => {console.error(err);});
        return peticion;
    }

    getTipoGruposTrabajo() {
        // loading tipos de grupo de trabajo
        const peticion = new Promise((resolve) => {
            resolve(
                $.ajax(
                    {
                    type: 'GET',
                    url: URL_API_CAMPUS + 'api/gettipogrupos/0',
                    dataType: 'json',
                    async: true,
                    success: function (response) {
                        (response.meta.status == "SUCCESS") ? response.data.response : null;
                    },
                    error: function (request, status, error) { console.warn(error.responseText); }
                }).done(function () { }),
            );
        });

        peticion.then((response) => { return 0; }).catch((err) => { console.error(err); });
        return peticion;
    }

}
    
