class MapaService {
    constructor() { }

    get mapaCampus() {
        return this.getMap();
    }

    async getMap() {
        const peticion = new Promise((resolve) => {
            resolve(
                // OBTENER EL MAPA
                $.ajax({
                    method: 'GET',
                    url: URL_CAMPUS + '/assets/maps/CampusDigital.svg',
                    dataType: 'xml',
                    cache: false,
                    async: false,
                    beforeSend: () => { console.log('-> Loading map.'); },
                    success: async function (xml) {
                            return (xml) ? xml : null;
                    },
                    error: () => { console.warn('Error al cargar el mapa: 404 || 505'); },
                    complete: () => {}
                }),
            );
        });

        peticion.then((response) => { return 0; }).catch((err) => { console.error(err); });
        return peticion;
    }

}