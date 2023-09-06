class MapaModel {
    constructor() {
        this.setID(null);
    }

    getID() { return this.id; }
    setID(id) { this.id = id; }

    getPosition() {
        const data = {
            id: this.id
        }

        return data;
    }

    setPosition(data) {
        //const data = (args.data.response.length > 0) ? args.data.response[0] : null;
    }

    showInformation(format = true){
        console.info(this.getPosition());
    }

    // utils

}